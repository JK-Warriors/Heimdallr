#!/usr/bin/env python
#coding:utf-8
import os
import sys
import string
import time
import datetime
import traceback
import MySQLdb
import logging
import logging.config
logging.config.fileConfig("etc/logger.ini")
logger = logging.getLogger("check_mysql")
path='./include'
sys.path.insert(0,path)
import functions as func
import wl_mysql as mysql
import alert_mysql as alert
import alert_main as mail
from multiprocessing import Process;


def check_mysql(host,port,username,password,server_id,tags,bigtable_monitor,bigtable_size):
    url=host+':'+port

    try:  
        conn=MySQLdb.connect(host=host,user=username,passwd=password,port=int(port),connect_timeout=3,charset='utf8')
    except Exception, e:
        logger_msg="check mysql %s : %s" %(url,str(e).strip('\n'))
        logger.warning(logger_msg)
        
        try:
            connect=0
            
            func.mysql_exec("begin;",'')
            
            sql="delete from mysql_status where server_id = %s; " %(server_id)
            func.mysql_exec(sql,'')
            
            sql="insert into mysql_status(server_id,host,port,tags,connect) values(%s,%s,%s,%s,%s)"
            param=(server_id,host,port,tags,connect)
            func.mysql_exec(sql,param)
            
            # 更新容灾库 mysql_dr_s 表的信息
            func.mysql_exec("delete from mysql_dr_s where server_id in (%s);" %(server_id),'')
            
            logger.info("Generate mysql instance alert for server: %s begin:" %(server_id))
            alert.gen_alert_mysql_status(server_id)     # generate mysql instance alert
            logger.info("Generate mysql instance alert for server: %s end." %(server_id))
            
            func.mysql_exec("commit;",'')
        except Exception, e:
            func.mysql_exec("rollback;",'')
            logger.error(str(e).strip('\n'))
            sys.exit(1)
        finally:
            sys.exit(1)
    finally:
        func.check_db_status(server_id,host,port,tags,'mysql')   
        
        
    try:
        func.mysql_exec("begin;",'')
        func.mysql_exec("insert into mysql_status_his SELECT *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from mysql_status where server_id = %s;" %(server_id),'')
        func.mysql_exec('delete from mysql_status where server_id = %s;' %(server_id),'')

        func.mysql_exec("insert into mysql_bigtable_his SELECT *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from mysql_bigtable where server_id = %s;" %(server_id),'')
        func.mysql_exec('delete from mysql_bigtable where server_id = %s;' %(server_id),'')
    
        logger.info("Generate mysql instance information for server: %s port: %s begin:" %(host, port))
        
        cur=conn.cursor()
        conn.select_db('information_schema')
        #cur.execute('flush hosts;')
        
        ############################# CHECK MYSQL ####################################################
        mysql_variables = func.get_mysql_variables(cur)
        mysql_status = func.get_mysql_status(cur)       
        time.sleep(1)
        mysql_status_2 = func.get_mysql_status(cur)
        
        ############################# GET VARIABLES ###################################################
        version = func.get_item(mysql_variables,'version')
        key_buffer_size = func.get_item(mysql_variables,'key_buffer_size')
        sort_buffer_size = func.get_item(mysql_variables,'sort_buffer_size')
        join_buffer_size = func.get_item(mysql_variables,'join_buffer_size')
        max_connections = func.get_item(mysql_variables,'max_connections')
        max_connect_errors = func.get_item(mysql_variables,'max_connect_errors')
        open_files_limit = func.get_item(mysql_variables,'open_files_limit')
        table_open_cache = func.get_item(mysql_variables,'table_open_cache')
        max_tmp_tables = func.get_item(mysql_variables,'max_tmp_tables')
        max_heap_table_size = func.get_item(mysql_variables,'max_heap_table_size')
        max_allowed_packet = func.get_item(mysql_variables,'max_allowed_packet')
        log_bin = func.get_item(mysql_variables,'log_bin')
        
        ############################# GET INNODB INFO ##################################################
        #innodb variables
        innodb_version = func.get_item(mysql_variables,'innodb_version')
        innodb_buffer_pool_instances = func.get_item(mysql_variables,'innodb_buffer_pool_instances')
        innodb_buffer_pool_size = func.get_item(mysql_variables,'innodb_buffer_pool_size')
        innodb_doublewrite = func.get_item(mysql_variables,'innodb_doublewrite')
        innodb_file_per_table = func.get_item(mysql_variables,'innodb_file_per_table')
        innodb_flush_log_at_trx_commit = func.get_item(mysql_variables,'innodb_flush_log_at_trx_commit')
        innodb_flush_method = func.get_item(mysql_variables,'innodb_flush_method')
        innodb_force_recovery = func.get_item(mysql_variables,'innodb_force_recovery')
        innodb_io_capacity = func.get_item(mysql_variables,'innodb_io_capacity')
        innodb_read_io_threads = func.get_item(mysql_variables,'innodb_read_io_threads')
        innodb_write_io_threads = func.get_item(mysql_variables,'innodb_write_io_threads')
        #innodb status
        innodb_buffer_pool_pages_total = int(func.get_item(mysql_status,'Innodb_buffer_pool_pages_total'))
        innodb_buffer_pool_pages_data = int(func.get_item(mysql_status,'Innodb_buffer_pool_pages_data'))
        innodb_buffer_pool_pages_dirty = int(func.get_item(mysql_status,'Innodb_buffer_pool_pages_dirty'))
        innodb_buffer_pool_pages_flushed = int(func.get_item(mysql_status,'Innodb_buffer_pool_pages_flushed'))
        innodb_buffer_pool_pages_free = int(func.get_item(mysql_status,'Innodb_buffer_pool_pages_free'))
        innodb_buffer_pool_pages_misc = int(func.get_item(mysql_status,'Innodb_buffer_pool_pages_misc'))
        innodb_page_size = int(func.get_item(mysql_status,'Innodb_page_size'))
        innodb_pages_created = int(func.get_item(mysql_status,'Innodb_pages_created'))
        innodb_pages_read = int(func.get_item(mysql_status,'Innodb_pages_read'))
        innodb_pages_written = int(func.get_item(mysql_status,'Innodb_pages_written'))
        innodb_row_lock_current_waits = int(func.get_item(mysql_status,'Innodb_row_lock_current_waits'))
        #innodb persecond info
        innodb_buffer_pool_read_requests_persecond = int(func.get_item(mysql_status_2,'Innodb_buffer_pool_read_requests')) - int(func.get_item(mysql_status,'Innodb_buffer_pool_read_requests'))
        innodb_buffer_pool_reads_persecond = int(func.get_item(mysql_status_2,'Innodb_buffer_pool_reads')) - int(func.get_item(mysql_status,'Innodb_buffer_pool_reads'))
        innodb_buffer_pool_write_requests_persecond = int(func.get_item(mysql_status_2,'Innodb_buffer_pool_write_requests')) - int(func.get_item(mysql_status,'Innodb_buffer_pool_write_requests'))
        innodb_buffer_pool_pages_flushed_persecond = int(func.get_item(mysql_status_2,'Innodb_buffer_pool_pages_flushed')) - int(func.get_item(mysql_status,'Innodb_buffer_pool_pages_flushed'))
        innodb_rows_deleted_persecond = int(func.get_item(mysql_status_2,'Innodb_rows_deleted')) - int(func.get_item(mysql_status,'Innodb_rows_deleted'))
        innodb_rows_inserted_persecond = int(func.get_item(mysql_status_2,'Innodb_rows_inserted')) - int(func.get_item(mysql_status,'Innodb_rows_inserted'))
        innodb_rows_read_persecond = int(func.get_item(mysql_status_2,'Innodb_rows_read')) - int(func.get_item(mysql_status,'Innodb_rows_read'))
        innodb_rows_updated_persecond = int(func.get_item(mysql_status_2,'Innodb_rows_updated')) - int(func.get_item(mysql_status,'Innodb_rows_updated'))
        
        ############################# GET STATUS ##################################################
        connect = 1
        uptime = func.get_item(mysql_status,'Uptime')
        open_files = func.get_item(mysql_status,'Open_files')
        open_tables = func.get_item(mysql_status,'Open_tables')
        threads_connected = func.get_item(mysql_status,'Threads_connected')
        threads_running = func.get_item(mysql_status,'Threads_running')
        threads_created = func.get_item(mysql_status,'Threads_created')
        threads_cached = func.get_item(mysql_status,'Threads_cached')
        threads_waits = mysql.get_waits(conn)
        connections = func.get_item(mysql_status,'Connections')
        aborted_clients = func.get_item(mysql_status,'Aborted_clients')
        aborted_connects = func.get_item(mysql_status,'Aborted_connects')
        key_blocks_not_flushed = func.get_item(mysql_status,'Key_blocks_not_flushed')
        key_blocks_unused = func.get_item(mysql_status,'Key_blocks_unused')
        key_blocks_used = func.get_item(mysql_status,'Key_blocks_used')
        
        ############################# GET STATUS PERSECOND ##################################################
        connections_persecond = int(func.get_item(mysql_status_2,'Connections')) - int(func.get_item(mysql_status,'Connections'))
        bytes_received_persecond = (int(func.get_item(mysql_status_2,'Bytes_received')) - int(func.get_item(mysql_status,'Bytes_received')))/1024
        bytes_sent_persecond = (int(func.get_item(mysql_status_2,'Bytes_sent')) - int(func.get_item(mysql_status,'Bytes_sent')))/1024
        com_select_persecond = int(func.get_item(mysql_status_2,'Com_select')) - int(func.get_item(mysql_status,'Com_select'))
        com_insert_persecond = int(func.get_item(mysql_status_2,'Com_insert')) - int(func.get_item(mysql_status,'Com_insert'))
        com_update_persecond = int(func.get_item(mysql_status_2,'Com_update')) - int(func.get_item(mysql_status,'Com_update'))
        com_delete_persecond = int(func.get_item(mysql_status_2,'Com_delete')) - int(func.get_item(mysql_status,'Com_delete'))
        com_commit_persecond = int(func.get_item(mysql_status_2,'Com_commit')) - int(func.get_item(mysql_status,'Com_commit'))
        com_rollback_persecond = int(func.get_item(mysql_status_2,'Com_rollback')) - int(func.get_item(mysql_status,'Com_rollback'))
        questions_persecond = int(func.get_item(mysql_status_2,'Questions')) - int(func.get_item(mysql_status,'Questions'))
        queries_persecond = int(func.get_item(mysql_status_2,'Queries')) - int(func.get_item(mysql_status,'Queries'))
        transaction_persecond = (int(func.get_item(mysql_status_2,'Com_commit')) + int(func.get_item(mysql_status_2,'Com_rollback'))) - (int(func.get_item(mysql_status,'Com_commit')) + int(func.get_item(mysql_status,'Com_rollback')))
        created_tmp_disk_tables_persecond = int(func.get_item(mysql_status_2,'Created_tmp_disk_tables')) - int(func.get_item(mysql_status,'Created_tmp_disk_tables'))
        created_tmp_files_persecond = int(func.get_item(mysql_status_2,'Created_tmp_files')) - int(func.get_item(mysql_status,'Created_tmp_files'))
        created_tmp_tables_persecond = int(func.get_item(mysql_status_2,'Created_tmp_tables')) - int(func.get_item(mysql_status,'Created_tmp_tables'))
        table_locks_immediate_persecond = int(func.get_item(mysql_status_2,'Table_locks_immediate')) - int(func.get_item(mysql_status,'Table_locks_immediate'))
        table_locks_waited_persecond = int(func.get_item(mysql_status_2,'Table_locks_waited')) - int(func.get_item(mysql_status,'Table_locks_waited'))
        key_read_requests_persecond = int(func.get_item(mysql_status_2,'Key_read_requests')) - int(func.get_item(mysql_status,'Key_read_requests'))
        key_reads_persecond = int(func.get_item(mysql_status_2,'Key_reads')) - int(func.get_item(mysql_status,'Key_reads'))
        key_write_requests_persecond = int(func.get_item(mysql_status_2,'Key_write_requests')) - int(func.get_item(mysql_status,'Key_write_requests'))
        key_writes_persecond = int(func.get_item(mysql_status_2,'Key_writes')) - int(func.get_item(mysql_status,'Key_writes'))
        
        ############################# GET MYSQL HITRATE ##################################################
        if (string.atof(func.get_item(mysql_status,'Qcache_hits')) + string.atof(func.get_item(mysql_status,'Com_select'))) <> 0:
            query_cache_hitrate = string.atof(func.get_item(mysql_status,'Qcache_hits')) / (string.atof(func.get_item(mysql_status,'Qcache_hits')) + string.atof(func.get_item(mysql_status,'Com_select')))
            query_cache_hitrate =  "%9.2f" %query_cache_hitrate
        else:
            query_cache_hitrate = 0

        if string.atof(func.get_item(mysql_status,'Connections')) <> 0:
            thread_cache_hitrate = 1 - string.atof(func.get_item(mysql_status,'Threads_created')) / string.atof(func.get_item(mysql_status,'Connections'))
            thread_cache_hitrate =  "%9.2f" %thread_cache_hitrate
        else:
            thread_cache_hitrate = 0

        if string.atof(func.get_item(mysql_status,'Key_read_requests')) <> 0:
            key_buffer_read_rate = 1 - string.atof(func.get_item(mysql_status,'Key_reads')) / string.atof(func.get_item(mysql_status,'Key_read_requests'))
            key_buffer_read_rate =  "%9.2f" %key_buffer_read_rate
        else:
            key_buffer_read_rate = 0

        if string.atof(func.get_item(mysql_status,'Key_write_requests')) <> 0:
            key_buffer_write_rate = 1 - string.atof(func.get_item(mysql_status,'Key_writes')) / string.atof(func.get_item(mysql_status,'Key_write_requests'))
            key_buffer_write_rate =  "%9.2f" %key_buffer_write_rate
        else:
            key_buffer_write_rate = 0
        
        if (string.atof(func.get_item(mysql_status,'Key_blocks_used'))+string.atof(func.get_item(mysql_status,'Key_blocks_unused'))) <> 0:
            key_blocks_used_rate = string.atof(func.get_item(mysql_status,'Key_blocks_used')) / (string.atof(func.get_item(mysql_status,'Key_blocks_used'))+string.atof(func.get_item(mysql_status,'Key_blocks_unused')))
            key_blocks_used_rate =  "%9.2f" %key_blocks_used_rate
        else:
            key_blocks_used_rate = 0

        if (string.atof(func.get_item(mysql_status,'Created_tmp_disk_tables'))+string.atof(func.get_item(mysql_status,'Created_tmp_tables'))) <> 0:
            created_tmp_disk_tables_rate = string.atof(func.get_item(mysql_status,'Created_tmp_disk_tables')) / (string.atof(func.get_item(mysql_status,'Created_tmp_disk_tables'))+string.atof(func.get_item(mysql_status,'Created_tmp_tables')))
            created_tmp_disk_tables_rate =  "%9.2f" %created_tmp_disk_tables_rate
        else:
            created_tmp_disk_tables_rate = 0

        if string.atof(max_connections) <> 0:
            connections_usage_rate = string.atof(threads_connected)/string.atof(max_connections)
            connections_usage_rate =  "%9.2f" %connections_usage_rate
        else:
            connections_usage_rate = 0

        if string.atof(open_files_limit) <> 0:            
            open_files_usage_rate = string.atof(open_files)/string.atof(open_files_limit)
            open_files_usage_rate =  "%9.2f" %open_files_usage_rate
        else:
            open_files_usage_rate = 0

        if string.atof(table_open_cache) <> 0:            
            open_tables_usage_rate = string.atof(open_tables)/string.atof(table_open_cache)
            open_tables_usage_rate =  "%9.2f" %open_tables_usage_rate
        else:
            open_tables_usage_rate = 0
  
        #repl
        subordinate_status=cur.execute('show subordinate status;')
        if subordinate_status <> 0:
            role='subordinate'
            role_new='s'
        else:
            role='main'
            role_new='m'

        ############################# INSERT INTO SERVER ##################################################
        sql = "insert into mysql_status(server_id,host,port,tags,connect,role,uptime,version,max_connections,max_connect_errors,open_files_limit,table_open_cache,max_tmp_tables,max_heap_table_size,max_allowed_packet,open_files,open_tables,threads_connected,threads_running,threads_waits,threads_created,threads_cached,connections,aborted_clients,aborted_connects,connections_persecond,bytes_received_persecond,bytes_sent_persecond,com_select_persecond,com_insert_persecond,com_update_persecond,com_delete_persecond,com_commit_persecond,com_rollback_persecond,questions_persecond,queries_persecond,transaction_persecond,created_tmp_tables_persecond,created_tmp_disk_tables_persecond,created_tmp_files_persecond,table_locks_immediate_persecond,table_locks_waited_persecond,key_buffer_size,sort_buffer_size,join_buffer_size,key_blocks_not_flushed,key_blocks_unused,key_blocks_used,key_read_requests_persecond,key_reads_persecond,key_write_requests_persecond,key_writes_persecond,innodb_version,innodb_buffer_pool_instances,innodb_buffer_pool_size,innodb_doublewrite,innodb_file_per_table,innodb_flush_log_at_trx_commit,innodb_flush_method,innodb_force_recovery,innodb_io_capacity,innodb_read_io_threads,innodb_write_io_threads,innodb_buffer_pool_pages_total,innodb_buffer_pool_pages_data,innodb_buffer_pool_pages_dirty,innodb_buffer_pool_pages_flushed,innodb_buffer_pool_pages_free,innodb_buffer_pool_pages_misc,innodb_page_size,innodb_pages_created,innodb_pages_read,innodb_pages_written,innodb_row_lock_current_waits,innodb_buffer_pool_pages_flushed_persecond,innodb_buffer_pool_read_requests_persecond,innodb_buffer_pool_reads_persecond,innodb_buffer_pool_write_requests_persecond,innodb_rows_read_persecond,innodb_rows_inserted_persecond,innodb_rows_updated_persecond,innodb_rows_deleted_persecond,query_cache_hitrate,thread_cache_hitrate,key_buffer_read_rate,key_buffer_write_rate,key_blocks_used_rate,created_tmp_disk_tables_rate,connections_usage_rate,open_files_usage_rate,open_tables_usage_rate) values(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s);"
        param = (server_id,host,port,tags,connect,role,uptime,version,max_connections,max_connect_errors,open_files_limit,table_open_cache,max_tmp_tables,max_heap_table_size,max_allowed_packet,open_files,open_tables,threads_connected,threads_running,threads_waits,threads_created,threads_cached,connections,aborted_clients,aborted_connects,connections_persecond,bytes_received_persecond,bytes_sent_persecond,com_select_persecond,com_insert_persecond,com_update_persecond,com_delete_persecond,com_commit_persecond,com_rollback_persecond,questions_persecond,queries_persecond,transaction_persecond,created_tmp_tables_persecond,created_tmp_disk_tables_persecond,created_tmp_files_persecond,table_locks_immediate_persecond,table_locks_waited_persecond,key_buffer_size,sort_buffer_size,join_buffer_size,key_blocks_not_flushed,key_blocks_unused,key_blocks_used,key_read_requests_persecond,key_reads_persecond,key_write_requests_persecond,key_writes_persecond,innodb_version,innodb_buffer_pool_instances,innodb_buffer_pool_size,innodb_doublewrite,innodb_file_per_table,innodb_flush_log_at_trx_commit,innodb_flush_method,innodb_force_recovery,innodb_io_capacity,innodb_read_io_threads,innodb_write_io_threads,innodb_buffer_pool_pages_total,innodb_buffer_pool_pages_data,innodb_buffer_pool_pages_dirty,innodb_buffer_pool_pages_flushed,innodb_buffer_pool_pages_free,innodb_buffer_pool_pages_misc,innodb_page_size,innodb_pages_created,innodb_pages_read,innodb_pages_written,innodb_row_lock_current_waits,innodb_buffer_pool_pages_flushed_persecond,innodb_buffer_pool_read_requests_persecond,innodb_buffer_pool_reads_persecond,innodb_buffer_pool_write_requests_persecond,innodb_rows_read_persecond,innodb_rows_inserted_persecond,innodb_rows_updated_persecond,innodb_rows_deleted_persecond,query_cache_hitrate,thread_cache_hitrate,key_buffer_read_rate,key_buffer_write_rate,key_blocks_used_rate,created_tmp_disk_tables_rate,connections_usage_rate,open_files_usage_rate,open_tables_usage_rate)
        func.mysql_exec(sql,param)
        func.update_db_status_init(server_id,'mysql',role_new,version,tags)

        # generate mysql status alert
        alert.gen_alert_mysql_status(server_id)    
        
        
        #check mysql process
        processlist=cur.execute("select * from information_schema.processlist where DB !='information_schema' and command !='Sleep';")
        if processlist:
            for line in cur.fetchall():
                sql="insert into mysql_processlist(server_id,host,port,tags,pid,p_user,p_host,p_db,command,time,status,info) values(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
                param=(server_id,host,port,tags,line[0],line[1],line[2],line[3],line[4],line[5],line[6],line[7])
                func.mysql_exec(sql,param)

        #check mysql connected
        connected=cur.execute("select SUBSTRING_INDEX(host,':',1) as connect_server, user connect_user,db connect_db, count(SUBSTRING_INDEX(host,':',1)) as connect_count from information_schema.processlist where db is not null and db!='information_schema' and db !='performance_schema' group by connect_server,connect_user,connect_db;");
        if connected:
            for line in cur.fetchall():
                sql="insert into mysql_connected(server_id,host,port,tags,connect_server,connect_user,connect_db,connect_count) values(%s,%s,%s,%s,%s,%s,%s,%s);"
                param =(server_id,host,port,tags,line[0],line[1],line[2],line[3])
                func.mysql_exec(sql,param)

        #check mysql bigtable
        if bigtable_monitor == 1:
            bigtable=cur.execute("""SELECT table_schema,table_name,ROUND(data_length/( 1024 * 1024 ), 2), table_comment as COMMENT 
																	    FROM information_schema.TABLES 
																	   where ROUND(( data_length ) / ( 1024 * 1024 ), 2) > %s
																	   ORDER BY 3 DESC; """ %(bigtable_size));
            if bigtable:
                for row in cur.fetchall():
                    sql="insert into mysql_bigtable(server_id,host,port,tags,db_name,table_name,table_size,table_comment) values(%s,%s,%s,%s,%s,%s,%s,%s);"
                    param=(server_id,host,port,tags,row[0],row[1],row[2],row[3])
                    func.mysql_exec(sql,param)
        

        func.mysql_exec("commit;",'')
        
        logger.info("Generate mysql instance information for server: %s port: %s end:" %(host, port))
        #send mail
        mail.send_alert_mail(server_id, host)   
        

    except Exception, e:
        logger.error('server_id: %s host: %s port: %s \n' %(server_id,host, port))
        logger.error('traceback.format_exc():\n%s' % traceback.format_exc())
        func.mysql_exec("rollback;",'')
        sys.exit(1)

    finally:
        cur.close()

   

######################################################################################################
# function get_connect
######################################################################################################    
def get_connect(server_id):
    host = ""
    port = ""
    username = ""
    password = ""
    tags = ""
    
    server=func.mysql_query("select host,port,username,password,tags from db_cfg_mysql where id=%s;" %(server_id))
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
        
    url=host+':'+port
    
    try:  
        conn=MySQLdb.connect(host=host,user=username,passwd=passwd,port=int(port),connect_timeout=3,charset='utf8')
        return conn  
    except Exception, e:
        logger_msg="check mysql %s : %s" %(url,str(e).strip('\n'))
        logger.warning(logger_msg)
        
        try:
            connect=0
            
            func.mysql_exec("begin;",'')
            
            sql="delete from mysql_status where server_id = %s; " %(server_id)
            func.mysql_exec(sql,'')
            
            sql="insert into mysql_status(server_id,host,port,tags,connect) values(%s,%s,%s,%s,%s)"
            param=(server_id,host,port,tags,connect)
            func.mysql_exec(sql,param)
            
            # 更新容灾库 mysql_dr_s 表的信息
            func.mysql_exec("delete from mysql_dr_s where server_id in (%s);" %(server_id),'')
            
            logger.info("Generate mysql instance alert for server: %s begin:" %(server_id))
            alert.gen_alert_mysql_status(server_id)     # generate mysql instance alert
            logger.info("Generate mysql instance alert for server: %s end." %(server_id))
            
            func.mysql_exec("commit;",'')
        except Exception, e:
            func.mysql_exec("rollback;",'')
            logger.error(str(e).strip('\n'))
        finally:
            pass
    finally:
        func.check_db_status(server_id,host,port,tags,'mysql') 
        
                 
######################################################################################################
# function check_replication
######################################################################################################       
def check_replication(group_id, pri_id, sta_id, is_switch):
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
        func.mysql_exec("insert into mysql_dr_p_his SELECT *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from mysql_dr_p where server_id in (%s, %s);" %(pri_id, sta_id),'')
        func.mysql_exec("delete from mysql_dr_p where server_id in (%s, %s);" %(pri_id, sta_id),'')
        
        func.mysql_exec("insert into mysql_dr_s_his SELECT *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from mysql_dr_s where server_id in (%s, %s);" %(pri_id, sta_id),'')
        func.mysql_exec("delete from mysql_dr_s where server_id in (%s, %s);" %(pri_id, sta_id),'')


        logger.info("Generate replication primary info for server: %s begin:" %(p_id))
        if p_conn:
            p_cur=p_conn.cursor()
            
            # check role
            subordinate_status=p_cur.execute('show subordinate status;')
            if subordinate_status <> 0:
                role='subordinate'
                logger.warn("The primary server: %s configured in replication group is NOT match the role!" %(p_id))
            else:
                role='main'
            
                mysql_variables = func.get_mysql_variables(p_cur)
            
                gtid_mode = func.get_item(mysql_variables,'gtid_mode')
                read_only=func.get_item(mysql_variables,'read_only')
                log_bin = func.get_item(mysql_variables,'log_bin')
            
                main=p_cur.execute('show main status;')
                main_result=p_cur.fetchone()
            
                binlog_file = '---'
                binlog_pos = '---'
                if main_result:
                    binlog_file = main_result[0]
                    binlog_pos = main_result[1]
                
                binlogs=0
                if log_bin == 'ON':
                    binlog_file=p_cur.execute('show main logs;')
                    if binlog_file:
                        for row in p_cur.fetchall():
                            binlogs = binlogs + row[1]
                else:
                    binlogs=0
                
                sql="insert into mysql_dr_p(server_id,gtid_mode,read_only,main_binlog_file,main_binlog_pos,main_binlog_space) values(%s,%s,%s,%s,%s,%s)"
                param=(p_id,gtid_mode,read_only,binlog_file,binlog_pos,binlogs)
                func.mysql_exec(sql,param)
            
        logger.info("Generate replication primary info for server: %s end:" %(p_id))
            
        logger.info("Generate replication standby info for server: %s begin:" %(s_id))
        if s_conn:
            s_cur=s_conn.cursor()
            
            # check role
            subordinate_status=s_cur.execute('show subordinate status;')
            if subordinate_status <> 0:
                role='subordinate'
                
                mysql_variables = func.get_mysql_variables(s_cur)
            
                gtid_mode = func.get_item(mysql_variables,'gtid_mode')
                read_only = func.get_item(mysql_variables,'read_only')
            
                subordinate_info=s_cur.execute('show subordinate status;')
                result=s_cur.fetchone()
                if result:
                    main_server=result[1]
                    main_port=result[3]
                    subordinate_io_run=result[10]
                    subordinate_sql_run=result[11]
                    delay=result[32]
                    current_binlog_file=result[9]
                    current_binlog_pos=result[21]
                    main_binlog_file=result[5]
                    main_binlog_pos=result[6]
            
                    sql="insert into mysql_dr_s(server_id,gtid_mode,read_only,main_server,main_port,subordinate_io_run,subordinate_sql_run,delay,current_binlog_file,current_binlog_pos,main_binlog_file,main_binlog_pos,main_binlog_space) values(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
                    param=(s_id,gtid_mode,read_only,main_server,main_port,subordinate_io_run,subordinate_sql_run,delay,current_binlog_file,current_binlog_pos,main_binlog_file,main_binlog_pos,0)
                    func.mysql_exec(sql,param)
            
                    logger.info("Generate replication standby info for server: %s end:" %(s_id))
            else:
                role='main'
                logger.warn("The standby server: %s configured in replication group is NOT match the role!" %(s_id))
                
            # generate mysql replication alert
            alert.gen_alert_mysql_replcation(s_id)   
                
        logger.info("Generate replication standby info for server: %s end:" %(s_id))
             

             
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
        func.mysql_exec("insert into mysql_status_his SELECT *,sysdate() from mysql_status where server_id not in(select id from db_cfg_mysql where is_delete = 0 and monitor = 1);",'')
        func.mysql_exec('delete from mysql_status where server_id not in(select id from db_cfg_mysql where is_delete = 0 and monitor = 1);','')
        
        func.mysql_exec("insert into mysql_bigtable_his SELECT *,sysdate() from mysql_bigtable where server_id not in(select id from db_cfg_mysql where is_delete = 0 and monitor = 1);",'')
        func.mysql_exec('delete from mysql_bigtable where server_id not in(select id from db_cfg_mysql where is_delete = 0 and monitor = 1);','')

        func.mysql_exec("insert into mysql_dr_p_his SELECT *,sysdate() from mysql_dr_p where server_id in(select id from db_cfg_mysql where is_delete = 1 or monitor = 0);",'')
        func.mysql_exec('delete from mysql_dr_p where server_id in(select id from db_cfg_mysql where is_delete = 1 or monitor = 0);','')
                
        func.mysql_exec("insert into mysql_dr_s_his SELECT *,sysdate() from mysql_dr_s where server_id in(select id from db_cfg_mysql where is_delete = 1 or monitor = 0);",'')
        func.mysql_exec('delete from mysql_dr_s where server_id in(select id from db_cfg_mysql where is_delete = 1 or monitor = 0);','')
        
        #func.mysql_exec("insert into mysql_slow_query_review_his SELECT *,sysdate() from mysql_slow_query_review where server_id not in(select id from db_cfg_mysql where is_delete = 0);",'')
        #func.mysql_exec('delete from mysql_slow_query_review where server_id not in(select id from db_cfg_mysql where is_delete = 0);','')
        
        func.mysql_exec('delete from mysql_connected where server_id not in(select id from db_cfg_mysql where is_delete = 0 and monitor = 1);','')
        
        func.mysql_exec('delete from mysql_processlist where server_id not in(select id from db_cfg_mysql where is_delete = 0 and monitor = 1);','')
                                  
        func.mysql_exec("delete from db_status where db_type = 'mysql' and server_id not in(select id from db_cfg_mysql where is_delete = 0 and monitor = 1);",'')
        
        func.mysql_exec("update db_status t set t.repl = -1, repl_delay = -1 where db_type = 'mysql' and server_id not in(select server_id from mysql_dr_s);",'')
        
    except Exception, e:
        logger.error(e)
    finally:
        pass
           

def main():
    #get mysql servers list
    servers = func.mysql_query('select id,host,port,username,password,tags,bigtable_monitor,bigtable_size from db_cfg_mysql where is_delete=0 and monitor=1;')


    logger.info("check mysql controller started.")
    if servers:
         func.update_check_time('mysql')

         plist = []
         for row in servers:
             server_id=row[0]
             host=row[1]
             port=row[2]
             username=row[3]
             password=row[4]
             tags=row[5]
             bigtable_monitor=row[6]
             bigtable_size=row[7]
             #thread.start_new_thread(check_mysql, (host,port,user,passwd,server_id,application_id))
             #time.sleep(1)
             p = Process(target = check_mysql, args = (host,port,username,password,server_id,tags,bigtable_monitor,bigtable_size))
             plist.append(p)
         for p in plist:
             p.start()
         time.sleep(10)
         for p in plist:
             p.terminate()	
         for p in plist:
             p.join()
         
    else:
         logger.warning("check mysql: not found any servers")

    logger.info("check mysql controller finished.")


    #check for mysql_replication group
    rep_list=func.mysql_query("select id, group_name, primary_db_id, standby_db_id, is_switch from db_cfg_mysql_dr where is_delete=0 and on_process = 0;")

    logger.info("check mysql replication start.")
    if rep_list:
        plist_2 = []
        for row in rep_list:
            group_id=row[0]
            group_name=row[1]
            pri_id=row[2]
            sta_id=row[3]
            is_switch=row[4]
            p2 = Process(target = check_replication, args = (group_id,pri_id,sta_id,is_switch))
            plist_2.append(p2)
            p2.start()
            
        for p2 in plist_2:
            p2.join()

    else:
        logger.warning("check mysql replication: not found any replication group")

    logger.info("check mysql replication finished.")
    
    # Clean invalid data
    logger.info("Clean invalid mysql status start.")   
    clean_invalid_db_status()
    logger.info("Clean invalid mysql status finished.")     

if __name__=='__main__':
    main()
