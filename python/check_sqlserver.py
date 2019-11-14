#!/usr/bin/env python
import os
import sys
import string
import time
import datetime
import traceback
import MySQLdb
import pymssql
import logging
import logging.config
logging.config.fileConfig("etc/logger.ini")
logger = logging.getLogger("check_sqlserver")
path='./include'
sys.path.insert(0,path)
import functions as func
import wl_sqlserver as sqlserver
import alert_sqlserver as alert
import alert_main as mail
from multiprocessing import Process;

     

def check_sqlserver(host,port,username,passwd,server_id,tags):
    try:
        conn = pymssql.connect(host=host,port=int(port),user=username,password=passwd,charset="utf8")
    except Exception, e:
        func.mysql_exec("rollback;",'')
        logger_msg="check sqlserver %s:%s : %s" %(host,port,e)
        logger.warning(logger_msg)
   
        try:
            connect=0
            
            func.mysql_exec("begin;",'')
            
            sql="delete from sqlserver_status where server_id = %s; " %(server_id)
            func.mysql_exec(sql,'')
            
            sql="insert into sqlserver_status(server_id,host,port,tags,connect) values(%s,%s,%s,%s,%s)"
            param=(server_id,host,port,tags,connect)
            func.mysql_exec(sql,param)
            
            logger.info("Generate sqlserver instance alert for server: %s begin:" %(server_id))
            alert.gen_alert_sqlserver_status(server_id)     # generate oracle instance alert
            logger.info("Generate sqlserver instance alert for server: %s end." %(server_id))
            
            func.mysql_exec("commit;",'')

        except Exception, e:
            logger.error(e)
            sys.exit(1)
        finally:
            sys.exit(1)

    finally:
        func.check_db_status(server_id,host,port,tags,'sqlserver')  
        
        
    try:
        func.mysql_exec("begin;",'')
        func.mysql_exec("insert into sqlserver_status_his SELECT *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from sqlserver_status where server_id = %s;" %(server_id),'')
        func.mysql_exec('delete from sqlserver_status where server_id = %s;' %(server_id),'')

        #func.mysql_exec("insert into sqlserver_space_his SELECT *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from sqlserver_space where server_id = %s;" %(server_id),'')
        func.mysql_exec('delete from sqlserver_space where server_id = %s;' %(server_id),'')

        connect = 1
        role = -1
        uptime = sqlserver.get_uptime(conn)
        version = sqlserver.get_version(conn)
        
        lock_timeout = sqlserver.get_variables(conn,'LOCK_TIMEOUT')
        trancount = sqlserver.get_variables(conn,'TRANCOUNT')
        max_connections = sqlserver.get_variables(conn,'MAX_CONNECTIONS')
        processes = sqlserver.ger_processes(conn)
        processes_running = sqlserver.ger_processes_running(conn)
        processes_waits = sqlserver.ger_processes_waits(conn)

        connections = sqlserver.get_variables(conn,'CONNECTIONS')
        pack_received = sqlserver.get_variables(conn,'PACK_RECEIVED')
        pack_sent = sqlserver.get_variables(conn,'PACK_SENT')
        packet_errors = sqlserver.get_variables(conn,'PACKET_ERRORS')

        time.sleep(1)

        connections_2 = sqlserver.get_variables(conn,'CONNECTIONS')
        pack_received_2 = sqlserver.get_variables(conn,'PACK_RECEIVED')
        pack_sent_2 = sqlserver.get_variables(conn,'PACK_SENT')
        packet_errors_2 = sqlserver.get_variables(conn,'PACKET_ERRORS')

        connections_persecond = int(connections_2) - int(connections)
        pack_received_persecond = int(pack_received_2) - int(pack_received)
        pack_sent_persecond = int(pack_sent_2) - int(pack_sent)
        packet_errors_persecond = int(packet_errors_2) - int(packet_errors)

        sql = "insert into sqlserver_status(server_id,tags,host,port,connect,role,uptime,version,lock_timeout,trancount,max_connections,processes,processes_running,processes_waits,connections_persecond,pack_received_persecond,pack_sent_persecond,packet_errors_persecond) values(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s);"
        param = (server_id,tags,host,port,connect,role,uptime,version,lock_timeout,trancount,max_connections,processes,processes_running,processes_waits,connections_persecond,pack_received_persecond,pack_sent_persecond,packet_errors_persecond)
        func.mysql_exec(sql,param)
        func.update_db_status_init(server_id,'sqlserver',role,version,tags)
              
        # generate sqlserver status alert
        alert.gen_alert_sqlserver_status(server_id)   
        
        #check logspace
        logspace = sqlserver.get_logspace(conn)
        if logspace:
           for line in logspace:
              sql="insert into sqlserver_space(server_id,host,port,tags,db_name,total_size,max_rate,status) values(%s,%s,%s,%s,%s,%s,%s,%s)"
              param=(server_id,host,port,tags,line[0],line[1],line[2],line[3])
              func.mysql_exec(sql,param)
              
           #logger.info("Generate logspace alert for server: %s begin:" %(server_id))
           #alert.gen_alert_sqlserver_logspace(server_id)    # generate logspace alert
           #logger.info("Generate logspace alert for server: %s end." %(server_id))
           
        curr_time = sqlserver.get_curr_time(conn)
        snap_id = sqlserver.get_snap_id(conn)
        
        # get total session, active session into table "sqlserver_session" for big view
        sql = "select count(1) from sqlserver_session where server_id='%s' and snap_id=%s " %(server_id,snap_id)
        li_count = func.mysql_single_query(sql)  
        if li_count == 0:
           sql = "insert into sqlserver_session(server_id, snap_id, end_time, total_session, active_session) values(%s,%s,%s,%s,%s);"
           param = (server_id, snap_id, curr_time, processes, processes_running)
           func.mysql_exec(sql,param)  
                               
        ##### get Buffer cache hit ratio
        buf_cache_hit = sqlserver.get_buffer_cache_hit_rate(conn)
        sql = "select count(1) from sqlserver_hit where server_id='%s' and snap_id='%s'; " %(server_id,snap_id)
        li_count = func.mysql_single_query(sql)
        if li_count == 0:
           sql = "insert into sqlserver_hit(server_id, snap_id, end_time, type, rate) values(%s,%s,%s,%s,%s);"
           param = (server_id, snap_id, curr_time, 'buffer cache hit ratio', buf_cache_hit)
           func.mysql_exec(sql,param)                              
                    
        ##### get Buffer cache hit ratio
        incr_logMbyte = 0
        logMegabyte = sqlserver.get_logMegabyte(conn)
        sql = "select count(1) from sqlserver_log where server_id='%s' and snap_id='%s'; " %(server_id,snap_id)
        li_count = func.mysql_single_query(sql)
        if li_count == 0:
           sql = "select cntr_value from sqlserver_log where server_id='%s' and snap_id=(select max(snap_id) from sqlserver_log where server_id = '%s');; " %(server_id,server_id)
           last_logMbyte = func.mysql_single_query(sql)
           if last_logMbyte:
              incr_logMbyte = logMegabyte - last_logMbyte
           
           sql = "insert into sqlserver_log(server_id, snap_id, end_time, cntr_value, incr_value) values(%s,%s,%s,%s,%s);"
           param = (server_id, snap_id, curr_time, logMegabyte, incr_logMbyte)
           func.mysql_exec(sql,param)  
        
                              
        func.mysql_exec("commit;",'')
        
        #send mail
        mail.send_alert_mail(server_id, host)   

    except Exception, e:
        logger.error('traceback.format_exc():\n%s' % traceback.format_exc())
        #print 'traceback.print_exc():'; traceback.print_exc()
        #print 'traceback.format_exc():\n%s' % traceback.format_exc()
        func.mysql_exec("rollback;",'')
        sys.exit(1)

    finally:
        conn.close()


######################################################################################################
# function get_connect
######################################################################################################    
def get_connect(server_id):
    host = ""
    port = ""
    username = ""
    password = ""
    tags = ""
    
    server=func.mysql_query("select host,port,username,password,tags from db_cfg_sqlserver where id=%s;" %(server_id))
    if server:
        for row in server:
            host=row[0]
            port=row[1]
            username=row[2]
            passwd=row[3]
            tags=row[4]

    if host=="":
        logger.warning("get host failed, exit!")
        sys.exit(1)
        

    try:
        conn = pymssql.connect(host=host,port=int(port),user=username,password=passwd,charset="utf8")
        return conn
    except Exception, e:
        func.mysql_exec("rollback;",'')
        logger_msg="check sqlserver %s:%s : %s" %(host,port,e)
        logger.warning(logger_msg)
   
        try:
            connect=0
            
            func.mysql_exec("begin;",'')
            
            sql="delete from sqlserver_status where server_id = %s; " %(server_id)
            func.mysql_exec(sql,'')
            
            # delete for the mirror record
            sql="delete from sqlserver_mirror_p where server_id = %s; " %(server_id)
            func.mysql_exec(sql,'')
            
            sql="delete from sqlserver_mirror_s where server_id = %s; " %(server_id)
            func.mysql_exec(sql,'')
            
            sql="insert into sqlserver_status(server_id,host,port,tags,connect) values(%s,%s,%s,%s,%s)"
            param=(server_id,host,port,tags,connect)
            func.mysql_exec(sql,param)
            
            logger.info("Generate sqlserver instance alert for server: %s begin:" %(server_id))
            alert.gen_alert_sqlserver_status(server_id)     # generate oracle instance alert
            logger.info("Generate sqlserver instance alert for server: %s end." %(server_id))
            
            func.mysql_exec("commit;",'')

        except Exception, e:
            logger.error(e)
            sys.exit(1)
        finally:
            sys.exit(1)

    finally:
        func.check_db_status(server_id,host,port,tags,'sqlserver')  
        
        
######################################################################################################
# function check_mirror
######################################################################################################       
def check_mirror(mirror_id, pri_id, sta_id, db_name,is_switch):
    p_id = ""
    s_id = ""
    p_conn = ""
    s_conn = ""
    if is_switch == 0:
        p_id = pri_id
        s_id = sta_id
    else:
        p_id = sta_id
        s_id = pri_id

    try:
        p_conn = get_connect(p_id)
        s_conn = get_connect(s_id)

        func.mysql_exec("begin;",'')
        func.mysql_exec("insert into sqlserver_mirror_p_his SELECT *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from sqlserver_mirror_p where db_name = '%s' and server_id in (%s, %s);" %(db_name, pri_id, sta_id),'')
        func.mysql_exec("delete from sqlserver_mirror_p where db_name = '%s' and server_id in (%s, %s);" %(db_name, pri_id, sta_id),'')
        
        func.mysql_exec("insert into sqlserver_mirror_s_his SELECT *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from sqlserver_mirror_s where db_name = '%s' and server_id in (%s, %s);" %(db_name, pri_id, sta_id),'')
        func.mysql_exec("delete from sqlserver_mirror_s where db_name = '%s' and server_id in (%s, %s);" %(db_name, pri_id, sta_id),'')
        
        if p_conn:
            # collect primary information
            logger.info("Generate mirror primary info for server: %s begin:" %(p_id))
            mp_info = sqlserver.get_mirror_info(p_conn, db_name)
            if mp_info:
                if mp_info[4] == 1:
                    sql="insert into sqlserver_mirror_p(mirror_id,server_id,db_id,db_name,mirroring_role,mirroring_state,mirroring_state_desc,mirroring_safety_level,mirroring_partner_name,mirroring_partner_instance,mirroring_failover_lsn,mirroring_connection_timeout,mirroring_redo_queue,mirroring_end_of_log_lsn,mirroring_replication_lsn) values(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
                    param=(mirror_id,p_id,mp_info[0],mp_info[1],mp_info[4],mp_info[5],mp_info[6],mp_info[7],mp_info[8],mp_info[9],mp_info[10],mp_info[11],mp_info[12],mp_info[13],mp_info[14])
                    func.mysql_exec(sql,param)
                    logger.info("Generate mirror primary info for server: %s end:" %(p_id))
                else:
                    logger.warn("The primary server: %s configured in mirror group is NOT match the mirroring_role!" %(p_id))
    	

        if s_conn:
            # collect standby information
            logger.info("Generate mirror standby info for server: %s begin:" %(s_id))
            ms_info = sqlserver.get_mirror_info(s_conn, db_name)
            if ms_info:
                if ms_info[4] == 1:
                    logger.warn("The standby server: %s configured in mirror group is NOT match the mirroring_role!" %(s_id))
                else:
                    sql="insert into sqlserver_mirror_s(mirror_id,server_id,db_id,db_name,master_server,master_port,mirroring_role,mirroring_state,mirroring_state_desc,mirroring_safety_level,mirroring_partner_name,mirroring_partner_instance,mirroring_failover_lsn,mirroring_connection_timeout,mirroring_redo_queue,mirroring_end_of_log_lsn,mirroring_replication_lsn) values(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
                    param=(mirror_id,s_id,ms_info[0],ms_info[1],ms_info[2],ms_info[3],ms_info[4],ms_info[5],ms_info[6],ms_info[7],ms_info[8],ms_info[9],ms_info[10],ms_info[11],ms_info[12],ms_info[13],ms_info[14])
                    func.mysql_exec(sql,param)
                    logger.info("Generate mirror standby info for server: %s end:" %(s_id))
             
        func.mysql_exec("commit;",'')       	
    except Exception, e:
        logger.error(e)
        func.mysql_exec("rollback;",'')

    finally:
        None
	
######################################################################################################
# function clean_invalid_db_status
######################################################################################################   
def clean_invalid_db_status():
    try:
        func.mysql_exec("insert into sqlserver_status_his SELECT *,sysdate() from sqlserver_status where server_id not in(select id from db_cfg_sqlserver where is_delete = 0);",'')
        func.mysql_exec('delete from sqlserver_status where server_id not in(select id from db_cfg_sqlserver where is_delete = 0);','')
        
        func.mysql_exec("insert into sqlserver_mirror_p_his SELECT *,sysdate() from sqlserver_mirror_p where server_id not in(select id from db_cfg_sqlserver where is_delete = 0);",'')
        func.mysql_exec('delete from sqlserver_mirror_p where server_id not in(select id from db_cfg_sqlserver where is_delete = 0);','')

        func.mysql_exec("insert into sqlserver_mirror_s_his SELECT *,sysdate() from sqlserver_mirror_s where server_id not in(select id from db_cfg_sqlserver where is_delete = 0);",'')
        func.mysql_exec('delete from sqlserver_mirror_s where server_id not in(select id from db_cfg_sqlserver where is_delete = 0);','')
                                
        func.mysql_exec("delete from db_status where db_type = 'sqlserver' and server_id not in(select id from db_cfg_sqlserver where is_delete = 0);",'')
        
    except Exception, e:
        logger.error(e)
    finally:
        pass
        
        
def main():
    servers = func.mysql_query('select id,host,port,username,password,tags from db_cfg_sqlserver where is_delete=0 and monitor=1;')

    logger.info("check sqlserver controller started.")

    if servers:
         plist = []

         for row in servers:
             server_id=row[0]
             host=row[1]
             port=row[2]
             username=row[3]
             passwd=row[4]
             tags=row[5]
             p = Process(target = check_sqlserver, args = (host,port,username,passwd,server_id,tags))
             plist.append(p)
             p.start()

         for p in plist:
             p.join()

    else:
         logger.warning("check sqlserver: not found any servers")

    logger.info("check sqlserver controller finished.")

    #check for mirror group
    mirror_list=func.mysql_query("select id, mirror_name, primary_db_id, standby_db_id, db_name, is_switch from db_cfg_sqlserver_mirror where is_delete=0 and on_process = 0;")

    logger.info("check sqlserver mirror start.")
    if mirror_list:
        plist_2 = []
        for row in mirror_list:
            mirror_id=row[0]
            mirror_name=row[1]
            pri_id=row[2]
            sta_id=row[3]
            db_name=row[4]
            is_switch=row[5]
            p2 = Process(target = check_mirror, args = (mirror_id,pri_id,sta_id,db_name,is_switch))
            plist_2.append(p2)
            p2.start()
            
        for p2 in plist_2:
            p2.join()

    else:
        logger.warning("check sqlserver mirror: not found any mirror group")

    logger.info("check sqlserver mirror finished.")
    
		# Clean invalid data
    logger.info("Clean invalid sqlserver status start.")   
    clean_invalid_db_status()
    logger.info("Clean invalid sqlserver status finished.")       


if __name__=='__main__':
    main()
