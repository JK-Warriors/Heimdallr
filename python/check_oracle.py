#!/usr/bin/python
# -*- coding: utf-8 -*-
import os
import sys
import string
import time
import datetime
from subprocess import Popen, PIPE
import MySQLdb
import cx_Oracle
import logging
import logging.config
logging.config.fileConfig("etc/logger.ini")
logger = logging.getLogger("check_oracle")
path='./include'
sys.path.insert(0,path)
import functions as func
import wl_oracle as oracle
import alert_oracle as alert
import alert_main as mail
from multiprocessing import Process;


######################################################################################################
# function check_oracle
######################################################################################################    
def check_oracle(host,port,dsn,username,password,server_id,tags):
    url=host+':'+port+'/'+dsn

    try:
        conn=cx_Oracle.connect(username,password,url, mode=cx_Oracle.SYSDBA) #获取connection对象
    except Exception, e:
        logger_msg="check oracle %s : %s" %(url,str(e).strip('\n'))
        logger.warning(logger_msg)

        try:
            connect=0
            
            func.mysql_exec("begin;",'')
            
            sql="delete from oracle_status where server_id = %s; " %(server_id)
            func.mysql_exec(sql,'')
            
            sql="insert into oracle_status(server_id,host,port,tags,connect) values(%s,%s,%s,%s,%s)"
            param=(server_id,host,port,tags,connect)
            func.mysql_exec(sql,param)
            
            logger.info("Generate oracle instance alert for server: %s begin:" %(server_id))
            alert.gen_alert_oracle_status(server_id)     # generate oracle instance alert
            logger.info("Generate oracle instance alert for server: %s end." %(server_id))
            
            func.mysql_exec("commit;",'')
        except Exception, e:
            func.mysql_exec("rollback;",'')
            logger.error(str(e).strip('\n'))
            sys.exit(1)
        finally:
            sys.exit(1)

    finally:
        func.check_db_status(server_id,host,port,tags,'oracle')   




    try:
        ##func.mysql_exec('delete from oracle_redo where server_id = %s;' %(server_id),'')
        
        #get info by v$instance
        connect = 1
        instance_name = oracle.get_instance(conn,'instance_name')
        instance_role = oracle.get_instance(conn,'instance_role')
        database_role = oracle.get_database(conn,'database_role')
        
        db_name = oracle.get_database(conn,'name')
        open_mode = oracle.get_database(conn,'open_mode')
        protection_mode = oracle.get_database(conn,'protection_mode')
        if database_role == 'PRIMARY':  
            database_role_new = 'm'  
            dg_stats = '-1'
            dg_delay = '-1'
        else:  
            database_role_new = 's'
            #dg_stats = oracle.get_stats(conn)
            #dg_delay = oracle.get_delay(conn)
            dg_stats = '1'
            dg_delay = '1'
        instance_status = oracle.get_instance(conn,'status')
        startup_time = oracle.get_instance(conn,'startup_time')
        #print startup_time
        #startup_time = time.strftime('%Y-%m-%d %H:%M:%S',startup_time) 
        #localtime = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime())
        #uptime =  (localtime - startup_time).seconds        
        #print uptime
        uptime = oracle.get_instance(conn,'startup_time')
        version = oracle.get_instance(conn,'version')
        instance_status = oracle.get_instance(conn,'status')
        instance_number = oracle.get_instance(conn,'instance_number')
        database_status = oracle.get_instance(conn,'database_status')
        host_name = oracle.get_instance(conn,'host_name')
        archiver = oracle.get_instance(conn,'archiver')
        
        #get info by sql count
        session_total = oracle.get_sessions(conn)
        session_actives = oracle.get_actives(conn)
        session_waits = oracle.get_waits(conn)
        
        #get snap_id, end_interval_time
        snap_id = oracle.get_current_snap_id(conn, instance_number)
        end_interval_time = oracle.get_end_interval_time(conn, instance_number)
        
        #get info by v$parameters
        parameters = oracle.get_parameters(conn)
        processes = parameters['processes']
        
        ##get info by v$parameters
        sysstat_0 = oracle.get_sysstat(conn)
        time.sleep(1)
        sysstat_1 = oracle.get_sysstat(conn)
        session_logical_reads_persecond = sysstat_1['session logical reads']-sysstat_0['session logical reads']
        physical_reads_persecond = sysstat_1['physical reads']-sysstat_0['physical reads']
        physical_writes_persecond = sysstat_1['physical writes']-sysstat_0['physical writes']
        physical_read_io_requests_persecond = sysstat_1['physical write total IO requests']-sysstat_0['physical write total IO requests']
        physical_write_io_requests_persecond = sysstat_1['physical read IO requests']-sysstat_0['physical read IO requests']
        db_block_changes_persecond = sysstat_1['db block changes']-sysstat_0['db block changes']
        
        os_cpu_wait_time = -1
        if version >= "11":
            os_cpu_wait_time = sysstat_0['OS CPU Qt wait time']
        
        logons_persecond = sysstat_1['logons cumulative']-sysstat_0['logons cumulative']
        logons_current = sysstat_0['logons current']
        opened_cursors_persecond = sysstat_1['opened cursors cumulative']-sysstat_0['opened cursors cumulative']
        opened_cursors_current = sysstat_0['opened cursors current']
        user_commits_persecond = sysstat_1['user commits']-sysstat_0['user commits']
        user_rollbacks_persecond = sysstat_1['user rollbacks']-sysstat_0['user rollbacks']
        user_calls_persecond = sysstat_1['user calls']-sysstat_0['user calls']
        db_block_gets_persecond = sysstat_1['db block gets']-sysstat_0['db block gets']
        
        # get flashback information
        flashback_on = oracle.get_database(conn,'flashback_on')
        #earliest_fbscn = oracle.get_earliest_fbscn(conn)
        flashback_retention = parameters['db_flashback_retention_target']
        flashback_earliest_time = oracle.get_earliest_fbtime(conn,flashback_retention)
        #print "flashback_earliest_time: %s" %(flashback_earliest_time)
        flashback_space_used = oracle.get_flashback_space_used(conn)


        ##################### insert data to mysql server#############################
        func.mysql_exec("begin;",'')
        func.mysql_exec("insert into oracle_status_his SELECT *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from oracle_status where server_id = %s;" %(server_id),'')
        func.mysql_exec('delete from oracle_status where server_id = %s;' %(server_id),'')
        sql = "insert into oracle_status(server_id,host,port,tags,connect,db_name, instance_name,instance_role,instance_status,database_role,open_mode,protection_mode,host_name,database_status,startup_time,uptime,version,archiver,session_total,session_actives,session_waits,dg_stats,dg_delay,processes,session_logical_reads_persecond,physical_reads_persecond,physical_writes_persecond,physical_read_io_requests_persecond,physical_write_io_requests_persecond,db_block_changes_persecond,os_cpu_wait_time,logons_persecond,logons_current,opened_cursors_persecond,opened_cursors_current,user_commits_persecond,user_rollbacks_persecond,user_calls_persecond,db_block_gets_persecond,flashback_on,flashback_earliest_time,flashback_space_used) values(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s);"
        param = (server_id,host,port,tags,connect,db_name,instance_name,instance_role,instance_status,database_role,open_mode,protection_mode,host_name,database_status,startup_time,uptime,version,archiver,session_total,session_actives,session_waits,dg_stats,dg_delay,processes,session_logical_reads_persecond,physical_reads_persecond,physical_writes_persecond,physical_read_io_requests_persecond,physical_write_io_requests_persecond,db_block_changes_persecond,os_cpu_wait_time,logons_persecond,logons_current,opened_cursors_persecond,opened_cursors_current,user_commits_persecond,user_rollbacks_persecond,user_calls_persecond,db_block_gets_persecond,flashback_on,flashback_earliest_time,flashback_space_used)
        func.mysql_exec(sql,param) 
        func.update_db_status_init(server_id,'oracle',database_role_new,version,tags)
        func.mysql_exec("commit;",'')
        
        logger.info("Generate oracle instance alert for server: %s begin:" %(server_id))
        alert.gen_alert_oracle_status(server_id)     # generate oracle instance alert
        logger.info("Generate oracle instance alert for server: %s end." %(server_id))

        #check tablespace
        func.mysql_exec("begin;",'')
        func.mysql_exec("insert into oracle_tablespace_his SELECT *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from oracle_tablespace where server_id = %s;" %(server_id),'')
        func.mysql_exec('delete from oracle_tablespace where server_id = %s;' %(server_id),'')
        tablespace = oracle.get_tablespace(conn)
        if tablespace:
           for line in tablespace:
              sql="insert into oracle_tablespace(server_id,host,port,tags,tablespace_name,status,management,total_size,used_size,max_rate) values(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
              param=(server_id,host,port,tags,line[0],line[1],line[2],line[3],line[4],line[5])
              func.mysql_exec(sql,param)
              
           logger.info("Generate tablespace alert for server: %s begin:" %(server_id))
           alert.gen_alert_oracle_tablespace(server_id)    # generate tablespace alert
           logger.info("Generate tablespace alert for server: %s end." %(server_id))
           func.mysql_exec("commit;",'')
        else:
           func.mysql_exec("rollback;",'')
              
              
        #check diskgroup 
        func.mysql_exec("begin;",'')
        func.mysql_exec("insert into oracle_diskgroup_his SELECT *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from oracle_diskgroup where server_id = %s;" %(server_id),'')
        func.mysql_exec('delete from oracle_diskgroup where server_id = %s;' %(server_id),'')
        diskgroup = oracle.get_diskgroup(conn)
        if diskgroup:
           for line in diskgroup:
              sql="insert into oracle_diskgroup(server_id,host,tags,diskgroup_name,state,type,total_mb,free_mb,used_rate) values(%s,%s,%s,%s,%s,%s,%s,%s,%s)"
              param=(server_id,host,tags,line[0],line[1],line[2],line[3],line[4],line[5])
              func.mysql_exec(sql,param)
              
           logger.info("Generate diskgroup alert for server: %s begin:" %(server_id))
           alert.gen_alert_oracle_diskgroup(server_id)    # generate diskgroup alert
           logger.info("Generate diskgroup alert for server: %s end." %(server_id))
           func.mysql_exec("commit;",'')
        else:
           func.mysql_exec("rollback;",'')
              
        ##### get redo per hour
        ora_redo = oracle.get_redo_per_hour(conn)
        if ora_redo:
           key_time=ora_redo[0]
           redo_p_h=ora_redo[1]
        else:
           key_time = datetime.datetime.now().strftime('%Y-%m-%d %H')+':00'
           redo_p_h=0
            
        ##################### insert data to mysql server#############################
        sql = "select count(1) from oracle_redo where server_id='%s' and key_time='%s'; " %(server_id,key_time)
        li_count = func.mysql_single_query(sql)  
        if li_count == 0:
           sql = "insert into oracle_redo(server_id, key_time, redo_log) values(%s,%s,%s);"
           param = (server_id, key_time, redo_p_h)
           func.mysql_exec(sql,param)  
        else:
           sql = "update oracle_redo set redo_log = %s, create_time = DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') where server_id = '%s' and key_time='%s'; " %(redo_p_h,server_id,key_time)
           func.mysql_exec(sql,'')  

              
              
        ##### get db time
        sql = "select count(1) from oracle_db_time where server_id='%s' and snap_id='%s'; " %(server_id,snap_id)
        li_count = func.mysql_single_query(sql)
        if li_count == 0:
           ora_dbtime = oracle.get_db_time(conn, snap_id, instance_number)
           if ora_dbtime:
              end_time=ora_dbtime[1]
              db_time=ora_dbtime[2]
              elapsed=ora_dbtime[3]
              rate=ora_dbtime[4]
              
              if rate < 0:
                 rate = 0
              ##################### insert data to mysql server#############################
              sql = "insert into oracle_db_time(server_id, snap_id, end_time, db_time, elapsed, rate) values(%s,%s,%s,%s,%s,%s);"
              param = (server_id, snap_id, end_time, db_time, elapsed, rate)
              func.mysql_exec(sql,param)  
              
              
              
        ##### insert total session, active session into table "oracle_session" for big view
        sql = "select count(1) from oracle_session where server_id='%s' and snap_id='%s'; " %(server_id,snap_id)
        li_count = func.mysql_single_query(sql)  
        if li_count == 0:
           sql = "insert into oracle_session(server_id, snap_id, end_time, total_session, active_session) values(%s,%s,%s,%s,%s);"
           param = (server_id, snap_id, end_interval_time, session_total, session_actives)
           func.mysql_exec(sql,param)  
                               
                               
                               
        #check restore point
        restore_point = oracle.get_restorepoint(conn, flashback_retention)
        if restore_point:
           func.mysql_exec("begin;",'')
           func.mysql_exec('delete from oracle_flashback where server_id = %s;'%(server_id),'')
           for line in restore_point:
              sql="insert into oracle_flashback(server_id,host,port,tags,name) values(%s,%s,%s,%s,%s)"
              param=(server_id,host,port,tags,line[0])
              func.mysql_exec(sql,param)
           func.mysql_exec("commit;",'')


        # auto create restore point for standby database  
        if database_role == 'PHYSICAL STANDBY' and flashback_on == 'YES':  
            logger.info("Automatic create restore point for server:" + str(server_id))
            create_restore_point(conn, flashback_retention)
            update_fb_retention(conn, server_id, flashback_retention)


				#send mail
        mail.send_alert_mail(server_id, host)     
        
        
    except Exception, e:
        logger.error(e)
        func.mysql_exec("rollback;",'')
        sys.exit(1)

    finally:
        conn.close()
        

######################################################################################################
# function get_connect
######################################################################################################    
def get_connect(server_id):
    url = ""
    host = ""
    port = ""
    username = ""
    password = ""
    tags = ""
    
    server=func.mysql_query("select host,port,dsn,username,password,tags from db_cfg_oracle where id=%s;" %(server_id))
    if server:
        for row in server:
            host=row[0]
            port=row[1]
            username=row[3]
            password=row[4]
            tags=row[5]
            url=row[0]+':'+row[1]+'/'+row[2]

    if host=="":
        logger.warning("get host failed, exit!")
        sys.exit(1)
        
    try:
        conn=cx_Oracle.connect(username,password,url, mode=cx_Oracle.SYSDBA) #获取connection对象
        return conn
    except Exception, e:
        logger_msg="check oracle %s : %s" %(url,str(e).strip('\n'))
        logger.warning(logger_msg)

        try:
            connect=0
            
            func.mysql_exec("begin;",'')
            
            sql="delete from oracle_status where server_id = %s; " %(server_id)
            func.mysql_exec(sql,'')
            
            sql="insert into oracle_status(server_id,host,port,tags,connect) values(%s,%s,%s,%s,%s)"
            param=(server_id,host,port,tags,connect)
            func.mysql_exec(sql,param)
            
            func.mysql_exec("commit;",'')
        except Exception, e:
            func.mysql_exec("rollback;",'')
            logger.error(str(e).strip('\n'))

    finally:
        func.check_db_status(server_id,host,port,tags,'oracle')   
        
                
######################################################################################################
# function check_dataguard
######################################################################################################       
def check_dataguard(dg_id, pri_id, sta_id, is_switch):
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
    
        #check dataguard status
        dg_p_curr_time = ""
        dg_s_curr_time = ""


        func.mysql_exec("begin;",'')
        func.mysql_exec("insert into oracle_dg_p_status_his SELECT *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from oracle_dg_p_status where server_id in (%s, %s);" %(pri_id, sta_id),'')
        func.mysql_exec('delete from oracle_dg_p_status where server_id in (%s, %s);' %(pri_id, sta_id),'')
        
        func.mysql_exec("insert into oracle_dg_s_status_his SELECT *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from oracle_dg_s_status where server_id in (%s, %s);" %(pri_id, sta_id),'')
        func.mysql_exec('delete from oracle_dg_s_status where server_id in (%s, %s);' %(pri_id, sta_id),'')
        
                             
        if p_conn:
            # collect primary information
            # dg_p_info = oracle.get_dg_p_info(p_conn, 1)
            p_dest=func.mysql_single_query("select case when t.primary_db_id = %s then t.primary_db_dest else t.standby_db_dest end from db_cfg_oracle_dg t where t.id = %s;" %(p_id, dg_id))
            if p_dest is None:
                p_dest = 2
            dg_p_info = oracle.get_dg_p_info_2(p_conn, p_dest)
            
            dest_id = -1
            transmit_mode = "null"
            thread = -1
            sequence = -1
            archived_delay = -1
            applied_delay = -1
            current_scn = -1
            if dg_p_info:
                # get new check_seq
                new_check_seq=func.mysql_single_query("select ifnull(max(check_seq),0)+1 from oracle_dg_p_status where server_id=%s;" %(p_id))
                    
                for line in dg_p_info:
                    dest_id=line[0]
                    transmit_mode=line[1]
                    thread=line[2]
                    sequence=line[3]
                    archived=line[4]
                    applied=line[5]
                    current_scn=line[6]
                    dg_p_curr_time=line[7]
                    
                    archived_delay = oracle.get_log_archived_delay(p_conn, dest_id, thread)
                    applied_delay = oracle.get_log_applied_delay(p_conn, dest_id, thread)
                    #print thread, archived_delay, applied_delay
                    ##################### insert data to mysql server#############################
                    #print dest_id, thread, sequence, archived, applied, current_scn, curr_db_time
                    sql = "insert into oracle_dg_p_status(server_id, check_seq, dest_id, transmit_mode, `thread#`, `sequence#`, curr_scn, curr_db_time, archived_delay, applied_delay) values(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s);"
                    param = (p_id, new_check_seq, dest_id, transmit_mode, thread, sequence, current_scn, dg_p_curr_time, archived_delay, applied_delay)
                    func.mysql_exec(sql,param) 
                    
                logger.info("Gather primary database infomation for server: %s" %(p_id))
            else:
                logger.warning("Get no data from primary server: %s" %(p_id))
        else:  
            ##################### update data to db_status#############################
            func.mysql_exec("update db_status set repl_delay=-1 where server_id = %s;" %(s_id),'')  
        	
        	
        	
        	     
        if s_conn and p_conn:
            dg_s_ms = oracle.get_dg_s_ms(s_conn)
            dg_s_rate = oracle.get_dg_s_rate(s_conn)
            dg_s_mrp = oracle.get_dg_s_mrp(s_conn)
            dg_s_scn = oracle.get_database(s_conn, 'current_scn')
            
            dg_s_al = oracle.get_dg_s_al(p_conn, dg_s_scn)
            
            logger.info("Tye to get timestamp by scn(%s) from primary server %s for server %s" %(dg_s_scn, p_id, s_id))
            dg_s_curr_time = oracle.get_time_by_scn(p_conn, dg_s_scn)
            if dg_s_curr_time == None:
                logger.info("Try to get timestamp by scn(%s) from v$restorepoint of standby server %s" %(dg_s_scn, s_id))
                dg_s_curr_time = oracle.get_time_from_restorepoint(s_conn, dg_s_scn)
            #logger.info("dg_s_curr_time: %s" %(dg_s_curr_time))
                
            
            thread=-1
            sequence=-1
            block=-1
            if dg_s_ms:
                thread=dg_s_ms[0]
                sequence=dg_s_ms[1]
                block=dg_s_ms[2]
            else:
                if dg_s_ms:
                    thread=dg_s_al[0]
                    sequence=dg_s_al[1]
                    block=0
        
            dg_delay=-1
            if dg_s_curr_time ==None or dg_p_curr_time==None or dg_s_curr_time=="" or dg_p_curr_time == "":
                dg_delay=-1
            else:
                p_time=datetime.datetime.strptime(dg_p_curr_time,'%Y-%m-%d %H:%M:%S')
                s_time=datetime.datetime.strptime(dg_s_curr_time,'%Y-%m-%d %H:%M:%S')
                dg_delay_days=(p_time - s_time).days 
                dg_delay_seconds=(p_time - s_time).seconds
                dg_delay=dg_delay_days * 86400 + dg_delay_seconds
                #logger.info("p_time: %s" %(p_time))
                #logger.info("s_time: %s" %(s_time))
                #logger.info("dg_delay_days: %s" %(dg_delay_days))
                #logger.info("dg_delay_seconds: %s" %(dg_delay_seconds))
                #logger.info("dg_delay: %s" %(dg_delay))
                if dg_delay < 0:
                    dg_delay = 0
                
            avg_apply_rate = -1
            if dg_s_mrp==0:
                avg_apply_rate=0
            elif dg_s_rate:
                avg_apply_rate=dg_s_rate[0]
        
        
            ##################### insert data to mysql server#############################
            sql = "insert into oracle_dg_s_status(server_id, `thread#`, `sequence#`, `block#`, delay_mins, avg_apply_rate, curr_scn, curr_db_time, mrp_status) values(%s,%s,%s,%s,%s,%s,%s,%s,%s);"
            param = (s_id, thread, sequence, block, dg_delay, avg_apply_rate, dg_s_scn, dg_s_curr_time, dg_s_mrp)
            func.mysql_exec(sql,param)  
        
            ##################### update data to oracle_status#############################
            sql = "update oracle_status set dg_stats=%s, dg_delay=%s where server_id = %s;"
            param = (dg_s_mrp, dg_delay, s_id)
            func.mysql_exec(sql,param)  
            
            

            
            # generate dataguard alert
            logger.info("Generate dataguard alert for server: %s begin:" %(s_id))
            alert.gen_alert_oracle_dg(s_id)    
            logger.info("Generate dataguard alert for server: %s end." %(s_id))
            
             
            logger.info("Gather standby database infomation for server: %s" %(s_id))
        
        func.mysql_exec("commit;",'')
        
        #send mail
        host = func.mysql_single_query("select host from db_cfg_oracle where id = %s;" %(s_id)) 
        mail.send_alert_mail(s_id, host)    
    except Exception, e:
        logger.error(e)
        func.mysql_exec("rollback;",'')

    finally:
        if p_conn:
            p_conn.close()
        if s_conn:
            s_conn.close()
              
######################################################################################################
# function create_restore_point
######################################################################################################           
def create_restore_point(conn, flashback_retention):
    cur = None
    try:
        last_restore_time = oracle.get_last_fbtime(conn)
        db_time = oracle.get_sysdate(conn)

        time_def = -1
        if last_restore_time <> 'null':
            time_def = (datetime.datetime.strptime(db_time,'%Y%m%d%H%M%S') - datetime.datetime.strptime(last_restore_time,'%Y%m%d%H%M%S')).seconds
        
        # 没有闪回点，或者当前数据库时间和最后的闪回点时间相差1小时以上，创建闪回点
        logger.info('last_restore_time: %s' %(last_restore_time))
        logger.info('db_time: %s' %(db_time))
        logger.info('time_def: %s' %(time_def))
        if last_restore_time == 'null' or time_def > 3600:
            db_unique_name = oracle.get_database(conn,'db_unique_name')

            cur = conn.cursor()

            
            
            try:
                # 关闭MRP进程
                stb_redo_count = oracle.get_standby_redo_count(conn)
                #logger.info("type stb_redo_count : %s" %(type(stb_redo_count)))
                mrp_status = oracle.get_dg_s_mrp(conn)
                #logger.info('mrp_status: %s' %(mrp_status))
                if mrp_status == 1:
                    str = 'alter database recover managed standby database cancel'
                    cur.execute(str)
                    
                #生成闪回点
                restore_name = db_unique_name + db_time
                str = 'create restore point %s' %(restore_name)
                cur.execute(str)
            finally: 
                # 如果一开始MRP进程是开启状态，则创建完成后，再次开启MRP进程
                if mrp_status == 1:
                    if stb_redo_count == 0:
                        str = 'alter database recover managed standby database disconnect from session'
                    else:
                        str = 'alter database recover managed standby database using current logfile disconnect from session'
                    cur.execute(str)
            
                  
		
    except Exception, e:
        logger.error(e)
    finally:
        if cur:
            cur.close()


######################################################################################################
# function drop_expire_restore_point
######################################################################################################           
def drop_expire_restore_point(host,port,dsn,username,password,server_id,tags):
    try:
        conn = get_connect(server_id)
        cur = conn.cursor()
        
        db_time = oracle.get_sysdate(conn)
        open_mode = oracle.get_database(conn,'open_mode')
        stb_redo_count = oracle.get_standby_redo_count(conn)
        
        parameters = oracle.get_parameters(conn)
        flashback_retention = parameters['db_flashback_retention_target']
        
        p_str = """select concat(username, '/', password, '@', host, ':', port, '/', dsn) from db_cfg_oracle where id=%s """ %(server_id)
        p_conn_str = func.mysql_single_query(p_str)

        recover_str = ""
        if stb_redo_count > 0:
            recover_str = "alter database recover managed standby database using current logfile disconnect from session;"
        else:
            recover_str = "alter database recover managed standby database disconnect from session;"
        
        # 每天0点，删除过期的闪回点
        if db_time[8:10] == "00":
            r_name_list = oracle.get_expire_restore_list(conn, flashback_retention)
            if r_name_list:
                logger.info("begin drop expire restore point for server: %s" %(server_id))
                if open_mode == "MOUNTED" or open_mode == "READ WRITE":
                    for r_name in r_name_list:
                        str = 'drop restore point %s' %(r_name[0])
                        cur.execute(str)
                        logger.info('drop expire restore point: %s for %s' %(r_name[0], server_id))
                elif open_mode == "READ ONLY" or open_mode == "READ ONLY WITH APPLY":
                    sqlplus = Popen(["sqlplus", "-S", p_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
                    sqlplus.stdin.write(bytes("shutdown immediate"+os.linesep))
                    sqlplus.stdin.write(bytes("startup mount"+os.linesep))
                    out, err = sqlplus.communicate()
                    logger.info(out)
                    logger.error(err)
                
                    
                    try:
                        conn = get_connect(server_id)
                        cur = conn.cursor()
                        for r_name in r_name_list:
                            str = 'drop restore point %s' %(r_name[0])
                            cur.execute(str)
                            logger.info('drop expire restore point: %s for %s' %(r_name[0], server_id))
                    except Exception, e:
                        logger.error(e)
                    finally:
                        sqlplus = Popen(["sqlplus", "-S", p_conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
                        sqlplus.stdin.write(bytes("alter database open;"+os.linesep))
                        sqlplus.stdin.write(bytes(recover_str+os.linesep))
                        out, err = sqlplus.communicate()
                        logger.info(out)
                        logger.error(err)
    
                logger.info("end drop expire restore point for server: %s" %(server_id))
		
    except Exception, e:
        logger.error(e)
    finally:
        if cur:
            cur.close()
######################################################################################################
# function update_fb_retention
######################################################################################################   
def update_fb_retention(conn, server_id, old_value):
    cur = None
    try:
        sql = "select fb_retention from db_cfg_oracle_dg where primary_db_id=%s or standby_db_id=%s limit 1;" %(server_id,server_id)
        res = func.mysql_single_query(sql)
        
        if res:
            sta_retention = res*24*60
            
            # 如果dg配置的闪回保留时间和数据库里面的不一致，则更新数据库 flashback_retention参数
            logger.info('dg flashback retention config: %s' %(sta_retention))
            logger.info('db_flashback_retention_target: %s' %(old_value))
                 
            if int(sta_retention) <> int(old_value):
                logger.info('Update db_flashback_retention_target to %s' %(sta_retention))
                cur = conn.cursor()
                str = 'alter system set db_flashback_retention_target=%s  scope=both' %(sta_retention)
                cur.execute(str)
		
    except Exception, e:
        logger.error(e)
    finally:
        if cur:
            cur.close()
            

######################################################################################################
# function clean_invalid_db_status
######################################################################################################   
def clean_invalid_db_status():
    try:
        func.mysql_exec("insert into oracle_status_his SELECT *,sysdate() from oracle_status where server_id not in(select id from  db_cfg_oracle where is_delete = 0);",'')
        func.mysql_exec('delete from oracle_status where server_id not in(select id from  db_cfg_oracle where is_delete = 0);','')
        
        func.mysql_exec("insert into oracle_tablespace_his SELECT *,sysdate() from oracle_tablespace where server_id not in(select id from  db_cfg_oracle where is_delete = 0);",'')
        func.mysql_exec('delete from oracle_tablespace where server_id not in(select id from  db_cfg_oracle where is_delete = 0);','')
        
        func.mysql_exec("insert into oracle_diskgroup_his SELECT *,sysdate() from oracle_diskgroup where server_id not in(select id from  db_cfg_oracle where is_delete = 0);",'')
        func.mysql_exec('delete from oracle_diskgroup where server_id not in(select id from  db_cfg_oracle where is_delete = 0);','')
        
        func.mysql_exec("delete from db_status where db_type = 'oracle' and server_id not in(select id from  db_cfg_oracle where is_delete = 0);",'')
        func.mysql_exec("delete from db_status where db_type = 'oracle' and host not in(select host from  db_cfg_oracle where is_delete = 0);",'')
        
    except Exception, e:
        logger.error(e)
    finally:
        pass
            
                     
######################################################################################################
# function main
######################################################################################################              
def main():
		
    #get oracle servers list
    servers=func.mysql_query("select id,host,port,dsn,username,password,tags from db_cfg_oracle where is_delete=0 and monitor=1;")

    logger.info("check oracle controller start.")
    if servers:
        func.update_check_time('oracle')

        plist = []
        for row in servers:
            server_id=row[0]
            host=row[1]
            port=row[2]
            dsn=row[3]
            username=row[4]
            password=row[5]
            tags=row[6]
            p = Process(target = check_oracle, args = (host,port,dsn,username,password,server_id,tags))
            plist.append(p)
            p.start()
        #time.sleep(10)
        #for p in plist:
        #    p.terminate()
        for p in plist:
            p.join()

    else:
        logger.warning("check oracle: not found any servers")

    logger.info("check oracle controller finished.")

    # Clean invalid data
    logger.info("Clean invalid oracle status start.")   
    clean_invalid_db_status()
    logger.info("Clean invalid oracle status finished.")       


    #check for dataguard group
    dg_list=func.mysql_query("select id, group_name, primary_db_id, standby_db_id, is_switch from db_cfg_oracle_dg where is_delete=0 and on_process = 0;")

    logger.info("check oracle dataguard start.")
    if dg_list:
        plist_2 = []
        for row in dg_list:
            dg_id=row[0]
            dg_name=row[1]
            pri_id=row[2]
            sta_id=row[3]
            is_switch=row[4]
            p2 = Process(target = check_dataguard, args = (dg_id,pri_id,sta_id,is_switch))
            plist_2.append(p2)
            p2.start()
            
        for p2 in plist_2:
            p2.join()

    else:
        logger.warning("check oracle dataguard: not found any dataguard group")

    logger.info("check oracle dataguard finished.")
    
    
    # drop expire restore point
    logger.info("drop expire restore point start.")
    if servers:
        plist_3 = []
        for row in servers:
            server_id=row[0]
            host=row[1]
            port=row[2]
            dsn=row[3]
            username=row[4]
            password=row[5]
            tags=row[6]
            p3 = Process(target = drop_expire_restore_point, args = (host,port,dsn,username,password,server_id,tags))
            plist_3.append(p3)
            p3.start()
            
        for p3 in plist_3:
            p3.join()

    else:
        logger.warning("drop expire restore point: not found any dataguard group")

    logger.info("drop expire restore point finished.")

if __name__=='__main__':
    main()
