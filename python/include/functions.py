#!/bin/env python
#-*-coding:utf-8-*-

import MySQLdb
import string
import time  
import datetime
import sys 
reload(sys) 
sys.setdefaultencoding('utf8')
import ConfigParser
import smtplib
from email.mime.text import MIMEText
from email.message import Message
from email.header import Header


def get_item(data_dict,item):
    try:
       item_value = data_dict[item]
       return item_value
    except:
       return '-1'

def get_config(group,config_name):
    config = ConfigParser.ConfigParser()
    config.readfp(open('./etc/config.ini','rw'))
    config_value=config.get(group,config_name).strip(' ').strip('\'').strip('\"')
    return config_value

def filters(data):
    return data.strip(' ').strip('\n').strip('\br')


def get_option(key):
    conn=MySQLdb.connect(host=host,user=user,passwd=passwd,port=int(port),connect_timeout=5,charset='utf8')
    conn.select_db(dbname)
    cursor = conn.cursor()
    sql="select value from options where name=+'"+key+"'"
    count=cursor.execute(sql)
    if count == 0 :
        result=0
    else:
        result=cursor.fetchone()
    return result[0]
    cursor.close()
    conn.close()
    
    
host = get_config('monitor_server','host')
port = get_config('monitor_server','port')
user = get_config('monitor_server','user')
passwd = get_config('monitor_server','passwd')
dbname = get_config('monitor_server','dbname')

send_mail_sleep_time = get_option('send_mail_sleep_time')
send_sms_sleep_time = get_option('send_sms_sleep_time')

def mysql_exec(sql,param):
    try:
    	conn=MySQLdb.connect(host=host,user=user,passwd=passwd,port=int(port),connect_timeout=5,charset='utf8')
    	conn.select_db(dbname)
    	curs = conn.cursor()
    	if param <> '':
            curs.execute(sql,param)
        else:
            curs.execute(sql)
        conn.commit()
        curs.close()
        conn.close()
    except Exception,e:
       print "mysql execute: " + str(e) 
       print "sql: " + sql


def mysql_query(sql):
    conn=MySQLdb.connect(host=host,user=user,passwd=passwd,port=int(port),connect_timeout=5,charset='utf8')
    conn.select_db(dbname)
    cursor = conn.cursor()
    count=cursor.execute(sql)
    if count == 0 :
        result=0
    else:
        result=cursor.fetchall()
    return result
    cursor.close()
    conn.close()

def mysql_single_query(sql):
    result = None
    try: 
        conn=MySQLdb.connect(host=host,user=user,passwd=passwd,port=int(port),connect_timeout=5,charset='utf8')
        conn.select_db(dbname)
        curs = conn.cursor()
        count=curs.execute(sql)
        
        if count:
            result=curs.fetchone()[0]
        
        return result
    except Exception,e:
        print "Get single value: " + str(e) 
    finally:
        curs.close()
        conn.close()    
    
def add_alarm(server_id,tags,db_host,db_port,create_time,db_type,alert_item,alert_value,level,message,send_mail,send_mail_to_list,send_sms,send_sms_to_list):
   try: 
       conn=MySQLdb.connect(host=host,user=user,passwd=passwd,port=int(port),connect_timeout=5,charset='utf8')
       conn.select_db(dbname)
       curs = conn.cursor()
       sql="insert into alarm(server_id,tags,host,port,create_time,db_type,alert_item,alert_value,level,message,send_mail,send_mail_to_list,send_sms,send_sms_to_list) values(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s);"
       param=(server_id,tags,db_host,db_port,create_time,db_type,alert_item,alert_value,level,message,send_mail,send_mail_to_list,send_sms,send_sms_to_list)
       curs.execute(sql,param)

       if send_mail == 1:
           temp_sql = "insert into alarm_temp(server_id,ip,db_type,alert_item,alarm_type) values(%s,%s,%s,%s,%s);"
           temp_param = (server_id,db_host,db_type,alert_item,'mail')
           curs.execute(temp_sql,temp_param)
       if send_sms == 1:
           temp_sql = "insert into alarm_temp(server_id,ip,db_type,alert_item,alarm_type) values(%s,%s,%s,%s,%s);"
           temp_param = (server_id,db_host,db_type,alert_item,'sms')
           curs.execute(temp_sql,temp_param)
       if (send_mail ==0 and send_sms==0):
           temp_sql = "insert into alarm_temp(server_id,ip,db_type,alert_item,alarm_type) values(%s,%s,%s,%s,%s);"
           temp_param = (server_id,db_host,db_type,alert_item,'none')
           curs.execute(temp_sql,temp_param)
       conn.commit()
       curs.close()
       conn.close()
   except Exception,e:
       print "Add alarm: " + str(e)     


def add_alert(server_id,tags,db_host,db_port,create_time,db_type,alert_item,alert_value,level,message,send_mail,send_mail_to_list,send_sms,send_sms_to_list):
    try: 
        conn=MySQLdb.connect(host=host,user=user,passwd=passwd,port=int(port),connect_timeout=5,charset='utf8')
        conn.select_db(dbname)
        curs = conn.cursor()
        if db_type=='os':
            alert_count=curs.execute("select id from alerts where host='%s' and alert_item='%s';" %(db_host,alert_item))
        
            if int(alert_count) > 0 :
                sql="insert into alerts_his select *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from alerts where host='%s' and alert_item='%s';" %(db_host,alert_item)
                mysql_exec(sql,'')
        
                mysql_exec("delete from alerts where host='%s'  and alert_item='%s' ;" %(db_host,alert_item),'')
        else:
            alert_count=curs.execute("select id from alerts where server_id=%s and db_type='%s' and alert_item='%s';" %(server_id,db_type,alert_item))  
            if int(alert_count) > 0 :
                sql="insert into alerts_his select *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from alerts where server_id=%s and db_type='%s' and alert_item='%s';" %(server_id,db_type,alert_item) 
                mysql_exec(sql,'')
                          
                mysql_exec("delete from alerts where server_id=%s and db_type='%s' and alert_item='%s' ;" %(server_id,db_type,alert_item),'')

       	
        sql="insert into alerts(server_id,tags,host,port,create_time,db_type,alert_item,alert_value,level,message,send_mail,send_mail_to_list,send_sms,send_sms_to_list) values(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s);"
        param=(server_id,tags,db_host,db_port,create_time,db_type,alert_item,alert_value,level,message,send_mail,send_mail_to_list,send_sms,send_sms_to_list)
        curs.execute(sql,param)

        if send_mail == 1:
            mysql_exec("delete from alerts_temp where server_id='%s' and ip='%s' and db_type='%s' and alert_item='%s' and alert_type='mail';" %(server_id,db_host,db_type,alert_item),'')
            temp_sql = "insert into alerts_temp(server_id,ip,db_type,alert_item,alert_type) values(%s,%s,%s,%s,%s);"
            temp_param = (server_id,db_host,db_type,alert_item,'mail')
            curs.execute(temp_sql,temp_param)
        if send_sms == 1:
            mysql_exec("delete from alerts_temp where server_id='%s' and ip='%s' and db_type='%s' and alert_item='%s' and alert_type='sms';" %(server_id,db_host,db_type,alert_item),'')
            temp_sql = "insert into alerts_temp(server_id,ip,db_type,alert_item,alert_type) values(%s,%s,%s,%s,%s);"
            temp_param = (server_id,db_host,db_type,alert_item,'sms')
            curs.execute(temp_sql,temp_param)
            
           
        conn.commit()
        curs.close()
        conn.close()
    except Exception,e:
        print "Add alert: " + str(e)   
       
       
def check_if_ok(server_id,tags,db_host,db_port,create_time,db_type,alert_item,alert_value,message,send_mail,send_mail_to_list,send_sms,send_sms_to_list):
    conn=MySQLdb.connect(host=host,user=user,passwd=passwd,port=int(port),connect_timeout=5,charset='utf8')
    conn.select_db(dbname)
    curs = conn.cursor()
    if db_type=='os':
        alert_count=curs.execute("select id from alerts where server_id = 0 and host='%s' and alert_item='%s' and level !='ok';" %(db_host,alert_item))
        
        if int(alert_count) > 0 :
            sql="insert into alerts_his select *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from alerts where server_id = 0 and host='%s' and alert_item='%s' ;" %(db_host,alert_item)
            mysql_exec(sql,'')
        
            mysql_exec("delete from alerts where server_id = 0 and host='%s'  and alert_item='%s' ;" %(db_host,alert_item),'')
    else:
        sql="select id from alerts where server_id=%s and db_type='%s' and alert_item='%s' and level !='ok';" %(server_id,db_type,alert_item)
        #print sql
        alert_count=curs.execute(sql)  
        if int(alert_count) > 0 :
            sql="insert into alerts_his select *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from alerts where server_id=%s and db_type='%s' and alert_item='%s' ;" %(server_id,db_type,alert_item) 
            mysql_exec(sql,'')
                          
            mysql_exec("delete from alerts where server_id=%s and db_type='%s' and alert_item='%s' ;" %(server_id,db_type,alert_item),'')
    #print alert_count
    if int(alert_count) > 0 :
        sql="insert into alerts(server_id,tags,host,port,create_time,db_type,alert_item,alert_value,level,message,send_mail,send_mail_to_list,send_sms,send_sms_to_list) values(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s);"
        param=(server_id,tags,db_host,db_port,create_time,db_type,alert_item,alert_value,'ok',message,send_mail,send_mail_to_list,send_sms,send_sms_to_list)
        mysql_exec(sql,param)

    curs.close()
    conn.close()
    
    
def update_send_mail_status(server,db_type,alert_item,send_mail,send_mail_max_count):
    conn=MySQLdb.connect(host=host,user=user,passwd=passwd,port=int(port),connect_timeout=5,charset='utf8')
    conn.select_db(dbname)
    curs = conn.cursor()
    sql=""
    if db_type == "os":
        sql="select id from alerts_temp where ip='%s' and db_type='%s' and alert_item='%s' and alert_type='mail' and create_time > date_add(sysdate(), interval -%s second);" %(server,db_type,alert_item,send_mail_sleep_time)
    else:
        sql="select id from alerts_temp where server_id=%s and db_type='%s' and alert_item='%s' and alert_type='mail' and create_time > date_add(sysdate(), interval -%s second);" %(server,db_type,alert_item,send_mail_sleep_time)
        
    #print sql
    alert_count=curs.execute(sql) 
    #print alert_count
    if int(alert_count) > 0 :
        send_mail = 0
    else:
        send_mail = send_mail
    return send_mail
    curs.close()
    conn.close()

def update_send_sms_status(server,db_type,alert_item,send_sms,send_sms_max_count):
    conn=MySQLdb.connect(host=host,user=user,passwd=passwd,port=int(port),connect_timeout=5,charset='utf8')
    conn.select_db(dbname)
    curs = conn.cursor()
    if db_type == "os":
        alert_count=curs.execute("select id from alerts_temp where ip='%s' and db_type='%s' and alert_item='%s' and alert_type='sms' and create_time > date_add(sysdate(), interval -%s second);" %(server,db_type,alert_item,send_sms_sleep_time))
    else:
        alert_count=curs.execute("select id from alerts_temp where server_id=%s and db_type='%s' and alert_item='%s' and alert_type='sms' and create_time > date_add(sysdate(), interval -%s second);" %(server,db_type,alert_item,send_sms_sleep_time))

    if int(alert_count) > 0 :
        send_sms = 0
    else:
        send_sms = send_sms
    return send_sms
    curs.close()
    conn.close()
    
    
def check_db_status(server_id,db_host,db_port,tags,db_type):
    try:
        conn=MySQLdb.connect(host=host,user=user,passwd=passwd,port=int(port),connect_timeout=5,charset='utf8')
        conn.select_db(dbname)
        curs = conn.cursor()
        sql=""
        sort=0

        if db_type=='mysql':
             sort=1
             sql="""delete s from db_status s, db_cfg_mysql o 
                    where s.host= o.host
                    and s.tags != o.tags
                    and s.db_type_sort = 1 """
        elif db_type=='oracle':
             sort=2
             sql="""delete s from db_status s, db_cfg_oracle o 
                    where s.host= o.host
                    and s.tags != o.tags
                    and s.db_type_sort = 2 """
        elif db_type=='mongodb':            
             sort=3
             sql="""delete s from db_status s, db_cfg_mongodb o 
                    where s.host= o.host
                    and s.tags != o.tags
                    and s.db_type_sort = 3 """
        elif db_type=='redis':
             sort=4
             sql="""delete s from db_status s, db_cfg_redis o 
                    where s.host= o.host
                    and s.tags != o.tags
                    and s.db_type_sort = 4 """
        elif db_type=='sqlserver':
             sort=5
             sql="""delete s from db_status s, db_cfg_sqlserver o 
                    where s.host= o.host
                    and s.tags != o.tags
                    and s.db_type_sort = 5 """
        else:
             sort=0


        curs.execute(sql)
        conn.commit()


        sql="select id from db_status where host=+'"+db_host+"'  and tags='"+tags+"' "
        count=curs.execute(sql) 
        if count ==0:
             sql="insert into db_status(server_id,host,port,tags,db_type,db_type_sort) values(%s,%s,%s,%s,%s,%s);"
             param=(server_id,db_host,db_port,tags,db_type,sort)
             curs.execute(sql,param)
             conn.commit()
             
    except Exception,e:
        print "Check db status table: " + str(e) 
    finally:
        curs.close()
        conn.close()          
        

def update_db_status_init(server_id,db_type,role,version,tags):
    try:
        conn=MySQLdb.connect(host=host,user=user,passwd=passwd,port=int(port),connect_timeout=5,charset='utf8')
        conn.select_db(dbname)
        curs = conn.cursor()
        curs.execute("update db_status set role='%s',version='%s',tags='%s' where server_id='%s' and db_type='%s';" %(role,version,tags,server_id,db_type))
        conn.commit()
    except Exception, e:
        print "update db status init: " + str(e)
    finally:
      curs.close()
      conn.close()


def update_db_status(field,value,server_id, db_host, db_tpye, alert_time,alert_item,alert_value,alert_level):
    try:
        field_tips=field+'_tips'
        if value==-1:
            value_tips='no data'
        else:
            value_tips="""
                          item: %s\n<br/>
                         value: %s\n<br/> 
                          level: %s\n<br/>
                          time: %s\n<br/> 
                    """ %(alert_item,alert_value,alert_level,alert_time)

        conn=MySQLdb.connect(host=host,user=user,passwd=passwd,port=int(port),connect_timeout=5,charset='utf8')
        conn.select_db(dbname)
        curs = conn.cursor()
        if db_tpye == 'os':
            curs.execute("update db_status set %s='%s',%s='%s' where host='%s';" %(field,value,field_tips,value_tips,db_host))
        else:
            curs.execute("update db_status set %s='%s',%s='%s' where server_id='%s' and db_type='%s';" %(field,value,field_tips,value_tips,server_id,db_tpye))
        conn.commit()
    except Exception, e:
        print "update db status: " + str(e)
    finally:
      curs.close()
      conn.close()


def update_check_time():
    try:
        conn=MySQLdb.connect(host=host,user=user,passwd=passwd,port=int(port),connect_timeout=5,charset='utf8')
        conn.select_db(dbname)
        curs = conn.cursor()
        localtime = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime())
        curs.execute("update wlblazers_status set wl_value='%s'  where wl_variables='wlblazers_checktime';" %(localtime))
        conn.commit()
    except Exception, e:
        print "update check time: " + str(e)
    finally:
      curs.close()
      conn.close()




def flush_hosts():
    conn=MySQLdb.connect(host=host,user=user,passwd=passwd,port=int(port),connect_timeout=5,charset='utf8')
    conn.select_db(dbname)
    cursor = conn.cursor()
    cursor.execute('flush hosts;');

def get_mysql_status(cursor):
    data=cursor.execute('show global status;');
    data_list=cursor.fetchall()
    data_dict={}
    for item in data_list:
        data_dict[item[0]] = item[1]
    return data_dict

def get_mysql_variables(cursor):
    data=cursor.execute('show global variables;');
    data_list=cursor.fetchall()
    data_dict={}
    for item in data_list:
        data_dict[item[0]] = item[1]
    return data_dict

def get_mysql_version(cursor):
    cursor.execute('select version();');
    return cursor.fetchone()[0]

