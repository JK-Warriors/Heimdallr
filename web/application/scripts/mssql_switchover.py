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
##  07/11/2019                      Baseline version 1.0.0
##
######################################################################

import os
import string
import datetime
import sys, getopt
import traceback

import mysql_handle as mysql
import sqlserver_handle as sqlserver
import common

import logging
import logging.config

logging.config.fileConfig('./logging.conf')
logger = logging.getLogger('WLBlazers')

###############################################################################
# function switch_mirror
###############################################################################
def switch_mirror(mysql_conn, db_type, group_id, db_name, p_conn, s_conn, pri_id):
    result=-1
    
    logger.info("Switchover database to physical standby in progress...")
    # get database role
    str='''select m.mirroring_role
					  from sys.database_mirroring m, sys.databases d
					 where M.mirroring_guid is NOT NULL
					   AND m.database_id = d.database_id
					   AND d.name = '%s'; ''' %(db_name)
    role=sqlserver.GetSingleValue(p_conn, str)
    common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '获取数据库角色成功', 20, 2)
    logger.info("The current database role is: %s (1:PRIMARY; 2:STANDBY)" %(role))
	
    # get mirror status
    str='''select m.mirroring_state
					  from sys.database_mirroring m, sys.databases d
					 where M.mirroring_guid is NOT NULL
					   AND m.database_id = d.database_id
					   AND d.name = '%s'; ''' %(db_name)
    mirror_status=sqlserver.GetSingleValue(p_conn, str)
    logger.info("The current database mirror status is: %s (0:已挂起; 1:与其他伙伴断开; 2:正在同步; 3:挂起故障转移; 4:已同步; 5:伙伴未同步; 6:伙伴已同步;)" %(mirror_status))
	
    # get database version
    #str="""SELECT @@VERSION"""
    #version=sqlserver.GetSingleValue(p_conn, str)
	
    
    if role==1:
        common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '验证数据库角色成功', 30, 2)
        logger.info("Now we are going to switch database %s to physical standby." %(pri_id))
        if mirror_status==4:
            common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '正在将主库切换成备库，可能会花费几分钟时间，请耐心等待...', 40, 0)
            
            #设置自动提交，否则alter database执行报错
            p_conn.autocommit(True)
            
            logger.info("SET SAFETY FULL... ")
            #设置镜像传输模式为高安全模式
            str='''ALTER DATABASE %s SET SAFETY FULL; ''' %(db_name)
            res=sqlserver.ExecuteSQL(p_conn, str)
            common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '主库已经成功切换成高安全模式', 50, 2)
    
            logger.info("SET PARTNER FAILOVER begin... ")
            #切换镜像
            str='''ALTER DATABASE %s SET PARTNER FAILOVER;''' %(db_name)
            res=sqlserver.ExecuteSQL(p_conn, str)
            p_conn.autocommit(False)
            
            str='''select m.mirroring_role
									  from sys.database_mirroring m, sys.databases d
									 where M.mirroring_guid is NOT NULL
									   AND m.database_id = d.database_id
									   AND d.name = '%s'; ''' %(db_name)
            new_role=sqlserver.GetSingleValue(p_conn, str)
    
            if new_role==2:
                common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '主库已经成功切换成备库', 70, 2)
                logger.info("SET PARTNER FAILOVER successfully.")
                
                #设置镜像传输模式为高性能模式
                s_conn.autocommit(True)
                str='''ALTER DATABASE %s SET SAFETY OFF; ''' %(db_name)
                res=sqlserver.ExecuteSQL(s_conn, str)
                s_conn.autocommit(False)
        
                common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '主库已经成功切换成高性能模式', 90, 2)
                logger.info("SET SAFETY OFF successfully.")
                
                result=0
            else:
                common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '主库切换备库失败', 70, 2)
                logger.info("SET PARTNER FAILOVER failed.")
                result=-1
    else:
        common.update_db_op_reason(mysql_conn, db_type, group_id, 'SWITCHOVER', '验证数据库角色失败，当前数据库不是主库，不能切换到备库')
        common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '验证数据库角色失败，当前数据库不是主库，不能切换到备库', 20, 2)
        logger.error("You can not switchover a standby database to standby!")
        
    return result


    
###############################################################################
# function update_switch_flag
###############################################################################
def update_switch_flag(mysql_conn, group_id):
    logger.info("Update switch flag in db_cfg_sqlserver_mirror for group %s in progress..." %(group_id))
    # get current switch flag
    str='select is_switch from db_cfg_sqlserver_mirror where id= %s' %(group_id)
    is_switch=mysql.GetSingleValue(mysql_conn, str)
    logger.info("The current switch flag is: %s" %(is_switch))
	
    if is_switch==0:
        str="""update db_cfg_sqlserver_mirror set is_switch = 1 where id = %s"""%(group_id)
    else:
        str="""update db_cfg_sqlserver_mirror set is_switch = 0 where id = %s"""%(group_id)

		
    is_succ = mysql.ExecuteSQL(mysql_conn, str)

    if is_succ==1:
        common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '镜像组更新状态成功', 100, 2)
        logger.info("Update switch flag in db_cfg_sqlserver_mirror for group %s successfully." %(group_id))
    else:
        logger.info("Update switch flag in db_cfg_sqlserver_mirror for group %s failed." %(group_id))
	



    

	
	      
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
    
    
    name_str = """select db_name from db_cfg_sqlserver_mirror where id=%s; """ %(group_id)
    db_name = mysql.GetSingleValue(mysql_conn, name_str)
    
    p_str = """select host, port, username, password from db_cfg_sqlserver where id=%s; """ %(pri_id)
    res1 = mysql.GetSingleRow(mysql_conn, p_str)
    if res1:
        p_host = res1[0]
        p_port = res1[1]
        p_username = res1[2]
        p_password = res1[3]
        
    
    s_str = """select host, port, username, password from db_cfg_sqlserver where id=%s; """ %(sta_id)
    res2 = mysql.GetSingleRow(mysql_conn, s_str)
    if res2:
        s_host = res2[0]
        s_port = res2[1]
        s_username = res2[2]
        s_password = res2[3]
    #print s_host,s_port,s_username,s_password

	
    p_str = """select concat(host, ':', port) from db_cfg_sqlserver where id=%s; """ %(pri_id)
    p_nopass_str = mysql.GetSingleValue(mysql_conn, p_str)
    s_str = """select concat(host, ':', port) from db_cfg_sqlserver where id=%s; """ %(sta_id)
    s_nopass_str = mysql.GetSingleValue(mysql_conn, s_str)
	
    logger.info("The primary database is: " + p_nopass_str + ", the id is: " + str(pri_id))
    logger.info("The standby database is: " + s_nopass_str + ", the id is: " + str(sta_id))




    db_type = "sqlserver"
    try:
        common.db_op_lock(mysql_conn, db_type, group_id, 'SWITCHOVER')			# 加锁
        common.init_db_op_instance(mysql_conn, db_type, group_id, 'SWITCHOVER')					#初始化切换实例
	
        # connect to sqlserver
        p_conn = sqlserver.ConnectMssql(p_host,p_port,p_username,p_password)
        s_conn = sqlserver.ConnectMssql(s_host,s_port,s_username,s_password)
        if p_conn is None:
            common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '连接主库失败，请根据相应日志查看原因', 10, 3)
            logger.error("Connect to primary database error, exit!!!")
            
            common.update_db_op_reason(mysql_conn, db_type, group_id, 'SWITCHOVER', '连接主库失败')
            common.update_db_op_result(mysql_conn, db_type, group_id, 'SWITCHOVER', '-1')
            sys.exit(2)
        if s_conn is None:
            common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '连接备库失败，请根据相应日志查看原因', 10, 3)
            logger.error("Connect to standby database error, exit!!!")
            
            common.update_db_op_reason(mysql_conn, db_type, group_id, 'SWITCHOVER', '连接备库失败')
            common.update_db_op_result(mysql_conn, db_type, group_id, 'SWITCHOVER', '-1')
            sys.exit(2)
        

    
   
        # 正式开始切换  
        try:
            common.log_db_op_process(mysql_conn, db_type, group_id, 'SWITCHOVER', '准备执行主备切换', 10, 2)
        
            res_2s=switch_mirror(mysql_conn, db_type, group_id, db_name, p_conn, s_conn, pri_id)
            if res_2s ==0:
                update_switch_flag(mysql_conn, group_id)
                common.gen_alert_sqlserver(sta_id, 1, db_name)     # generate alert
                common.update_db_op_result(mysql_conn, db_type, group_id, 'SWITCHOVER', '0')
            else:
                common.update_db_op_result(mysql_conn, db_type, group_id, 'SWITCHOVER', res_2s)
        except Exception,e:
            logger.error(traceback.format_exc())
            pass

    except Exception,e:
        logger.error(traceback.format_exc())
        pass
    finally:
        common.db_op_unlock(mysql_conn, db_type, group_id, 'SWITCHOVER')
       