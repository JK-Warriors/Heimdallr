#!/usr/bin/python
#-*- coding: utf-8 -*-

######################################################################
# Copyright (c)  2017 by WLBlazers Corporation
#
# mrp_start.py
# 
# 
######################################################################
# Modifications Section:
######################################################################
##     Date        File            Changes
######################################################################
##  01/22/2018                      Baseline version 1.0.0
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
# function start_mrp
###############################################################################
def start_mrp(mysql_conn, group_id, s_conn, s_conn_str, sta_id):
    result=-1
    
    logger.info("Start the MRP process for databaes %s in progress..." %(sta_id))
    # get database role
    str='select database_role from v$database'
    role=oracle.GetSingleValue(s_conn, str)
    common.log_dg_op_process(mysql_conn, group_id, 'MRP_START', '获取数据库角色成功', 20, 2)
    logger.info("The current database role is: " + role)
	
    # get database version
    str="""select substr(version, 0, instr(version, '.')-1) from v$instance"""
    version=oracle.GetSingleValue(s_conn, str)
    
    # get instance status
    str="""select status from v$instance"""
    inst_status=oracle.GetSingleValue(s_conn, str)
	
    # get mrp process status
    str="""select count(1) from gv$session where program like '%(MRP0)' """
    mrp_process=oracle.GetSingleValue(s_conn, str)
    common.log_dg_op_process(mysql_conn, group_id, 'MRP_START', '获取MRP进程状态成功', 30, 2)
	
    if role=="PHYSICAL STANDBY":
        common.log_dg_op_process(mysql_conn, group_id, 'MRP_START', '验证数据库角色成功', 50, 2)
        if(mrp_process > 0):
            logger.info("The mrp process is already active... ")
            common.log_dg_op_process(mysql_conn, group_id, 'MRP_START', '验证MRP进程，已经是激活状态', 70, 2)
        else:
            if version> 10 and inst_status=="MOUNTED":
                common.log_dg_op_process(mysql_conn, group_id, 'MRP_START', '检测到当前实例处于MOUNTED状态，正在启动到OPEN...', 70, 2)
                sqlplus = Popen(["sqlplus", "-S", s_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
                sqlplus.stdin.write(bytes("alter database open;"+os.linesep))
                out, err = sqlplus.communicate()
                
                if 'ORA-' in out:
                    logger.info("Alter database open failed.")
                else:
                    common.log_dg_op_process(mysql_conn, group_id, 'MRP_START', '启动实例到OPEN状态成功', 70, 2)
                    logger.info("Alter database open successfully.")
            	
            
            logger.info("Now we are going to start the mrp process... ")
            common.log_dg_op_process(mysql_conn, group_id, 'MRP_START', '正在开启MRP进程...', 70, 2)
            sqlplus = Popen(["sqlplus", "-S", s_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
            sqlplus.stdin.write(bytes("alter database recover managed standby database using current logfile disconnect from session;"+os.linesep))
            out, err = sqlplus.communicate()
            logger.info(out)
            #logger.error(err)
            if err is None:
                common.log_dg_op_process(mysql_conn, group_id, 'MRP_START', 'MRP进程开启成功', 90, 2)
                logger.info("Start the MRP process successfully.")
                result=0
    else:
        common.update_op_reason(mysql_conn, group_id, 'MRP_START', '验证数据库角色失败，当前数据库不是PHYSICAL STANDBY，不能开启MRP')
        common.log_dg_op_process(mysql_conn, group_id, 'MRP_START', '验证数据库角色失败，当前数据库不是PHYSICAL STANDBY，不能开启MRP', 90)
	
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
# function enable_rfs
###############################################################################
def enable_rfs(mysql_conn, p_conn):
    logger.info("Enable RFS process in primary...")
    str="alter system set log_archive_dest_state_2=defer sid='*' "
    rfs_status=oracle.ExecuteSQL(p_conn, str)

    str="alter system set log_archive_dest_state_2=enable sid='*' "
    rfs_status=oracle.ExecuteSQL(p_conn, str)
    
    str="alter system archive log current "
    res=oracle.ExecuteSQL(p_conn, str)



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
		
    
    p_str = """select concat(username, '/', password, '@', host, ':', port, '/', dsn) from db_cfg_oracle where id=%s """ %(pri_id)
    p_conn_str = mysql.GetSingleValue(mysql_conn, p_str)
    s_str = """select concat(username, '/', password, '@', host, ':', port, '/', dsn) from db_cfg_oracle where id=%s """ %(sta_id)
    s_conn_str = mysql.GetSingleValue(mysql_conn, s_str)

	
    p_str = """select concat(username, '@', host, ':', port, '/', dsn) from db_cfg_oracle where id=%s """ %(pri_id)
    p_nopass_str = mysql.GetSingleValue(mysql_conn, p_str)
    s_str = """select concat(username, '/', password, '@', host, ':', port, '/', dsn) from db_cfg_oracle where id=%s """ %(sta_id)
    s_nopass_str = mysql.GetSingleValue(mysql_conn, s_str)
	
    logger.info("The primary database is: " + p_nopass_str + ", the id is: " + str(pri_id))
    logger.info("The standby database is: " + s_nopass_str + ", the id is: " + str(sta_id))
	
    p_conn = oracle.ConnectOracleAsSysdba(p_conn_str)
    s_conn = oracle.ConnectOracleAsSysdba(s_conn_str)
	
		
    try:
        common.operation_lock(mysql_conn, group_id, 'MRP_START')
    
        common.init_op_instance(mysql_conn, group_id, 'MRP_START')					#初始化操作实例
    
        if s_conn is None:
            logger.error("Connect to standby database error, exit!!!")
            
            common.update_op_reason(mysql_conn, group_id, 'MRP_START', '连接数据库失败')
            common.update_op_result(mysql_conn, group_id, 'MRP_START', '-1')
            sys.exit(2)
        
        common.log_dg_op_process(mysql_conn, group_id, 'MRP_START', '准备开始启动MRP进程', 10, 2)
        res = start_mrp(mysql_conn, group_id, s_conn, s_conn_str, sta_id)
        if res ==0:
            update_mrp_status(mysql_conn, sta_id)
            common.update_op_result(mysql_conn, group_id, 'MRP_START', '0')
            
        enable_rfs(mysql_conn, p_conn)    
    finally:
        common.operation_unlock(mysql_conn, group_id, 'MRP_START')
        None
