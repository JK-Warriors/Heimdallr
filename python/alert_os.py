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
logger = logging.getLogger("alert_os")
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
def gen_alert_os_status(os_ip):
    if g_alert != "1":
        return -1
        
    sql = """SELECT a.ip,
										a.hostname,
										a.connect,
										a.process,
										a.load_1,
										a.cpu_idle_time,
										a.mem_usage_rate,
										a.create_time,
										b.tags,
										b.alarm_os_process,
										b.alarm_os_load,
										b.alarm_os_cpu,
										b.alarm_os_memory,
										b.threshold_warning_os_process,
										b.threshold_critical_os_process,
										b.threshold_warning_os_load,
										b.threshold_critical_os_load,
										b.threshold_warning_os_cpu,
										b.threshold_critical_os_cpu,
										b.threshold_warning_os_memory,
										b.threshold_critical_os_memory,
										b.send_mail,
										b.send_mail_to_list,
										b.send_sms,
										b.send_sms_to_list
									FROM os_status a, db_cfg_os b
									WHERE a.ip = b.host
									  and a.ip = '%s' """ %(os_ip)
    result=func.mysql_query(sql)
    if result <> 0:
        for line in result:
            host=line[0]
            hostname=line[1]
            connect=line[2]
            process=line[3]
            load_1=line[4]
            cpu_idle=line[5]
            memory_usage=line[6]
            create_time=line[7]
            tags=line[8]
            alarm_os_process=line[9]
            alarm_os_load=line[10]
            alarm_os_cpu=line[11]
            alarm_os_memory=line[12]
            threshold_warning_os_process=line[13]
            threshold_critical_os_process=line[14]
            threshold_warning_os_load=line[15]
            threshold_critical_os_load=line[16]
            threshold_warning_os_cpu=line[17]
            threshold_critical_os_cpu=line[18]
            threshold_warning_os_memory=line[19]
            threshold_critical_os_memory=line[20]
            send_mail=line[21]
            send_mail_to_list=line[22]
            send_sms=line[23]
            send_sms_to_list=line[24]

            server_id=0
            tags=tags
            db_type="os"
            port=''

            if send_mail_to_list is None or  send_mail_to_list.strip()=='':
                send_mail_to_list = mail_to_list_common
            if send_sms_to_list is None or  send_sms_to_list.strip()=='':
                send_sms_to_list = sms_to_list_common
                
            if connect <> 1:
                send_mail = func.update_send_mail_status(host,db_type,'connect_server',send_mail,send_mail_max_count)
                send_sms = func.update_send_sms_status(host,db_type,'connect_server',send_sms,send_sms_max_count)
                func.add_alert(server_id,tags,host,port,create_time,db_type,'connect_server','down','critical','connect server fail',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                func.update_db_status('connect','3',server_id, host, db_type,create_time,'connect_server','down','critical')
                func.update_db_status('process','-1',server_id, host, db_type,'','','','')
                func.update_db_status('load_1','-1',server_id, host, db_type,'','','','')
                func.update_db_status('cpu','-1',server_id, host, db_type,'','','','')
                func.update_db_status('memory','-1',server_id, host, db_type,'','','','')
                func.update_db_status('network','-1',server_id, host, db_type,'','','','')
                func.update_db_status('disk','-1',server_id, host, db_type,'','','','')
            else:
                func.check_if_ok(server_id,tags,host,port,create_time,db_type,'connect_server','up','connect server success',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                func.update_db_status('connect',1,server_id, host, db_type,create_time,'connect_server','up','ok')

                if int(alarm_os_process)==1:
                    if int(process) >= int(threshold_critical_os_process):
                        send_mail = func.update_send_mail_status(host,db_type,'process',send_mail,send_mail_max_count)
                        send_sms = func.update_send_sms_status(host,db_type,'process',send_sms,send_sms_max_count)
                        func.add_alert(server_id,tags,host,port,create_time,db_type,'process',process,'critical','too more process running',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('process',3,server_id, host, db_type,create_time,'process',process,'critical')
                    elif int(process) >= int(threshold_warning_os_process):
                        send_mail = func.update_send_mail_status(host,db_type,'process',send_mail,send_mail_max_count)
                        send_sms = func.update_send_sms_status(host,db_type,'process',send_sms,send_sms_max_count)
                        func.add_alert(server_id,tags,host,port,create_time,db_type,'process',process,'warning','too more process running',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('process',2,server_id, host, db_type,create_time,'process',process,'warning')
                    else:
                        func.update_db_status('process',1,server_id, host, db_type,create_time,'process',process,'ok')
                        func.check_if_ok(server_id,tags,host,port,create_time,db_type,'process',process,'process running ok',send_mail,send_mail_to_list,send_sms,send_sms_to_list)

                if int(alarm_os_load)==1:
                    if int(load_1) >= int(threshold_critical_os_load):
                        send_mail = func.update_send_mail_status(host,db_type,'load',send_mail,send_mail_max_count)
                        send_sms = func.update_send_sms_status(host,db_type,'load',send_sms,send_sms_max_count)
                        func.add_alert(server_id,tags,host,port,create_time,db_type,'load',load_1,'critical','too high load',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('load_1',3,server_id, host, db_type,create_time,'load',load_1,'critical')
                    elif int(load_1) >= int(threshold_warning_os_load):
                        send_mail = func.update_send_mail_status(server_id,db_type,'load',send_mail,send_mail_max_count)
                        send_sms = func.update_send_sms_status(host,db_type,'load',send_sms,send_sms_max_count)
                        func.add_alert(server_id,tags,host,port,create_time,db_type,'load',load_1,'warning','too high load',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('load_1',2,server_id, host, db_type,create_time,'load',load_1,'warning')
                    else:
                        func.check_if_ok(server_id,tags,host,port,create_time,db_type,'load',load_1,'load ok',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('load_1',1,server_id, host, db_type,create_time,'load',load_1,'ok')

                if int(alarm_os_cpu)==1:
                    threshold_critical_os_cpu = int(100-threshold_critical_os_cpu)
                    threshold_warning_os_cpu = int(100-threshold_warning_os_cpu)
                    if int(cpu_idle) <= int(threshold_critical_os_cpu):
                        send_mail = func.update_send_mail_status(host,db_type,'cpu_idle',send_mail,send_mail_max_count)
                        send_sms = func.update_send_sms_status(host,db_type,'cpu_idle',send_sms,send_sms_max_count)
                        func.add_alert(server_id,tags,host,port,create_time,db_type,'cpu_idle',str(cpu_idle)+'%','critical','too little cpu idle',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('cpu',3,server_id, host, db_type,create_time,'cpu_idle',str(cpu_idle)+'%','critical')
                    elif int(cpu_idle) <= int(threshold_warning_os_cpu):
                        send_mail = func.update_send_mail_status(host,db_type,'cpu_idle',send_mail,send_mail_max_count)
                        send_sms = func.update_send_sms_status(host,db_type,'cpu_idle',send_sms,send_sms_max_count)
                        func.add_alert(server_id,tags,host,port,create_time,db_type,'cpu_idle',str(cpu_idle)+'%','warning','too little cpu idle',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('cpu',2,server_id, host, db_type,create_time,'cpu_idle',str(cpu_idle)+'%','warning')
                    else:
                        func.check_if_ok(server_id,tags,host,port,create_time,db_type,'cpu_idle',str(cpu_idle)+'%','cpu idle ok',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('cpu',1,server_id, host, db_type,create_time,'cpu_idle',str(cpu_idle)+'%','ok')

                if int(alarm_os_memory)==1:
                    if memory_usage:
                        memory_usage_int = int(memory_usage.split('%')[0])
                    else:
                        memory_usage_int = 0 
                    if int(memory_usage_int) >= int(threshold_critical_os_memory):
                        send_mail = func.update_send_mail_status(host,db_type,'memory',send_mail,send_mail_max_count)
                        send_sms = func.update_send_sms_status(host,db_type,'memory',send_sms,send_sms_max_count)
                        func.add_alert(server_id,tags,host,port,create_time,db_type,'memory',memory_usage,'critical','too more memory usage',send_mail,send_mail_to_list,send_sms,send_sms_to_list) 
                        func.update_db_status('memory',3,server_id, host, db_type,create_time,'memory',memory_usage,'critical')
                    elif int(memory_usage_int) >= int(threshold_warning_os_memory):
                        send_mail = func.update_send_mail_status(host,db_type,'memory',send_mail,send_mail_max_count)
                        send_sms = func.update_send_sms_status(host,db_type,'memory',send_sms,send_sms_max_count)
                        func.add_alert(server_id,tags,host,port,create_time,db_type,'memory',memory_usage,'warning','too more memory usage',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('memory',2,server_id, host, db_type,create_time,'memory',memory_usage,'warning')
                    else:
                        func.check_if_ok(server_id,tags,host,port,create_time,db_type,'memory',memory_usage,'memory usage ok',send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                        func.update_db_status('memory',1,server_id, host, db_type,create_time,'memory',memory_usage,'ok') 


    else:
       pass



def gen_alert_os_disk(os_ip):
    if g_alert != "1":
        return -1
        
    sql="""SELECT a.ip,
									a.mounted,
									a.used_rate,
									a.create_time,
									b.tags,
									b.alarm_os_disk,
									b.threshold_warning_os_disk,
									b.threshold_critical_os_disk,
									b.send_mail,
									b.send_mail_to_list,
									b.send_sms,
									b.send_sms_to_list
								FROM os_disk a, db_cfg_os b
								WHERE a.ip = b.host
								 AND a.ip = '%s' """ %(os_ip)
    result=func.mysql_query(sql)
    if result <> 0:
        for line in result:
            host=line[0]
            mounted=line[1]
            used_rate=line[2]
            create_time=line[3]
            tags=line[4]
            alarm_os_disk=line[5]
            threshold_warning_os_disk=line[6]
            threshold_critical_os_disk=line[7]
            send_mail=line[8]
            send_mail_to_list=line[9]
            send_sms=line[10]
            send_sms_to_list=line[11]

            server_id=0
            tags=tags
            db_type="os"
            port=''

            used_rate_arr=used_rate.split("%")
            used_rate_int=int(used_rate_arr[0])
            
            if send_mail_to_list is None or  send_mail_to_list.strip()=='':
                send_mail_to_list = mail_to_list_common
            if send_sms_to_list is None or  send_sms_to_list.strip()=='':
                send_sms_to_list = sms_to_list_common
                
            if int(alarm_os_disk)==1:
                #logger.info('disk_usage(%s)' %(mounted))
                if int(used_rate_int) >= int(threshold_critical_os_disk):
                    send_mail = func.update_send_mail_status(host,db_type,'disk_usage(%s)' %(mounted),send_mail,send_mail_max_count)
                    send_sms = func.update_send_sms_status(host,db_type,'disk_usage(%s)' %(mounted),send_sms,send_sms_max_count)
                    func.add_alert(server_id,tags,host,port,create_time,db_type,'disk_usage(%s)' %(mounted),used_rate,'critical','disk %s usage reach %s' %(mounted,used_rate),send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                    func.update_db_status('disk',3,server_id, host, db_type,create_time,'disk_usage(%s)' %(mounted),used_rate,'critical')
                elif int(used_rate_int) >= int(threshold_warning_os_disk):
                    send_mail = func.update_send_mail_status(host,db_type,'disk_usage(%s)' %(mounted),send_mail,send_mail_max_count)
                    send_sms = func.update_send_sms_status(host,db_type,'disk_usage(%s)' %(mounted),send_sms,send_sms_max_count)
                    func.add_alert(server_id,tags,host,port,create_time,db_type,'disk_usage(%s)' %(mounted),used_rate,'warning','disk %s usage reach %s' %(mounted,used_rate),send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                    func.update_db_status('disk',2,server_id, host, db_type,create_time,'disk_usage(%s)' %(mounted),used_rate,'warning')
                else:
                    func.check_if_ok(server_id,tags,host,port,create_time,db_type,'disk_usage(%s)' %(mounted),used_rate,'disk %s usage ok' %(mounted),send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                    func.update_db_status('disk',1,server_id, host, db_type,create_time,'disk_usage','max(%s:%s)' %(mounted,used_rate),'ok')
    else:
       pass


def gen_alert_os_network(os_ip):
    if g_alert != "1":
        return -1
        
    sql="""SELECT a.ip,
									a.if_descr,
									a.in_bytes,
									a.out_bytes,
									sum(in_bytes + out_bytes) sum_bytes,
									a.create_time,
									b.tags,
									b.alarm_os_network,
									b.threshold_warning_os_network,
									b.threshold_critical_os_network,
									b.send_mail,
									b.send_mail_to_list,
									b.send_sms,
									b.send_sms_to_list
								FROM os_net a, db_cfg_os b
								WHERE a.ip = b.host
								 AND a.ip = '%s'
								GROUP BY ip, if_descr
								ORDER BY sum(in_bytes + out_bytes) ASC """ %(os_ip)
    result=func.mysql_query(sql)
    if result <> 0:
        for line in result:
            host=line[0]
            if_descr=line[1]
            in_bytes=line[2]
            out_bytes=line[3]
            sum_bytes=line[4]
            create_time=line[5]
            tags=line[6]
            alarm_os_network=line[7]
            threshold_warning_os_network=(line[8])*1024*1024
            threshold_critical_os_network=(line[9])*1024*1024
            send_mail=line[10]
            send_mail_to_list=line[11]
            send_sms=line[12]
            send_sms_to_list=line[13]

            server_id=0
            tags=tags
            db_type="os"
            port=''

            if send_mail_to_list is None or  send_mail_to_list.strip()=='':
                send_mail_to_list = mail_to_list_common
            if send_sms_to_list is None or  send_sms_to_list.strip()=='':
                send_sms_to_list = sms_to_list_common
                
            if int(alarm_os_network)==1:
                if int(sum_bytes) >= int(threshold_critical_os_network):
                    send_mail = func.update_send_mail_status(host,db_type,'network(%s)' %(if_descr),send_mail,send_mail_max_count)
                    send_sms = func.update_send_sms_status(host,db_type,'network(%s)' %(if_descr),send_sms,send_sms_max_count)
                    func.add_alert(server_id,tags,host,port,create_time,db_type,'network(%s)' %(if_descr),'in:%s,out:%s' %(in_bytes,out_bytes),'critical','network %s bytes reach %s' %(if_descr,sum_bytes),send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                    func.update_db_status('network',3,server_id, host, db_type,create_time,'network(%s)'%(if_descr),'in:%s,out:%s' %(in_bytes,out_bytes),'critical')
                elif int(sum_bytes) >= int(threshold_warning_os_network):
                    send_mail = func.update_send_mail_status(host,db_type,'network(%s)' %(if_descr),send_mail,send_mail_max_count)
                    send_sms = func.update_send_sms_status(host,db_type,'network(%s)' %(if_descr),send_sms,send_sms_max_count)
                    func.add_alert(server_id,tags,host,port,create_time,db_type,'network(%s)'%(if_descr),'in:%s,out:%s' %(in_bytes,out_bytes),'warning','network %s bytes reach %s' %(if_descr,sum_bytes),send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                    func.update_db_status('network',2,server_id, host, db_type,create_time,'network(%s)'%(if_descr),'in:%s,out:%s' %(in_bytes,out_bytes),'warning')
                else:
                    func.check_if_ok(server_id,tags,host,port,create_time,db_type,'network(%s)'%(if_descr),'in:%s,out:%s' %(in_bytes,out_bytes),'network %s bytes ok' %(if_descr),send_mail,send_mail_to_list,send_sms,send_sms_to_list)
                    func.update_db_status('network',1,server_id, host, db_type,create_time,'network','max(%s-in:%s,out:%s)' %(if_descr,in_bytes,out_bytes),'ok')
    else:
       pass

##############################################################################
# function main  
##############################################################################
def main():

    logger.info("alert os test begin.")
    ip = "192.168.210.210"
		
    #gen_alert_os_status(ip)    
    #gen_alert_os_disk(ip)    
    #gen_alert_os_network(ip)   
    logger.info("alert os test finished.")

if __name__ == '__main__':
    main()