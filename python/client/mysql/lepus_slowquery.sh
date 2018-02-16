#!/bin/bash
#****************************************************************#
# ScriptName: /usr/local/sbin/wlblazers_slowquery.sh
# Create Date: 2014-03-25 10:01
# Modify Date: 2014-03-25 10:01
#***************************************************************#

#config wlblazers database server
wlblazers_db_host=""
wlblazers_db_port=3306
wlblazers_db_user=""
wlblazers_db_password=""
wlblazers_db_database="wlblazers"

#config mysql server
mysql_client="/data/mysql/bin/mysql"
mysql_host="127.0.0.1"
mysql_port=3306
mysql_user=""
mysql_password=""

#config slowqury
slowquery_dir="/data/mysql/sh/"
slowquery_long_time=1
slowquery_file=`$mysql_client -h$mysql_host -P$mysql_port -u$mysql_user -p$mysql_password  -e "show variables like 'slow_query_log_file'"|grep log|awk '{print $2}'`
pt_query_digest="/usr/bin/pt-query-digest"

#config server_id
wlblazers_server_id=1

#collect mysql slowquery log into wlblazers database
$pt_query_digest --user=$wlblazers_db_user --password=$wlblazers_db_password --port=$wlblazers_db_port --review h=$wlblazers_db_host,D=$wlblazers_db_database,t=mysql_slow_query_review  --history h=$wlblazers_db_host,D=$wlblazers_db_database,t=mysql_slow_query_review_history  --no-report --limit=100% --filter=" \$event->{add_column} = length(\$event->{arg}) and \$event->{serverid}=$wlblazers_server_id " $slowquery_file > /tmp/wlblazers_slowquery.log

##### set a new slow query log ###########
tmp_log=`$mysql_client -h$mysql_host -P$mysql_port -u$mysql_user -p$mysql_password -e "select concat('$slowquery_dir','slowquery_',date_format(now(),'%Y%m%d%H'),'.log');"|grep log|sed -n -e '2p'`

#config mysql slowquery
$mysql_client -h$mysql_host -P$mysql_port -u$mysql_user -p$mysql_password -e "set global slow_query_log=1;set global long_query_time=$slowquery_long_time;"
$mysql_client -h$mysql_host -P$mysql_port -u$mysql_user -p$mysql_password -e "set global slow_query_log_file = '$tmp_log'; "

#delete log before 7 days
cd $slowquery_dir
/usr/bin/find ./ -name 'slowquery_*' -mtime +7|xargs rm -rf ;

####END####
