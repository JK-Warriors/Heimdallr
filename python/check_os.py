#!//bin/env python
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
import alert_os as alert
import thread
from multiprocessing import Process;

dbhost = func.get_config('monitor_server','host')
dbport = func.get_config('monitor_server','port')
dbuser = func.get_config('monitor_server','user')
dbpasswd = func.get_config('monitor_server','passwd')
dbname = func.get_config('monitor_server','dbname')

def check_os(ip,community,filter_os_disk,tags):

    command="sh check_os.sh"
    try :
        os.system("%s %s %s %s %s %s %s %s %s %s"%(command,ip,dbhost,dbport,dbuser,dbpasswd,dbname,community,filter_os_disk,tags))
        logger.info("%s:%s statspack complete."%(dbhost,dbport))
        
        # generate OS alert
        alert.gen_alert_os_status(ip)    
        alert.gen_alert_os_disk(ip)    
        alert.gen_alert_os_network(ip)     
    except Exception, e:
            print e
            logger.error("%s:%s statspack error: %s"%(dbhost,dbport,e))
            sys.exit(1)
    finally:
            sys.exit(1)


def main():

    #get os servers list
    servers=func.mysql_query("select host,community,filter_os_disk,tags from db_cfg_os where is_delete=0 and monitor=1;")
    
    logger.info("check os controller started.")
    if servers:
         plist = []
         for row in servers:
             host=row[0]
             community=row[1]
             filter_os_disk=row[2]
             tags=row[3]
             if host <> '' :
                 thread.start_new_thread(check_os, (host,community,filter_os_disk,tags))
                 time.sleep(1)
		 #p = Process(target = check_os, args=(host,filter_os_disk))
                 #plist.append(p)
                 #p.start()

         #for p in plist:
         #    p.join()

    else: 
         logger.warning("check os: not found any servers")

    logger.info("check os controller finished.")

if __name__=='__main__':
     main()
