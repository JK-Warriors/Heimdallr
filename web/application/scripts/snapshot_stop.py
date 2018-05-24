#!/usr/bin/python
#-*- coding: utf-8 -*-

######################################################################
# Copyright (c)  2017 by WLBlazers Corporation
#
# snapshot_stop.py
# 
# 
######################################################################
# Modifications Section:
######################################################################
##     Date        File            Changes
######################################################################
##  03/22/2018                      Baseline version 1.0.0
##
######################################################################

import os
import string
from subprocess import Popen, PIPE
import sys, getopt

import mysql_handle as mysql
import oracle_handle as oracle
import common

import logging
import logging.config

logging.config.fileConfig('./logging.conf')
logger = logging.getLogger('WLBlazers')

###############################################################################
# function stop_mrp
###############################################################################
def stop_mrp(mysql_conn, group_id, s_conn, s_conn_str, sta_id):
    result=-1
    
    logger.info("Stop the MRP process for databaes %s in progress..." %(sta_id))
    # get database role
    str='select database_role from v$database'
    role=oracle.GetSingleValue(s_conn, str)
    common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_STOP', '获取数据库角色成功', 20, 2)
    logger.info("The current database role is: " + role)
    
    # get database version
    str="""select substr(version, 0, instr(version, '.')-1) from v$instance"""
    version=oracle.GetSingleValue(s_conn, str)
    logger.info("The current database version is: " + version)
	
    # get instance status
    str='select status from v$instance'
    status=oracle.GetSingleValue(s_conn, str)
    common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_STOP', '获取数据库角色成功', 20, 2)
    logger.info("The current instance status is: " + status)

    if version <=10:
        common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_STOP', '停止快照状态失败，当前数据库版本不支持', 90, 2)
        return result;
    	
    	
    if role=="SNAPSHOT STANDBY":
        common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_STOP', '验证数据库角色成功', 50, 2)
        
        if status != "MOUNTED":
            logger.info("Instance is not in MOUNT, startup mount first... ")
            sqlplus = Popen(["sqlplus", "-S", s_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
            sqlplus.stdin.write(bytes("shutdown immediate;"+os.linesep))
            sqlplus.stdin.write(bytes("startup mount;"+os.linesep))
            out, err = sqlplus.communicate()
        	
        logger.info("Now we are going to convert to physical standby... ")
        sqlplus = Popen(["sqlplus", "-S", s_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
        sqlplus.stdin.write(bytes("alter database convert to physical standby;"+os.linesep))
        out, err = sqlplus.communicate()
        logger.info(out)
        #logger.error(err)
        if 'ORA-' in out:
            rea_str = '停止快照模式失败，原因是：%s' %(out[out.index("ORA-"):])
            common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_STOP', rea_str, 90, 2)
            logger.info("Convert to physical standby failed!!! ")
        else:
            common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_STOP', '停止快照模式成功', 90, 2)
            logger.info("Convert to physical standby successfully.")
            result=0
            
        common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_STOP', '正在开启MRP进程...', 90, 0)
        sqlplus = Popen(["sqlplus", "-S", s_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
        sqlplus.stdin.write(bytes("shutdown immediate;"+os.linesep))
        sqlplus.stdin.write(bytes("startup mount;"+os.linesep))
        sqlplus.stdin.write(bytes("alter database open;"+os.linesep))
        sqlplus.stdin.write(bytes("alter database recover managed standby database using current logfile disconnect from session;"+os.linesep))
        out, err = sqlplus.communicate()
        common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_STOP', '开启MRP进程成功', 90, 0)
    else:
        common.update_op_reason(mysql_conn, group_id, 'SNAPSHOT_STOP', '验证数据库角色失败，当前数据库不是PHYSICAL STANDBY，不能停止快照')
        common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_STOP', '验证数据库角色失败，当前数据库不是SNAPSHOT STANDBY，不能停止快照', 90)
	 
    return result;

	
		
###############################################################################
# function update the mrp status in mysql
###############################################################################
def update_mrp_status(mysql_conn, sta_id):
    logger.info("Update MRP status in oracle_dg_s_status for server %s in progress..." %(sta_id))
    
    # get current switch flag
    str='select mrp_status from oracle_dg_s_status where server_id= %s' %(sta_id)
    mrp_status=mysql.GetSingleValue(mysql_conn, str)
    logger.info("debug the mrp_status: %s" %(mrp_status))
    
    if mrp_status == '0':
        logger.info("The current MRP status is inactive.")
        str="""update oracle_dg_s_status s set s.mrp_status = 1 where s.id in (select * from (select max(t.id) from oracle_dg_s_status t where t.server_id = %s) m) """%(sta_id)
        is_succ = mysql.ExecuteSQL(mysql_conn, str)
        
        if is_succ==1:
            logger.info("Update MRP status to active in oracle_dg_s_status for server %s successfully." %(sta_id))
        else:
            logger.info("Update MRP status to active in oracle_dg_s_status for server %s failed." %(sta_id))
	

	
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
		
    
    s_str = """select concat(username, '/', password, '@', host, ':', port, '/', dsn) from db_cfg_oracle where id=%s """ %(sta_id)
    s_conn_str = mysql.GetSingleValue(mysql_conn, s_str)

    s_str = """select concat(username, '/', password, '@', host, ':', port, '/', dsn) from db_cfg_oracle where id=%s """ %(sta_id)
    s_nopass_str = mysql.GetSingleValue(mysql_conn, s_str)
	
    logger.info("The standby database is: " + s_nopass_str + ", the id is: " + str(sta_id))
	
    s_conn = oracle.ConnectOracleAsSysdba(s_conn_str)
	
		
    try:
        common.operation_lock(mysql_conn, group_id, 'SNAPSHOT_STOP')

        common.init_op_instance(mysql_conn, group_id, 'SNAPSHOT_STOP')					#初始化切换实例
        
        if s_conn is None:
            logger.error("Connect to standby database error, exit!!!")
            common.update_op_reason(mysql_conn, group_id, 'SNAPSHOT_STOP', '连接数据库失败')
            common.update_op_result(mysql_conn, group_id, 'SNAPSHOT_STOP', '-1')
            sys.exit(2)
        
        common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_STOP', '准备停止快照模式', 10, 2)
        res = stop_mrp(mysql_conn, group_id, s_conn, s_conn_str, sta_id)
        if res ==0:
            update_mrp_status(mysql_conn, sta_id)
            common.update_op_result(mysql_conn, group_id, 'SNAPSHOT_STOP', '0')
            
    finally:
        common.operation_unlock(mysql_conn, group_id, 'SNAPSHOT_STOP')
		

	