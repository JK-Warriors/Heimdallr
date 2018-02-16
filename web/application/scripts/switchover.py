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
import traceback

import mysql_handle as mysql
import oracle_handle as oracle
import common

import logging
import logging.config

logging.config.fileConfig('./logging.conf')
logger = logging.getLogger('WLBlazers')

###############################################################################
# function switch2standby
###############################################################################
def switch2standby(mysql_conn, group_id, p_conn, p_conn_str, pri_id):
    result=-1
    
    logger.info("Switchover database to physical standby in progress...")
    # get database role
    str='select database_role from v$database'
    role=oracle.GetSingleValue(p_conn, str)
    common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '获取数据库角色成功。', 15, 2)
    logger.info("The current database role is: " + role)
	
    # get switchover status
    str='select switchover_status from v$database'
    switch_status=oracle.GetSingleValue(p_conn, str)
    logger.info("The current database switchover status is: " + switch_status)
	
    # get database version
    str="""select substr(version, 0, instr(version, '.')-1) from v$instance"""
    version=oracle.GetSingleValue(p_conn, str)
	
    if role=="PRIMARY":
        common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '验证数据库角色成功。', 20, 2)
        logger.info("Now we are going to switch database %s to physical standby." %(pri_id))
        if switch_status=="TO STANDBY" or switch_status=="SESSIONS ACTIVE":
            logger.info("Switchover to physical standby... ")
            common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '正在将主库切换成备库，可能会花费几分钟时间，请耐心等待...', 25, 0)
            sqlplus = Popen(["sqlplus", "-S", p_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
            sqlplus.stdin.write(bytes("alter database commit to switchover to physical standby with session shutdown;"+os.linesep))
            sqlplus.stdin.write(bytes("shutdown immediate"+os.linesep))
            sqlplus.stdin.write(bytes("startup mount"+os.linesep))
            sqlplus.stdin.write(bytes("alter database recover managed standby database using current logfile disconnect from session;"+os.linesep))
            out, err = sqlplus.communicate()
            logger.info(out)
            #logger.error(err)
            
            # 获取oracle连接
            p_conn = oracle.ConnectOracleAsSysdba(p_conn_str)
    
            if version > '10':
                logger.info("Alter standby database to open read only in progress... ")
                common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '正在将备库启动到open readonly状态...', 40, 0)
                sqlplus = Popen(["sqlplus", "-S", p_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
                sqlplus.stdin.write(bytes("alter database recover managed standby database cancel;"+os.linesep))
                sqlplus.stdin.write(bytes("alter database open;"+os.linesep))
                sqlplus.stdin.write(bytes("alter database recover managed standby database using current logfile disconnect from session;"+os.linesep))
                out, err = sqlplus.communicate()
                logger.info(out)
                #logger.error(err)

                str='select open_mode from v$database'
                open_mode=oracle.GetSingleValue(p_conn, str)
                if open_mode=="READ ONLY" or open_mode=="READ ONLY WITH APPLY" :
                    common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '备库已经成功启动到open readonly状态。', 45, 2)
                    logger.info("Alter standby database to open successfully.")
                else:
                    common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '备库已经成功启动到open readonly状态。', 45, 2)
                    logger.error("Start MRP process failed!")

            str='select database_role from v$database'
            role=oracle.GetSingleValue(p_conn, str)
            if role=="PHYSICAL STANDBY":
                common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '主库已经成功切换成备库。', 50, 2)
                logger.info("Switchover to physical standby successfully.")
                result=0
            else:
                common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '主库切换备库失败。', 50, 2)
                logger.info("Switchover to physical standby failed.")
                result=-1
            
    else:
        common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '验证数据库角色失败，当前数据库不是主库，不能切换到备库。', 90, 2)
        logger.error("You can not switchover a standby database to physical standby!")
        
    return result;

	
###############################################################################
# function standby2primary
###############################################################################
def standby2primary(mysql_conn, group_id, s_conn, s_conn_str, sta_id):
    result=-1
    
    logger.info("Switchover database to primary in progress...")
    # get database role
    str='select database_role from v$database'
    role=oracle.GetSingleValue(s_conn, str)
    common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '获取数据库角色成功。', 55, 2)
    logger.info("The current database role is: " + role)
	
    # get switchover status
    str='select switchover_status from v$database'
    switch_status=oracle.GetSingleValue(s_conn, str)
    logger.info("The current database switchover status is: " + switch_status)
	

    if role=="PHYSICAL STANDBY":
        common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '验证数据库角色成功。', 70, 2)
        logger.info("Now we are going to switch database %s to primary." %(sta_id))
        if switch_status=="NOT ALLOWED" or switch_status=="SWITCHOVER PENDING":
            show_str="数据库状态为 %s，无法进行切换，尝试重启MRP进程" %(switch_status)
            common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', show_str, 70, 0)
            
            logger.info("The standby database not allowed to switchover, restart the MRP process...")
            sqlplus = Popen(["sqlplus", "-S", s_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
            sqlplus.stdin.write(bytes("alter database recover managed standby database cancel;"+os.linesep))
            sqlplus.stdin.write(bytes("alter database recover managed standby database disconnect from session;"+os.linesep))
            out, err = sqlplus.communicate()
            logger.info(out)
            #logger.error(err)
            
            # check MRP status
            str="select count(1) from gv$session where program like '%(MRP0)' "
            mrp_status=oracle.GetSingleValue(s_conn, str)
            if mrp_status > 0:
                common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '重启数据库MRP进程成功。', 72, 0)
                logger.info("Restart the MRP process successfully.")
            else:
                common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '重启数据库MRP进程失败。', 72, 0)
                logger.info("Restart the MRP process failed.")


						# 再次验证切换状态
            timeout=0
            str='select switchover_status from v$database'
            switch_status=oracle.GetSingleValue(s_conn, str)
            while switch_status=="NOT ALLOWED" or switch_status=="SWITCHOVER PENDING":
                if timeout > 30:
                    break
                	
                show_str="数据库状态为 %s，无法进行切换。" %(switch_status)
                common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', show_str, 72, 2)
                str='select switchover_status from v$database'
                switch_status=oracle.GetSingleValue(s_conn, str)
                timeout=timeout + 2


            if timeout > 30:
                logger.info("Switchover standby database to primary failed.")
                return -1							#超时退出
                
                
            if switch_status=="TO PRIMARY" or switch_status=="SESSIONS ACTIVE":
                to_primary(mysql_conn, group_id, s_conn_str)
				
        if switch_status=="TO PRIMARY" or switch_status=="SESSIONS ACTIVE":
            to_primary(mysql_conn, group_id, s_conn_str)

            
        # 重新切换后数据库角色
        s_conn = oracle.ConnectOracleAsSysdba(s_conn_str)
        str='select database_role from v$database'
        db_role=oracle.GetSingleValue(s_conn, str)
        if db_role=="PRIMARY":
            common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '备库已经成功切换成主库。', 90, 2)
            logger.info("Switchover standby database to primary successfully.")
            result = 0
        else:
            common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '备库切换主库失败。', 90, 2)
            logger.info("Switchover standby database to primary failed.")
            result = -1
        	
    else:
        logger.error("You can not switchover primary database to primary!")

    return result



###############################################################################
# function to_primary
###############################################################################
def to_primary(mysql_conn, group_id, s_conn_str):
    common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '正在将备库切换成主库，可能会花费几分钟时间，请耐心等待...', 80, 0)
    logger.info("Switchover standby database to primary... ")
    sqlplus = Popen(["sqlplus", "-S", s_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
    sqlplus.stdin.write(bytes("alter database commit to switchover to primary with session shutdown;"+os.linesep))
    sqlplus.stdin.write(bytes("shutdown immediate"+os.linesep))
    sqlplus.stdin.write(bytes("startup mount"+os.linesep))
    sqlplus.stdin.write(bytes("alter database open;"+os.linesep))
    sqlplus.stdin.write(bytes("alter system archive log current;"+os.linesep))
    out, err = sqlplus.communicate()
    logger.info(out)
    logger.error(err)

    
    
    
###############################################################################
# function update_switch_flag
###############################################################################
def update_switch_flag(mysql_conn, group_id):
    logger.info("Update switch flag in db_cfg_oracle_dg for group %s in progress..." %(group_id))
    # get current switch flag
    str='select is_switch from db_cfg_oracle_dg where id= %s' %(group_id)
    is_switch=mysql.GetSingleValue(mysql_conn, str)
    logger.info("The current switch flag is: %s" %(is_switch))
	
    if is_switch==0:
        str="""update db_cfg_oracle_dg set is_switch = 1 where id = %s"""%(group_id)
    else:
        str="""update db_cfg_oracle_dg set is_switch = 0 where id = %s"""%(group_id)

		
    is_succ = mysql.ExecuteSQL(mysql_conn, str)

    if is_succ==1:
        logger.info("Update switch flag in db_cfg_oracle_dg for group %s successfully." %(group_id))
    else:
        logger.info("Update switch flag in db_cfg_oracle_dg for group %s failed." %(group_id))
	


    



         	
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
		
    
    # get infomation from mysql
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

	



    try:
        common.operation_lock(mysql_conn, group_id, 'SWITCHOVER')			# 加锁

        # connect to oracle
        p_conn = oracle.ConnectOracleAsSysdba(p_conn_str)
        s_conn = oracle.ConnectOracleAsSysdba(s_conn_str)
        if p_conn is None:
            common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '连接主库失败，请根据相应日志查看原因。', 10, 5)
            logger.error("Connect to primary database error, exit!!!")
            sys.exit(2)
        if s_conn is None:
            common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '连接备库失败，请根据相应日志查看原因。', 10, 5)
            logger.error("Connect to standby database error, exit!!!")
            sys.exit(2)
        
        str='select count(1) from gv$instance'
        p_count=oracle.GetSingleValue(p_conn, str)
        s_count=oracle.GetSingleValue(s_conn, str)
    
        # try to kill all "(LOCAL=NO)" connections in database
        try:
            if p_count > 1 or s_count > 1:
                common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '正在尝试杀掉"(LOCAL=NO)"的会话，并关闭集群的其他节点。可能需要一段时间，请耐心等待...', 5, 0)
            else:
                common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '正在尝试杀掉"(LOCAL=NO)"的会话。可能需要一段时间，请耐心等待...', 5, 0)
    	  		
            common.kill_sessions(mysql_conn, p_conn, pri_id)
            common.kill_sessions(mysql_conn, s_conn, sta_id)
        except Exception,e:
            logger.error("kill sessions error!!!")
            logger.error("traceback.format_exc(): \n%s" %(traceback.format_exc()))
            pass
            
     
        # 验证其他实例是否关闭
        str='select count(1) from gv$instance'
        p_count=oracle.GetSingleValue(p_conn, str)
        s_count=oracle.GetSingleValue(s_conn, str)
        show_msg=""
        if p_count > 1 and s_count > 1:
            show_msg='关闭实例失败，主库端依然有 %s 个存活实例，备库端依然有 %s 个存活实例，请手工关闭后重新尝试切换。' %(p_count, s_count)
            common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', show_msg, 10, 5)
            logger.error("Shutdown instance failed. There are still more than one instance active both in primary and standby.")
            sys.exit(2)
        elif p_count > 1:
            show_msg='关闭实例失败，主库端依然有 %s 个存活实例，请手工关闭后重新尝试切换。' %(p_count)
            common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', show_msg, 10, 5)
            logger.error("Shutdown instance failed. There are still more than one instance active in primary.")
            sys.exit(2)
        elif s_count > 1:
            show_msg='关闭实例失败，备库端依然有 %s 个存活实例，请手工关闭后重新尝试切换。' %(s_count)
            common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', show_msg, 10, 5)
            logger.error("Shutdown instance failed. There are still more than one instance active in standby.")
            sys.exit(2)
    	
    	
    
               
        # 正式开始切换  
        try:
            common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '准备执行主备切换。', 10, 2)
        
            res_2s=switch2standby(mysql_conn, group_id, p_conn, p_conn_str, pri_id)

            res_2p=""
            if res_2s ==0:
                res_2p=standby2primary(mysql_conn, group_id, s_conn, s_conn_str, sta_id)

                if res_2p == 0:
                    update_switch_flag(mysql_conn, group_id)
                else:
                    common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '切换失败，请通过相关日志查看原因！', 90, 2)
            else:
                common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '切换失败，请通过相关日志查看原因！', 50, 2)
        except Exception,e:
            pass
        
    except Exception,e:
        pass
    finally:
        common.operation_unlock(mysql_conn, group_id, 'SWITCHOVER')
        