#!/bin/sh

#this is script rotation for rtcp, logs and stats tables

# Set correct bin path if we are running as a cron job
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin

bin_dir=`dirname $0`
new_table="$bin_dir/homer_mysql_new_table.pl"
programm="$bin_dir/homer_mysql_partrotate_unixtimestamp.pl"

#Make new table rotate. Homer5 Style. 10 days for calls, 7 days for registrations, 5 days for rest
$new_table 0 10,7,5

#rtcp_capture: part step: 1 day and 10 days keep data.
# ARGV[0] = DB, ARGV[1] = Table, ARGV[2] = STEP, ARGV[3] = MAX DAYS
$programm homer_data rtcp_capture 0 10
#logs_capture: part step: 1 day and 10 days keep data.
$programm homer_data logs_capture 0 10
#logs_capture: part step: 1 day and 10 days keep data.
$programm homer_data report_capture  0 10
#stats: part step: 1 day and 20 days keep data
$programm homer_statistic stats_ip 0 20
#stats: part step: 1 day and 20 days keep data
$programm homer_statistic alarm_data 0 20
$programm homer_statistic stats_method 0 20
$programm homer_statistic stats_useragent 0 20
$programm homer_statistic stats_useragent 0 20

# Dealing with calls, registrations and a rest of captured data
# delete tables older then 30 days
TS=`date +%s`;
MYSQL_USER="sipcapture"
MYSQL_PASSWORD="sipcapturestrongpassword"
MYSQL_DB="homer_data"

TS=`expr $TS - 2592000`
for table in `mysql -u${MYSQL_USER} -p${MYSQL_PASSWORD} -D${MYSQL_DB} -B -e "show tables" | grep sip_capture`;
do
        table_date=`echo $table | awk -F'_' '{print $4}'`;
        table_ts=`date --date=$table_date +%s`;
        if [ $table_ts -lt $TS ];
        then
                logger "sipcapture: Deleting table $table";
                mysql -u${MYSQL_USER} -p${MYSQL_PASSWORD} -D${MYSQL_DB} -e "drop table ${table}";
        fi;
done;
