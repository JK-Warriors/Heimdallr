#!//bin/env python
#coding:utf-8
import os
import sys
import string
import time
import datetime
import MySQLdb
import cx_Oracle
import logging
import logging.config
logging.config.fileConfig("etc/logger.ini")
logger = logging.getLogger("lepus")
path='./include'
sys.path.insert(0,path)
import functions as func
import lepus_oracle as oracle
from multiprocessing import Process;


def check_oracle_dg(host,port,dsn,username,password,server_id,tags):
    url=host+':'+port+'/'+dsn

    try:
        conn=cx_Oracle.connect(username,password,url) #获取connection对象

    except Exception, e:
        logger_msg="check oracle %s : %s" %(url,str(e).strip('\n'))
        logger.warning(logger_msg)

        try:
            connect=0
            sql="insert into oracle_status(server_id,host,port,tags,connect) values(%s,%s,%s,%s,%s)"
            param=(server_id,host,port,tags,connect)
            func.mysql_exec(sql,param)
        except Exception, e:
            logger.error(str(e).strip('\n'))
            sys.exit(1)
        finally:
            sys.exit(1)

    finally:
        func.check_db_status(server_id,host,port,tags,'oracle')   

    try:
        #get info by v$instance
        connect = 1
        database_role = oracle.get_database(conn,'database_role')

        if database_role == 'PRIMARY':  
            dg_p_info = oracle.get_dg_p_info(conn, 2)

            for row in dg_p_info:
                dest_id=row[0]
                thread=row[1]
                sequence=row[2]
                archived=row[3]
                applied=row[4]
                current_scn=row[5]
                curr_db_time=row[6]

            
            ##################### insert data to mysql server#############################
            sql = "insert into oracle_dg_p_status(server_id,host,port,tags,connect,instance_name,instance_role,instance_status,database_role,open_mode,protection_mode,host_name,database_status,startup_time,uptime,version,archiver,session_total,session_actives,session_waits,dg_stats,dg_delay,processes,session_logical_reads_persecond,physical_reads_persecond,physical_writes_persecond,physical_read_io_requests_persecond,physical_write_io_requests_persecond,db_block_changes_persecond,os_cpu_wait_time,logons_persecond,logons_current,opened_cursors_persecond,opened_cursors_current,user_commits_persecond,user_rollbacks_persecond,user_calls_persecond,db_block_gets_persecond) values(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s);"
            param = (server_id,host,port,tags,connect,instance_name,instance_role,instance_status,database_role,open_mode,protection_mode,host_name,database_status,startup_time,uptime,version,archiver,session_total,session_actives,session_waits,dg_stats,dg_delay,processes,session_logical_reads_persecond,physical_reads_persecond,physical_writes_persecond,physical_read_io_requests_persecond,physical_write_io_requests_persecond,db_block_changes_persecond,os_cpu_wait_time,logons_persecond,logons_current,opened_cursors_persecond,opened_cursors_current,user_commits_persecond,user_rollbacks_persecond,user_calls_persecond,db_block_gets_persecond)
            func.mysql_exec(sql,param) 
            func.update_db_status_init(database_role_new,version,host,port,tags)
        else:  
            dg_s_info = oracle.get_dg_s_info(conn)
            
            for row in dg_p_info:
                thread=row[0]
                sequence=row[1]
                block=row[2]
                delay_mins=row[3]
                avg_apply_rate=row[4]
                current_scn=row[5]
                curr_db_time=row[6]

            ##################### insert data to mysql server#############################
            sql = "insert into oracle_dg_s_status(server_id,host,port,tags,connect,instance_name,instance_role,instance_status,database_role,open_mode,protection_mode,host_name,database_status,startup_time,uptime,version,archiver,session_total,session_actives,session_waits,dg_stats,dg_delay,processes,session_logical_reads_persecond,physical_reads_persecond,physical_writes_persecond,physical_read_io_requests_persecond,physical_write_io_requests_persecond,db_block_changes_persecond,os_cpu_wait_time,logons_persecond,logons_current,opened_cursors_persecond,opened_cursors_current,user_commits_persecond,user_rollbacks_persecond,user_calls_persecond,db_block_gets_persecond) values(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s);"
            param = (server_id,host,port,tags,connect,instance_name,instance_role,instance_status,database_role,open_mode,protection_mode,host_name,database_status,startup_time,uptime,version,archiver,session_total,session_actives,session_waits,dg_stats,dg_delay,processes,session_logical_reads_persecond,physical_reads_persecond,physical_writes_persecond,physical_read_io_requests_persecond,physical_write_io_requests_persecond,db_block_changes_persecond,os_cpu_wait_time,logons_persecond,logons_current,opened_cursors_persecond,opened_cursors_current,user_commits_persecond,user_rollbacks_persecond,user_calls_persecond,db_block_gets_persecond)
            func.mysql_exec(sql,param) 
            func.update_db_status_init(database_role_new,version,host,port,tags)        

    except Exception, e:
        logger.error(e)
        sys.exit(1)

    finally:
        conn.close()
        




def main():
    #get oracle dg list


if __name__=='__main__':
    main()
