#!/bin/env python
#-*-coding:utf-8-*-
import os
import sys
import string
import time
import datetime
import MySQLdb
import logging
import logging.config
logging.config.fileConfig("etc/logger.ini")
logger = logging.getLogger("alert_mysql")
path='./include'
sys.path.insert(0,path)
import functions as func
import sendmail
import sendsms_fx
import sendsms_api

send_mail_max_count = func.get_option('send_mail_max_count')
send_mail_sleep_time = func.get_option('send_mail_sleep_time')
mail_to_list_common = func.get_option('send_mail_to_list')

send_sms_max_count = func.get_option('send_sms_max_count')
send_sms_sleep_time = func.get_option('send_sms_sleep_time')
sms_to_list_common = func.get_option('send_sms_to_list')

g_alert = str(func.get_option('alert'))
    
    
#################################################################################################    
def gen_alert_mysql_status(server_id):
    if g_alert != "1":
        return -1
        
    sql="""SELECT a.server_id,
									a.connect,
									a.threads_connected,
									a.threads_running,
									a.threads_waits,
									a.create_time,
									a.host,
									a.port,
									b.alarm_threads_connected,
									b.alarm_threads_running,
									alarm_threads_waits,
									b.threshold_warning_threads_connected,
									b.threshold_critical_threads_connected,
									b.threshold_warning_threads_running,
									b.threshold_critical_threads_running,
									threshold_warning_threads_waits,
									threshold_critical_threads_waits,
									b.send_mail,
									b.send_mail_to_list,
									b.send_sms,
									b.send_sms_to_list,
									b.send_wx,
									b.tags,
									'mysql' AS db_type
						FROM mysql_status a, db_cfg_mysql b
					 WHERE a.server_id = b.id
						 AND a.server_id = %s """ %(server_id)
    result=func.mysql_query(sql)
    if result <> 0:
        for line in result:
            server_id=line[0]
            connect=line[1]
            threads_connected=line[2]
            threads_running=line[3]
            threads_waits=line[4]
            create_time=line[5]
            host=line[6]
            port=line[7]
            alarm_threads_connected=line[8]
            alarm_threads_running=line[9]
            alarm_threads_waits=line[10]
            threshold_warning_threads_connected=line[11]
            threshold_critical_threads_connected=line[12]
            threshold_warning_threads_running=line[13]
            threshold_critical_threads_running=line[14]
            threshold_warning_threads_waits=line[15]
            threshold_critical_threads_waits=line[16]
            send_mail=line[17]
            send_mail_to_list=line[18]
            send_sms=line[19]
            send_sms_to_list=line[20]
            send_wx=line[21]
            tags=line[22]
            db_type=line[23]

            if send_mail_to_list is None or  send_mail_to_list.strip()=='':
                send_mail_to_list = mail_to_list_common
            if send_sms_to_list is None or  send_sms_to_list.strip()=='':
                send_sms_to_list = sms_to_list_common
            if connect <> 1:
                send_mail = func.update_send_mail_status(server_id,db_type,'connect',send_mail,send_mail_max_count)
                send_sms  = func.update_send_sms_status(server_id,db_type,'connect',send_sms,send_sms_max_count)
                send_wx  = func.update_send_wx_status(server_id,db_type,'connect',send_wx)
                func.add_alert(server_id,tags,host,port,create_time,db_type,'connect','down','critical','mysql server down',send_mail,send_mail_to_list,send_sms,send_sms_to_list,send_wx)
                func.update_db_status('connect','3',server_id, host, db_type,create_time,'connect','down','critical')
                func.update_db_status('sessions','-1',server_id, host, db_type,'','','','')
                func.update_db_status('actives','-1',server_id, host, db_type,'','','','')
                func.update_db_status('waits','-1',server_id, host, db_type,'','','','')
                func.update_db_status('repl','-1',server_id, host, db_type,'','','','')
                func.update_db_status('repl_delay','-1',server_id, host, db_type,'','','','')
            else:
                func.check_if_ok(server_id,tags,host,port,create_time,db_type,'connect','up','mysql server up',send_mail,send_mail_to_list,send_sms,send_sms_to_list,send_wx)
                func.update_db_status('connect','1',server_id, host, db_type, create_time,'connect','up','ok')

                if int(alarm_threads_connected)==1:
                    if int(threads_connected)>=int(threshold_critical_threads_connected):
                        #send_mail = func.update_send_mail_status(server_id,db_type,'threads_connected',send_mail,send_mail_max_count) 
                        #send_sms = func.update_send_sms_status(server_id,db_type,'threads_connected',send_sms,send_sms_max_count) 
                        #func.add_alert(server_id,tags,host,port,create_time,db_type,'threads_connected',threads_connected,'critical','too many threads connected',send_mail,send_mail_to_list,send_sms,send_sms_to_list,send_wx)
                        func.update_db_status('sessions',3,server_id, host, db_type,create_time,'threads_connected',threads_connected,'critical')
                    elif int(threads_connected)>=int(threshold_warning_threads_connected):
                        #send_mail = func.update_send_mail_status(server_id,db_type,'threads_connected',send_mail,send_mail_max_count) 
                        #send_sms = func.update_send_sms_status(server_id,db_type,'threads_connected',send_sms,send_sms_max_count) 
                        #func.add_alert(server_id,tags,host,port,create_time,db_type,'threads_connected',threads_connected,'warning','too many threads connected',send_mail,send_mail_to_list,send_sms,send_sms_to_list,send_wx)
                        func.update_db_status('sessions',2,server_id, host, db_type,create_time,'threads_connected',threads_connected,'warning')
                    else:
                        func.update_db_status('sessions',1,server_id, host, db_type,create_time,'threads_connected',threads_connected,'ok')
                        func.check_if_ok(server_id,tags,host,port,create_time,db_type,'threads_connected',threads_connected,'threads connected ok',send_mail,send_mail_to_list,send_sms,send_sms_to_list,send_wx)

                if int(alarm_threads_running)==1:
                    if int(threads_running)>=int(threshold_critical_threads_running):
                        #send_mail = func.update_send_mail_status(server_id,db_type,'threads_running',send_mail,send_mail_max_count) 
                        #send_sms = func.update_send_sms_status(server_id,db_type,'threads_running',send_sms,send_sms_max_count) 
                        #func.add_alert(server_id,tags,host,port,create_time,db_type,'threads_running',threads_running,'critical','too many threads running',send_mail,send_mail_to_list,send_sms,send_sms_to_list,send_wx)
                        func.update_db_status('actives',3,server_id, host, db_type,create_time,'threads_running',threads_running,'critical')
                    elif int(threads_running)>=int(threshold_warning_threads_running):
                         #send_mail = func.update_send_mail_status(server_id,db_type,'threads_running',send_mail,send_mail_max_count) 
                         #send_sms = func.update_send_sms_status(server_id,db_type,'threads_running',send_sms,send_sms_max_count) 
                         #func.add_alert(server_id,tags,host,port,create_time,db_type,'threads_running',threads_running,'warning','too many threads running',send_mail,send_mail_to_list,send_sms,send_sms_to_list,send_wx)
                         func.update_db_status('actives',2,server_id, host, db_type,create_time,'threads_running',threads_running,'warning')
                    else:
                         func.update_db_status('actives',1,server_id, host, db_type,create_time,'threads_running',threads_running,'ok')
                         func.check_if_ok(server_id,tags,host,port,create_time,db_type,'threads_running',threads_running,'threads running ok',send_mail,send_mail_to_list,send_sms,send_sms_to_list,send_wx)
                         
                if int(alarm_threads_waits)==1:
                    if int(threads_waits)>=int(threshold_critical_threads_waits):
                        #send_mail = func.update_send_mail_status(server_id,db_type,'threads_waits',send_mail,send_mail_max_count) 
                        #send_sms = func.update_send_sms_status(server_id,db_type,'threads_waits',send_sms,send_sms_max_count) 
                        #func.add_alert(server_id,tags,host,port,create_time,db_type,'threads_waits',threads_waits,'critical','too many threads waits',send_mail,send_mail_to_list,send_sms,send_sms_to_list,send_wx)
                        func.update_db_status('waits',3,server_id, host, db_type,create_time,'threads_waits',threads_waits,'critical')
                    elif int(threads_waits)>=int(threshold_warning_threads_running):
                         #send_mail = func.update_send_mail_status(server_id,db_type,'threads_waits',send_mail,send_mail_max_count) 
                         #send_sms = func.update_send_sms_status(server_id,db_type,'threads_waits',send_sms,send_sms_max_count) 
                         #func.add_alert(server_id,tags,host,port,create_time,db_type,'threads_waits',threads_waits,'warning','too many threads waits',send_mail,send_mail_to_list,send_sms,send_sms_to_list,send_wx)
                         func.update_db_status('waits',2,server_id, host, db_type,create_time,'threads_waits',threads_waits,'warning')
                    else:
                         func.update_db_status('waits',1,server_id, host, db_type,create_time,'threads_waits',threads_waits,'ok')
                         func.check_if_ok(server_id,tags,host,port,create_time,db_type,'threads_waits',threads_waits,'threads waits ok',send_mail,send_mail_to_list,send_sms,send_sms_to_list,send_wx)

    else:
       pass


def gen_alert_mysql_replcation(server_id):
    if g_alert != "1":
        return -1
        
    sql = """SELECT a.server_id,
										a.slave_io_run,
										a.slave_sql_run,
										a.delay,
										a.create_time,
										b.host,
										b.port,
										b.alarm_repl_status,
										b.alarm_repl_delay,
										b.threshold_warning_repl_delay,
										b.threshold_critical_repl_delay,
										b.send_mail,
										b.send_mail_to_list,
										b.send_sms,
										b.send_sms_to_list,
										b.send_wx,
										b.tags,
										'mysql' AS db_type
									FROM mysql_dr_s a, db_cfg_mysql b
									WHERE a.server_id = b.id
									and a.server_id = %s """ %(server_id)
    result=func.mysql_query(sql)
    if result <> 0:
        for line in result:
            server_id=line[0]
            slave_io_run=line[1]
            slave_sql_run=line[2]
            delay=line[3]
            create_time=line[4]
            host=line[5]
            port=line[6]
            alarm_repl_status=line[7]
            alarm_repl_delay=line[8]
            threshold_warning_repl_delay=line[9]
            threshold_critical_repl_delay=line[10]
            send_mail=line[11]
            send_mail_to_list=line[12]
            send_sms=line[13]
            send_sms_to_list=line[14]
            send_wx=line[15]
            tags=line[16]
            db_type=line[17]
            
            if send_mail_to_list is None or  send_mail_to_list.strip()=='':
                send_mail_to_list = mail_to_list_common
            if send_sms_to_list is None or  send_sms_to_list.strip()=='':
                send_sms_to_list = sms_to_list_common
                
            if int(alarm_repl_status)==1:
                if (slave_io_run== "Yes") and (slave_sql_run== "Yes"):
                    func.check_if_ok(server_id,tags,host,port,create_time,db_type,'replication','IO:'+slave_io_run+',SQL:'+slave_sql_run,'replication ok',send_mail,send_mail_to_list,send_sms,send_sms_to_list,send_wx)
                    func.update_db_status('repl',1,server_id, host, db_type,create_time,'replication','IO:'+slave_io_run+',SQL:'+slave_sql_run,'ok')
                    if int(alarm_repl_delay)==1:
                        if int(delay)>=int(threshold_critical_repl_delay):
                            send_mail = func.update_send_mail_status(server_id,db_type,'repl_delay',send_mail,send_mail_max_count) 
                            send_sms = func.update_send_sms_status(server_id,db_type,'repl_delay',send_sms,send_sms_max_count) 
                            send_wx  = func.update_send_wx_status(server_id,db_type,'repl_delay',send_wx)
                            func.add_alert(server_id,tags,host,port,create_time,db_type,'repl_delay',delay,'critical','replication has delay',send_mail,send_mail_to_list,send_sms,send_sms_to_list,send_wx)
                            func.update_db_status('repl_delay',3,server_id, host, db_type,create_time,'repl_delay',delay,'critical')
                        elif int(delay)>=int(threshold_warning_repl_delay):
                            send_mail = func.update_send_mail_status(server_id,db_type,'repl_delay',send_mail,send_mail_max_count) 
                            send_sms = func.update_send_sms_status(server_id,db_type,'repl_delay',send_sms,send_sms_max_count) 
                            send_wx  = func.update_send_wx_status(server_id,db_type,'repl_delay',send_wx)
                            func.add_alert(server_id,tags,host,port,create_time,db_type,'repl_delay',delay,'warning','replication has delay',send_mail,send_mail_to_list,send_sms,send_sms_to_list,send_wx)
                            func.update_db_status('repl_delay',2,server_id, host, db_type,create_time,'repl_delay',delay,'warning')
                        else:
                            func.check_if_ok(server_id,tags,host,port,create_time,db_type,'repl_delay',delay,'replication delay ok',send_mail,send_mail_to_list,send_sms,send_sms_to_list,send_wx)
                            func.update_db_status('repl_delay',1,server_id, host, db_type,create_time,'repl_delay',delay,'ok')
                else:
                    send_mail = func.update_send_mail_status(server_id,db_type,'replication',send_mail,send_mail_max_count)
                    send_sms = func.update_send_sms_status(server_id,db_type,'replication',send_sms,send_sms_max_count) 
                    send_wx  = func.update_send_wx_status(server_id,db_type,'replication',send_wx)
                    func.add_alert(server_id,tags,host,port,create_time,db_type,'replication','IO:'+slave_io_run+',SQL:'+slave_sql_run,'critical','replication stop',send_mail,send_mail_to_list,send_sms,send_sms_to_list,send_wx)
                    func.update_db_status('repl',3,server_id, host, db_type,create_time,'replication','IO:'+slave_io_run+',SQL:'+slave_sql_run,'critical')
                    func.update_db_status('repl_delay','-1',server_id, host, db_type,'','','','')
    else:
       pass



















