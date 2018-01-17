#!/usr/bin/python
#-*- coding: utf-8 -*-

######################################################################
# Copyright (c)  2017 by WLBlazers Corporation
#
# switchover.py
# 
# 
######################################################################
# Modifications Section:
######################################################################
##     Date        File            Changes
######################################################################
##  12/13/2017                      Baseline version 1.0.0
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
# function switch2standby
###############################################################################
def switch2standby(p_conn, p_conn_str, pri_id):
    result=-1
    
    logger.info("Switchover database to physical standby in progress...")
	# get database role
    str='select database_role from v$database'
    role=oracle.GetSingleValue(p_conn, str)
    logger.info("The current database role is: " + role)
	
	# get switchover status
    str='select switchover_status from v$database'
    switch_status=oracle.GetSingleValue(p_conn, str)
    logger.info("The current database switchover status is: " + switch_status)
	
	# get database version
    str="""select substr(version, 0, instr(version, '.')-1) from v$instance"""
    version=oracle.GetSingleValue(p_conn, str)
	
    if role=="PRIMARY":
        logger.info("Now we are going to switch database %s to physical standby." %(pri_id))
        if switch_status=="TO STANDBY" or switch_status=="SESSIONS ACTIVE":
            logger.info("Switchover to physical standby... ")
            sqlplus = Popen(["sqlplus", "-S", p_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
            sqlplus.stdin.write(bytes("alter database commit to switchover to physical standby with session shutdown;"+os.linesep))
            sqlplus.stdin.write(bytes("shutdown immediate"+os.linesep))
            sqlplus.stdin.write(bytes("startup mount"+os.linesep))
            sqlplus.stdin.write(bytes("alter database recover managed standby database disconnect from session;"+os.linesep))
            out, err = sqlplus.communicate()
            logger.info(out)
            logger.error(err)
			
            if err is None:
                logger.info("Switchover to physical standby successfully.")
                result=0
    
            if version > '10':
                logger.info("Alter standby database to open read only in progress... ")
                sqlplus = Popen(["sqlplus", "-S", p_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
                sqlplus.stdin.write(bytes("alter database recover managed standby database cancel;"+os.linesep))
                sqlplus.stdin.write(bytes("alter database open;"+os.linesep))
                sqlplus.stdin.write(bytes("alter database recover managed standby database using current logfile disconnect from session;"+os.linesep))
                out, err = sqlplus.communicate()
                logger.info(out)
                logger.error(err)
				
                if err is None:
                    logger.info("Alter standby database to open successfully.")
                else:
                    logger.error("Start MRP process failed!")
    
    else:
        logger.error("You can not switchover a standby database to physical standby!")
        
    return result;

	
###############################################################################
# function standby2primary
###############################################################################
def standby2primary(s_conn, s_conn_str, sta_id):
    result=-1
    
    logger.info("Switchover database to primary in progress...")
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
        if switch_status=="NOT ALLOWED" or switch_status=="SWITCHOVER PENDING":
            logger.info("The standby database not allowed to switchover, restart the MRP process...")
            sqlplus = Popen(["sqlplus", "-S", s_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
            sqlplus.stdin.write(bytes("alter database recover managed standby database cancel;"+os.linesep))
            sqlplus.stdin.write(bytes("alter database recover managed standby database disconnect from session;"+os.linesep))
            out, err = sqlplus.communicate()
            logger.info(out)
            logger.error(err)
			
            if err is None:
                logger.info("Restart the MRP process successfully.")
				
            logger.info("Switchover standby database to primary... ")
            sqlplus = Popen(["sqlplus", "-S", s_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
            sqlplus.stdin.write(bytes("alter database commit to switchover to primary with session shutdown;"+os.linesep))
            sqlplus.stdin.write(bytes("shutdown immediate"+os.linesep))
            sqlplus.stdin.write(bytes("startup"+os.linesep))
            out, err = sqlplus.communicate()
            logger.info(out)
            logger.error(err)
			
            if err is None:
                logger.info("Switchover standby database to primary successfully.")
                result = 0
				
        if switch_status=="TO PRIMARY" or switch_status=="SESSIONS ACTIVE":
            logger.info("Switchover standby database to primary... ")
            sqlplus = Popen(["sqlplus", "-S", s_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
            sqlplus.stdin.write(bytes("alter database commit to switchover to primary with session shutdown;"+os.linesep))
            sqlplus.stdin.write(bytes("shutdown immediate"+os.linesep))
            sqlplus.stdin.write(bytes("startup"+os.linesep))
            out, err = sqlplus.communicate()
            logger.info(out)
            logger.error(err)
			
            if err is None:
                logger.info("Switchover standby database to primary successfully.")
                result = 0
    else:
        logger.error("You can not switchover primary database to primary!")

    return result

		
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
		
    
    p_str = """select concat(username, '/', password, '@', host, ':', port, '/', dsn) from db_servers_oracle where id=%s """ %(pri_id)
    p_conn_str = mysql.GetSingleValue(mysql_conn, p_str)
    s_str = """select concat(username, '/', password, '@', host, ':', port, '/', dsn) from db_servers_oracle where id=%s """ %(sta_id)
    s_conn_str = mysql.GetSingleValue(mysql_conn, s_str)

	
    p_str = """select concat(username, '@', host, ':', port, '/', dsn) from db_servers_oracle where id=%s """ %(pri_id)
    p_nopass_str = mysql.GetSingleValue(mysql_conn, p_str)
    s_str = """select concat(username, '/', password, '@', host, ':', port, '/', dsn) from db_servers_oracle where id=%s """ %(sta_id)
    s_nopass_str = mysql.GetSingleValue(mysql_conn, s_str)
	
    logger.info("The primary database is: " + p_nopass_str + ", the id is: " + str(pri_id))
    logger.info("The standby database is: " + s_nopass_str + ", the id is: " + str(sta_id))
	

    p_conn = oracle.ConnectOracleAsSysdba(p_conn_str)
    s_conn = oracle.ConnectOracleAsSysdba(s_conn_str)
	
    if p_conn is None:
        logger.error("Connect to primary database error, exit!!!")
        sys.exit(2)
		
    if s_conn is None:
        logger.error("Connect to standby database error, exit!!!")
        sys.exit(2)
		
    res_2s=switch2standby(p_conn, p_conn_str, pri_id)
    res_2p=standby2primary(s_conn, s_conn_str, sta_id)
    logger.info("res_2s: " + str(res_2s))
    logger.info("res_2p: " + str(res_2p))
    if res_2s ==0 and res_2p == 0:
        update_switch_flag(mysql_conn, group_id)
	