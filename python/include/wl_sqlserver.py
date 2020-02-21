#!/bin/env python
#-*-coding:utf-8-*-

import pymssql
import string
import sys 
import datetime
import time
reload(sys) 
sys.setdefaultencoding('utf8')
import ConfigParser

def get_item(data_dict,item):
    try:
       item_value = data_dict[item]
       return item_value
    except:
       pass


def get_variables(conn,var_name):
    try:
        curs=conn.cursor()
        data=curs.execute('select @@'+var_name);
        data=curs.fetchone()
        parameters=data[0]

    except Exception,e:
        print e

    finally:
        curs.close()

    return parameters

def get_version(conn):
    try:
        curs=conn.cursor()
        data=curs.execute("SELECT @@VERSION");
        data=curs.fetchone()
        result  = data[0].split(' ')[3]
    except Exception,e:
        print e

    finally:
        curs.close()

    return result

def get_uptime(conn):
    try:
        curs=conn.cursor()
        data=curs.execute("SELECT sqlserver_start_time as time_restart,GETDATE() AS time_now,DATEDIFF(mi,sqlserver_start_time,GETDATE()) AS days_since_restart FROM sys.dm_os_sys_info");
        data=curs.fetchone()
        result  = int(data[2]*60)
    except Exception,e:
        print e

    finally:
        curs.close()

    return result


def get_curr_time(conn):
    try:
        curs=conn.cursor()
        data=curs.execute("SELECT CONVERT(varchar(100), GETDATE(), 120 ) AS time_now");
        result=curs.fetchone()
    except Exception,e:
        print e

    finally:
        curs.close()

    return result
    
def get_snap_id(conn):
    try:
        curs=conn.cursor()
        data=curs.execute("select CONVERT(varchar(100), GETDATE(), 112) + left(CONVERT(varchar(100), GETDATE(), 14),2) ");
        result=curs.fetchone()[0]
    except Exception,e:
        print e

    finally:
        curs.close()

    return result  
    

def get_buffer_cache_hit_rate(conn):
    try:
        curs=conn.cursor()
        data=curs.execute("""SELECT CAST(CAST((a.cntr_value * 1.0 / b.cntr_value)*100 as int) AS VARCHAR(20)) as BufferCacheHitRatio
															FROM (
															        SELECT * FROM sys.dm_os_performance_counters
															        WHERE counter_name = 'Buffer cache hit ratio'
															        AND object_name = CASE WHEN @@SERVICENAME = 'MSSQLSERVER'
															        THEN 'SQLServer:Buffer Manager'
															        ELSE 'MSSQL$' + rtrim(@@SERVICENAME) +
															        ':Buffer Manager' END 
															    ) a
															CROSS JOIN
															(
															    SELECT * from sys.dm_os_performance_counters
															    WHERE counter_name = 'Buffer cache hit ratio base'
															    and object_name = CASE WHEN @@SERVICENAME = 'MSSQLSERVER'
															    THEN 'SQLServer:Buffer Manager'
															    ELSE 'MSSQL$' + rtrim(@@SERVICENAME) +
															    ':Buffer Manager' END 
															) b """);
        result=curs.fetchone()[0]
    except Exception,e:
        print e

    finally:
        curs.close()

    return result  
    

def get_logMegabyte(conn):
    try:
        curs=conn.cursor()
        data=curs.execute("""select cntr_value/1024
															from  sys.dm_os_performance_counters
															where counter_name  =  'Log File(s) Size (KB)'
															and instance_name = '_Total' """);
        result=curs.fetchone()[0]
    except Exception,e:
        print e

    finally:
        curs.close()

    return result  
    
        
def get_database(conn,field):
    try:
        curs=conn.cursor()
        curs.execute("select %s from v$database" %(field) );
        result = curs.fetchone()[0]

    except Exception,e:
        result = ''
        print e

    finally:
        curs.close()

    return result


def ger_processes(conn):
    try:
        curs=conn.cursor()
        curs.execute("SELECT COUNT(*) FROM [master].[dbo].[sysprocesses] WHERE [DBID] IN ( SELECT  [dbid] FROM [master].[dbo].[sysdatabases])");
        result = curs.fetchone()[0]
        return result

    except Exception,e:
        return null    
        print e

    finally:
        curs.close()


def ger_processes_running(conn):
    try:
        curs=conn.cursor()
        curs.execute("SELECT COUNT(*) FROM [master].[dbo].[sysprocesses] WHERE [DBID] IN ( SELECT  [dbid] FROM [master].[dbo].[sysdatabases])  AND  status !='SLEEPING' AND status !='BACKGROUND'");
        result = curs.fetchone()[0]
        return result

    except Exception,e:
        return null
        print e

    finally:
        curs.close()

def ger_processes_waits(conn):
    try:
        curs=conn.cursor()
        curs.execute("SELECT COUNT(*) FROM [master].[dbo].[sysprocesses] WHERE [DBID] IN ( SELECT  [dbid] FROM [master].[dbo].[sysdatabases])  AND  status ='SUSPENDED' AND waittime >2 ");
        result = curs.fetchone()[0]
        return result

    except Exception,e:
        return null
        print e

    finally:
        curs.close()


def get_mirror_info(conn, db_name):
    try:
        curs=conn.cursor()
        curs.execute("""select m.database_id,
															d.name,
															substring(mirroring_partner_name, 7, charindex(':',mirroring_partner_name,7)-7) as master_server,
															right(mirroring_partner_name, len(mirroring_partner_name) - charindex(':',mirroring_partner_name,7)) as master_port,
															m.mirroring_role,
															m.mirroring_state,
															m.mirroring_state_desc,
															m.mirroring_safety_level,
															m.mirroring_partner_name,
															m.mirroring_partner_instance,
															m.mirroring_failover_lsn,
															m.mirroring_connection_timeout,
															m.mirroring_redo_queue,
															m.mirroring_end_of_log_lsn,
															m.mirroring_replication_lsn
												 from sys.database_mirroring m, sys.databases d
											  where m.mirroring_guid is NOT NULL
												  AND m.database_id = d.database_id
												  and d.name = '%s'; """ %(db_name));
												  
        result = curs.fetchone()
        return result
    except Exception,e:
        print e
        return None

    finally:
        curs.close()
        
        
def get_logspace(conn):
    try:
        curs=conn.cursor()
        curs.execute("""DBCC SQLPERF(LOGSPACE) """);
        list = curs.fetchall()
        return list

    except Exception,e:
        return None
        print e

    finally:
        curs.close()

