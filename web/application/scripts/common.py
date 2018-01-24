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
##  01/22/2018                      Baseline version 1.0.0
##
######################################################################

import os
import string
import time
import sys, getopt

import mysql_handle as mysql
import oracle_handle as oracle

import logging
import logging.config

logging.config.fileConfig('./logging.conf')
logger = logging.getLogger('WLBlazers')

###############################################################################
# function log_dg_op_process
###############################################################################
def log_dg_op_process(mysql_conn, dg_id, process_type, process_desc, rate, block_time=0):
    #logger.info("Log the operate process in db_oracle_dg_process for dataguard group: %s" %(dg_id))
    
    # get current switch flag
    str="insert into db_oracle_dg_process(group_id, process_type, process_desc, rate) values (%s, '%s', '%s', %s) " %(dg_id, process_type, process_desc, rate)
    log_status=mysql.ExecuteSQL(mysql_conn, str)
    
    if log_status == 1:
        logger.info("Log the operate process for dataguard group: %s; completed %s." %(dg_id, rate))
    else:
        logger.error("Log the operate process for dataguard group: %s failed." %(dg_id))

    time.sleep(block_time)
    
###############################################################################
# function operation_lock
###############################################################################
def operation_lock(mysql_conn, dg_id, process_type):
    logger.info("Lock the %s process status in db_servers_oracle_dg for dataguard group: %s" %(process_type, dg_id))
    
    # update process status to 1
    col_name=""
    if process_type == "SWITCHOVER":
        col_name="on_switchover"
    elif process_type == "FAILOVER":
        col_name="on_failover"
    elif process_type == "MRP_START":
        col_name="on_startmrp"
    elif process_type == "MRP_STOP":
        col_name="on_stopmrp"
    else:
        col_name=""
    	
    str='update db_servers_oracle_dg set on_process = 1, %s = 1 where id= %s ' %(col_name, dg_id)
    op_status=mysql.ExecuteSQL(mysql_conn, str)
    logger.info(str)
    
    if op_status == 1:
        logger.info("Lock the process status for dataguard group: %s successfully." %(dg_id))
    else:
        logger.error("Lock the process status for dataguard group: %s failed." %(dg_id))
        

###############################################################################
# function operation_unlock
###############################################################################
def operation_unlock(mysql_conn, dg_id, process_type):
    logger.info("Unlock the %s process status in db_servers_oracle_dg for dataguard group: %s" %(process_type, dg_id))
    
    # update process status to 1
    col_name=""
    if process_type == "SWITCHOVER":
        col_name="on_switchover"
    elif process_type == "FAILOVER":
        col_name="on_failover"
    elif process_type == "MRP_START":
        col_name="on_startmrp"
    elif process_type == "MRP_STOP":
        col_name="on_stopmrp"
    else:
        col_name=""
    	
    str='update db_servers_oracle_dg set on_process = 0, %s = 0 where id= %s ' %(col_name, dg_id)
    op_status=mysql.ExecuteSQL(mysql_conn, str)
    logger.info(str)
    
    if op_status == 1:
        logger.info("Unlock process status for dataguard group: %s successfully." %(dg_id))
    else:
        logger.error("Unlock process status for dataguard group: %s failed." %(dg_id))
        
