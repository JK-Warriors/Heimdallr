#-*- coding: utf-8 -*-

######################################################################
# Copyright (c)  2017 by WLBlazers Corporation
#
# failover.py
# 
# 
######################################################################
# Modifications Section:
######################################################################
##     Date        File            Changes
######################################################################
##  12/28/2017                      Baseline version 1.0.0
##
######################################################################

import os
import string
from subprocess import Popen, PIPE
import sys, getopt

import mysql_handle as mysql
import oracle_handle as oracle

import logging
import logging.config

logging.config.fileConfig('./logging.conf')
logger = logging.getLogger('WLBlazers')

	
###############################################################################
# function failover2primary
###############################################################################
def failover2primary(s_conn, s_conn_str, sta_id):
    logger.info("Failover database to primary in progress...")
	# get database role
    str='select database_role from v$database'
    role=oracle.GetSingleValue(s_conn, str)
    logger.info("The current database role is: " + role)
	
	# get switchover status
    str='select switchover_status from v$database'
    switch_status=oracle.GetSingleValue(s_conn, str)
    logger.info("The current database switchover status is: " + switch_status)
	

    if role=="PHYSICAL STANDBY":
        logger.info("Now we are going to switch database %s to primary." %(sta_id))
		
        logger.info("Restart the standby database MRP process...")
        sqlplus = Popen(["sqlplus", "-S", s_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
        sqlplus.stdin.write(bytes("alter database recover managed standby database cancel;"+os.linesep,encoding="utf-8"))
        sqlplus.stdin.write(bytes("alter database recover managed standby database disconnect from session;"+os.linesep,encoding="utf-8"))
        out, err = sqlplus.communicate()
        logger.info(out)
		
        if err=="":
            logger.info("Restart the MRP process successfully.")
			
        logger.info("Failover standby database to primary... ")
        sqlplus = Popen(["sqlplus", "-S", s_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
        sqlplus.stdin.write(bytes("alter database recover managed standby database finish;"+os.linesep,encoding="utf-8"))
        sqlplus.stdin.write(bytes("alter database activate standby database;"+os.linesep,encoding="utf-8"))
        sqlplus.stdin.write(bytes("shutdown immediate"+os.linesep,encoding="utf-8"))
        sqlplus.stdin.write(bytes("startup"+os.linesep,encoding="utf-8"))
        out, err = sqlplus.communicate()
        logger.info(out)
		
        if err=="":
            logger.info("Failover standby database to primary successfully.")


    else:
        logger.error("You can not failover primary database to primary!")
        sys.exit(2)	

		
###############################################################################
# function update_switch_flag
###############################################################################
def update_switch_flag(mysql_conn, group_id):
    logger.info("Update switch flag in db_servers_oracle_dg for group %s in progress..." %(group_id))
	# get current switch flag
    str='select is_switch from db_servers_oracle_dg where id= %s' %(group_id)
    is_switch=mysql.GetSingleValue(mysql_conn, str)
    logger.info("The current switch flag is: %s" %(is_switch))
	
    if is_switch==0:
        str="""update db_servers_oracle_dg set is_switch = 1 where id = %s"""%(group_id)
    else:
        str="""update db_servers_oracle_dg set is_switch = 0 where id = %s"""%(group_id)

		
    is_succ = mysql.ExecuteSQL(mysql_conn, str)

    if is_succ==1:
        logger.info("Update switch flag in db_servers_oracle_dg for group %s successfully." %(group_id))
    else:
        logger.info("Update switch flag in db_servers_oracle_dg for group %s failed." %(group_id))
	

	
###############################################################################
# main function
###############################################################################
if __name__=="__main__":
    # parse argv
    pri_id = ''
    sta_id = ''
    try:
        opts, args = getopt.getopt(sys.argv[1:],"p:s:g:")
    except getopt.GetoptError:
        sys.exit(2)
		
    for opt, arg in opts:
        if opt == '-p':
            pri_id = arg
        elif opt == '-s':
            sta_id = arg
        elif opt == '-g':
            group_id = arg
    
	
	###########################################################################
	# connect to mysql
    mysql_conn = ''
    try:
        mysql_conn = mysql.ConnectMysql()
    except Exception as e:
        logger.error(e)
        sys.exit(2)
		
    
    s_str = """select concat(username, '/', password, '@', host, ':', port, '/', dsn) from db_servers_oracle where id=%s """ %(sta_id)
    s_conn_str = mysql.GetSingleValue(mysql_conn, s_str)

	
    s_str = """select concat(username, '/', password, '@', host, ':', port, '/', dsn) from db_servers_oracle where id=%s """ %(sta_id)
    s_nopass_str = mysql.GetSingleValue(mysql_conn, s_str)
	
    logger.info("The standby database is: " + s_nopass_str + ", the id is: " + str(sta_id))
	

    s_conn = oracle.ConnectOracleAsSysdba(s_conn_str)
		
    if s_conn is None:
        logger.error("Connect to standby database error, exit!!!")
        sys.exit(2)
		
    failover2primary(s_conn, s_conn_str, sta_id)
	
	
    update_switch_flag(mysql_conn, group_id)
	