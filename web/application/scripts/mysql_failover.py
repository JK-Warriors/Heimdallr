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
##  08/17/2019                      Baseline version 1.0.0
##
######################################################################

import os
import string
import sys, getopt
import traceback

import mysql_handle as mysql
import common

import logging
import logging.config

logging.config.fileConfig('./logging.conf')
logger = logging.getLogger('WLBlazers')

	
###############################################################################
# function switch2master
###############################################################################
def switch2master(mysql_conn, db_type, group_id, s_conn, sta_id):
    result=-1
    
    logger.info("FAILOVER database to master in progress...")
    # get database role
    role=mysql.IsSlave(s_conn)
    common.log_db_op_process(mysql_conn, db_type, group_id, 'FAILOVER', '获取数据库角色成功', 0, 2)
    logger.info("The current database role is: %s (0:MASTER; 1:SLAVE)" %(role))
	
    # get database version
	
    
    if role==1:
        common.log_db_op_process(mysql_conn, db_type, group_id, 'FAILOVER', '验证从库数据库角色成功', 0, 2)
        
        # check slave status
        slave_info=mysql.GetSingleRow(s_conn, 'show slave status;')
        if slave_info:
            current_binlog_file=slave_info[9]
            current_binlog_pos=slave_info[21]
            master_binlog_file=slave_info[5]
            master_binlog_pos=slave_info[6]
            
            logger.debug("current_binlog_file: %s" %(current_binlog_file))
            logger.debug("current_binlog_pos: %s" %(current_binlog_pos))
            logger.debug("master_binlog_file: %s" %(master_binlog_file))
            logger.debug("master_binlog_pos: %s" %(master_binlog_pos))
            
            # can switch now
            logger.info("Now we are going to switch database %s to master." %(sta_id))
            common.log_db_op_process(mysql_conn, db_type, group_id, 'FAILOVER', '正在将从库切换成主库...', 0, 0)
            str='''stop slave io_thread; '''
            res=mysql.ExecuteSQL(s_conn, str)
            logger.debug("Stop slave io_thread.")
        
            str='''stop slave; '''
            res=mysql.ExecuteSQL(s_conn, str)
            logger.debug("Stop slave.")
        
            str='''reset master; '''
            res=mysql.ExecuteSQL(s_conn, str)
            logger.debug("Reset master.")
                
            logger.info("FAILOVER slave to master successfully.")
            common.log_db_op_process(mysql_conn, db_type, group_id, 'FAILOVER', '从库已经成功切换成主库', 0, 2)
            result=0
        else:
            logger.info("Check slave status failed.")
            common.log_db_op_process(mysql_conn, db_type, group_id, 'FAILOVER', '从库切换主库失败', 0, 2)
            result=-1
        
    else:
        common.update_db_op_reason(mysql_conn, db_type, group_id, 'FAILOVER', '验证数据库角色失败，当前数据库不是从库，不能切换到主库')
        common.log_db_op_process(mysql_conn, db_type, group_id, 'FAILOVER', '验证数据库角色失败，当前数据库不是从库，不能切换到主库', 0, 2)
        logger.error("You can not FAILOVER a master database to master!")
        result=-1
        
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
        common.log_db_op_process(mysql_conn, db_type, group_id, 'FAILOVER', '容灾组更新状态成功', 100, 2)
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
    s_host = ""
    s_port = ""
    s_username = ""
    s_password = ""
    
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
	
    logger.info("The master database is: " + p_nopass_str + ", the id is: " + str(pri_id))
    logger.info("The slave database is: " + s_nopass_str + ", the id is: " + str(sta_id))



    db_type = "mysql"
    try:
        common.db_op_lock(mysql_conn, db_type, group_id, 'FAILOVER')			# 加锁
        common.init_db_op_instance(mysql_conn, db_type, group_id, 'FAILOVER')					#初始化切换实例
	
        # connect to mysql
        s_conn = mysql.ConnectMysql_T(s_host,s_port,s_username,s_password)

        if s_conn is None:
            common.log_db_op_process(mysql_conn, db_type, group_id, 'FAILOVER', '连接从库失败，请根据相应日志查看原因', 0, 3)
            logger.error("Connect to standby database error, exit!!!")
            
            common.update_db_op_reason(mysql_conn, db_type, group_id, 'FAILOVER', '连接从库失败')
            common.update_db_op_result(mysql_conn, db_type, group_id, 'FAILOVER', '-1')
            sys.exit(2)
        

    
   
        # 正式开始切换  
        try:
            common.log_db_op_process(mysql_conn, db_type, group_id, 'FAILOVER', '准备执行主从切换', 0, 2)
            
            res_2m=switch2master(mysql_conn, db_type, group_id, s_conn, sta_id)
            if res_2m ==0:
                update_switch_flag(mysql_conn, group_id)
                common.update_db_op_result(mysql_conn, db_type, group_id, 'FAILOVER', '0')
            else:
                common.update_db_op_result(mysql_conn, db_type, group_id, 'FAILOVER', res_2m)
                
        except Exception,e:
            logger.error(traceback.format_exc())
            pass

    except Exception,e:
        logger.error(traceback.format_exc())
        pass
    finally:
        common.db_op_unlock(mysql_conn, db_type, group_id, 'FAILOVER')
	