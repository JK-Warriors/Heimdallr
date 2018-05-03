#!/usr/bin/python
#-*- coding: utf-8 -*-

######################################################################
# Copyright (c)  2017 by WLBlazers Corporation
#
# snapshot_start.py
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
# function start_snapshot
###############################################################################
def start_snapshot(mysql_conn, group_id, s_conn, s_conn_str, sta_id):
    result=-1
    
    logger.info("Start to activate standby databaes %s in progress..." %(sta_id))
    # get database role
    str='select database_role from v$database'
    role=oracle.GetSingleValue(s_conn, str)
    common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_START', '获取数据库角色成功', 20, 2)
    logger.info("The current database role is: " + role)
	
    # get flashback status
    str='select flashback_on from v$database'
    fb_status=oracle.GetSingleValue(s_conn, str)
    common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_START', '获取数据库闪回状态成功', 20, 2)
    logger.info("The current flashback status is: " + fb_status)
	
    # get database version
    str="""select substr(version, 0, instr(version, '.')-1) from v$instance"""
    version=oracle.GetSingleValue(s_conn, str)
	
    # get mrp process status
    str="""select count(1) from gv$session where program like '%(MRP0)' """
    mrp_process=oracle.GetSingleValue(s_conn, str)
    common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_START', '获取MRP进程状态成功', 30, 2)
    
    if version <=10:
        common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_START', '开启快照模式失败，当前数据库版本不支持', 90, 2)
        return result;
        
    if fb_status == "NO":
        common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_START', '开启快照模式失败，当前数据库没有开启闪回', 90, 2)
        return result;
        
    if role=="PHYSICAL STANDBY":
        common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_START', '验证数据库角色成功', 40, 2)
        if(mrp_process > 0):
            logger.info("The mrp process is already active, need stop first... ")
            common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_START', '检测到MRP进程正在运行，需要先停止MRP', 50, 2)
            common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_START', '正在停止MRP进程...', 50, 2)
            
            sqlplus = Popen(["sqlplus", "-S", s_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
            sqlplus.stdin.write(bytes("alter database recover managed standby database cancel;"+os.linesep))
            out, err = sqlplus.communicate()
            logger.info(out)
            if 'ORA-' in out:
                common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_START', '停止MRP进程失败', 70, 2)
            else:
                common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_START', '停止MRP进程成功', 70, 2)
            
                common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_START', '正在激活数据库快照...', 75, 2)
                sqlplus = Popen(["sqlplus", "-S", s_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
                sqlplus.stdin.write(bytes("alter database convert to snapshot standby;"+os.linesep))
                sqlplus.stdin.write(bytes("alter database open;"+os.linesep))
                out, err = sqlplus.communicate()
                logger.info(out)
                if 'ORA-' in out:
                    common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_START', '数据库快照激活失败', 90, 2)
                else:
                    common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_START', '数据库快照激活成功', 90, 2)
                    result=0
        else:
            common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_START', '正在激活数据库快照...', 70, 2)
            sqlplus.stdin.write(bytes("alter database convert to snapshot standby;"+os.linesep))
            sqlplus.stdin.write(bytes("alter database open;"+os.linesep))
            out, err = sqlplus.communicate()
            logger.info(out)
            if 'ORA-' in out:
                common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_START', '数据库快照激活失败', 90, 2)
            else:
                common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_START', '数据库快照激活成功', 90, 2)
                result=0
    else:
        common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_START', '验证数据库角色失败，当前数据库不是PHYSICAL STANDBY，不能激活快照', 90)
	
    return result;



###############################################################################
# function update the mrp status in mysql
###############################################################################
def update_mrp_status(mysql_conn, sta_id):
    logger.info("Update MRP status in oracle_dg_s_status for server %s in progress..." %(sta_id))
    
    # get current MRP status
    str='select mrp_status from oracle_dg_s_status where server_id= %s' %(sta_id)
    mrp_status=mysql.GetSingleValue(mysql_conn, str)
    # logger.info("debug the mrp_status: %s" %(mrp_status))
    
    if mrp_status == '1':
        logger.info("The current MRP status is active.")
        str="""update oracle_dg_s_status s set s.mrp_status = 0 where s.id in (select * from (select max(t.id) from oracle_dg_s_status t where t.server_id = %s) m) """%(sta_id)
        is_succ = mysql.ExecuteSQL(mysql_conn, str)
        
        if is_succ==1:
            logger.info("Update MRP status to inactive in oracle_dg_s_status for server %s successfully." %(sta_id))
        else:
            logger.info("Update MRP status to inactive in oracle_dg_s_status for server %s failed." %(sta_id))
            
            
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
	
		
    if s_conn is None:
        logger.error("Connect to standby database error, exit!!!")
        sys.exit(2)
    else:
        try:
            common.operation_lock(mysql_conn, group_id, 'SNAPSHOT_START')
            common.log_dg_op_process(mysql_conn, group_id, 'SNAPSHOT_START', '准备进入快照状态', 10, 2)
            res = start_snapshot(mysql_conn, group_id, s_conn, s_conn_str, sta_id)
            if res ==0:
                update_mrp_status(mysql_conn, sta_id)
  
        finally:
            common.operation_unlock(mysql_conn, group_id, 'SNAPSHOT_START')

