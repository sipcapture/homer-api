#!/bin/sh

#this is script rotation for rtcp, logs and stats tables

new_table="/usr/local/bin/new_table.pl"
programm="/usr/local/bin/partrotate_unixtimestamp.pl"

#Make new table rotate. Homer5 Style.
$new_table

#rtcp_capture: part step: 1 day and 10 days keep data.
# ARGV[0] = DB, ARGV[1] = Table, ARGV[2] = STEP, ARGV[3] = MAX DAYS
$programm homer_db rtcp_capture 0 10
#logs_capture: part step: 1 day and 10 days keep data.
$programm homer_db logs_capture 0 10
#logs_capture: part step: 1 day and 10 days keep data.
$programm homer_db report_capture  0 10
#stats: part step: 1 day and 20 days keep data
$programm homer_statistic stats_ip 0 20
#stats: part step: 1 day and 20 days keep data
$programm homer_statistic alarm_data 0 20
$programm homer_statistic stats_method 0 20
$programm homer_statistic stats_useragent 0 20
$programm homer_statistic stats_useragent 0 20
