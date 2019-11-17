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
##  01/29/2018                      Baseline version 1.0.0
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
# function failover2primary
###############################################################################
def failover2primary(mysql_conn, group_id, s_conn, s_conn_str, sta_id):
    logger.info("Failover database to primary in progress...")
    result=-1
    
	# get database role
    str='select database_role from v$database'
    role=oracle.GetSingleValue(s_conn, str)
    common.log_dg_op_process(mysql_conn, group_id, 'FAILOVER', '获取数据库角色成功', 20, 2)
    logger.info("The current database role is: " + role)
	
	

    if role=="PHYSICAL STANDBY":
        common.log_dg_op_process(mysql_conn, group_id, 'FAILOVER', '验证数据库角色成功', 40, 2)
        
        logger.info("Now we are going to failover standby database %s to primary." %(sta_id))
        logger.info("Restart the standby database MRP process...")
        
        # 判断是否有已经传输过来的归档没有应用
        str="select count(1) from v$archived_log where dest_id = 1 and archived='YES' and applied='NO' "
        left_arch=oracle.GetSingleValue(s_conn, str)
        if left_arch > 1:
            show_str="还有 %s 个归档等待应用" %(left_arch)
            common.log_dg_op_process(mysql_conn, group_id, 'FAILOVER', show_str, 50, 2)
            
            sqlplus = Popen(["sqlplus", "-S", s_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
            sqlplus.stdin.write(bytes("alter database recover managed standby database cancel;"+os.linesep))
            sqlplus.stdin.write(bytes("alter database recover managed standby database disconnect from session;"+os.linesep))
            out, err = sqlplus.communicate()
            logger.info(out)
            logger.info(err)
		
		
            # check MRP status
            str="select count(1) from gv$session where program like '%(MRP0)' "
            mrp_status=oracle.GetSingleValue(s_conn, str)
            if mrp_status > 0:
                common.log_dg_op_process(mysql_conn, group_id, 'FAILOVER', '重启数据库同步进程成功', 60, 2)
                logger.info("Restart the MRP process successfully.")
                
            else:
                common.log_dg_op_process(mysql_conn, group_id, 'FAILOVER', '重启数据库同步进程失败', 60, 2)
                logger.info("Restart the MRP process failed.")


            timeout=0
            while left_arch > 1:
                if timeout > 60:
                    break
                	
                str="select count(1) from v$archived_log where dest_id = 1 and archived='YES' and applied='NO' "
                left_arch=oracle.GetSingleValue(s_conn, str)
                    
                show_str="还有 %s 个归档等待应用" %(left_arch)
                common.log_dg_op_process(mysql_conn, group_id, 'FAILOVER', show_str, 65, 2)
                timeout=timeout + 2
                
                
            if timeout > 300:
                common.log_dg_op_process(mysql_conn, group_id, 'FAILOVER', '归档应用超时，灾难切换失败！', 90, 2)
                logger.info("Failover standby database to primary failed.")
                return -1							#超时退出
                
                     
            # 归档应用完毕，开始切换
            failover(mysql_conn, group_id, s_conn_str)
        else:	
            failover(mysql_conn, group_id, s_conn_str)


				# 重新验证切换后数据库角色
        s_conn = oracle.ConnectOracleAsSysdba(s_conn_str)
        str='select database_role from v$database'
        db_role=oracle.GetSingleValue(s_conn, str)
        logger.info("Now the database role is: %s" %(db_role))
    
        if db_role=="PRIMARY":
            common.log_dg_op_process(mysql_conn, group_id, 'FAILOVER', '数据库灾难切换成功', 90, 2)
            logger.info("Failover standby database to primary successfully.")
            result = 0
        else:
            common.log_dg_op_process(mysql_conn, group_id, 'FAILOVER', '数据库灾难切换失败，请根据相关日志查看原因', 90, 2)
            logger.info("Failover standby database to primary failed.")
            result = -1

    else:
        common.update_op_reason(mysql_conn, group_id, 'FAILOVER', '验证数据库角色失败，当前数据库不是PHYSICAL STANDBY，无法切换到Primary')
        common.log_dg_op_process(mysql_conn, group_id, 'FAILOVER', '验证数据库角色失败，当前数据库不是PHYSICAL STANDBY，无法切换到Primary', 90)
        logger.error("You can not failover primary database to primary!")
        
    return result

###############################################################################
# function failover2primary
###############################################################################
def failover(mysql_conn, group_id, s_conn_str):
    logger.info("Failover standby database to primary... ")
    common.log_dg_op_process(mysql_conn, group_id, 'FAILOVER', '正在进行灾难切换...', 75, 2)
    sqlplus = Popen(["sqlplus", "-S", s_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
    sqlplus.stdin.write(bytes("alter database recover managed standby database finish;"+os.linesep))
    sqlplus.stdin.write(bytes("alter database activate standby database;"+os.linesep))
    sqlplus.stdin.write(bytes("shutdown immediate"+os.linesep))
    sqlplus.stdin.write(bytes("startup"+os.linesep))
    out, err = sqlplus.communicate()
    logger.info(out)
    
    		
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
# function bind_ip
# 函数功能：绑定或者解绑IP
################################################################################################################################
def bind_ip(mysql_conn, group_id, server_id, dg_pid, op_type):
    result=0
    
    host_ip=""
    host_type=""
    host_user=""
    host_pwd=""
    host_protocol=""
    shift_vip=""
    node_vips=""
    network_card=""
    network_card_p=""
    network_card_s=""
    query_str = """select host, host_type, host_user, host_pwd, host_protocol, d.shift_vip, d.node_vips, d.network_card_p, d.network_card_s
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
        network_card_p = row[7]
        network_card_s = row[8]
        
    logger.info("The database host type is %s" %(host_type))


    if server_id == dg_pid:
        network_card = network_card_p
    else:
        network_card = network_card_s
        

    
		# check host username
    if host_user is None or host_user == "":
        logger.info("The host user name is None, connect failed.")
        return -1

		# check shift vip
    if shift_vip is None or shift_vip == 0:
        logger.info("This DG Group have no request for shift vip, no need to unbind ip.")
        return -1
        
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
                        #get network mask
                        mask_cmd = "ifconfig -a %s | grep 'Mask' | awk -F ':' '{print $NF}'" %(network_card)
                        stdin, stdout, stderr = ssh.exec_command(mask_cmd + "\n")  
                        mask=stdout.read()
                        
                        #get network gateway
                        gateway_cmd = "route -n | grep %s | awk '{print $2}' | grep -v '0.0.0.0'" %(network_card)
                        stdin, stdout, stderr = ssh.exec_command(gateway_cmd + "\n")  
                        gateway=stdout.read()


                        ip_cmd = "ifconfig %s:%s %s netmask %s" %(network_card, i, ip, mask)
                        ck_cmd = "ifconfig | grep %s:%s | wc -l" %(network_card, i)
                        print ip_cmd
                        stdin, stdout, stderr = ssh.exec_command(ip_cmd + "\n")  
                        out_str=stdout.read()

                        #arp
                        arp_cmd = "arping -U -c 1 -I %s -s %s %s" %(network_card, ip, gateway)
                        #arping -U -c 1 -I $nic -s $scanip $net_gateway
                        print arp_cmd
                        stdin, stdout, stderr = ssh.exec_command(arp_cmd + "\n")  
                        out_str=stdout.read()

                    elif op_type == "unbind":
                        ip_cmd = "ifconfig %s:%s down" %(network_card, i)
                        ck_cmd = "ifconfig | grep %s:%s | wc -l" %(network_card, i)
                        
                        print ip_cmd
                        stdin, stdout, stderr = ssh.exec_command(ip_cmd + "\n")  
                        out_str=stdout.read()
                    

                    ck_cmd = "ifconfig | grep %s:%s | wc -l" %(network_card, i)
                    stdin, stdout, stderr = ssh.exec_command(ck_cmd + "\n")  
                    out_str=stdout.read().strip("\n")
                    if out_str.isdigit():
                        if op_type == "bind" and out_str == "0":
                            result = -1
                        if op_type == "unbind" and out_str == "1":
                            result = -1
                    else:
                        result = -1
                        
                    
                    
                    
                    i = i + 1
            		
            except:
            		pass
            finally:
            		ssh.close() 
            		pass
        elif host_protocol ==1:   				#protocol is telnet
            result = -1
            pass
    elif host_type==4:		#host type: 4:Windows
        result = -1
        logger.info("The database host type is Windows, Exit!")   
              
    return result

    
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
	
    dg_pid_str = """select t.primary_db_id from db_cfg_oracle_dg t where id = %s """ %(group_id)
    dg_pid = mysql.GetSingleValue(mysql_conn, dg_pid_str)


    try:
        common.operation_lock(mysql_conn, group_id, 'FAILOVER')
        
        common.init_op_instance(mysql_conn, group_id, 'FAILOVER')					#初始化切换实例
        
        s_conn = oracle.ConnectOracleAsSysdba(s_conn_str)
        if s_conn is None:
            common.log_dg_op_process(mysql_conn, group_id, 'FAILOVER', '连接备库失败，请根据相应日志查看原因', 5, 5)
            logger.error("Connect to standby database error, exit!!!")
            
            common.update_op_reason(mysql_conn, group_id, 'FAILOVER', '连接备库失败')
            common.update_op_result(mysql_conn, group_id, 'FAILOVER', '-1')
            sys.exit(2)
        str='select count(1) from gv$instance'
        s_count=oracle.GetSingleValue(s_conn, str)

        # try to kill all "(LOCAL=NO)" connections in database
        try:
            if s_count > 1:
                common.log_dg_op_process(mysql_conn, group_id, 'FAILOVER', '正在尝试杀掉"(LOCAL=NO)"的会话，并关闭集群的其他节点可能需要一段时间，请耐心等待...', 5, 0)
            else:
                common.log_dg_op_process(mysql_conn, group_id, 'FAILOVER', '正在尝试杀掉"(LOCAL=NO)"的会话可能需要一段时间，请耐心等待...', 5, 0)
    	  		
            common.kill_sessions(mysql_conn, s_conn, sta_id)
        except Exception,e:
            logger.error("kill sessions error!!!")
            logger.error("traceback.format_exc(): \n%s" %(traceback.format_exc()))
            pass
            
        	
        
        # 验证其他实例是否关闭
        str='select count(1) from gv$instance'
        s_count=oracle.GetSingleValue(s_conn, str)
        logger.error("Instance count is : %s" %(s_count))
        if s_count > 1:
            show_msg="关闭实例失败，备库端依然有 %s 个存活实例，请手工关闭后重新尝试切换"
            common.log_dg_op_process(mysql_conn, group_id, 'FAILOVER', show_msg, 10, 2)
            logger.error(show_msg)
            common.update_op_reason(mysql_conn, group_id, 'FAILOVER', show_msg)
            common.update_op_result(mysql_conn, group_id, 'FAILOVER', '-1')
            
            sys.exit(2)
        
           
        try:
            common.log_dg_op_process(mysql_conn, group_id, 'FAILOVER', '准备执行灾难切换', 10, 2)
            res = failover2primary(mysql_conn, group_id, s_conn, s_conn_str, sta_id)
            if res ==0:
                #shift vip
                if is_shift == 1:
                    common.log_dg_op_process(mysql_conn, group_id, 'FAILOVER', '开始绑定VIP...', 92, 2)
                    logger.info("开始绑定VIP...")   
                    res = bind_ip(mysql_conn, group_id, sta_id, dg_pid, "bind")
                    if res == -1:
                        common.log_dg_op_process(mysql_conn, group_id, 'FAILOVER', '绑定VIP失败', 95, 2)
                    else:
                        common.log_dg_op_process(mysql_conn, group_id, 'FAILOVER', '绑定VIP成功', 95, 2)
                    common.log_dg_op_process(mysql_conn, group_id, 'FAILOVER', 'VIP绑定结束', 95, 2)

                update_switch_flag(mysql_conn, group_id)
                common.gen_alert_oracle(sta_id, 1)     # generate alert
                common.update_op_result(mysql_conn, group_id, 'FAILOVER', '0')
        except Exception,e:
            pass
    except Exception,e:
        pass
    finally:
        common.operation_unlock(mysql_conn, group_id, 'FAILOVER')
	