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
logger = logging.getLogger("wlblazers")
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
def gen_alert_oracle_status(server_id):
    if g_alert != "1":
        return -1
        
    sql = """SELECT a.server_id,
										a.connect,
										a.session_total,
										a.session_actives,
										a.session_waits,
										CONVERT(a.flashback_space_used, DECIMAL(10,2)) as flashback_space_used,
										a.database_role,
										a.dg_stats,
										a.dg_delay,
										a.startup_time,
										a.create_time,
										b.HOST,
										b.PORT,
										b.alarm_session_total,
										b.alarm_session_actives,
										b.alarm_session_waits,
									  b.alarm_fb_space,
										b.threshold_warning_session_total,
										b.threshold_critical_session_total,
										b.threshold_warning_session_actives,
										b.threshold_critical_session_actives,
										b.threshold_warning_session_waits,
										b.threshold_critical_session_waits,
										b.threshold_warning_fb_space,
										b.threshold_critical_fb_space,
										b.send_mail,
										b.send_mail_to_list,
										b.send_sms,
										b.send_sms_to_list,
										b.tags,
										'oracle' AS db_type
									FROM oracle_status a, db_cfg_oracle b
									WHERE a.server_id = b.id
									  and a.server_id = %s """ %(server_id)
    result=func.mysql_query(sql)
    if result <> 0:
        for line in result:
            server_id=line[0]
            connect=line[1]
            session_total=line[2]
            session_actives=line[3]
            session_waits=line[4]
            flashback_space_used=line[5]
            database_role=line[6]
            mrp_status=line[7]
            dg_delay=line[8]
            startup_time=line[9]
            create_time=line[10]
            host=line[11]
            port=line[12]
            alarm_session_total=line[13]
            alarm_session_actives=line[14]
            alarm_session_waits=line[15]
            alarm_fb_space=line[16]
            threshold_warning_session_total=line[17]
            threshold_critical_session_total=line[18]
            threshold_warning_session_actives=line[19]
            threshold_critical_session_actives=line[20]
            threshold_warning_session_waits=line[21]
            threshold_critical_session_waits=line[22]
            threshold_warning_fb_space=line[23]
            threshold_critical_fb_space=line[24]
            send_mail=line[25]
            send_mail_to_list=line[26]
            send_sms=line[27]
            send_sms_to_list=line[28]
            tags=line[29]
            db_type=line[30]
        
            if send_mail_to_list is None or  send_mail_to_list.strip()=='':
                send_mail_to_list = mail_to_list_common
            if send_sms_to_list is None or  send_sms_to_list.strip()=='':
                send_sms_to_list = sms_to_list_common
                
            if connect <> 1:
                send_mail = func.update_send_mail_status(server_id,db_type,'connect',send_mail,send_mail_max_count)
                send_sms  = func.update_send_sms_status(server_id,db_type,'connect',send_sms,send_sms_max_count)
                func.add_alert(server_id,tags,host,port,create_time,db_type,'connect','down','critical','oracle server down',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                func.update_db_status('connect','3',host,port,create_time,'connect','down','critical')
                func.update_db_status('sessions','-1',host,port,'','','','')
                func.update_db_status('actives','-1',host,port,'','','','')
                func.update_db_status('waits','-1',host,port,'','','','')
                func.update_db_status('repl','-1',host,port,'','','','')
                func.update_db_status('repl_delay','-1',host,port,'','','','')
            else:
                func.check_if_ok(server_id,tags,host,port,create_time,db_type,'connect','up','oracle server up',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                func.update_db_status('connect','1',host,port,create_time,'connect','up','ok')
                
                # 数据库角色变化告警
                sql= "select database_role from oracle_status_history s where s.server_id = %s order by id desc limit 1;" %(server_id)
                last_role=func.mysql_single_query(sql)
                if last_role:
                    if last_role != database_role:
                        msg = "database role changed from %s to %s" %(last_role, database_role)
                        send_mail = func.update_send_mail_status(server_id,db_type,'role_changed',send_mail,send_mail_max_count)
                        send_sms  = func.update_send_sms_status(server_id,db_type,'role_changed',send_sms,send_sms_max_count)
                        func.add_alert(server_id,tags,host,port,create_time,db_type,'role_changed',session_total,'warning',msg,send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                    

                # 数据库重启告警
                sql= "select startup_time from oracle_status_history s where s.server_id = %s order by id desc limit 1;" %(server_id)
                last_startup=func.mysql_single_query(sql)
                if last_startup:
                    if last_startup != startup_time:
                        msg = "database on %s has been restarted" %(host)   
                        send_mail = func.update_send_mail_status(server_id,db_type,'db_restarted',send_mail,send_mail_max_count)
                        send_sms  = func.update_send_sms_status(server_id,db_type,'db_restarted',send_sms,send_sms_max_count)                                             
                        func.add_alert(server_id,tags,host,port,create_time,db_type,'db_restarted',session_total,'warning',msg,send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        
                        
                if int(alarm_session_total)==1:
                    if int(session_total) >= int(threshold_critical_session_total):
                        send_mail = func.update_send_mail_status(server_id,db_type,'session_total',send_mail,send_mail_max_count)
                        send_sms  = func.update_send_sms_status(server_id,db_type,'session_total',send_sms,send_sms_max_count)
                        func.add_alert(server_id,tags,host,port,create_time,db_type,'session_total',session_total,'critical','too many sessions',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('sessions',3,host,port,create_time,'session_total',session_total,'critical')
                    elif int(session_total) >= int(threshold_warning_session_total):
                        send_mail = func.update_send_mail_status(server_id,db_type,'session_total',send_mail,send_mail_max_count)
                        send_sms  = func.update_send_sms_status(server_id,db_type,'session_total',send_sms,send_sms_max_count)
                        func.add_alert(server_id,tags,host,port,create_time,db_type,'session_total',session_total,'warning','too many sessions',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('sessions',2,host,port,create_time,'session_total',session_total,'warning')
                    else:
                        func.check_if_ok(server_id,tags,host,port,create_time,db_type,'session_total',session_total,'sessions ok',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('sessions',1,host,port,create_time,'session_total',session_total,'ok')
        
                if int(alarm_session_actives)==1:
                    if int(session_actives) >= int(threshold_critical_session_actives):
                        send_mail = func.update_send_mail_status(server_id,db_type,'session_actives',send_mail,send_mail_max_count)
                        send_sms  = func.update_send_sms_status(server_id,db_type,'session_actives',send_sms,send_sms_max_count)
                        func.add_alert(server_id,tags,host,port,create_time,db_type,'session_actives',session_actives,'critical','too many active sessions',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('actives',3,host,port,create_time,'session_actives',session_actives,'critical')
                    elif int(session_actives) >= int(threshold_warning_session_actives): 
                        send_mail = func.update_send_mail_status(server_id,db_type,'session_actives',send_mail,send_mail_max_count)
                        send_sms  = func.update_send_sms_status(server_id,db_type,'session_actives',send_sms,send_sms_max_count)
                        func.add_alert(server_id,tags,host,port,create_time,db_type,'session_actives',session_actives,'warning','too many active sessions',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('actives',2,host,port,create_time,'session_actives',session_actives,'warning')
                    else:
                        func.check_if_ok(server_id,tags,host,port,create_time,db_type,'session_actives',session_actives,'active sessions ok',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('actives',1,host,port,create_time,'session_actives',session_actives,'ok')

                if int(alarm_session_waits)==1:
                    if int(session_waits) >= int(threshold_critical_session_waits):
                        send_mail = func.update_send_mail_status(server_id,db_type,'session_waits',send_mail,send_mail_max_count)
                        send_sms  = func.update_send_sms_status(server_id,db_type,'session_waits',send_sms,send_sms_max_count)
                        func.add_alert(server_id,tags,host,port,create_time,db_type,'session_waits',session_waits,'critical','too many waits sessions',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('waits',3,host,port,create_time,'session_waits',session_waits,'critical')
                    elif int(session_waits) >= int(threshold_warning_session_waits):
                        send_mail = func.update_send_mail_status(server_id,db_type,'session_waits',send_mail,send_mail_max_count)
                        send_sms  = func.update_send_sms_status(server_id,db_type,'session_waits',send_sms,send_sms_max_count)
                        func.add_alert(server_id,tags,host,port,create_time,db_type,'session_waits',session_waits,'warning','too many waits sessions',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('waits',2,host,port,create_time,'session_waits',session_waits,'warning')
                    else:                        
                        func.check_if_ok(server_id,tags,host,port,create_time,db_type,'session_waits',session_waits,'waits sessions ok',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('waits',1,host,port,create_time,'session_waits',session_waits,'ok')
	
                if int(alarm_fb_space)==1:
                    if int(flashback_space_used) >= int(threshold_critical_fb_space):
                        send_mail = func.update_send_mail_status(server_id,db_type,'flashback_space_used',send_mail,send_mail_max_count)
                        send_sms  = func.update_send_sms_status(server_id,db_type,'flashback_space_used',send_sms,send_sms_max_count)
                        func.add_alert(server_id,tags,host,port,create_time,db_type,'flashback_space_used',flashback_space_used,'critical','flashback space usage reach %s'%(flashback_space_used),send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('waits',3,host,port,create_time,'flashback_space_used',flashback_space_used,'critical')
                    elif int(flashback_space_used) >= int(threshold_warning_fb_space):
                        send_mail = func.update_send_mail_status(server_id,db_type,'flashback_space_used',send_mail,send_mail_max_count)
                        send_sms  = func.update_send_sms_status(server_id,db_type,'flashback_space_used',send_sms,send_sms_max_count)
                        func.add_alert(server_id,tags,host,port,create_time,db_type,'flashback_space_used',flashback_space_used,'warning','flashback space usage reach %s'%(flashback_space_used),send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('flashback_space',2,host,port,create_time,'flashback_space_used',flashback_space_used,'warning')
                    else:                        
                        func.check_if_ok(server_id,tags,host,port,create_time,db_type,'flashback_space_used',flashback_space_used,'flashback space ok',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('flashback_space',1,host,port,create_time,'flashback_space_used',flashback_space_used,'ok')
                        
    else:
       pass



def gen_alert_oracle_dg(server_id):
    if g_alert != "1":
        return -1
     
    sql = """SELECT a.server_id,
										a.connect,
										a.database_role,
										a.dg_stats,
										a.dg_delay,
										a.create_time,
										b.HOST,
										b.PORT,
										b.send_mail,
										b.send_mail_to_list,
										b.send_sms,
										b.send_sms_to_list,
										b.tags,
										'oracle' AS db_type
									FROM oracle_status a, db_cfg_oracle b
									WHERE a.server_id = b.id
									  and a.server_id = %s """ %(server_id)
    result=func.mysql_query(sql)
    if result <> 0:
        for line in result:
            server_id=line[0]
            connect=line[1]
            database_role=line[2]
            mrp_status=line[3]
            dg_delay=line[4]
            create_time=line[5]
            host=line[6]
            port=line[7]
            send_mail=line[8]
            send_mail_to_list=line[9]
            send_sms=line[10]
            send_sms_to_list=line[11]
            tags=line[12]
            db_type=line[13]
        
            if send_mail_to_list is None or  send_mail_to_list.strip()=='':
                send_mail_to_list = mail_to_list_common
            if send_sms_to_list is None or  send_sms_to_list.strip()=='':
                send_sms_to_list = sms_to_list_common
                
            if connect <> 1:
                send_mail = func.update_send_mail_status(server_id,db_type,'connect',send_mail,send_mail_max_count)
                send_sms = func.update_send_sms_status(server_id,db_type,'connect',send_sms,send_sms_max_count) 
                func.add_alert(server_id,tags,host,port,create_time,db_type,'connect','down','critical','oracle server down',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                func.update_db_status('connect','3',host,port,create_time,'connect','down','critical')
                func.update_db_status('sessions','-1',host,port,'','','','')
                func.update_db_status('actives','-1',host,port,'','','','')
                func.update_db_status('waits','-1',host,port,'','','','')
                func.update_db_status('repl','-1',host,port,'','','','')
                func.update_db_status('repl_delay','-1',host,port,'','','','')
            else:
                func.check_if_ok(server_id,tags,host,port,create_time,db_type,'connect','up','oracle server up',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                func.update_db_status('connect','1',host,port,create_time,'connect','up','ok')
                
                if database_role=="PHYSICAL STANDBY":
                    #print "dg_delay: %s" %(dg_delay)
                    if int(dg_delay) >= 1800:
                        if int(dg_delay) >= 3600*3:
                            send_mail = func.update_send_mail_status(server_id,db_type,'repl_delay',send_mail,send_mail_max_count)
                            send_sms  = func.update_send_sms_status(server_id,db_type,'repl_delay',send_sms,send_sms_max_count)
                            func.add_alert(server_id,tags,host,port,create_time,db_type,'repl_delay',dg_delay,'critical','replication delay more than 3 hours',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                            func.update_db_status('repl_delay',3,host,port,create_time,'repl_delay',dg_delay,'critical')
                        elif int(dg_delay) >= 1800:
                            send_mail = func.update_send_mail_status(server_id,db_type,'repl_delay',send_mail,send_mail_max_count)
                            send_sms  = func.update_send_sms_status(server_id,db_type,'repl_delay',send_sms,send_sms_max_count)
                            func.add_alert(server_id,tags,host,port,create_time,db_type,'repl_delay',dg_delay,'warning','replication delay more than 30 minutes',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                            func.update_db_status('repl_delay',3,host,port,create_time,'repl_delay',dg_delay,'warning')
                    else:    
                        func.check_if_ok(server_id,tags,host,port,create_time,db_type,'repl_delay',dg_delay,'replication delay ok',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('repl_delay',1,host,port,create_time,'repl_delay',dg_delay,'ok')
                        	
                        	   
                    if int(mrp_status) < 1:
                        send_mail = func.update_send_mail_status(server_id,db_type,'mrp_status',send_mail,send_mail_max_count)
                        send_sms  = func.update_send_sms_status(server_id,db_type,'mrp_status',send_sms,send_sms_max_count)
                        func.add_alert(server_id,tags,host,port,create_time,db_type,'mrp_status',mrp_status,'warning','MRP process is down',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('repl',2,host,port,create_time,'mrp_status',mrp_status,'warning')
                    else:
                        func.check_if_ok(server_id,tags,host,port,create_time,db_type,'mrp_status',mrp_status,'MRP process is up',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('repl',1,host,port,create_time,'repl',mrp_status,'ok')
    else:
       pass



def gen_alert_oracle_tablespace(server_id):
    if g_alert != "1":
        return -1
        
    sql = """SELECT a.server_id,
										a.tablespace_name,
										a.total_size,
										a.used_size,
										CONVERT(a.max_rate, DECIMAL(6,2)) as max_rate,
										a.create_time,
										b. HOST,
										b. PORT,
										b.alarm_tablespace,
										b.threshold_warning_tablespace,
										b.threshold_critical_tablespace,
										b.filter_tbs,
										b.send_mail,
										b.send_mail_to_list,
										b.send_sms,
										b.send_sms_to_list,
										b.tags,
										'oracle' AS db_type
						FROM oracle_tablespace a, db_cfg_oracle b
						WHERE a.server_id = b.id
							and a.server_id = %s
						ORDER BY max_rate desc """ %(server_id)
    result=func.mysql_query(sql)
    if result <> 0:
        for line in result:
            server_id=line[0]
            tablespace_name=line[1]
            total_size=line[2]
            used_size=line[3]
            max_rate=line[4]
            create_time=line[5]
            host=line[6]
            port=line[7]
            alarm_tablespace=line[8]
            threshold_warning_tablespace=line[9]
            threshold_critical_tablespace=line[10]
            filter_tbs=line[11]
            send_mail=line[12]
            send_mail_to_list=line[13]
            send_sms=line[14]
            send_sms_to_list=line[15]
            tags=line[16]
            db_type=line[17]

            
            if send_mail_to_list is None or  send_mail_to_list.strip()=='':
                send_mail_to_list = mail_to_list_common
            if send_sms_to_list is None or  send_sms_to_list.strip()=='':
                send_sms_to_list = sms_to_list_common
            
            
            # 验证是否属于过滤表空间
            is_skip = 0
            if filter_tbs:
                list_tbs = filter_tbs.split(',');
                if list_tbs:
                    for i in range(len(list_tbs)):
                        if tablespace_name == list_tbs[i].upper():
                            is_skip = 1
                            break

            if int(alarm_tablespace)==1 and is_skip == 0:
                if int(max_rate) >= int(threshold_critical_tablespace):
                    send_mail = func.update_send_mail_status(server_id,db_type,'tablespace(%s)' %(tablespace_name),send_mail,send_mail_max_count)
                    send_sms  = func.update_send_sms_status(server_id,db_type,'tablespace(%s)' %(tablespace_name),send_sms,send_sms_max_count)
                    func.add_alert(server_id,tags,host,port,create_time,db_type,'tablespace(%s)' %(tablespace_name),max_rate,'critical','tablespace %s usage reach %s' %(tablespace_name,max_rate),send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                    func.update_db_status('tablespace',3,host,port,create_time,'tablespace(%s)' %(tablespace_name),max_rate,'critical')
                elif int(max_rate) >= int(threshold_warning_tablespace):
                    send_mail = func.update_send_mail_status(server_id,db_type,'tablespace(%s)' %(tablespace_name),send_mail,send_mail_max_count)
                    send_sms  = func.update_send_sms_status(server_id,db_type,'tablespace(%s)' %(tablespace_name),send_sms,send_sms_max_count)
                    func.add_alert(server_id,tags,host,port,create_time,db_type,'tablespace(%s)' %(tablespace_name),max_rate,'warning','tablespace %s usage reach %s' %(tablespace_name,max_rate),send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                    func.update_db_status('tablespace',2,host,port,create_time,'tablespace(%s)' %(tablespace_name),max_rate,'warning')
                else:
                    func.check_if_ok(server_id,tags,host,port,create_time,db_type,'tablespace(%s)' %(tablespace_name),max_rate,'tablespace %s usage ok' %(tablespace_name),send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                    func.update_db_status('tablespace',1,host,port,create_time,'tablespace','max(%s:%s)' %(tablespace_name,max_rate),'ok')
    else:
       pass



def gen_alert_oracle_diskgroup(server_id):
    if g_alert != "1":
        return -1
        
    sql = """SELECT a.server_id,
										a.diskgroup_name,
										a.total_mb,
										a.free_mb,
										CONVERT(a.used_rate, DECIMAL(5,2)) as used_rate,
										a.create_time,
										b.HOST,
										b.PORT,
										b.alarm_asm_space,
										b.threshold_warning_asm_space,
										b.threshold_critical_asm_space,
										b.send_mail,
										b.send_mail_to_list,
										b.send_sms,
										b.send_sms_to_list,
										b.tags,
										'oracle' AS db_type
						FROM oracle_diskgroup a, db_cfg_oracle b
						WHERE a.server_id = b.id
							and a.server_id = %s
							and CONVERT(a.used_rate, DECIMAL(5,2)) >= b.threshold_warning_asm_space
						ORDER BY used_rate desc """ %(server_id)
    result=func.mysql_query(sql)
    if result <> 0:
        for line in result:
            #print "diskgroup_name: %s" %(line[1])
            server_id=line[0]
            diskgroup_name=line[1]
            total_mb=line[2]
            free_mb=line[3]
            used_rate=line[4]
            create_time=line[5]
            host=line[6]
            port=line[7]
            alarm_asm_space=line[8]
            threshold_warning_asm_space=line[9]
            threshold_critical_asm_space=line[10]
            send_mail=line[11]
            send_mail_to_list=line[12]
            send_sms=line[13]
            send_sms_to_list=line[14]
            tags=line[15]
            db_type=line[16]
            
            if send_mail_to_list is None or  send_mail_to_list.strip()=='':
                send_mail_to_list = mail_to_list_common
            if send_sms_to_list is None or  send_sms_to_list.strip()=='':
                send_sms_to_list = sms_to_list_common
                
            if int(alarm_asm_space)==1:
                if int(used_rate) >= int(threshold_critical_asm_space):
                    send_mail = func.update_send_mail_status(server_id,db_type,'diskgroup(%s)' %(diskgroup_name),send_mail,send_mail_max_count)
                    send_sms  = func.update_send_sms_status(server_id,db_type,'diskgroup(%s)' %(diskgroup_name),send_sms,send_sms_max_count)
                    func.add_alert(server_id,tags,host,port,create_time,db_type,'diskgroup(%s)' %(diskgroup_name),used_rate,'critical','diskgroup %s usage reach %s' %(diskgroup_name,used_rate),send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                    func.update_db_status('diskgroup',3,host,port,create_time,'diskgroup(%s)' %(diskgroup_name),used_rate,'critical')
                elif int(used_rate) >= int(threshold_warning_asm_space):
                    send_mail = func.update_send_mail_status(server_id,db_type,'diskgroup(%s)' %(diskgroup_name),send_mail,send_mail_max_count)
                    send_sms  = func.update_send_sms_status(server_id,db_type,'diskgroup(%s)' %(diskgroup_name),send_sms,send_sms_max_count)
                    func.add_alert(server_id,tags,host,port,create_time,db_type,'diskgroup(%s)' %(diskgroup_name),used_rate,'warning','diskgroup %s usage reach %s' %(diskgroup_name,used_rate),send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                    func.update_db_status('diskgroup',2,host,port,create_time,'diskgroup(%s)' %(diskgroup_name),used_rate,'warning')
                else:
                    func.check_if_ok(server_id,tags,host,port,create_time,db_type,'diskgroup(%s)' %(diskgroup_name),used_rate,'tablespace %s usage ok' %(diskgroup_name),send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                    func.update_db_status('diskgroup',1,host,port,create_time,'diskgroup','max(%s:%s)' %(diskgroup_name,used_rate),'ok')
    else:
       pass
       
