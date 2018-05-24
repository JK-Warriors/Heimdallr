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
import time
import sys, getopt
import paramiko

import mysql_handle as mysql
import oracle_handle as oracle

import logging
import logging.config

logging.config.fileConfig('./logging.conf')
logger = logging.getLogger('WLBlazers')

###############################################################################
# function log_dg_op_process
###############################################################################
def log_dg_op_process(mysql_conn, dg_id, process_type, process_desc, rate, block_time=0):
    #logger.info("Log the operate process in oracle_dg_process for dataguard group: %s" %(dg_id))
    
    # get current switch flag
    str="insert into oracle_dg_process(group_id, process_type, process_desc, rate) values (%s, '%s', '%s', %s) " %(dg_id, process_type, process_desc, rate)
    log_status=mysql.ExecuteSQL(mysql_conn, str)
    
    if log_status == 1:
        logger.info("Log the operate process for dataguard group: %s; completed %s." %(dg_id, rate))
    else:
        logger.error("Log the operate process for dataguard group: %s failed." %(dg_id))

    time.sleep(block_time)
    
###############################################################################
# function operation_lock
###############################################################################
def operation_lock(mysql_conn, dg_id, process_type):
    logger.info("Lock the %s process status in db_cfg_oracle_dg for dataguard group: %s" %(process_type, dg_id))
    
    # update process status to 1
    col_name=""
    if process_type == "SWITCHOVER":
        col_name="on_switchover"
    elif process_type == "FAILOVER":
        col_name="on_failover"
    elif process_type == "MRP_START":
        col_name="on_startmrp"
    elif process_type == "MRP_STOP":
        col_name="on_stopmrp"
    elif process_type == "SNAPSHOT_START":
        col_name="on_startsnapshot"
    elif process_type == "SNAPSHOT_STOP":
        col_name="on_stopsnapshot"
    else:
        col_name=""
    	
    str='update db_cfg_oracle_dg set on_process = 1, %s = 1 where id= %s ' %(col_name, dg_id)
    op_status=mysql.ExecuteSQL(mysql_conn, str)
    logger.info(str)
    
    if op_status == 1:
        logger.info("Lock the process status for dataguard group: %s successfully." %(dg_id))
    else:
        logger.error("Lock the process status for dataguard group: %s failed." %(dg_id))
        

###############################################################################
# function operation_unlock
###############################################################################
def operation_unlock(mysql_conn, dg_id, process_type):
    logger.info("Unlock the %s process status in db_cfg_oracle_dg for dataguard group: %s" %(process_type, dg_id))
    
    # update process status to 1
    col_name=""
    if process_type == "SWITCHOVER":
        col_name="on_switchover"
    elif process_type == "FAILOVER":
        col_name="on_failover"
    elif process_type == "MRP_START":
        col_name="on_startmrp"
    elif process_type == "MRP_STOP":
        col_name="on_stopmrp"
    elif process_type == "SNAPSHOT_START":
        col_name="on_startsnapshot"
    elif process_type == "SNAPSHOT_STOP":
        col_name="on_stopsnapshot"
    else:
        col_name=""
    	
    str='update db_cfg_oracle_dg set on_process = 0, %s = 0 where id= %s ' %(col_name, dg_id)
    op_status=mysql.ExecuteSQL(mysql_conn, str)
    logger.info(str)
    
    # 清理操作日志 
    str='delete from oracle_dg_process '
    op_status=mysql.ExecuteSQL(mysql_conn, str)
    
    if op_status == 1:
        logger.info("Unlock process status for dataguard group: %s successfully." %(dg_id))
    else:
        logger.error("Unlock process status for dataguard group: %s failed." %(dg_id))
        


###############################################################################
# function init_op_instance
###############################################################################
def init_op_instance(mysql_conn, group_id, op_type):
    logger.info("Initialize switch instance for group %s." %(group_id))
    
    str="""insert into oracle_dg_opration(group_id, op_type) values('%s', '%s') """%(group_id, op_type)
    is_succ = mysql.ExecuteSQL(mysql_conn, str)



###############################################################################
# function update_op_result
###############################################################################
def update_op_result(mysql_conn, group_id, op_type, result):
    logger.info("update switch result for group %s." %(group_id))
	
    # get max inst id
    str="""select max(id) from oracle_dg_opration where group_id= %s and op_type = '%s' """ %(group_id, op_type)
    max_id=mysql.GetSingleValue(mysql_conn, str)
    
    str="""update oracle_dg_opration set result = '%s' where id = %s and op_type = '%s' """%(result, max_id, op_type)
    is_succ = mysql.ExecuteSQL(mysql_conn, str)
    


###############################################################################
# function update_op_reason
###############################################################################
def update_op_reason(mysql_conn, group_id, op_type, reason):
    logger.info("update switch fail reason for group %s." %(group_id))
	
    # get max inst id
    str="""select max(id) from oracle_dg_opration where group_id= %s and op_type = '%s' """ %(group_id, op_type)
    max_id=mysql.GetSingleValue(mysql_conn, str)
    
    str="""update oracle_dg_opration set reason = '%s'  where id = %s and op_type = '%s' """%(reason, max_id, op_type)
    is_succ = mysql.ExecuteSQL(mysql_conn, str)
    
    

################################################################################################################################
# function kill_sessions
# 该函数主要实现2个功能：
# 1）杀掉本机的 (LOCAL=NO)的连接
# 2）如果是RAC并且另外的instance是active状态，则先ssh跳转到其他节点，杀掉其他节点上的 (LOCAL=NO)的连接，并关闭所有的其他instance
################################################################################################################################
def kill_sessions(mysql_conn, ora_conn, server_id):
    host_ip=""
    host_type=""
    host_user=""
    host_pwd=""
    host_protocol=""
    query_str = """select host, host_type, host_user, host_pwd, host_protocol from db_cfg_oracle t where t.id = %s """ %(server_id)
    res = mysql.GetMultiValue(mysql_conn, query_str)
    for row in res:
        host_ip = row[0]
        host_type = row[1]
        host_user = row[2]
        host_pwd = row[3]
        host_protocol = row[4]
        
    logger.info("The database host type is %s" %(host_type))
    
		# check host username
    if host_user is None:
        logger.info("The host user name is None, connect failed.")
        return


    # structure the srvctl command to shutdown the other instance when there are more then 1 active instance
    srvctl_cmd=""
    host_list=""
    inst_list=""
    spid_list=""        
    # get the process spid which are the python connections 
    str="select p.spid from v$session s, v$process p where s.paddr = p.addr and s.program like 'python%' and type!='BACKGROUND' "
    spid_list=oracle.GetMultiValue(ora_conn, str)
    logger.info("spid list: %s" %(spid_list))
    
    # check if more than one instance
    str='select count(1) from gv$instance'
    inst_count=oracle.GetSingleValue(ora_conn, str)
    if inst_count > 1:
        # get database name
        str='select name from v$database'
        db_name=oracle.GetSingleValue(ora_conn, str)
        
        # get current instance name
        str='select instance_name from v$instance'
        curr_name=oracle.GetSingleValue(ora_conn, str)
        
        # get other instance name list
        str="""select instance_name from gv$instance where instance_name != '%s' """ %(curr_name)
        res=oracle.GetMultiValue(ora_conn, str)
        for row in res:
            inst_list = row[0] + ","
        
        # get other host name list
        str="""select host_name from gv$instance where instance_name != '%s' """ %(curr_name)
        host_list=oracle.GetMultiValue(ora_conn, str)
        
        srvctl_cmd="srvctl stop instance -d %s -i %s" %(db_name, inst_list)
        logger.info("srvctl command: %s" %(srvctl_cmd))
    else:
        logger.info("There is only one active instance.")
        
     
    
    paramiko.util.log_to_file("paramiko.log")  
    if host_type==0 or host_type==1 or host_type==2 or host_type==3:			#host type: 0:Linux; 1:AIX; 2:HP-UX; 3:Solaris
        if host_protocol ==0:			#protocol is ssh2
            try:
                ssh = paramiko.SSHClient()  
                ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())  
      
                ssh.connect(hostname=host_ip, port=22, username=host_user, password=host_pwd) 
                stdin, stdout, stderr = ssh.exec_command("whoami")   
                stdin.write("Y")  # Generally speaking, the first connection, need a simple interaction.  
                print stdout.read()
            
						    # kill all "(LOCAL=NO)" processes
                execmd = "ps -ef | grep 'LOCAL=NO' | grep -v grep | awk '{print $2}' "  
                stdin, stdout, stderr = ssh.exec_command(execmd + "\n")   
                pid_str=stdout.read() 
                pid_str=pid_str.replace("\n", " ")
                #print stdout.read()  
                for spid in spid_list:
            		    pid_str = pid_str.replace(spid[0], " ")
            		
                execmd= "kill -9 %s" %(pid_str)
                stdin, stdout, stderr = ssh.exec_command(execmd + "\n") 
                logger.info("kill os id list: %s" %(pid_str))  
            		
            		
            		
            		# kill processes on other nodes when there have more than one instance active
                chan=""
                if inst_count > 1:
            		    logger.info("There are more than one active instance, should shutdown the others first.")
            		    #kill "(LOCAL=NO)" processes in other node
            		    chan = ssh.get_transport().open_session()  
            		    chan.settimeout(20)
            		    chan.get_pty()
            		    chan.invoke_shell()
            		
            		    for server in host_list:
            		        ssh_cmd = "ssh %s \n" %(server[0])
            		        logger.info("ssh_cmd: %s" %(ssh_cmd))
            		        chan.send(ssh_cmd)
            		        chan.send(execmd + "\n")
            		        result=""
            		        while True:																# 这个循环很重要，保证接受到所有命令执行的返回结果。
            		            time.sleep(0.5)
            		            res = chan.recv(1024)
            		            result += res
            		            if result:
            		                sys.stdout.write(result.strip('\n'))
            		            if res.endswith('# ') or res.endswith('$ '):
            		                break
            
            		    #shutdown oracle instance in other node
            		    stdin, stdout, stderr = ssh.exec_command(". ~/.bash_profile; %s" %(srvctl_cmd)) 
            		    print stdout.read()   
            		    print stderr.read()   
            		    
            		    chan.close() 
            except:
            		pass
            finally:
            		ssh.close() 
        elif host_protocol ==1:   				#protocol is telnet
            pass
    elif host_type==4:		#host type: 4:Windows
        logger.info("The database host type is Windows, Exit!")
     