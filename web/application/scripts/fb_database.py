#!/usr/bin/python
#-*- coding: utf-8 -*-

######################################################################
# Copyright (c)  2017 by WLBlazers Corporation
#
# mrp_start.py
# 
# 
######################################################################
# Modifications Section:
######################################################################
##     Date        File            Changes
######################################################################
##  03/01/2018                      Baseline version 1.0.0
##
######################################################################

import os
import string
from subprocess import Popen, PIPE
import sys, getopt

import mysql_handle as mysql
import oracle_handle as oracle
import common

import logging
import logging.config

logging.config.fileConfig('./logging.conf')
logger = logging.getLogger('WLBlazers')

###############################################################################
# function flashback_db
###############################################################################
def flashback_db(mysql_conn, server_id, conn, conn_str, restore_str):
    result=-1
    
    logger.info("Check the target database role. server_id is %s" %(server_id))
    # get database role
    str='select database_role from v$database'
    role=oracle.GetSingleValue(conn, str)
    logger.info("The current database role is: " + role)
	
	
    # get mrp process status
    str="""select count(1) from gv$session where program like '%(MRP0)' """
    mrp_process=oracle.GetSingleValue(conn, str)
	
    if role=="PHYSICAL STANDBY":
        if(mrp_process > 0):
            logger.info("The mrp process is already active, should to stop it first. ")
            sqlplus = Popen(["sqlplus", "-S", conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
            sqlplus.stdin.write(bytes("alter database recover managed standby database cancel;"+os.linesep))
            sqlplus.stdin.write(bytes(restore_str + os.linesep))
            out, err = sqlplus.communicate()
            logger.debug(out)
            #logger.error(err)
            if err is None:
                logger.info("Flashback successfully.")
                str = """update oracle_fb_process set result='1', reason='' where server_id=%s """ %(server_id)
                op_status = mysql.ExecuteSQL(mysql_conn, str)
                result=0
            else:
                logger.info("Flashback failed.")
                str = """update oracle_fb_process set result='0', reason='%s' where server_id=%s """ %(err, server_id)
                op_status = mysql.ExecuteSQL(mysql_conn, str)
        else:
            sqlplus = Popen(["sqlplus", "-S", conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
            sqlplus.stdin.write(bytes(restore_str + os.linesep))
            out, err = sqlplus.communicate()
            logger.debug(out)
            #logger.error(err)
            if err is None:
                logger.info("Flashback successfully.")
                str = """update oracle_fb_process set result='1', reason='' where server_id=%s """ %(server_id)
                op_status = mysql.ExecuteSQL(mysql_conn, str)
                result=0
            else:
                logger.info("Flashback failed.")
                str = """update oracle_fb_process set result='0', reason='%s' where server_id=%s """ %(err, server_id)
                op_status = mysql.ExecuteSQL(mysql_conn, str)
    else:
        logger.info("The current database role is not PHYSICAL STANDBY, not allow to flashback databaes.")
        
    return result;

	

###############################################################################
# function flashback_table
###############################################################################
def flashback_table(mysql_conn, server_id, conn, conn_str, restore_str, tab_name):
    result=-1
    
    logger.info("Check the target database role. server_id is %s" %(server_id))
    # get database role
    str='select database_role from v$database'
    role=oracle.GetSingleValue(conn, str)
    logger.info("The current database role is: " + role)

    # get instance status
    str='select status from v$instance'
    ins_status=oracle.GetSingleValue(conn, str)
    logger.info("The current instance status is: " + ins_status)
	
	
    if role=="PRIMARY" and ins_status=="OPEN":
        sqlplus = Popen(["sqlplus", "-S", conn_str, "as", "sysdba"], stdout=PIPE, stdin=PIPE)
        sqlplus.stdin.write(bytes("alter table " + tab_name + " enable row movement;" + os.linesep))
        sqlplus.stdin.write(bytes(restore_str + os.linesep))
        out, err = sqlplus.communicate()
        logger.debug(out)
        #logger.error(err)
        if err is None:
            logger.info("Flashback table successfully.")
            str = """update oracle_fb_process set result='1', reason='' where server_id=%s """ %(server_id)
            op_status = mysql.ExecuteSQL(mysql_conn, str)
            result=0
        else:
            logger.info("Flashback failed.")
            str = """update oracle_fb_process set result='0', reason='%s' where server_id=%s """ %(err, server_id)
            op_status = mysql.ExecuteSQL(mysql_conn, str)
    else:
        msg="The current database role is not PRIMARY, not allow to flashback table."
        logger.info(msg)
        str = """update oracle_fb_process set result='0', reason='%s' where server_id=%s """ %(msg, server_id)
        op_status = mysql.ExecuteSQL(mysql_conn, str)
        
    return result;
    
    
###############################################################################
# function pre_flashback
###############################################################################
def pre_flashback(mysql_conn, server_id, fb_type, fb_object):
    logger.info("Check the flashback process status for server: %s" %(server_id))
    
    # get flashback process
    str = """select count(1) from oracle_fb_process where server_id=%s """ %(server_id)
    fb_record = mysql.GetSingleValue(mysql_conn, str)
    
    if fb_record == 0:	
        logger.info("Generate a flashback process for server: %s" %(server_id))
        str="""insert into oracle_fb_process(server_id, fb_type, fb_object) values('%s', '%s', '%s') """ %(server_id, fb_type, fb_object)
        op_status=mysql.ExecuteSQL(mysql_conn, str)
        return 0
    else:
        str = """select on_process from oracle_fb_process where server_id=%s """ %(server_id)
        fb_process = mysql.GetSingleValue(mysql_conn, str)
        
        if fb_process == 1:
            logger.error("Blocked, another flashback process is running.")
            str = """update oracle_fb_process set blocked = 1 where server_id=%s """ %(server_id)
            op_status = mysql.ExecuteSQL(mysql_conn, str)
            sys.exit(2)
        else:
            logger.info("Update the flashback process infomation for server: %s" %(server_id))
            str = """update oracle_fb_process set fb_type = '%s', fb_object='%s', result='', reason='', blocked=0, create_time=CURRENT_TIMESTAMP where server_id=%s """ %(fb_type, fb_object,server_id)
            op_status = mysql.ExecuteSQL(mysql_conn, str)
            logger.info("The flashback process is already prepared for server: %s" %(server_id))
            return 0
    


###############################################################################
# function finish_flashback
###############################################################################
def finish_flashback(mysql_conn, server_id):
    logger.info("Finish the flashback process infomation for server: %s" %(server_id))
    
    # update flashback process
    str = """update oracle_fb_process set on_process=0, blocked=0 where server_id=%s """ %(server_id)
    op_status = mysql.ExecuteSQL(mysql_conn, str)
    

###############################################################################
# main function
###############################################################################
if __name__=="__main__":
    # parse argv
    server_id = ''
    fb_type = ''
    restore_point = ''
    tbs_name = ''
    tab_name = ''
    
    try:
        opts, args = getopt.getopt(sys.argv[1:],"d:t:m:v:n:")
    except getopt.GetoptError:
        sys.exit(2)
		
    for opt, arg in opts:
        if opt == '-d':
            server_id = arg
        elif opt == '-t':
            fb_type = arg
        elif opt == '-m':
            fb_method = arg
        elif opt == '-v':
            fb_value = arg
        elif opt == '-n':
            tab_name = arg
    
	
	###########################################################################
	# connect to mysql
    mysql_conn = ''
    try:
        mysql_conn = mysql.ConnectMysql()
    except Exception as e:
        logger.error(e)
        sys.exit(2)
		
    
    str = """select concat(username, '/', password, '@', host, ':', port, '/', dsn) from db_cfg_oracle where id=%s """ %(server_id)
    conn_str = mysql.GetSingleValue(mysql_conn, str)

    str = """select concat(username, '/', password, '@', host, ':', port, '/', dsn) from db_cfg_oracle where id=%s """ %(server_id)
    nopass_str = mysql.GetSingleValue(mysql_conn, str)
	
    logger.info("The flashback database is: %s, the id is: %s" %(nopass_str,server_id))
	
    conn = oracle.ConnectOracleAsSysdba(conn_str)
	

    
    if conn is None:
        logger.error("Connect to flashback database error, exit!!!")
        sys.exit(2)
    else:
        try:
            restore_str = ""
            if fb_type == "1" or fb_type == "2":			#数据库或者表空间闪回
                pre_flashback(mysql_conn, server_id, fb_type, 'database')
                
                if fb_method == "1":									#按闪回点闪回
                    restore_str = "flashback database to restore point %s;" %(fb_value)
                elif fb_method == "2":								#按时间戳闪回
                    new_value = fb_value.replace("T", " ") + ":00"
                    restore_str = "flashback database to timestamp to_timestamp('%s','yy-mm-dd hh24:mi:ss');" %(new_value)
                    
                logger.info("The flashback command is: %s" %(restore_str))
                res = flashback_db(mysql_conn, server_id, conn, conn_str, restore_str)   
                    
                finish_flashback(mysql_conn, server_id) 
            elif fb_type == "3":											#表格闪回
                pre_flashback(mysql_conn, server_id, fb_type, tab_name)
                
                if fb_method == "1":
                    restore_str = "flashback table %s to restore point %s;" %(tab_name, fb_value)
                elif fb_method == "2":
                    new_value = fb_value.replace("T", " ") + ":00"
                    restore_str = "flashback table %s to timestamp to_timestamp('%s','yy-mm-dd hh24:mi:ss');" %(tab_name, new_value)
                    
                logger.info("The flashback command is: %s" %(restore_str))
                res = flashback_table(mysql_conn, server_id, conn, conn_str, restore_str, tab_name)  
                    
                finish_flashback(mysql_conn, server_id)   
              
        except Exception, e:
            print e.message
        finally:
            pass

