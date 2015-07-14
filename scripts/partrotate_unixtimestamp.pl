#!/usr/bin/perl
#
# partrotate_unixtimestamp - perl script for mySQL partition rotation
#
# Copyright (C) 2011-2014 Alexandr Dubovikov (alexandr.dubovikov@gmail.com)
#
# This file is part of webhomer, a free capture server.
#
# partrotate_unixtimestamp is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 3 of the License, or
# (at your option) any later version
#
# partrotate_unixtimestamp is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

use DBI;

$version = "0.4.0";
$mysql_table = $ARGV[1] // "sip_capture";
$mysql_dbname = $ARGV[0] // "homer_data";
$mysql_user = "homer_user";
$mysql_password = "homer_password";
$mysql_host = "localhost";
$maxparts = $ARGV[3] // 6; #6 days How long keep the data in the DB
$newparts = 2; #new partitions for 2 days. Anyway, start this script daily!
@stepsvalues = (86400, 3600, 1800, 900); 
$partstep = $ARGV[2] // 0; # 0 - Day, 1 - Hour, 2 - 30 Minutes, 3 - 15 Minutes 
$engine = "InnoDB"; #MyISAM or InnoDB
$compress = "ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=8"; #Enable this if you want use barracuda format or set var to empty.

#Check it
$partstep=0 if(!defined $stepsvalues[$partstep]);
#Mystep
$mystep = $stepsvalues[$partstep];
#Coof

# Optionally load override configuration. perl format
$rc = "/etc/sysconfig/partrotaterc";
if (-e $rc) {
  do $rc;
}

$coof=int(86400/$mystep);

#How much partitions
$maxparts*=$coof;
$newparts*=$coof;
$totalparts = ($maxparts+$newparts);

my $db = DBI->connect("DBI:mysql:$mysql_dbname:$mysql_host:3306", $mysql_user, $mysql_password);

#$db->{PrintError} = 0;

#we have now multiple tables with different schema. Better do it in sql script 

#Name of part key
if( $mysql_table =~/alarm_/) { $part_key = "create_date"; }
elsif( $mysql_table =~/stats_/) { $part_key = "from_date"; }
else { $part_key = "date"; }

#check if the table has partitions. If not, create one
my $query = "SHOW TABLE STATUS FROM ".$mysql_dbname. " WHERE Name='".$mysql_table."'";
$sth = $db->prepare($query);
$sth->execute();
my $tstatus = $sth->fetchrow_hashref()->{Create_options};
if ($tstatus !~ /partitioned/) {
   my $query = "ALTER TABLE ".$mysql_table. " PARTITION BY RANGE ( UNIX_TIMESTAMP(`".$part_key."`)) (PARTITION pmax VALUES LESS THAN MAXVALUE)";
   $sth = $db->prepare($query);
   $sth->execute();
}

my $query = "SELECT UNIX_TIMESTAMP(CURDATE())";
$sth = $db->prepare($query);
$sth->execute();
my ($curtstamp) = $sth->fetchrow_array();
$curtstamp+=0; 
$todaytstamp+=0;

my %PARTS;
#Geting all partitions
$query = "SELECT PARTITION_NAME, PARTITION_DESCRIPTION"
             ."\n FROM INFORMATION_SCHEMA.PARTITIONS WHERE TABLE_NAME='".$mysql_table."'"
             ."\n AND TABLE_SCHEMA='".$mysql_dbname."' ORDER BY PARTITION_DESCRIPTION ASC;";
$sth = $db->prepare($query);
$sth->execute();
my @oldparts;
my @partsremove;
my @partsadd;

while(my @ref = $sth->fetchrow_array())
{
   my $minpart = $ref[0];
   my $todaytstamp = $ref[1];
       
   next if($minpart eq "pmax");
      
   if($curtstamp <= $todaytstamp) { 
          $PARTS{$minpart."_".$todaytstamp} = 1;
   }
   else { push(@oldparts, \@ref); }   
}

my $partcount = $#oldparts;
if($partcount > $maxparts)
{
    foreach my $ref (@oldparts) {

       $minpart = $ref->[0];
       $todaytstamp = $ref->[1];

       push(@partsremove,$minpart);

       $partcount--;
       last if($partcount <= $maxparts);
    }
}

#Delete all partitions
if($#partsremove > 0)   
{
    $query = "ALTER TABLE ".$mysql_table." DROP PARTITION ".join(',', @partsremove);
    $db->do($query);
    if (!$db->{Executed}) {
           print "Couldn't drop partition: $minpart\n";
           break;
    }
}

# < condition
$curtstamp+=(86400);
for(my $i=0; $i<$newparts; $i++) {

    $oldstamp = $curtstamp;
    $curtstamp+=$mystep;   

    ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($oldstamp);

    my $newpartname = sprintf("p%04d%02d%02d%02d",($year+=1900),(++$mon),$mday,$hour);
    $newpartname.= sprintf("%02d", $min) if($partstep > 1);

    if(!defined $PARTS{$newpartname."_".$curtstamp}) {
        $query = "\nPARTITION ".$newpartname." VALUES LESS THAN (".$curtstamp.")";
        push(@partsadd,$query);
    }    
}
 
my $parts_count=scalar @partsadd;
if($parts_count > 0)
{
    # Fix MAXVALUE. Thanks Dorn B. <djbinter@gmail.com> for report and fix.
    $query = "ALTER TABLE ".$mysql_table." REORGANIZE PARTITION pmax INTO (".join(',', @partsadd)
                                ."\n, PARTITION pmax VALUES LESS THAN MAXVALUE)";
    $db->do($query);
    if (!$db->{Executed}) {
           print "Couldn't drop partition: $minpart\n";
           break;
    }
}
