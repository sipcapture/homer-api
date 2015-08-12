#!/usr/bin/perl
#
# new_table - perl script for mySQL partition rotation
#
# Copyright (C) 2011-2015 Alexandr Dubovikov (alexandr.dubovikov@gmail.com)
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

$version = "0.5.1";
$mysql_dbname = "homer_data";
$mysql_user = "homer_user";
$mysql_password = "homer_password";
$mysql_host = "localhost";
$maxparts = $ARGV[1] // 6; #6 days How long keep the data in the DB
$newtables = 2; #new tables for 2 days. Anyway, start this script daily!
@stepsvalues = (86400, 3600, 1800, 900); 
$partstep = $ARGV[0] // 0; # 0 - Day, 1 - Hour, 2 - 30 Minutes, 3 - 15 Minutes 
$engine = "InnoDB"; #MyISAM or InnoDB
$compress = "ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=8"; #Enable this if you want use barracuda format or set var to empty.
@transactions = ("call","registration","rest");

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


$ORIGINAL_TABLE=<<END;
CREATE TABLE IF NOT EXISTS `sip_capture_[TRANSACTION]_[TIMESTAMP]` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
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

$DROP="DROP TABLE IF EXISTS `sip_capture_[TRANSACTION]_[TIMESTAMP]`;";

$db = DBI->connect("DBI:mysql:$mysql_dbname:$mysql_host:3306", $mysql_user, $mysql_password);
#$db->{PrintError} = 0;


for(my $y = 0 ; $y < ($newtables+1); $y++)
{
    $curtstamp = time()+(86400*$y);    
    new_table($curtstamp, $mystep, $partstep, $ORIGINAL_TABLE);    
}

#And remove
for(my $y = 0 ; $y < 2; $y++)
{
    $curtstamp = time()-(86400*($maxparts+$y));    
    $ltable = $DROP;
    my ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = gmtime($curtstamp);
    my $kstamp = mktime (0, 0, 0, $mday, $mon, $year, $wday, $yday, $isdst);
    my $table_timestamp = sprintf("%04d%02d%02d",($year+=1900),(++$mon),$mday);
    $ltable=~s/\[TIMESTAMP\]/$table_timestamp/ig;
    foreach $key (@transactions)
    {
            $query = $ltable;
            $query=~s/\[TRANSACTION\]/$key/ig;
            $db->do($query);
    }
}


sub new_table()
{    

    my $cstamp = shift;
    my $mystep = shift;
    my $partstep = shift;
    my $table = shift;
    
    $newparts=int(86400/$mystep);
        
    my @partsadd;
    my ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = gmtime($cstamp);
    my $kstamp = mktime (0, 0, 0, $mday, $mon, $year, $wday, $yday, $isdst);

    my $table_timestamp = sprintf("%04d%02d%02d",($year+=1900),(++$mon),$mday);

    $table=~s/\[TIMESTAMP\]/$table_timestamp/ig;
    
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
        $table=~s/\[PARTITIONS\]/$val/ig;
        
        foreach $key (@transactions)
        {
            $query = $table;
            $query=~s/\[TRANSACTION\]/$key/ig;
            $db->do($query);
        }
    }
}

