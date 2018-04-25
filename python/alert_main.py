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


def send_alert_media():
    sql = """SELECT id, 
										tags,
										host,
										port,
										create_time,
										db_type,
										alert_item,
										alert_value,
										LEVEL,
										message,
										send_mail,
										send_mail_to_list,
										send_mail_status,
										send_sms,
										send_sms_to_list,
										send_sms_status
									FROM alerts 
									where (send_mail = 1 and send_mail_status = 0)
									  or (send_sms = 1 and send_sms_status = 0) """
    result=func.mysql_query(sql)
    if result:
        send_alarm_mail = func.get_option('send_alarm_mail')
        send_alarm_sms = func.get_option('send_alarm_sms')
        for line in result:
            alert_id=line[0]
            tags=line[1]
            host=line[2]
            port=line[3]
            create_time=line[4]
            db_type=line[5]
            alert_item=line[6]
            alert_value=line[7]
            level=line[8]
            message=line[9]
            send_mail=line[10]
            send_mail_to_list=line[11]
            send_mail_status=line[12]
            send_sms=line[13]
            send_sms_to_list=line[14]
            send_sms_status=line[15]

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

            logger.info("alert_id: %s" %(alert_id))
            if int(send_alarm_mail)==1:
                if int(send_mail)==1:
                    mail_subject='['+level+'] '+db_type+'-'+tags+'-'+server+' '+message+' Time:'+create_time.strftime('%Y-%m-%d %H:%M:%S')
                    mail_content="""
                         Type: %s\n<br/>
                         Tags: %s\n<br/> 
                         Host: %s:%s\n<br/> 
                        Level: %s\n<br/>
                         Item: %s\n<br/>  
                        Value: %s\n<br/> 
                       Message: %s\n<br/> 
                         
                    """ %(db_type,tags,host,port,level,alert_item,alert_value,message)
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
                      result = sendsms_fx.send_sms(sms_to_list,sms_msg,db_type,tags,host,port,level,alert_item,alert_value,message)
                   else:
                      result = sendsms_api.send_sms(sms_to_list,sms_msg,db_type,tags,host,port,level,alert_item,alert_value,message)

                   if result:
                      send_sms_status=1
                   else:
                      send_sms_status=0
                else:
                   send_sms_status=0  
            else:
                send_sms_status=0

            try:
                sql="update alerts set send_mail_status = %s, send_sms_status = %s where id = %s; "
                param=(send_mail_status,send_sms_status,alert_id)
                func.mysql_exec(sql,param)
            except Exception, e:
                print e 


    else:
        pass




def send_alert_mail(server_id, host):
    sql = """SELECT id, 
										tags,
										host,
										port,
										create_time,
										db_type,
										alert_item,
										alert_value,
										LEVEL,
										message,
										send_mail,
										send_mail_to_list,
										send_mail_status,
										send_sms,
										send_sms_to_list,
										send_sms_status
									FROM alerts 
									where server_id = %s
									  and host = '%s' 
									  and ((send_mail = 1 and send_mail_status = 0)
									  or (send_sms = 1 and send_sms_status = 0)) """ %(server_id, host)
    result=func.mysql_query(sql)
    if result:
        send_alarm_mail = func.get_option('send_alarm_mail')
        send_alarm_sms = func.get_option('send_alarm_sms')
        for line in result:
            alert_id=line[0]
            tags=line[1]
            host=line[2]
            port=line[3]
            create_time=line[4]
            db_type=line[5]
            alert_item=line[6]
            alert_value=line[7]
            level=line[8]
            message=line[9]
            send_mail=line[10]
            send_mail_to_list=line[11]
            send_mail_status=line[12]
            send_sms=line[13]
            send_sms_to_list=line[14]
            send_sms_status=line[15]

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

            logger.info("alert_id: %s" %(alert_id))
            if int(send_alarm_mail)==1:
                if int(send_mail)==1:
                    mail_subject='['+level+'] '+db_type+'-'+tags+'-'+server+' '+message+' Time:'+create_time.strftime('%Y-%m-%d %H:%M:%S')
                    mail_content="""
                         Type: %s\n<br/>
                         Tags: %s\n<br/> 
                         Host: %s:%s\n<br/> 
                        Level: %s\n<br/>
                         Item: %s\n<br/>  
                        Value: %s\n<br/> 
                       Message: %s\n<br/> 
                         
                    """ %(db_type,tags,host,port,level,alert_item,alert_value,message)
                    result = sendmail.send_mail(mail_to_list,mail_subject,mail_content)
                    if result:
                        send_mail_status=1
                    else:
                        send_mail_status=0
                else:
                    send_mail_status=0
            else:
                send_mail_status=0


            try:
                sql="update alerts set send_mail_status = %s, send_sms_status = %s where id = %s; "
                param=(send_mail_status,send_sms_status,alert_id)
                func.mysql_exec(sql,param)
            except Exception, e:
                print e 


    else:
        pass


##############################################################################
# function used to move alerts to history which create 3 days before
##############################################################################
def alert_to_history():
    sql="insert into alerts_his select *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from alerts where create_time > date_add(sysdate(), interval -3 day);"
    mysql_exec(sql,'')
    
    
    sql="delete from alerts where id in(select id from alerts_his);"
    mysql_exec(sql,'')
    


##############################################################################
# function main  
##############################################################################
def main():

    logger.info("Send alert controller started.")
    # move alert to history 3 days before
    alert_to_history()
    
    # send mail and sms
    send_alert_media()
    
    func.update_check_time() 
    logger.info("Send alert controller finished.")

if __name__ == '__main__':
    main()










