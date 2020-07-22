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
##  08/11/2019                      Baseline version 1.0.0
##
######################################################################

import os
import string
import datetime
import sys, getopt
import traceback

import mysql_handle as mysql
import common

import logging
import logging.config

logging.config.fileConfig('./logging.conf')
logger = logging.getLogger('WLBlazers')


###############################################################################
# function switch2main
###############################################################################
def switch2main(mysql_conn, db_type, group_id, p_conn, s_conn, sta_id):
    result=-1
    
    logger.info("Switchover database to main in progress...")
    # get database role
    role=mysql.IsSubordinate(s_conn)
    common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '获取数据库角色成功', 0, 2)
    logger.info("The current database role is: %s (0:MASTER; 1:SLAVE)" %(role))
	
    # get database version
	
    
    if role==1:
        common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '验证从库数据库角色成功', 0, 2)
        # get main status
        m_binlog_file=""
        m_binlog_pos=-1
        main_info = mysql.GetSingleRow(p_conn, 'show main status;')
        if main_info:
            m_binlog_file=main_info[0]
            m_binlog_pos=main_info[1]
            logger.debug("Main: main_binlog_file: %s" %(m_binlog_file))
            logger.debug("Main: main_binlog_pos: %s" %(m_binlog_pos))
            
        # check subordinate status
        subordinate_info=mysql.GetSingleRow(s_conn, 'show subordinate status;')
        if subordinate_info:
            current_binlog_file=subordinate_info[9]
            current_binlog_pos=subordinate_info[21]
            main_binlog_file=subordinate_info[5]
            main_binlog_pos=subordinate_info[6]
            
            logger.debug("Subordinate: current_binlog_file: %s" %(current_binlog_file))
            logger.debug("Subordinate: current_binlog_pos: %s" %(current_binlog_pos))
            logger.debug("Subordinate: main_binlog_file: %s" %(main_binlog_file))
            logger.debug("Subordinate: main_binlog_pos: %s" %(main_binlog_pos))
            
            if (current_binlog_file == main_binlog_file and m_binlog_file==main_binlog_file and current_binlog_pos==main_binlog_pos and main_binlog_pos==m_binlog_pos):
                # can switch now
                logger.info("Now we are going to switch database %s to main." %(sta_id))
                common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '正在将从库切换成主库...', 0, 0)
                str='''stop subordinate io_thread; '''
                res=mysql.ExecuteSQL(s_conn, str)
                logger.debug("Stop subordinate io_thread.")
        
                str='''stop subordinate; '''
                res=mysql.ExecuteSQL(s_conn, str)
                logger.debug("Stop subordinate.")
        
                str='''reset subordinate all; '''
                res=mysql.ExecuteSQL(s_conn, str)
                logger.debug("Reset subordinate all.")
                
                logger.info("Switchover subordinate to main successfully.")
                common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '从库已经成功切换成主库', 0, 2)
                result=0
            else:
                logger.error("Check binlog position failed.")
                common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '验证数据库binlog复制位置失败', 0, 2)
                result=-1
        else:
            logger.info("Check subordinate status failed.")
            common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '从库切换主库失败', 0, 2)
            result=-1
        
    else:
        common.update_db_op_reason(mysql_conn, db_type, group_id, 'SWITCHOVER', '验证数据库角色失败，当前数据库不是从库，不能切换到主库')
        common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '验证数据库角色失败，当前数据库不是从库，不能切换到主库', 0, 2)
        logger.error("You can not switchover a main database to main!")
        result=-1
        
    return result



###############################################################################
# function rebuild_replication
###############################################################################
def rebuild_replication(mysql_conn, db_type, group_id, p_conn, pri_id, s_conn, sta_id, s_host, s_port, s_username, s_password):
    result=-1
    
    logger.info("Rebuild replication in progress...")
    
    # unlock tables
    logger.debug("Unlock tables for database: %s first" %(pri_id))
    unlock_tables(p_conn, pri_id)
    
    
    # get main status
    main_info=mysql.GetSingleRow(s_conn, 'show main status;')
    if main_info:
        main_binlog_file=main_info[0]
        main_binlog_pos=main_info[1]
	
    str='''stop subordinate; '''
    res=mysql.ExecuteSQL(p_conn, str)
    logger.debug("Stop subordinate")
    
    str='''change main to main_host='%s',main_port=%s,main_user='%s',main_password='%s',main_log_file='%s',main_log_pos=%s; '''%(s_host, s_port, s_username, s_password, main_binlog_file, main_binlog_pos)
    logger.debug("Change main command: %s" %(str))
    res=mysql.ExecuteSQL(p_conn, str)
        
    str='''start subordinate; '''
    res=mysql.ExecuteSQL(p_conn, str)
    logger.debug("Start subordinate")

    subordinate_info=mysql.GetSingleRow(p_conn, 'show subordinate status;')
    if subordinate_info:
        logger.info("Rebuild replication successfully !")
        common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '重建复制关系成功', 0, 2)
        result=0
    else:
        common.update_db_op_reason(mysql_conn, db_type, group_id, 'SWITCHOVER', '重建复制关系失败')
        common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '重建复制关系失败', 0, 2)
        logger.error("Rebuild replication failed !")
        result=-1
        
    return result


###############################################################################
# function lock tables
###############################################################################
def lock_tables(p_conn, pri_id):
    result=-1
    
    logger.info("Flush tables with read lock for database: %s" %(pri_id))
    str='''flush tables with read lock;  '''
    res=mysql.ExecuteSQL(p_conn, str)
    
    if res == 1:
        logger.info("Flush tables with read lock successfully !")
        common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '锁定数据库成功', 0, 2)
    else:
        logger.error("Flush tables with read lock failed !")
        common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '锁定数据库失败', 0, 2)
    return result
    
    
    
    
###############################################################################
# function unlock_tables
###############################################################################
def unlock_tables(p_conn, pri_id):
    result=-1
    
    logger.info("Unlock tables for database: %s" %(pri_id))
    str='''unlock tables;  '''
    res=mysql.ExecuteSQL(p_conn, str)
    
    if res == 1:
        logger.info("Unlock tables successfully !")
        common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '解锁数据库成功', 0, 2)
    else:
        logger.error("Unlock tables failed !")
        common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '解锁数据库失败', 0, 2)
    return result
    
    
    
###############################################################################
# function update_switch_flag
###############################################################################
def update_switch_flag(mysql_conn, group_id):
    logger.info("Update switch flag in db_cfg_mysql_dr for group %s in progress..." %(group_id))
    # get current switch flag
    str='select is_switch from db_cfg_mysql_dr where id= %s' %(group_id)
    is_switch=mysql.GetSingleValue(mysql_conn, str)
    logger.info("The current switch flag is: %s" %(is_switch))
	
    if is_switch==0:
        str="""update db_cfg_mysql_dr set is_switch = 1 where id = %s"""%(group_id)
    else:
        str="""update db_cfg_mysql_dr set is_switch = 0 where id = %s"""%(group_id)

		
    is_succ = mysql.ExecuteSQL(mysql_conn, str)

    if is_succ==1:
        common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '容灾组更新状态成功', 100, 2)
        logger.info("Update switch flag in db_cfg_mysql_dr for group %s successfully." %(group_id))
    else:
        logger.info("Update switch flag in db_cfg_mysql_dr for group %s failed." %(group_id))
	



    
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
    db_name = ""
    
    p_host = ""
    p_port = ""
    p_username = ""
    p_password = ""
    
    s_host = ""
    s_port = ""
    s_username = ""
    s_password = ""
    
    
    
    p_str = """select host, port, username, password from db_cfg_mysql where id=%s; """ %(pri_id)
    res1 = mysql.GetSingleRow(mysql_conn, p_str)
    if res1:
        p_host = res1[0]
        p_port = res1[1]
        p_username = res1[2]
        p_password = res1[3]
        
    
    s_str = """select host, port, username, password from db_cfg_mysql where id=%s; """ %(sta_id)
    res2 = mysql.GetSingleRow(mysql_conn, s_str)
    if res2:
        s_host = res2[0]
        s_port = res2[1]
        s_username = res2[2]
        s_password = res2[3]
    #print s_host,s_port,s_username,s_password

	
    p_str = """select concat(host, ':', port) from db_cfg_mysql where id=%s; """ %(pri_id)
    p_nopass_str = mysql.GetSingleValue(mysql_conn, p_str)
    s_str = """select concat(host, ':', port) from db_cfg_mysql where id=%s; """ %(sta_id)
    s_nopass_str = mysql.GetSingleValue(mysql_conn, s_str)
	
    logger.info("The main database is: " + p_nopass_str + ", the id is: " + str(pri_id))
    logger.info("The subordinate database is: " + s_nopass_str + ", the id is: " + str(sta_id))



    db_type = "mysql"
    try:
        common.db_op_lock(mysql_conn, db_type, group_id, 'SWITCHOVER')			# 加锁
        common.init_db_op_instance(mysql_conn, db_type, group_id, 'SWITCHOVER')					#初始化切换实例
	
        # connect to mysql
        p_conn = mysql.ConnectMysql_T(p_host,p_port,p_username,p_password)
        s_conn = mysql.ConnectMysql_T(s_host,s_port,s_username,s_password)
        if p_conn is None:
            common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '连接主库失败，请根据相应日志查看原因', 0, 3)
            logger.error("Connect to primary database error, exit!!!")
            
            common.update_db_op_reason(mysql_conn, db_type, group_id, 'SWITCHOVER', '连接主库失败')
            common.update_db_op_result(mysql_conn, db_type, group_id, 'SWITCHOVER', '-1')
            sys.exit(2)
        if s_conn is None:
            common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '连接从库失败，请根据相应日志查看原因', 0, 3)
            logger.error("Connect to standby database error, exit!!!")
            
            common.update_db_op_reason(mysql_conn, db_type, group_id, 'SWITCHOVER', '连接从库失败')
            common.update_db_op_result(mysql_conn, db_type, group_id, 'SWITCHOVER', '-1')
            sys.exit(2)
        

    
   
        # 正式开始切换  
        try:
            common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '准备执行主从切换', 0, 2)
        
            lock_tables(p_conn, pri_id)
            
            res_2m=switch2main(mysql_conn, db_type, group_id, p_conn, s_conn, sta_id)
            if res_2m ==0:
                res_2s=rebuild_replication(mysql_conn, db_type, group_id, p_conn, pri_id, s_conn, sta_id, s_host, s_port, s_username, s_password)
                
                if res_2s == 0:
                    update_switch_flag(mysql_conn, group_id)
                    common.gen_alert_mysql(sta_id, 1)     # generate alert
                    common.update_db_op_result(mysql_conn, db_type, group_id, 'SWITCHOVER', '0')
                else:
                    common.update_db_op_result(mysql_conn, db_type, group_id, 'SWITCHOVER', res_2s)
            else:
                common.update_db_op_result(mysql_conn, db_type, group_id, 'SWITCHOVER', res_2m)
                
        except Exception,e:
            logger.error(traceback.format_exc())
            pass

    except Exception,e:
        logger.error(traceback.format_exc())
        pass
    finally:
        common.db_op_unlock(mysql_conn, db_type, group_id, 'SWITCHOVER')
       