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
import datetime
from subprocess import Popen, PIPE
import sys, getopt
import traceback
import paramiko

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
    common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '获取数据库角色成功', 15, 2)
    logger.info("The current database role is: " + role)
	
    # get switchover status
    str='select switchover_status from v$database'
    switch_status=oracle.GetSingleValue(p_conn, str)
    logger.info("The current database switchover status is: " + switch_status)
	
    # get database version
    str="""select substr(version, 0, instr(version, '.')-1) from v$instance"""
    version=oracle.GetSingleValue(p_conn, str)
	
    # get standby redo log
    str='select count(1) from v$standby_log'
    log_count=oracle.GetSingleValue(p_conn, str)
    logger.info("The current database has %s standby log" %(log_count)) 
    
    recover_str = ""
    if log_count > 0:
        recover_str = "alter database recover managed standby database using current logfile disconnect from session;"
    else:
        recover_str = "alter database recover managed standby database disconnect from session;"
    	
    
    if role=="PRIMARY":
        common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '验证数据库角色成功', 20, 2)
        logger.info("Now we are going to switch database %s to physical standby." %(pri_id))
        if switch_status=="TO STANDBY" or switch_status=="SESSIONS ACTIVE" or switch_status=="FAILED DESTINATION":
            logger.info("Switchover to physical standby... ")
            common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '正在将主库切换成备库，可能会花费几分钟时间，请耐心等待...', 25, 0)
            sqlplus = Popen(["sqlplus", "-S", p_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
            sqlplus.stdin.write(bytes("alter database commit to switchover to physical standby with session shutdown;"+os.linesep))
            sqlplus.stdin.write(bytes("shutdown immediate"+os.linesep))
            sqlplus.stdin.write(bytes("startup mount"+os.linesep))
            sqlplus.stdin.write(bytes(recover_str + os.linesep))
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
                sqlplus.stdin.write(bytes(recover_str + os.linesep))
                out, err = sqlplus.communicate()
                logger.info(out)
                #logger.error(err)

                str='select open_mode from v$database'
                open_mode=oracle.GetSingleValue(p_conn, str)
                if open_mode=="READ ONLY" or open_mode=="READ ONLY WITH APPLY" :
                    common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '备库已经成功启动到open readonly状态', 45, 2)
                    logger.info("Alter standby database to open successfully.")
                else:
                    common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '备库已经成功启动到open readonly状态', 45, 2)
                    logger.error("Start MRP process failed!")

            str='select database_role from v$database'
            role=oracle.GetSingleValue(p_conn, str)
            if role=="PHYSICAL STANDBY":
                common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '主库已经成功切换成备库', 50, 2)
                logger.info("Switchover to physical standby successfully.")
                result=0
            else:
                common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '主库切换备库失败', 50, 2)
                logger.info("Switchover to physical standby failed.")
                result=-1
            
    else:
        common.update_op_reason(mysql_conn, group_id, 'SWITCHOVER', '验证数据库角色失败，当前数据库不是主库，不能切换到备库')
        common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '验证数据库角色失败，当前数据库不是主库，不能切换到备库', 90, 2)
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
    common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '获取数据库角色成功', 55, 2)
    logger.info("The current database role is: " + role)
	
    # get switchover status
    str='select switchover_status from v$database'
    switch_status=oracle.GetSingleValue(s_conn, str)
    logger.info("The current database switchover status is: " + switch_status)
	

    if role=="PHYSICAL STANDBY":
        common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '验证数据库角色成功', 70, 2)
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
                common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '重启数据库MRP进程成功', 72, 0)
                logger.info("Restart the MRP process successfully.")
            else:
                common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '重启数据库MRP进程失败', 72, 0)
                logger.info("Restart the MRP process failed.")


						# 再次验证切换状态
            timeout=0
            str='select switchover_status from v$database'
            switch_status=oracle.GetSingleValue(s_conn, str)
            while switch_status=="NOT ALLOWED" or switch_status=="SWITCHOVER PENDING":
                if timeout > 30:
                    break
                	
                show_str="数据库状态为 %s，无法进行切换" %(switch_status)
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
            common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '备库已经成功切换成主库', 90, 2)
            logger.info("Switchover standby database to primary successfully.")
            result = 0
        else:
            common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '备库切换主库失败', 90, 2)
            logger.info("Switchover standby database to primary failed.")
            result = -1
        	
    else:
        common.update_op_reason(mysql_conn, group_id, 'SWITCHOVER', '验证数据库角色失败，当前数据库不是PHYSICAL STANDBY，无法切换到Primary')
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
	


################################################################################################################################
# function disable_vip
# 函数功能：启动或停止vip，scan
################################################################################################################################
def disable_vip(mysql_conn, group_id, server_id, op_type):
    host_ip=""
    host_type=""
    host_user=""
    host_pwd=""
    host_protocol=""
    shift_vip=""
    node_vips=""
    network_card=""
    query_str = """select host, host_type, host_user, host_pwd, host_protocol, d.shift_vip, d.node_vips, d.network_card
										from db_cfg_oracle t, db_cfg_oracle_dg d
										where d.is_delete = 0
										and t.is_delete = 0
										and d.id = %s
										and t.id = %s """ %(group_id, server_id)
    res = mysql.GetMultiValue(mysql_conn, query_str)
    for row in res:
        host_ip = row[0]
        host_type = row[1]
        host_user = row[2]
        host_pwd = row[3]
        host_protocol = row[4]
        shift_vip = row[5]
        node_vips = row[6]
        network_card = row[7]
        
    logger.info("The database host type is %s" %(host_type))
    
		# check host username
    if host_user is None or host_user == "":
        logger.info("The host user name is None, connect failed.")
        return


		# check shift vip
    if shift_vip is None or shift_vip == 0:
        logger.info("This DG Group have no request for shift vip.")
        return
        
       
    paramiko.util.log_to_file("paramiko.log") 
    if host_type==0:                                    			#host type: 0:Linux; 1:AIX; 2:HP-UX; 3:Solaris
        if host_protocol ==0:			#protocol is ssh2
            try:
                ssh = paramiko.SSHClient()  
                ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())  
      
                ssh.connect(hostname=host_ip, port=22, username=host_user, password=host_pwd) 
                stdin, stdout, stderr = ssh.exec_command("su - grid -c 'which srvctl' ")   
                stdin.write("Y")  # Generally speaking, the first connection, need a simple interaction.  
                srvctl_cmd = stdout.read().strip("\n")
                print srvctl_cmd
                
                #scan
                if op_type == "start":
                    stdin, stdout, stderr = ssh.exec_command("%s enable scan " %(srvctl_cmd))  
                    out_str=stdout.read()																				# read一定要有，不然可能命令没执行完进程就关了
                    stdin, stdout, stderr = ssh.exec_command("su - grid -c 'srvctl start scan_listener' ")   
                    out_str=stdout.read()
                    stdin, stdout, stderr = ssh.exec_command("su - grid -c 'srvctl start scan' ") 
                    out_str=stdout.read()
                elif op_type == "stop":
                    stdin, stdout, stderr = ssh.exec_command("su - grid -c 'srvctl stop scan_listener' ")   
                    out_str=stdout.read()
                    stdin, stdout, stderr = ssh.exec_command("su - grid -c 'srvctl stop scan' ")  
                    out_str=stdout.read()
                    stdin, stdout, stderr = ssh.exec_command("%s disable scan " %(srvctl_cmd))  
                    out_str=stdout.read()
                
                #vip
                exec_cmd = """su - grid -c "crs_stat | grep vip  | grep NAME | grep -v scan" | awk -F '.' '{print $2}' """
                stdin, stdout, stderr = ssh.exec_command(exec_cmd) 
                host_list = stdout.read().split("\n")
                print host_list
                
                for node in host_list:
                    if node != "":
                        if op_type == "start":
                            print srvctl_cmd
                            print "%s enable vip -i %s " %(srvctl_cmd, node) 
                            stdin, stdout, stderr = ssh.exec_command("%s enable vip -i %s' " %(srvctl_cmd, node))  
                            out_str=stdout.read()
                            stdin, stdout, stderr = ssh.exec_command("su - grid -c 'srvctl start vip -i %s' " %(node))  
                            out_str=stdout.read()
                            stdin, stdout, stderr = ssh.exec_command("su - grid -c 'srvctl start listener -n %s' " %(node))  
                            out_str=stdout.read()
                        elif op_type == "stop":
                            stdin, stdout, stderr = ssh.exec_command("su - grid -c 'srvctl stop listener -n %s' " %(node)) 
                            out_str=stdout.read() 
                            stdin, stdout, stderr = ssh.exec_command("su - grid -c 'srvctl stop vip -i %s' " %(node))  
                            out_str=stdout.read()
                            stdin, stdout, stderr = ssh.exec_command("%s disable vip -i %s' " %(srvctl_cmd, node))  
                            out_str=stdout.read()
                        
            		
            except:
            		pass
            finally:
            		ssh.close() 
        elif host_protocol ==1:   				#protocol is telnet
            pass
    elif host_type==4:		#host type: 4:Windows
        logger.info("The database host type is Windows, Exit!")      
      
  
      
################################################################################################################################
# function bind_ip
# 函数功能：绑定或者解绑IP
################################################################################################################################
def bind_ip(mysql_conn, group_id, server_id, op_type):
    host_ip=""
    host_type=""
    host_user=""
    host_pwd=""
    host_protocol=""
    shift_vip=""
    node_vips=""
    network_card=""
    query_str = """select host, host_type, host_user, host_pwd, host_protocol, d.shift_vip, d.node_vips, d.network_card
										from db_cfg_oracle t, db_cfg_oracle_dg d
										where d.is_delete = 0
										and t.is_delete = 0
										and d.id = %s
										and t.id = %s """ %(group_id, server_id)
    res = mysql.GetMultiValue(mysql_conn, query_str)
    for row in res:
        host_ip = row[0]
        host_type = row[1]
        host_user = row[2]
        host_pwd = row[3]
        host_protocol = row[4]
        shift_vip = row[5]
        node_vips = row[6]
        network_card = row[7]
        
    logger.info("The database host type is %s" %(host_type))
    
		# check host username
    if host_user is None or host_user == "":
        logger.info("The host user name is None, connect failed.")
        return

		# check shift vip
    if shift_vip is None or shift_vip == 0:
        logger.info("This DG Group have no request for shift vip, no need to unbind ip.")
        return
        
    paramiko.util.log_to_file("paramiko.log")  
    if host_type==0:                                    			#host type: 0:Linux; 1:AIX; 2:HP-UX; 3:Solaris
        if host_protocol ==0:			#protocol is ssh2
            try:
                ssh = paramiko.SSHClient()  
                ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())  
      
                ssh.connect(hostname=host_ip, port=22, username=host_user, password=host_pwd) 
                stdin, stdout, stderr = ssh.exec_command("hostname")   
                stdin.write("Y")  # Generally speaking, the first connection, need a simple interaction. 
                print stdout.read()
                
                ip_list = node_vips.split(',')
                i = 100
                print ip_list
                for ip in ip_list:
                    ip_cmd = ""
                    if op_type == "bind":
                        ip_cmd = "ifconfig %s:%s %s netmask 255.255.255.0" %(network_card, i, ip)
                    elif op_type == "unbind":
                        ip_cmd = "ifconfig %s:%s down" %(network_card, i)
                        
                    print ip_cmd
                    stdin, stdout, stderr = ssh.exec_command(ip_cmd + "\n")  
                    out_str=stdout.read()
                    i = i + 1
            		
            except:
            		pass
            finally:
            		ssh.close() 
            		pass
        elif host_protocol ==1:   				#protocol is telnet
            pass
    elif host_type==4:		#host type: 4:Windows
        logger.info("The database host type is Windows, Exit!")   
              

################################################################################################################################
# function shift_vip
# 函数功能：绑定或者解绑IP
################################################################################################################################
def shift_vip(mysql_conn, group_id, is_p_rac, is_s_rac, pri_id, sta_id, dg_pid, dg_sid):
    try:
        #切换
        logger.info("group_id: %s, is_p_rac: %s, is_s_rac: %s, pri_id: %s, sta_id: %s, dg_pid: %s, dg_sid: %s" %(group_id, is_p_rac, is_s_rac, pri_id, sta_id, dg_pid, dg_sid))   
        #logger.info("%s, %s, %s, %s" %(type(pri_id),type(sta_id),type(dg_pid),type(dg_sid)))  
        if is_p_rac == "TRUE" and int(pri_id) == int(dg_pid):
            logger.info("stop vip on %s..." %(pri_id))   
            disable_vip(mysql_conn, group_id, pri_id, "stop")
        if int(dg_sid) == int(sta_id):										#配置表里面的备库正是现在的备库
            logger.info("bind ip on %s..." %(sta_id))   
            bind_ip(mysql_conn, group_id, sta_id, "bind")
        
        #回切
        if int(dg_sid) == int(pri_id):									#配置表里面的备库已然是现在的主库，切换实际上是回切
            logger.info("unbind ip from %s..." %(pri_id))   
            bind_ip(mysql_conn, group_id, pri_id, "unbind")
            
        if is_s_rac == "TRUE" and int(sta_id) == int(dg_pid):
            logger.info("start vip on %s..." %(sta_id))   
            disable_vip(mysql_conn, group_id, sta_id, "start")
        
    except Exception,e:
        print e.message
    finally:
        pass
	
	      
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

		#是否需要漂移IP
    shift_vip_str = """select t.shift_vip from db_cfg_oracle_dg t where id = %s """ %(group_id)
    is_shift = mysql.GetSingleValue(mysql_conn, shift_vip_str)
    
    dg_pid_str = """select t.primary_db_id from db_cfg_oracle_dg t where id = %s """ %(group_id)
    dg_pid = mysql.GetSingleValue(mysql_conn, dg_pid_str)
    
    dg_sid_str = """select t.standby_db_id from db_cfg_oracle_dg t where id = %s """ %(group_id)
    dg_sid = mysql.GetSingleValue(mysql_conn, dg_sid_str)



    try:
        common.operation_lock(mysql_conn, group_id, 'SWITCHOVER')			# 加锁
        
        common.init_op_instance(mysql_conn, group_id, 'SWITCHOVER')					#初始化切换实例
	
        # connect to oracle
        p_conn = oracle.ConnectOracleAsSysdba(p_conn_str)
        s_conn = oracle.ConnectOracleAsSysdba(s_conn_str)
        if p_conn is None:
            common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '连接主库失败，请根据相应日志查看原因', 10, 5)
            logger.error("Connect to primary database error, exit!!!")
            
            common.update_op_reason(mysql_conn, group_id, 'SWITCHOVER', '连接主库失败')
            common.update_op_result(mysql_conn, group_id, 'SWITCHOVER', '-1')
            sys.exit(2)
        if s_conn is None:
            common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '连接备库失败，请根据相应日志查看原因', 10, 5)
            logger.error("Connect to standby database error, exit!!!")
            
            common.update_op_reason(mysql_conn, group_id, 'SWITCHOVER', '连接备库失败')
            common.update_op_result(mysql_conn, group_id, 'SWITCHOVER', '-1')
            sys.exit(2)
        
        #判断主备库是否是RAC
        str="select value from v$option a where a.PARAMETER='Real Application Clusters' "
        is_p_rac=oracle.GetSingleValue(p_conn, str)
        is_s_rac=oracle.GetSingleValue(s_conn, str)
        
        
        str='select count(1) from gv$instance'
        p_count=oracle.GetSingleValue(p_conn, str)
        s_count=oracle.GetSingleValue(s_conn, str)
    
        # try to kill all "(LOCAL=NO)" connections in database
        try:
            if p_count > 1 or s_count > 1:
                common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '正在尝试杀掉"(LOCAL=NO)"的会话，并关闭集群的其他节点可能需要一段时间，请耐心等待...', 5, 0)
            else:
                common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '正在尝试杀掉"(LOCAL=NO)"的会话可能需要一段时间，请耐心等待...', 5, 0)
    	  		
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
            show_msg='关闭实例失败，主库端依然有 %s 个存活实例，备库端依然有 %s 个存活实例，请手工关闭后重新尝试切换' %(p_count, s_count)
            common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', show_msg, 10, 5)
            logger.error("Shutdown instance failed. There are still more than one instance active both in primary and standby.")
            
            common.update_op_reason(mysql_conn, group_id, 'SWITCHOVER', show_msg)
            common.update_op_result(mysql_conn, group_id, 'SWITCHOVER', '-1')
            sys.exit(2)
        elif p_count > 1:
            show_msg='关闭实例失败，主库端依然有 %s 个存活实例，请手工关闭后重新尝试切换' %(p_count)
            common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', show_msg, 10, 5)
            logger.error("Shutdown instance failed. There are still more than one instance active in primary.")
            
            common.update_op_reason(mysql_conn, group_id, 'SWITCHOVER', show_msg)
            common.update_op_result(mysql_conn, group_id, 'SWITCHOVER', '-1')
            sys.exit(2)
        elif s_count > 1:
            show_msg='关闭实例失败，备库端依然有 %s 个存活实例，请手工关闭后重新尝试切换' %(s_count)
            common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', show_msg, 10, 5)
            logger.error("Shutdown instance failed. There are still more than one instance active in standby.")
            
            common.update_op_reason(mysql_conn, group_id, 'SWITCHOVER', show_msg)
            common.update_op_result(mysql_conn, group_id, 'SWITCHOVER', '-1')
            sys.exit(2)
    	
    	
    
               
        # 正式开始切换  
        try:
            common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '准备执行主备切换', 10, 2)
        
            res_2s=switch2standby(mysql_conn, group_id, p_conn, p_conn_str, pri_id)

            res_2p=""
            if res_2s ==0:
                res_2p=standby2primary(mysql_conn, group_id, s_conn, s_conn_str, sta_id)

                if res_2p == 0:
                    #shift vip
                    if is_shift == 1:
                        common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '开始切换IP...', 92, 2)
                        logger.info("开始切换IP...")   
                        shift_vip(mysql_conn, group_id, is_p_rac, is_s_rac, pri_id, sta_id, dg_pid, dg_sid)
                        common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', 'IP切换结束', 95, 2)
                    
                    
                    update_switch_flag(mysql_conn, group_id)
                    common.update_op_result(mysql_conn, group_id, 'SWITCHOVER', '0')
                else:
                    common.update_op_result(mysql_conn, group_id, 'SWITCHOVER', res_2p)
                    common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '切换失败，请通过相关日志查看原因！', 90, 2)
            else:
                common.update_op_result(mysql_conn, group_id, 'SWITCHOVER', res_2s)
                common.log_dg_op_process(mysql_conn, group_id, 'SWITCHOVER', '切换失败，请通过相关日志查看原因！', 50, 2)
        except Exception,e:
            pass
        
    except Exception,e:
        pass
    finally:
        common.operation_unlock(mysql_conn, group_id, 'SWITCHOVER')
        