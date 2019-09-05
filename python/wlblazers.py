#!/bin/env python
#coding:utf-8
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
from multiprocessing import Process;
  

def job_run(script_name,times):
    while True:
        if os.path.exists(script_name+".py"):
            os.system("python "+script_name+".py")
            time.sleep(int(times))
        else:
            os.system("python "+script_name+".pyc")
            time.sleep(int(times))


def main():
    logger.info("wlblazers controller start.")
    monitor = str(func.get_option('monitor'))
    monitor_mysql = str(func.get_option('monitor_mysql'))
    monitor_mongodb = str(func.get_option('monitor_mongodb'))
    monitor_oracle = str(func.get_option('monitor_oracle'))
    monitor_redis = str(func.get_option('monitor_redis'))
    monitor_sqlserver = str(func.get_option('monitor_sqlserver'))
    monitor_os = str(func.get_option('monitor_os'))
    alert = str(func.get_option('alert'))
    frequency_monitor = func.get_option('frequency_monitor')
    frequency_alert = 60

    joblist = []
    if monitor=="1":
        if monitor_mysql=="1":
            job = Process(target = job_run, args = ('check_mysql',frequency_monitor))
            joblist.append(job)
            job.start()

        time.sleep(3)
        if monitor_oracle=="1":
            job = Process(target = job_run, args = ('check_oracle',frequency_monitor))
            joblist.append(job)
            job.start()

        time.sleep(3)
        if monitor_sqlserver=="1":
            job = Process(target = job_run, args = ('check_sqlserver',frequency_monitor))
            joblist.append(job)
            job.start()

        time.sleep(3)
        if monitor_os=="1":
            job = Process(target = job_run, args = ('check_os',frequency_monitor))
            joblist.append(job)
            job.start()

        time.sleep(3)
        if alert=="1":
            job = Process(target = job_run, args = ('alert_main',frequency_alert))
            joblist.append(job)
            job.start()     

        time.sleep(3)
        job = Process(target = job_run, args = ('clean_history',3600))
        joblist.append(job)
        job.start()
            
        for job in joblist:
            job.join();

    logger.info("wlblazers controller finished.")
    

  
if __name__ == '__main__':  
    main()
