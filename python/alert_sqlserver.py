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
logger = logging.getLogger("alert_sqlserver")
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
def gen_alert_sqlserver_status(server_id):
    if g_alert != "1":
        return -1
        
    sql="""SELECT a.server_id,
									a.connect,
									a.processes,
									a.processes_running,
									a.processes_waits,
									a.create_time,
									a.host,
									a.port,
									b.alarm_processes,
									b.alarm_processes_running,
									alarm_processes_waits,
									b.threshold_warning_processes,
									b.threshold_warning_processes_running,
									b.threshold_warning_processes_waits,
									b.threshold_critical_processes,
									threshold_critical_processes_running,
									threshold_critical_processes_waits,
									b.send_mail,
									b.send_mail_to_list,
									b.send_sms,
									b.send_sms_to_list,
									b.tags,
									'sqlserver' AS db_type
								FROM sqlserver_status a, db_cfg_sqlserver b
								WHERE a.server_id = b.id 
									and a.server_id = %s """ %(server_id)
    result=func.mysql_query(sql)
    if result <> 0:
        for line in result:
            server_id=line[0]
            connect=line[1]
            processes=line[2]
            processes_running=line[3]
            processes_waits=line[4]
            create_time=line[5]
            host=line[6]
            port=line[7]
            alarm_processes=line[8]
            alarm_processes_running=line[9]
            alarm_processes_waits=line[10]
            threshold_warning_processes=line[11]
            threshold_warning_processes_running=line[12]
            threshold_warning_processes_waits=line[13]
            threshold_critical_processes=line[14]
            threshold_critical_processes_running=line[15]
            threshold_critical_processes_waits=line[16]
            send_mail=line[17]
            send_mail_to_list=line[18]
            send_sms=line[19]
            send_sms_to_list=line[20]
            tags=line[21]
            db_type=line[22]
            if send_mail_to_list is None or  send_mail_to_list.strip()=='':
                send_mail_to_list = mail_to_list_common
            if send_sms_to_list is None or  send_sms_to_list.strip()=='':
                send_sms_to_list = sms_to_list_common
            if connect <> 1:
                send_mail = func.update_send_mail_status(server_id,db_type,'connect',send_mail,send_mail_max_count)
                send_sms  = func.update_send_sms_status(server_id,db_type,'connect',send_sms,send_sms_max_count)
                func.add_alert(server_id,tags,host,port,create_time,db_type,'connect','down','critical','sqlserver server down',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                func.update_db_status('connect','3',server_id, host, db_type,create_time,'connect','down','critical')
                func.update_db_status('sessions','-1',server_id, host, db_type,'','','','')
                func.update_db_status('actives','-1',server_id, host, db_type,'','','','')
                func.update_db_status('waits','-1',server_id, host, db_type,'','','','')
                func.update_db_status('repl','-1',server_id, host, db_type,'','','','')
                func.update_db_status('repl_delay','-1',server_id, host, db_type,'','','','')
            else:

                func.check_if_ok(server_id,tags,host,port,create_time,db_type,'connect','up','sqlserver server up',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                func.update_db_status('connect','1',server_id, host, db_type,create_time,'connect','up','ok')
                if int(alarm_processes)==1:
                    if int(processes)>=int(threshold_critical_processes):
                        #send_mail = func.update_send_mail_status(server_id,db_type,'processes',send_mail,send_mail_max_count)
                        #send_sms = func.update_send_sms_status(server_id,db_type,'processes',send_sms,send_sms_max_count)
                        #func.add_alert(server_id,tags,host,port,create_time,db_type,'processes',processes,'critical','too many processes',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('sessions',3,server_id, host, db_type,create_time,'processes',processes,'critical')
                    elif int(processes)>=int(threshold_warning_processes):
                        #send_mail = func.update_send_mail_status(server_id,db_type,'processes',send_mail,send_mail_max_count)
                        #send_sms = func.update_send_sms_status(server_id,db_type,'processes',send_sms,send_sms_max_count)
                        #func.add_alert(server_id,tags,host,port,create_time,db_type,'processes',processes,'warning','too many processes',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('sessions',2,server_id, host, db_type,create_time,'processes',processes,'warning')
                    else:
                        func.update_db_status('sessions',1,server_id, host, db_type,create_time,'processes',processes,'ok')
                        func.check_if_ok(server_id,tags,host,port,create_time,db_type,'processes',processes,'processes ok',send_mail,send_mail_to_list,send_sms,send_sms_to_list)

                if int(alarm_processes_running)==1:
                    if int(processes_running)>=int(threshold_critical_processes_running):
                        #send_mail = func.update_send_mail_status(server_id,db_type,'processes_running',send_mail,send_mail_max_count)
                        #send_sms = func.update_send_sms_status(server_id,db_type,'processes_running',send_sms,send_sms_max_count)
                        #func.add_alert(server_id,tags,host,port,create_time,db_type,'processes_running',processes_running,'critical','too many processes running',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('actives',3,server_id, host, db_type,create_time,'processes_running',processes_running,'critical')
                    elif int(processes_running)>=int(threshold_warning_processes_running):
                        #send_mail = func.update_send_mail_status(server_id,db_type,'processes_running',send_mail,send_mail_max_count)
                        #send_sms = func.update_send_sms_status(server_id,db_type,'processes_running',send_sms,send_sms_max_count)
                        #func.add_alert(server_id,tags,host,port,create_time,db_type,'processes_running',processes_running,'critical','too many processes running',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('actives',2,server_id, host, db_type,create_time,'processes_running',processes_running,'warning')
                    else:
                        func.update_db_status('actives',1,server_id, host, db_type,create_time,'processes_running',processes_running,'ok')
                        func.check_if_ok(server_id,tags,host,port,create_time,db_type,'processes_running',processes_running,'processes running ok',send_mail,send_mail_to_list,send_sms,send_sms_to_list)

                if int(alarm_processes_waits)==1:
                    if int(processes_waits)>=int(threshold_critical_processes_waits):
                        #send_mail = func.update_send_mail_status(server_id,db_type,'processes_waits',send_mail,send_mail_max_count)
                        #send_sms = func.update_send_sms_status(server_id,db_type,'processes_waits',send_sms,send_sms_max_count)
                        #func.add_alert(server_id,tags,host,port,create_time,db_type,'processes_waits',processes_waits,'critical','too many processes waits',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('waits',3,server_id, host, db_type,create_time,'processes_waits',processes_waits,'critical')
                    elif int(processes_waits)>=int(threshold_warning_processes_waits):
                        #send_mail = func.update_send_mail_status(server_id,db_type,'processes_waits',send_mail,send_mail_max_count)
                        #send_sms = func.update_send_sms_status(server_id,db_type,'processes_waits',send_sms,send_sms_max_count)
                        #func.add_alert(server_id,tags,host,port,create_time,db_type,'processes_waits',processes_waits,'warning','too many processes waits',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('waits',2,server_id, host, db_type,create_time,'processes_waits',processes_waits,'warning')
                    else:
                        func.update_db_status('waits',1,server_id, host, db_type,create_time,'processes_waits',processes_waits,'ok')
                        func.check_if_ok(server_id,tags,host,port,create_time,db_type,'processes_waits',processes_waits,'processes waits ok',send_mail,send_mail_to_list,send_sms,send_sms_to_list)

    else:
       pass

       
       
def gen_alert_sqlserver_mirror(server_id, mirror_role):
    if g_alert != "1":
        return -1
        
    sql = """SELECT a.server_id,
									a.connect,
									a.create_time,
									a.host,
									a.port,
									b.send_mail,
									b.send_mail_to_list,
									b.send_sms,
									b.send_sms_to_list,
									b.tags,
									'sqlserver' AS db_type
								FROM sqlserver_status a, db_cfg_sqlserver b
								WHERE a.server_id = b.id 
									and a.server_id = %s """ %(server_id)
    result=func.mysql_query(sql)
    if result <> 0:
        for line in result:
            server_id=line[0]
            connect=line[1]
            create_time=line[2]
            host=line[3]
            port=line[4]
            send_mail=line[5]
            send_mail_to_list=line[6]
            send_sms=line[7]
            send_sms_to_list=line[8]
            tags=line[9]
            db_type=line[10]
        
            if send_mail_to_list is None or  send_mail_to_list.strip()=='':
                send_mail_to_list = mail_to_list_common
            if send_sms_to_list is None or  send_sms_to_list.strip()=='':
                send_sms_to_list = sms_to_list_common
                

            if mirror_role==1:
                send_mail = func.update_send_mail_status(server_id,db_type,'mirroring_role',send_mail,send_mail_max_count)
                send_sms  = func.update_send_sms_status(server_id,db_type,'mirroring_role',send_sms,send_sms_max_count)
                func.add_alert(server_id,tags,host,port,create_time,db_type,'mirroring_role',mirror_role,'critical','Database role is NOT match!',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                func.update_db_status('repl',3,server_id, host, db_type,create_time,'mirroring_role',mirror_role,'critical')
            else:
                func.check_if_ok(server_id,tags,host,port,create_time,db_type,'mirroring_role',mirror_role,'Database role is OK!',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                func.update_db_status('repl',1,server_id, host, db_type,create_time,'mirroring_role',mirror_role,'ok')
    else:
       pass








