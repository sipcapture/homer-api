#!/usr/bin/env perl
#
# cleanup old partitions- perl script for mySQL partition rotation
#
# Copyright (C) 2016 Sebastian Damm (damm@sipgate.de)
#
# This file is part of webhomer, a free capture server.
#
# homer_mysql_remove_partitions.pl is free software; you can redistribute it
# and/or modify it under the terms of the GNU General Public License as
# published by the Free Software Foundation; either version 3 of the License,
# or (at your option) any later version
#
# homer_mysql_remove_partitions.pl is distributed in the hope that it will be
# useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

use 5.010;
use strict;
use warnings;
use DBI;
use POSIX;
use Data::Dumper;

my $version = "1.0.0";
$| =1;

# Determine path and set default rotation.ini location
my $script_location = `dirname $0`;
$script_location =~ s/^\s+|\s+$//g;
my $default_ini = $script_location."/rotation.ini";

my $conf_file = $ARGV[0] // $default_ini;
our $CONFIG = read_config($conf_file);

my $db = db_connect($CONFIG, "db_data");

foreach my $table (keys %{ $CONFIG->{"DROP_OLD_PARTITIONS"} }) {
	my $removeseconds = $CONFIG->{"DROP_OLD_PARTITIONS"}{$table};
	say "Working on table $table, threshold $removeseconds." if($CONFIG->{"SYSTEM"}{"debug"} == 1);

	# Skip if unconfigured
	next if $removeseconds == 0;
	# Reference Timestamp
	my $mintstamp = time() - $removeseconds;

	# Find the current table
	my ($sec, $min, $hour, $mday, $mon, $year, $wday, $yday, $isdst) = gmtime();
	my $tablesuffix = sprintf("%04d%02d%02d",($year+=1900),(++$mon),$mday);
	my $todaytable = sprintf("%s_%s", $table, $tablesuffix);
	if ($table=~/^rtcp_capture_all/) {
        $todaytable = sprintf("%s", $table);
    }
	if (is_table_partitioned($db, $CONFIG->{"MYSQL"}{"db_data"}, $todaytable)) {
		# load all partitions of table
		my %partitions = load_partitions($db, $CONFIG->{"MYSQL"}{"db_data"}, $todaytable);
		say "Found Partitions: ".Dumper \%partitions if($CONFIG->{"SYSTEM"}{"debug"} == 1);
		# check timestamp of partitions
		my @partitions_to_remove;
		foreach my $part (sort keys %partitions) {
			say "Examining Partition $part" if($CONFIG->{"SYSTEM"}{"debug"} == 1); 
			if ($partitions{$part} < $mintstamp) {
				say "Partition $part is older than minimum tstamp ($mintstamp)." if($CONFIG->{"SYSTEM"}{"debug"} == 1); 
				push(@partitions_to_remove,$part);
			}
		}
		# delete old partitions
		if(scalar @partitions_to_remove > 0) {
			drop_partitions($db, $todaytable, @partitions_to_remove);
		}
	}

}

exit;

### END OF MAIN

sub drop_partitions {
	my $db = shift;
	my $table_name = shift;
	my @parts2remove = @_;

	my $query = "ALTER TABLE ".$table_name." DROP PARTITION ".join(',', @parts2remove);
	say "DROP Partition: [$query]" if($CONFIG->{"SYSTEM"}{"debug"} == 1);
	$db->do($query) or printf(STDERR "Failed to execute query [%s] with error: %s\n", ,$db->errstr) if($CONFIG->{"SYSTEM"}{"exec"} == 1);
	if (!$db->{Executed}) {
		say "Couldn't drop partitions: ".join(',', @parts2remove);
	}
}

sub load_partitions {
	my $db = shift;
	my $db_name = shift;
	my $table_name = shift;
	my %partitions;

	#Geting all partitions
	my $query = "SELECT PARTITION_NAME, PARTITION_DESCRIPTION"
		."\n FROM INFORMATION_SCHEMA.PARTITIONS WHERE TABLE_NAME='".$table_name."'"
		."\n AND TABLE_SCHEMA='".$db_name."' ORDER BY PARTITION_DESCRIPTION ASC;";
	my $sth = $db->prepare($query);
	$sth->execute();
	while(my @ref = $sth->fetchrow_array()) {
		next if($ref[0] eq "pmax");
		$partitions{$ref[0]} = $ref[1];
	}
	return %partitions;
}

sub is_table_partitioned {
	my $db = shift;
	my $db_name = shift;
	my $table_name = shift;

	my $query="SELECT create_options FROM information_schema.tables WHERE table_schema = '".$db_name."' and table_name = '".$table_name."'";
	say "Debug: $query" if($CONFIG->{"SYSTEM"}{"debug"} == 1);
	my $sth = $db->prepare($query);
	$sth->execute();
	if ($sth->rows == 0) {
		say "Table $table_name does not exist. Skipping..." if($CONFIG->{"SYSTEM"}{"debug"} == 1);
		return 0;
	}

	my ($tstatus) = $sth->fetchrow_array();
	if ($tstatus !~ /partitioned/) {
		say "Table $table_name is not partitioned. Skipping..." if($CONFIG->{"SYSTEM"}{"debug"} == 1);
		return 0;
	}
	return 1;
}

sub read_config {

	my $ini = shift;

	open (INI, "$ini") || die "Can't open $ini: $!\n";
	my $section;
	my $CONFIG;
	while (<INI>) {
		chomp;
		if (/^\s*\[(\w+)\].*/) {
			$section = $1;
		}
		if ((/^(.*)=(.*)$/)) {
			my ($keyword, $value) = split(/=/, $_, 2);
			$keyword =~ s/^\s+|\s+$//g;
			$value =~ s/(#.*)$//;
			$value =~ s/^\s+//;
			$value =~ s/\s+$//;
			$CONFIG->{$section}{$keyword} = $value;
		}
	}
	close(INI);
	return $CONFIG;
}

sub db_connect {
    my $CONFIG  = shift;
    my $db_name = shift;
    my $dbistring = "";
    if($CONFIG->{"MYSQL"}{"usesocket"}) {
    	$dbistring = "DBI:mysql:database=".$CONFIG->{"MYSQL"}{$db_name}.";mysql_socket=".$CONFIG->{"MYSQL"}{"socket"}
    } else {
    	$dbistring = "DBI:mysql:".$CONFIG->{"MYSQL"}{$db_name}.":".$CONFIG->{"MYSQL"}{"host"}.":".$CONFIG->{"MYSQL"}{"port"}
    }
    my $db = DBI->connect($dbistring, $CONFIG->{"MYSQL"}{"user"}, $CONFIG->{"MYSQL"}{"password"});
    return $db;
}

