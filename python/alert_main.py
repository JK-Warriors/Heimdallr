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
send_mail_sleep_time = func.get_option('send_mail_sleep_time')
send_sms_sleep_time = func.get_option('send_sms_sleep_time')
sms_to_list_common = func.get_option('send_sms_to_list')


def send_alarm():
    sql = "select tags,host,port,create_time,db_type,alarm_item,alarm_value,level,message,send_mail,send_mail_to_list,send_sms,send_sms_to_list,id alarm_id from alarm;"
    result=func.mysql_query(sql)
    if result <> 0:
        send_alarm_mail = func.get_option('send_alarm_mail')
        send_alarm_sms = func.get_option('send_alarm_sms')
        for line in result:
            tags=line[0]
            host=line[1]
            port=line[2]
            create_time=line[3]
            db_type=line[4]
            alarm_item=line[5]
            alarm_value=line[6]
            level=line[7]
            message=line[8]
            send_mail=line[9]
            send_mail_to_list=line[10]
            send_sms=line[11]
            send_sms_to_list=line[12]
            alarm_id=line[13]

            if port:
               server = host+':'+port
            else:
               server = host

            if send_mail_to_list:
                mail_to_list=send_mail_to_list.split(';')
            else:
                send_mail=0 

            if send_sms_to_list:
                sms_to_list=send_sms_to_list.split(';')
            else:
                send_sms=0

            if int(send_alarm_mail)==1:
                if send_mail==1:
                    mail_subject='['+level+'] '+db_type+'-'+tags+'-'+server+' '+message+' Time:'+create_time.strftime('%Y-%m-%d %H:%M:%S')
                    mail_content="""
                         Type: %s\n<br/>
                         Tags: %s\n<br/> 
                         Host: %s:%s\n<br/> 
                        Level: %s\n<br/>
                         Item: %s\n<br/>  
                        Value: %s\n<br/> 
                       Message: %s\n<br/> 
                         
                    """ %(db_type,tags,host,port,level,alarm_item,alarm_value,message)
                    result = sendmail.send_mail(mail_to_list,mail_subject,mail_content)
                    if result:
                        send_mail_status=1
                    else:
                        send_mail_status=0
                else:
                    send_mail_status=0
            else:
                send_mail_status=0
 
            if int(send_alarm_sms)==1:
                if send_sms==1:
                   sms_msg='['+level+'] '+db_type+'-'+tags+'-'+server+' '+message+' Time:'+create_time.strftime('%Y-%m-%d %H:%M:%S')
                   send_sms_type = func.get_option('smstype')
                   if send_sms_type == 'fetion':
                      result = sendsms_fx.send_sms(sms_to_list,sms_msg,db_type,tags,host,port,level,alarm_item,alarm_value,message)
                   else:
                      result = sendsms_api.send_sms(sms_to_list,sms_msg,db_type,tags,host,port,level,alarm_item,alarm_value,message)

                   if result:
                      send_sms_status=1
                   else:
                      send_sms_status=0
                else:
                   send_sms_status=0  
            else:
                send_sms_status=0

            try:
                sql="insert into alarm_history(server_id,tags,host,port,create_time,db_type,alarm_item,alarm_value,level,message,send_mail,send_mail_to_list,send_sms,send_sms_to_list,send_mail_status,send_sms_status) select server_id,tags,host,port,create_time,db_type,alarm_item,alarm_value,level,message,send_mail,send_mail_to_list,send_sms,send_sms_to_list,%s,%s from alarm where id=%s;"
                param=(send_mail_status,send_sms_status,alarm_id)
                func.mysql_exec(sql,param)
            except Exception, e:
                print e 

        func.mysql_exec("delete from alarm",'')

    else:
        pass


def check_send_alarm_sleep():
    send_mail_sleep_time = func.get_option('send_mail_sleep_time')
    send_sms_sleep_time = func.get_option('send_sms_sleep_time')

    if send_mail_sleep_time:
        now_time = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime())
        format="%Y-%m-%d %H:%M:%S"
        send_mail_sleep_time_format = "%d" %(int(send_mail_sleep_time))
        result=datetime.datetime(*time.strptime(now_time,format)[:6])-datetime.timedelta(minutes=int(send_mail_sleep_time_format))
        sleep_alarm_time= result.strftime(format)
        sql="delete from alarm_temp where alarm_type='mail' and create_time <= '%s' " %(sleep_alarm_time)
        param=()
        func.mysql_exec(sql,param)

    if send_sms_sleep_time:
        now_time = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime())
        format="%Y-%m-%d %H:%M:%S"
        send_sms_sleep_time_format = "%d" %(int(send_sms_sleep_time))
        result=datetime.datetime(*time.strptime(now_time,format)[:6])-datetime.timedelta(minutes=int(send_sms_sleep_time_format))
        sleep_alarm_time= result.strftime(format)
        sql="delete from alarm_temp where alarm_type='sms' and create_time <= '%s' " %(sleep_alarm_time)
        param=()
        func.mysql_exec(sql,param)


def main():

    logger.info("alarm controller started.")
    
    check_send_alarm_sleep()
        
    send_alarm()
    func.update_check_time() 
    logger.info("alarm controller finished.")

if __name__ == '__main__':
    main()










