#!/usr/bin/env python
import os
import sys
import string
import time
import datetime
import traceback
import MySQLdb
import pymssql
import logging
import logging.config
logging.config.fileConfig("etc/logger.ini")
logger = logging.getLogger("clean_history")
path='./include'
sys.path.insert(0,path)
import functions as func
from multiprocessing import Process;

his_retention = func.get_config('common','his_retention')

######################################################################################################
# function clean_history_data
######################################################################################################   
def clean_history_data():
    try:
        logger.info("Clean mysql history data start.")   
        func.mysql_exec("delete from mysql_status_his where create_time < date_add(now(), interval -%s day);" %(his_retention),'')
        func.mysql_exec("delete from mysql_dr_p_his where create_time < date_add(now(), interval -%s day);" %(his_retention),'')
        func.mysql_exec("delete from mysql_dr_s_his where create_time < date_add(now(), interval -%s day);" %(his_retention),'')
        func.mysql_exec("delete from mysql_bigtable_his where create_time < date_add(now(), interval -%s day);" %(his_retention),'')
        #func.mysql_exec("delete from mysql_slow_query_review_his where create_time < date_add(now(), interval -%s day);" %(his_retention),'')
        logger.info("Clean mysql history data finished.")   
        
        logger.info("Clean oracle history data start.")     
        func.mysql_exec("delete from oracle_status_his where create_time < date_add(now(), interval -%s day);" %(his_retention),'')
        func.mysql_exec("delete from oracle_tablespace_his where create_time < date_add(now(), interval -%s day);" %(his_retention),'')
        func.mysql_exec("delete from oracle_diskgroup_his where create_time < date_add(now(), interval -%s day);" %(his_retention),'')
        func.mysql_exec("delete from oracle_dg_p_status_his where create_time < date_add(now(), interval -%s day);" %(his_retention),'')
        func.mysql_exec("delete from oracle_dg_s_status_his where create_time < date_add(now(), interval -%s day);" %(his_retention),'')
        func.mysql_exec("delete from oracle_dg_process_his where create_time < date_add(now(), interval -%s day);" %(his_retention),'')
        func.mysql_exec("delete from oracle_redo where create_time < date_add(now(), interval -%s day);" %(his_retention),'')
        func.mysql_exec("delete from oracle_db_time where create_time < date_add(now(), interval -%s day);" %(his_retention),'')
        func.mysql_exec("delete from oracle_session where create_time < date_add(now(), interval -%s day);" %(his_retention),'')
        func.mysql_exec("delete from oracle_flashback where create_time < date_add(now(), interval -%s day);" %(his_retention),'')
        logger.info("Clean oracle history data finished.")   
        
        logger.info("Clean sqlserver history data start.")     
        func.mysql_exec("delete from sqlserver_status_his where create_time < date_add(now(), interval -%s day);" %(his_retention),'')
        func.mysql_exec("delete from sqlserver_mirror_p_his where create_time < date_add(now(), interval -%s day);" %(his_retention),'')
        func.mysql_exec("delete from sqlserver_mirror_s_his where create_time < date_add(now(), interval -%s day);" %(his_retention),'')
        logger.info("Clean sqlserver history data finished.")   
        
        logger.info("Clean os history data start.")     
        func.mysql_exec("delete from os_status_his where create_time < date_add(now(), interval -%s day);" %(his_retention),'')
        func.mysql_exec("delete from os_disk_his where create_time < date_add(now(), interval -%s day);" %(his_retention),'')
        func.mysql_exec("delete from os_diskio_his where create_time < date_add(now(), interval -%s day);" %(his_retention),'')
        func.mysql_exec("delete from os_net_his where create_time < date_add(now(), interval -%s day);" %(his_retention),'')
        logger.info("Clean os history data finished.")   
        
        logger.info("Clean alert history data start.")  
        func.mysql_exec("delete from alerts_his where create_time < date_add(now(), interval -%s day);" %(his_retention),'')  
        logger.info("Clean alert history data finished.")   


    except Exception, e:
        logger.error('traceback.format_exc():\n%s' % traceback.format_exc())
        #print 'traceback.print_exc():'; traceback.print_exc()
        #print 'traceback.format_exc():\n%s' % traceback.format_exc()
        func.mysql_exec("rollback;",'')
        sys.exit(1)
    finally:
        pass
        
        
def main():
		# Clean history data
    logger.info("Clean history data start.")   
    clean_history_data()
    logger.info("Clean history data finished.")       


if __name__=='__main__':
    main()
