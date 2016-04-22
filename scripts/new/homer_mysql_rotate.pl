#!/usr/bin/perl
#
# new_table - perl script for mySQL partition rotation
#
# Copyright (C) 2011-2016 Alexandr Dubovikov (alexandr.dubovikov@gmail.com)
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
use POSIX;

$version = "1.0.0";
$config = $ARGV[1] // "rotation.ini";
@stepsvalues = (86400, 3600, 1800, 900); 
$AFTER_FIX = 1;

# Optionally load override configuration. perl format
$rc = "/etc/sysconfig/partrotaterc";
if (-e $rc) {
  do $rc;
}

%CONFIG = {};

read_config($config);

$newtables = $CONFIG{"MYSQL"}{"newtables"};
$engine = $CONFIG{"MYSQL"}{"engine"};
$compress = $CONFIG{"MYSQL"}{"compress"};

# Debug only
#foreach my $section (sort keys %CONFIG) {
#    foreach my $value (keys %{ $CONFIG{$section} }) {
#            print "$section, $value: $CONFIG{$section}{$value}\n";
#    }
#}

$ORIGINAL_DATA_TABLE=<<END;
CREATE TABLE IF NOT EXISTS `[TRANSACTION]_[TIMESTAMP]` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `micro_ts` bigint(18) NOT NULL DEFAULT '0',
  `method` varchar(50) NOT NULL DEFAULT '',
  `reply_reason` varchar(100) NOT NULL DEFAULT '',
  `ruri` varchar(200) NOT NULL DEFAULT '',
  `ruri_user` varchar(100) NOT NULL DEFAULT '',
  `ruri_domain` varchar(150) NOT NULL DEFAULT '',
  `from_user` varchar(100) NOT NULL DEFAULT '',
  `from_domain` varchar(150) NOT NULL DEFAULT '',
  `from_tag` varchar(64) NOT NULL DEFAULT '',
  `to_user` varchar(100) NOT NULL DEFAULT '',
  `to_domain` varchar(150) NOT NULL DEFAULT '',
  `to_tag` varchar(64) NOT NULL DEFAULT '',
  `pid_user` varchar(100) NOT NULL DEFAULT '',
  `contact_user` varchar(120) NOT NULL DEFAULT '',
  `auth_user` varchar(120) NOT NULL DEFAULT '',
  `callid` varchar(120) NOT NULL DEFAULT '',
  `callid_aleg` varchar(120) NOT NULL DEFAULT '',
  `via_1` varchar(256) NOT NULL DEFAULT '',
  `via_1_branch` varchar(80) NOT NULL DEFAULT '',
  `cseq` varchar(25) NOT NULL DEFAULT '',
  `diversion` varchar(256) NOT NULL DEFAULT '',
  `reason` varchar(200) NOT NULL DEFAULT '',
  `content_type` varchar(256) NOT NULL DEFAULT '',
  `auth` varchar(256) NOT NULL DEFAULT '',
  `user_agent` varchar(256) NOT NULL DEFAULT '',
  `source_ip` varchar(60) NOT NULL DEFAULT '',
  `source_port` int(10) NOT NULL DEFAULT 0,
  `destination_ip` varchar(60) NOT NULL DEFAULT '',
  `destination_port` int(10) NOT NULL DEFAULT 0,
  `contact_ip` varchar(60) NOT NULL DEFAULT '',
  `contact_port` int(10) NOT NULL DEFAULT 0,
  `originator_ip` varchar(60) NOT NULL DEFAULT '',
  `originator_port` int(10) NOT NULL DEFAULT 0,
  `correlation_id` varchar(256) NOT NULL DEFAULT '',
  `custom_field1` varchar(120) NOT NULL DEFAULT '',
  `custom_field2` varchar(120) NOT NULL DEFAULT '',
  `custom_field3` varchar(120) NOT NULL DEFAULT '',
  `proto` int(5) NOT NULL DEFAULT 0,
  `family` int(1) DEFAULT NULL,
  `rtp_stat` varchar(256) NOT NULL DEFAULT '',
  `type` int(2) NOT NULL DEFAULT 0,
  `node` varchar(125) NOT NULL DEFAULT '',
  `msg` varchar(1500) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`,`date`),
  KEY `ruri_user` (`ruri_user`),
  KEY `from_user` (`from_user`),
  KEY `to_user` (`to_user`),
  KEY `pid_user` (`pid_user`),
  KEY `auth_user` (`auth_user`),
  KEY `callid_aleg` (`callid_aleg`),
  KEY `date` (`date`),
  KEY `callid` (`callid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=8 COMMENT='[TIMESTAMP]'
/*!50100 PARTITION BY RANGE ( UNIX_TIMESTAMP(`date`))
([PARTITIONS]
PARTITION pmax VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */ ;
END

$DROP_DATA_TABLE="DROP TABLE IF EXISTS `[TRANSACTION]_[TIMESTAMP]`;";

#Check DATA tables
foreach my $table (keys %{ $CONFIG{"DATA_TABLE_ROTATION"} }) {

    $rotate = $CONFIG{'DATA_TABLE_ROTATION'}{$table};
    $partstep = $CONFIG{'DATA_TABLE_STEP'}{$table};
    
    #Check it
    $partstep=0 if(!defined $stepsvalues[$partstep]);
    #Mystep
    $mystep = $stepsvalues[$partstep];

    $db = DBI->connect("DBI:mysql:".$CONFIG{"MYSQL"}{"db_data"}.":".$CONFIG{"MYSQL"}{"host"}.":".$CONFIG{"MYSQL"}{"port"}, $CONFIG{"MYSQL"}{"user"}, $CONFIG{"MYSQL"}{"password"});
    
    for(my $y = 0 ; $y < ($newtables+1); $y++)
    {
        $curtstamp = time()+(86400*$y);    
        new_data_table($curtstamp, $mystep, $partstep, $ORIGINAL_DATA_TABLE, $table);    
    }

    #And remove
    $ltable = $DROP_DATA_TABLE;
    $ltable =~s/\[TRANSACTION\]/$table/ig;	

    for(my $y = 0 ; $y < 2; $y++)
    {
        $curtstamp = time()-(86400*($maxparts[$i]+$y));    
	my ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = gmtime($curtstamp);
	my $kstamp = mktime (0, 0, 0, $mday, $mon, $year, $wday, $yday, $isdst);
	my $table_timestamp = sprintf("%04d%02d%02d",($year+=1900),(++$mon),$mday);

	$query = $ltable;
	$query=~s/\[TIMESTAMP\]/$table_timestamp/ig;	
	$db->do($query);
    }
}

#Check STATS tables
foreach my $table (keys %{ $CONFIG{"STATS_TABLE_ROTATION"} }) {

    $rotate = $CONFIG{'STATS_TABLE_ROTATION'}{$table};
    $partstep = $CONFIG{'STATS_TABLE_STEP'}{$table};
    
    #Check it
    $partstep=0 if(!defined $stepsvalues[$partstep]);
    #Mystep
    $mystep = $stepsvalues[$partstep];
    
    $coof=int(86400/$mystep);
    #How much partitions
    $maxparts*=$coof;
    $newparts*=$coof;
    $totalparts = ($maxparts+$newparts);
    
    $db = DBI->connect("DBI:mysql:".$CONFIG{"MYSQL"}{"db_stats"}.":".$CONFIG{"MYSQL"}{"host"}.":".$CONFIG{"MYSQL"}{"port"}, $CONFIG{"MYSQL"}{"user"}, $CONFIG{"MYSQL"}{"password"});
    
    $part_key = "date";
    #Name of part key
    if( $table =~/alarm_/) { $part_key = "create_date"; }
    elsif( $table =~/stats_/) { $part_key = "from_date"; }
    
    #check if the table has partitions. If not, create one
    my $query = "SHOW TABLE STATUS FROM ".$CONFIG{"MYSQL"}{"db_stats"}. " WHERE Name='".$table."'";
    $sth = $db->prepare($query);
    $sth->execute();
    my $tstatus = $sth->fetchrow_hashref()->{Create_options};
    if ($tstatus !~ /partitioned/) {
           my $query = "ALTER TABLE ".$table. " PARTITION BY RANGE ( UNIX_TIMESTAMP(`".$part_key."`)) (PARTITION pmax VALUES LESS THAN MAXVALUE)";
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
             ."\n FROM INFORMATION_SCHEMA.PARTITIONS WHERE TABLE_NAME='".$table."'"
             ."\n AND TABLE_SCHEMA='".$$CONFIG{"MYSQL"}{"db_stats"}."' ORDER BY PARTITION_DESCRIPTION ASC;";
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
      
         if($curtstamp <= $todaytstamp) { $PARTS{$minpart."_".$todaytstamp} = 1; }
         else { push(@oldparts, \@ref); }   
    }

    my $partcount = $#oldparts;
    if($partcount > $maxparts)
    {
          foreach my $ref (@oldparts) 
          {
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
          $query = "ALTER TABLE ".$table." DROP PARTITION ".join(',', @partsremove);
          $db->do($query) or printf(STDERR "Failed to execute query [%s] with error: %s", ,$db->errstr);
          if (!$db->{Executed}) {
                print "Couldn't drop partition: $minpart\n";
                break;
          }
    }

    # < condition
    $curtstamp+=(86400);
    for(my $i=0; $i<$newparts; $i++) 
    {
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
           $query = "ALTER TABLE ".$table." REORGANIZE PARTITION pmax INTO (".join(',', @partsadd) ."\n, PARTITION pmax VALUES LESS THAN MAXVALUE)";
           $db->do($query) or printf(STDERR "Failed to execute query [%s] with error: %s", ,$db->errstr);
           if (!$db->{Executed}) {
                 print "Couldn't drop partition: $minpart\n";
                 break;
           }
     }   
}

if($AFTER_FIX) {
    
    $db = DBI->connect("DBI:mysql:".$CONFIG{"MYSQL"}{"db_data"}.":".$CONFIG{"MYSQL"}{"host"}.":".$CONFIG{"MYSQL"}{"port"}, $CONFIG{"MYSQL"}{"user"}, $CONFIG{"MYSQL"}{"password"});    

    my ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time() - 24*60*60*30);
              
    my $oldest = sprintf("%04d%02d%02d",($year+=1900),(++$mon),$mday,$hour);              
    $oldest+=0;

    my $query = "SHOW TABLES LIKE 'sip_capture_%';";
    $sth = $db->prepare($query);    
    $sth->execute();
    
    while(my @ref = $sth->fetchrow_array())
    {
         my $table_full = $ref[0];
         my($proto, $cap, $type, $ts) = split(/_/, $table_full, 4);
         $ts+=0;
         if($ts < $oldest) {
                 my $drop = "DROP TABLE $full_table;";
                 $drh = $db->prepare($query);    
                 $drh->execute();
         
         }
    }
}


exit;


sub new_data_table()
{    

    my $cstamp = shift;
    my $mystep = shift;
    my $partstep = shift;
    my $sqltable = shift;
    my $table = shift;
    
    $newparts=int(86400/$mystep);
        
    my @partsadd;
    my ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = gmtime($cstamp);
    my $kstamp = mktime (0, 0, 0, $mday, $mon, $year, $wday, $yday, $isdst);

    my $table_timestamp = sprintf("%04d%02d%02d",($year+=1900),(++$mon),$mday);

    $sqltable=~s/\[TIMESTAMP\]/$table_timestamp/ig;
    
    # < condition
    for(my $i=0; $i<$newparts; $i++) {
        my $oldstamp = $kstamp;
        $kstamp+=$mystep;   
        my ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = gmtime($oldstamp);

        my $newpartname = sprintf("p%04d%02d%02d%02d",($year+=1900),(++$mon),$mday,$hour);
        $newpartname.= sprintf("%02d", $min) if($partstep > 1);
        $query = "PARTITION ".$newpartname." VALUES LESS THAN (".$kstamp.")";
        push(@partsadd,$query);        

    }
 
    my $parts_count=scalar @partsadd;
    if($parts_count > 0)
    {
        $val = join(','."\n", @partsadd).",";
        $sqltable=~s/\[PARTITIONS\]/$val/ig;

        $sqltable=~s/\[TRANSACTION\]/$table/ig;
        $db->do($query) or printf(STDERR "Failed to execute query [%s] with error: %s", ,$db->errstr);
        #print "$sqltable\n";        
    }
}


sub read_config() 
{

	my $ini = shift;

	open (INI, "$ini") || die "Can't open $ini: $!\n";
        while (<INI>) {
            chomp;            
            if (/^\s*\[(\w+)\].*/) {
                $section = $1;
            }                        
            if ((/^(.*)=(.*)$/)) {
                my($keyword, $value) = split(/=/, $_, 2);
                $keyword =~ s/^\s+|\s+$//g;            
                print "ZZ: [$keyword]\n";
                $value =~ s/(#.*)$//;
                $value =~ s/^\s+//;  
                $value =~ s/\s+$//;                      
                print "V: [$value]\n";
                $CONFIG{$section}{$keyword} = $value;
            }
        }

	close(INI);
}

