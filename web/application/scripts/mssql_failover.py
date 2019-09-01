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
##  07/13/2019                      Baseline version 1.0.0
##
######################################################################

import os
import string
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
# function failover2primary
###############################################################################
def failover2primary(mysql_conn, db_type, group_id, db_name, s_conn, sta_id):
    logger.info("Failover database to primary in progress...")
    result=-1
    
    # get database role
    str='''select m.mirroring_role
					  from sys.database_mirroring m, sys.databases d
					 where M.mirroring_guid is NOT NULL
					   AND m.database_id = d.database_id
					   AND d.name = '%s'; ''' %(db_name)
    role=sqlserver.GetSingleValue(s_conn, str)
    common.log_db_op_process(mysql_conn, db_type, group_id, 'FAILOVER', '获取数据库角色成功', 30, 2)
    logger.info("The current database role is: %s (1:PRIMARY; 2:STANDBY)" %(role))
	
	

    if role==2:
        common.log_db_op_process(mysql_conn, db_type, group_id, 'FAILOVER', '验证数据库角色成功', 40, 2)
        logger.info("Now we are going to failover standby database %s to primary." %(sta_id))
        
        #设置自动提交，否则alter database执行报错
        s_conn.autocommit(True)
            
        common.log_db_op_process(mysql_conn, db_type, group_id, 'FAILOVER', '镜像库正在切换成主库...', 60, 2)
        str='''ALTER DATABASE %s SET PARTNER FORCE_SERVICE_ALLOW_DATA_LOSS; ''' %(db_name)
        res=sqlserver.ExecuteSQL(s_conn, str)
        s_conn.autocommit(False)
            
            
				# 重新验证切换后数据库角色
        str='''select m.mirroring_role
							  from sys.database_mirroring m, sys.databases d
							 where M.mirroring_guid is NOT NULL
							   AND m.database_id = d.database_id
							   AND d.name = '%s'; ''' %(db_name)
        new_role=sqlserver.GetSingleValue(s_conn, str)
    
        if new_role==1:
            common.log_db_op_process(mysql_conn, db_type, group_id, 'FAILOVER', '镜像库切换成主库成功', 90, 2)
            logger.info("Failover standby database to primary successfully.")
            result = 0
        else:
            common.log_db_op_process(mysql_conn, db_type, group_id, 'FAILOVER', '镜像库切换成主库失败，请根据相关日志查看原因', 90, 2)
            logger.info("Failover standby database to primary failed.")
            result = -1

    else:
        common.update_db_op_reason(mysql_conn, db_type, group_id, 'FAILOVER', '验证数据库角色失败，当前数据库不是镜像库，无法切换到主库')
        common.log_db_op_process(mysql_conn, db_type, group_id, 'FAILOVER', '验证数据库角色失败，当前数据库不是镜像库，无法切换到主库', 40)
        logger.error("You can not failover primary database to primary!")
        
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
        common.log_db_op_process(mysql_conn, db_type, group_id, 'FAILOVER', '镜像组更新状态成功', 100, 2)
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
    
    s_host = ""
    s_port = ""
    s_username = ""
    s_password = ""
    
    
    name_str = """select db_name from db_cfg_sqlserver_mirror where id=%s; """ %(group_id)
    db_name = mysql.GetSingleValue(mysql_conn, name_str)
 
    s_str = """select host, port, username, password from db_cfg_sqlserver where id=%s; """ %(sta_id)
    res2 = mysql.GetSingleRow(mysql_conn, s_str)
    if res2:
        s_host = res2[0]
        s_port = res2[1]
        s_username = res2[2]
        s_password = res2[3]
    #print s_host,s_port,s_username,s_password

    s_str = """select concat(host, ':', port) from db_cfg_sqlserver where id=%s; """ %(sta_id)
    s_nopass_str = mysql.GetSingleValue(mysql_conn, s_str)
	
    logger.info("The standby database is: " + s_nopass_str + ", the id is: " + str(sta_id))
	
    db_type = "sqlserver"
    try:
        common.db_op_lock(mysql_conn, db_type, group_id, 'FAILOVER')			# 加锁
        common.init_db_op_instance(mysql_conn, db_type, group_id, 'FAILOVER')					#初始化切换实例
        
        # connect to sqlserver
        s_conn = sqlserver.ConnectMssql(s_host,s_port,s_username,s_password)
        if s_conn is None:
            common.log_db_op_process(mysql_conn, db_type, group_id, 'FAILOVER', '连接备库失败，请根据相应日志查看原因', 10, 3)
            logger.error("Connect to standby database error, exit!!!")
            
            common.update_db_op_reason(mysql_conn, db_type, group_id, 'FAILOVER', '连接备库失败')
            common.update_db_op_result(mysql_conn, db_type, group_id, 'FAILOVER', '-1')
            sys.exit(2)   
             

        try:
            common.log_db_op_process(mysql_conn, db_type, group_id, 'FAILOVER', '准备执行灾难切换', 20, 2)
            res = failover2primary(mysql_conn, db_type, group_id, db_name, s_conn, sta_id)

            if res ==0:
                update_switch_flag(mysql_conn, group_id)
                common.gen_alert_sqlserver(sta_id, 1, db_name)     # generate alert
                common.update_db_op_result(mysql_conn, db_type, group_id, 'FAILOVER', '0')
            else:
                common.update_db_op_result(mysql_conn, db_type, group_id, 'FAILOVER', res)
                
        except Exception,e:
            logger.error(traceback.format_exc())
            pass
    except Exception,e:
        logger.error(traceback.format_exc())
        pass
    finally:
        common.db_op_unlock(mysql_conn, db_type, group_id, 'FAILOVER')
	