#!/usr/bin/python
#coding:utf-8
import os
import sys
import string
import time
import datetime
import MySQLdb
import winrm
import logging
import logging.config
logging.config.fileConfig("etc/logger.ini")
logger = logging.getLogger("check_os")
path='./include'
sys.path.insert(0,path)
import functions as func
import alert_os as alert
import alert_main as mail
import thread
from multiprocessing import Process;

dbhost = func.get_config('monitor_server','host')
dbport = func.get_config('monitor_server','port')
dbuser = func.get_config('monitor_server','user')
dbpasswd = func.get_config('monitor_server','passwd')
dbname = func.get_config('monitor_server','dbname')

def check_os_snmp(ip,filter_os_disk,tags):
    try :
        community="public"
        
        # get hostname
        command="""/usr/bin/snmpwalk -v1 -c %s %s SNMPv2-MIB::sysName.0|awk '{print $NF}' """ %(community, ip)
        res_file=os.popen(command)
        hostname=res_file.read()
        
        if hostname != "":
            # get kernel
            command="""/usr/bin/snmpwalk -v1 -c %s %s SNMPv2-MIB::sysDescr.0|awk '{print $4 " " $6 " " $15}' """ %(community, ip)
            res_file=os.popen(command)
            kernel=res_file.read()
            
            # get system_date
            command="""/usr/bin/snmpwalk -v1 -c %s %s HOST-RESOURCES-MIB::hrSystemDate.0|cut -d '=' -f2|cut -d ' ' -f3 """ %(community, ip)
            date_file=os.popen(command)
            system_date=date_file.read()

            # get system_uptime
            command="""/usr/bin/snmpwalk -v1 -c %s %s HOST-RESOURCES-MIB::hrSystemUptime.0|cut -d ')' -f2 """ %(community, ip)
            uptime_file=os.popen(command)
            system_uptime=uptime_file.read()
            
            # get process
            command="""/usr/bin/snmpwalk -v1 -c %s %s HOST-RESOURCES-MIB::hrSystemProcesses.0|cut -d ' ' -f4 """ %(community, ip)
            uptime_file=os.popen(command)
            process=uptime_file.read()
            if process !="":
                process=int(process)
            
            # get load_1
            command="""/usr/bin/snmpwalk -v1 -c %s %s UCD-SNMP-MIB::laLoad.1 | awk '{print $NF}' """ %(community, ip)
            uptime_file=os.popen(command)
            load_1=uptime_file.read()
            if load_1 !="":
                load_1=("%.2f" %float(load_1))
            
            # get load_5
            command="""/usr/bin/snmpwalk -v1 -c %s %s UCD-SNMP-MIB::laLoad.2 | awk '{print $NF}' """ %(community, ip)
            uptime_file=os.popen(command)
            load_5=uptime_file.read()
            if load_5 !="":
                load_5=("%.2f" %float(load_5))
            
            # get load_15
            command="""/usr/bin/snmpwalk -v1 -c %s %s UCD-SNMP-MIB::laLoad.3 | awk '{print $NF}' """ %(community, ip)
            uptime_file=os.popen(command)
            load_15=uptime_file.read()
            if load_15 !="":
                load_15=("%.2f" %float(load_15))
            
        
            # get cpu_user_time
            command="""/usr/bin/snmpwalk -v1 -c %s %s UCD-SNMP-MIB::ssCpuUser.0 |awk '{print $NF}' """ %(community, ip)
            uptime_file=os.popen(command)
            cpu_user_time=uptime_file.read()
            if cpu_user_time !="":
                cpu_user_time=int(cpu_user_time)
            
            # get cpu_system_time
            command="""/usr/bin/snmpwalk -v1 -c %s %s UCD-SNMP-MIB::ssCpuSystem.0 |awk '{print $NF}' """ %(community, ip)
            uptime_file=os.popen(command)
            cpu_system_time=uptime_file.read()
            if cpu_system_time !="":
                cpu_system_time=int(cpu_system_time)
            
            # get cpu_idle_time
            command="""/usr/bin/snmpwalk -v1 -c %s %s UCD-SNMP-MIB::ssCpuIdle.0 |awk '{print $NF}' """ %(community, ip)
            uptime_file=os.popen(command)
            cpu_idle_time=uptime_file.read()
            if cpu_idle_time !="":
                cpu_idle_time=int(cpu_idle_time)
            
            
            # get swap_total
            command="""/usr/bin/snmpwalk -v1 -c %s %s UCD-SNMP-MIB::memTotalSwap.0 |cut -d= -f2 |awk -F ' ' '{print $2}' """ %(community, ip)
            uptime_file=os.popen(command)
            swap_total=uptime_file.read()
            if swap_total !="":
                swap_total=int(swap_total)
            
            # get swap_avail
            command="""/usr/bin/snmpwalk -v1 -c %s %s UCD-SNMP-MIB::memAvailSwap.0 |cut -d= -f2 |awk -F ' ' '{print $2}' """ %(community, ip)
            uptime_file=os.popen(command)
            swap_avail = uptime_file.read()
            if swap_avail !="":
                swap_avail=int(swap_avail)
            
            # get mem_total
            command="""/usr/bin/snmpwalk -v1 -c %s %s UCD-SNMP-MIB::memTotalReal.0 |cut -d= -f2 |awk -F ' ' '{print $2}' """ %(community, ip)
            uptime_file=os.popen(command)
            mem_total=uptime_file.read()
            if mem_total !="":
                mem_total=int(mem_total)
            
            # get mem_avail
            command="""/usr/bin/snmpwalk -v1 -c %s %s UCD-SNMP-MIB::memAvailReal.0 |cut -d= -f2 |awk -F ' ' '{print $2}' """ %(community, ip)
            uptime_file=os.popen(command)
            mem_avail=uptime_file.read()
            if mem_avail !="":
                mem_avail=int(mem_avail)
            
            # get mem_free
            command="""/usr/bin/snmpwalk -v1 -c %s %s UCD-SNMP-MIB::memTotalFree.0 |cut -d= -f2 |awk -F ' ' '{print $2}' """ %(community, ip)
            uptime_file=os.popen(command)
            mem_free=uptime_file.read()
            if mem_free !="":
                mem_free=int(mem_free)
            
            # get mem_shared
            command="""/usr/bin/snmpwalk -v1 -c %s %s UCD-SNMP-MIB::memShared.0 |cut -d= -f2 |awk -F ' ' '{print $2}' """ %(community, ip)
            uptime_file=os.popen(command)
            mem_shared=uptime_file.read()
            if mem_shared !="":
                mem_shared=int(mem_shared)
            
            # get mem_buffered
            command="""/usr/bin/snmpwalk -v1 -c %s %s UCD-SNMP-MIB::memBuffer.0 |cut -d= -f2 |awk -F ' ' '{print $2}' """ %(community, ip)
            uptime_file=os.popen(command)
            mem_buffered = uptime_file.read()
            if mem_buffered !="":
                mem_buffered=int(mem_buffered)
            
            # get mem_cached
            command="""/usr/bin/snmpwalk -v1 -c %s %s UCD-SNMP-MIB::memCached.0 |cut -d= -f2 |awk -F ' ' '{print $2}' """ %(community, ip)
            uptime_file=os.popen(command)
            mem_cached = uptime_file.read()
            if mem_cached !="":
                mem_cached=int(mem_cached)
            
            # calculate mem_available
            mem_available = -1
            if mem_avail != "" and mem_avail != "" and mem_avail != "":
                mem_available=int(mem_avail) + int(mem_buffered) + int(mem_cached)
            

            # get mem_usage_rate
            command="""/usr/bin/snmpdf -v1 -c %s %s |grep "Real Memory"|awk '{print $6}' | awk -F '%%' '{print $1}' """ %(community, ip)
            mem_file=os.popen(command)
            mem_usage_rate=mem_file.read()
            #print mem_usage_rate
        
            #print hostname
            #print kernel
            #print system_date
            #print system_uptime
            #print process
            #print load_1
            #print load_5
            #print load_15
        
        
            # disk usage
            command=""
            if filter_os_disk=="":
                command="""/usr/bin/snmpdf -v1 -c %s %s |grep -E "/"|grep -vE "/boot" """ %(community, ip)
            else:
                command="""/usr/bin/snmpdf -v1 -c %s %s |grep -E "/"|grep -vE "/boot" |grep -vE "%s" """ %(community, ip, filter_os_disk)
            #print command
            disk_all=os.popen(command)
            result=disk_all.readlines()
            if result:
                func.mysql_exec("insert into os_disk_his SELECT *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from os_disk where ip = '%s';" %(ip),'')
                func.mysql_exec("delete from os_disk where ip = '%s';" %(ip),'')
                for i in range(len(result)):
                    line=result[i].split()
                    mounted=line[0]
                    total_size=line[1]
                    used_size=line[2]
                    avail_size=line[3]
                    used_rate=line[4][:-1]
                    print mounted, total_size, used_size, avail_size, used_rate
                
                    ##################### insert data to mysql server#############################
                    sql = "insert into os_disk(ip,tags,mounted,total_size,used_size,avail_size,used_rate) values(%s,%s,%s,%s,%s,%s,%s);"
                    param = (ip, tags, mounted, total_size, used_size, avail_size, used_rate)
                    func.mysql_exec(sql,param) 
         
         
            #disk io begin
            disk_io_reads_total=0
            disk_io_writes_total=0
            print "get disk io table begin:"
            command="""/usr/bin/snmptable -v1 -c %s %s diskIOTable |grep -ivE "ram|loop|md|SNMP table|diskIOIndex" | grep -v '^$' """ %(community, ip)
            res_file_1=os.popen(command)
            print "wait for 5 seconds..."
            time.sleep(5)
            res_file_2=os.popen(command)
            print "get disk io table end."
            res_tab_1=res_file_1.readlines()
            res_tab_2=res_file_2.readlines()
            if res_tab_2:
                func.mysql_exec("insert into os_diskio_his SELECT *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from os_diskio where ip = '%s';" %(ip),'')
                func.mysql_exec("delete from os_diskio where ip = '%s';" %(ip),'')
                for i in range(len(res_tab_2)):
                    line_2=res_tab_2[i].split()
                    fdisk_id_2=line_2[0]
                    fdisk_name_2=line_2[1]
                    fdisk_io_reads_2=line_2[4]
                    fdisk_io_writes_2=line_2[5]
                    print fdisk_id_2, fdisk_name_2, fdisk_io_reads_2, fdisk_io_writes_2
                
                    if res_tab_1:
                        for j in range(len(res_tab_1)):
                            line_1=res_tab_1[j].split()
                            fdisk_id_1=line_1[0]
                            fdisk_name_1=line_1[1]
                            fdisk_io_reads_1=line_1[4]
                            fdisk_io_writes_1=line_1[5]
                            #print fdisk_id_1
                        
                            if fdisk_id_2==fdisk_id_1:
                                fdisk_io_reads=(int(fdisk_io_reads_2) - int(fdisk_io_reads_1))/5
                                fdisk_io_writes=(int(fdisk_io_writes_2) - int(fdisk_io_writes_1))/5
                                print fdisk_id_1, fdisk_io_reads, fdisk_io_writes
                            
                                disk_io_reads_total = disk_io_reads_total + fdisk_io_reads
                                disk_io_writes_total = disk_io_writes_total + fdisk_io_writes
                            
                                ##################### insert data to mysql server#############################
                                sql = "insert into os_diskio(ip,tags,fdisk,disk_io_reads,disk_io_writes) values(%s,%s,%s,%s,%s);"
                                param = (ip, tags, fdisk_name_1, fdisk_io_reads, fdisk_io_writes)
                                func.mysql_exec(sql,param) 
                            
                                break
                
        
            #disk io end 

         
            #net begin
            net_in_bytes_total=0
            net_out_bytes_total=0
            print "get network begin:"
            command="""/usr/bin/snmpwalk -v1 -c %s %s IF-MIB::ifDescr | grep -ivE "lo|sit0" """ %(community, ip)
            res_net_file=os.popen(command)
            net_str=res_net_file.readlines()
            if net_str:
                func.mysql_exec("insert into os_net_his SELECT *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from os_net where ip = '%s';" %(ip),'')
                func.mysql_exec("delete from os_net where ip = '%s';" %(ip),'')
                for i in range(len(net_str)):
                    line_2=net_str[i].split()
                    net_desc_id=line_2[0].split('.')[1]
                    net_desc=line_2[3]
                    #print net_desc_id
                    command="""/usr/bin/snmpwalk -v1 -c %s %s IF-MIB::ifInOctets.%s | awk '{print $NF}' """ %(community, ip, net_desc_id)
                    net_in_file_1=os.popen(command)
                
                    net_in_bytes_1 = net_in_file_1.readlines()[0]
                    #print net_in_bytes_1
                
                    command="""/usr/bin/snmpwalk -v1 -c %s %s IF-MIB::ifOutOctets.%s | awk '{print $NF}' """ %(community, ip, net_desc_id)
                    net_out_file_1=os.popen(command)
                    net_out_bytes_1 = net_out_file_1.readlines()[0]
                    #print net_out_bytes_1
                
                    time.sleep(1)
                    command="""/usr/bin/snmpwalk -v1 -c %s %s IF-MIB::ifInOctets.%s | awk '{print $NF}' """ %(community, ip, net_desc_id)
                    net_in_file_2=os.popen(command)
                
                    net_in_bytes_2 = net_in_file_2.readlines()[0]
                    #print net_in_bytes_2
                
                    command="""/usr/bin/snmpwalk -v1 -c %s %s IF-MIB::ifOutOctets.%s | awk '{print $NF}' """ %(community, ip, net_desc_id)
                    net_out_file_2=os.popen(command)
                    net_out_bytes_2 = net_out_file_2.readlines()[0]
                    #print net_out_bytes_2
                
                
                    net_in_bytes=int(net_in_bytes_2) - int(net_in_bytes_1)
                    net_out_bytes=int(net_out_bytes_2) - int(net_out_bytes_1)
                    print net_desc, net_in_bytes, net_out_bytes
                
                    net_in_bytes_total = net_in_bytes_total + net_in_bytes
                    net_out_bytes_total = net_out_bytes_total + net_out_bytes

                    ##################### insert data to mysql server#############################
                    sql = "insert into os_net(ip,tags,if_descr,in_bytes,out_bytes) values(%s,%s,%s,%s,%s);"
                    param = (ip, tags, net_desc, net_in_bytes, net_out_bytes)
                    func.mysql_exec(sql,param) 
            #net end 
        
        
        
        
            ##################### insert data to mysql server#############################
            func.mysql_exec("insert into os_status_his SELECT *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from os_status where ip = '%s';" %(ip),'')
            func.mysql_exec("delete from os_status where ip = '%s';" %(ip),'')
            sql = "insert into os_status(ip,connect,tags,hostname,kernel,system_date,system_uptime,process,load_1,load_5,load_15,cpu_user_time,cpu_system_time,cpu_idle_time,swap_total,swap_avail,mem_total,mem_avail,mem_free,mem_shared,mem_buffered,mem_cached,mem_usage_rate,mem_available,disk_io_reads_total,disk_io_writes_total,net_in_bytes_total,net_out_bytes_total) values(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
            param = (ip,1,tags, hostname, kernel, system_date,system_uptime,process,load_1,load_5,load_15,cpu_user_time,cpu_system_time,cpu_idle_time,swap_total,swap_avail,mem_total,mem_avail,mem_free,mem_shared,mem_buffered,mem_cached,mem_usage_rate,mem_available,disk_io_reads_total,disk_io_writes_total,net_in_bytes_total,net_out_bytes_total)
            func.mysql_exec(sql,param) 
        else:
            func.mysql_exec("insert into os_status_his SELECT *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from os_status where ip = '%s';" %(ip),'')
            func.mysql_exec("delete from os_status where ip = '%s';" %(ip),'')
            sql = "insert into os_status(ip,connect,tags) values(%s,%s,%s)"
            param = (ip,0,tags)
            func.mysql_exec(sql,param) 
        
        # generate OS alert
        alert.gen_alert_os_status(ip)    
        alert.gen_alert_os_disk(ip)    
        alert.gen_alert_os_network(ip)   
        
        mail.send_alert_mail(0, ip)      
    except Exception, e:
        print e.message
        logger.error("%s:%s statspack error: %s"%(dbhost,dbport,e))
    finally:
        pass



def check_os_winrm(ip, port, username, password, filter_os_disk, tags):
    try :
        # get winrm session
        url = 'http://%s:%s/wsman' %(ip,port)
        win = winrm.Session(url,auth=(username,password))


        r = win.run_cmd('wmic os get CSName /format:list')
        outstr = str(r.std_out.decode()).replace("\r","").replace("\n","")
        hostname = outstr.replace("CSName=","")
        print(hostname)

        # get kernel version
        r = win.run_cmd('wmic os get caption /format:list')
        outstr = str(r.std_out.decode()).replace("\r","").replace("\n","")
        kernel = outstr.replace("Caption=","")
        print(kernel)
        
            
        # get system_date
        r = win.run_cmd('wmic os get LocalDateTime /format:list')
        outstr = str(r.std_out.decode()).replace("\r","").replace("\n","")
        system_date = outstr.replace("LocalDateTime=","")[0:14]
        print(system_date)

        # get system_uptime
        r = win.run_cmd('wmic os get LastBootUpTime /format:list')
        outstr = str(r.std_out.decode()).replace("\r","").replace("\n","")
        system_uptime = outstr.replace("LastBootUpTime=","")[0:14]
        print(system_uptime)
            
            
        # get Process
        r = win.run_cmd('wmic process get CommandLine /format:list')
        outstr = str(r.std_out.decode())
        process = len(outstr.split("CommandLine="))-1
        print(process)


        load_1 = -1
        load_5 = -1
        load_15 = -1
        cpu_user_time = -1
        cpu_system_time = -1
        # get cpu_idle_time
        swap_total = -1
        swap_avail = -1
        # get mem_total
        # get mem_avail
        # get mem_free
        # get mem_shared
        # get mem_buffered
        # get mem_cached
        # calculate mem_available
        # get mem_usage_rate
            
        # get CPU Idle Percent
        r = win.run_cmd('wmic path Win32_PerfFormattedData_PerfOS_Processor where Name="_Total" get PercentIdleTime  /format:list')
        outstr = str(r.std_out.decode()).replace("\r","").replace("\n","")
        cpu_idle_time = outstr.replace("PercentIdleTime=","")
        print(cpu_idle_time)
    

        # get FreePhysicalMemory
        r = win.run_cmd('wmic os get FreePhysicalMemory /format:list')
        outstr = str(r.std_out.decode()).replace("\r","").replace("\n","")
        mem_free = outstr.replace("FreePhysicalMemory=","")
        print(mem_free)

        
        # get TotalVisibleMemorySize
        r = win.run_cmd('wmic os get TotalVisibleMemorySize /format:list')
        outstr = str(r.std_out.decode()).replace("\r","").replace("\n","")
        mem_total = outstr.replace("TotalVisibleMemorySize=","")
        print(mem_total)

        mem_avail = -1
        mem_shared = -1
        mem_buffered = -1
        mem_cached = -1
        mem_available = int(mem_total) - int(mem_free)
        mem_usage_rate = int(mem_available)*100/int(mem_total)

        #disk usage
        r = win.run_cmd('wmic LogicalDisk where DriveType=3 get DeviceID  /format:list')
        outstr = str(r.std_out.decode()).replace("\r","")
        list_drive = outstr.split("\n")
        for drive in list_drive:
            if drive.find("DeviceID=")>=0:
                func.mysql_exec("insert into os_disk_his SELECT *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from os_disk where ip = '%s';" %(ip),'')
                func.mysql_exec("delete from os_disk where ip = '%s';" %(ip),'')
                break
                
        for drive in list_drive:
            if drive.find("DeviceID=")>=0:
                disk = drive.replace("DeviceID=","")
                print disk
                rr = win.run_cmd('wmic LogicalDisk where DeviceID="%s" get FreeSpace  /format:list' %(disk))
                outstr = str(rr.std_out.decode()).replace("\r","").replace("\n","")
                free_space = outstr.replace("FreeSpace=","")
                free_space = int(free_space) / 1024
                print free_space
                
                rr = win.run_cmd('wmic LogicalDisk where DeviceID="%s" get Size  /format:list' %(disk))
                outstr = str(rr.std_out.decode()).replace("\r","").replace("\n","")
                total_size = outstr.replace("Size=","")
                total_size = int(total_size) / 1024
                print total_size
                
                used_size = int(total_size) - int(free_space)
                
                used_rate = used_size * 100 / int(total_size)
                
                ##################### insert data to mysql server#############################
                sql = "insert into os_disk(ip,tags,mounted,total_size,used_size,avail_size,used_rate) values(%s,%s,%s,%s,%s,%s,%s);"
                param = (ip, tags, disk, total_size, used_size, free_space, used_rate)
                func.mysql_exec(sql,param) 
         
         
        #disk io begin
        disk_io_reads_total=0
        disk_io_writes_total=0
        r = win.run_cmd('wmic LogicalDisk where DriveType=3 get DeviceID  /format:list')
        outstr = str(r.std_out.decode()).replace("\r","")
        list_disk = outstr.split("\n")
        for i in list_disk:
            if i.find("DeviceID=")>=0:
                func.mysql_exec("insert into os_diskio_his SELECT *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from os_diskio where ip = '%s';" %(ip),'')
                func.mysql_exec("delete from os_diskio where ip = '%s';" %(ip),'')
            	
        for i in list_disk:
            if i.find("DeviceID=")>=0:
                disk = i.replace("DeviceID=","")
                print disk
                
                rr = win.run_cmd('wmic path Win32_PerfFormattedData_PerfDisk_LogicalDisk where Name="%s" get DiskReadBytesPersec /format:list' %(disk))
                outstr = str(rr.std_out.decode()).replace("\r","").replace("\n","")
                io_reads = outstr.replace("DiskReadBytesPersec=","")
                print(io_reads)
                
                rr = win.run_cmd('wmic path Win32_PerfFormattedData_PerfDisk_LogicalDisk where Name="%s" get DiskWriteBytesPersec /format:list' %(disk))
                outstr = str(rr.std_out.decode()).replace("\r","").replace("\n","")
                io_writes = outstr.replace("DiskWriteBytesPersec=","")
                print(io_writes)
                
                disk_io_reads_total = disk_io_reads_total + int(io_reads)
                disk_io_writes_total = disk_io_writes_total + int(io_writes)
                ##################### insert data to mysql server#############################
                sql = "insert into os_diskio(ip,tags,fdisk,disk_io_reads,disk_io_writes) values(%s,%s,%s,%s,%s);"
                param = (ip, tags, disk, io_reads, io_writes)
                func.mysql_exec(sql,param) 
            
        #disk io end 
         
        #net begin
        net_in_bytes_total=0
        net_out_bytes_total=0
        r = win.run_cmd('wmic path Win32_PerfFormattedData_Tcpip_NetworkInterface get Name /format:list')
        outstr = str(r.std_out).replace("\r","")
        list_nic = outstr.split("\n")
        for i in list_nic:
            if i.find("Name=")>=0 and i.find("Network") > 0:
                func.mysql_exec("insert into os_net_his SELECT *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from os_net where ip = '%s';" %(ip),'')
                func.mysql_exec("delete from os_net where ip = '%s';" %(ip),'')
            	
        for i in list_nic:
            if i.find("Name=")>=0 and i.find("Network") > 0:
                nic = i.replace("Name=","")
                print nic
                	
                rr = win.run_cmd('wmic path Win32_PerfFormattedData_Tcpip_NetworkInterface where Name="%s" get BytesReceivedPersec /format:list' %(nic))
                outstr = str(rr.std_out.decode()).replace("\r","").replace("\n","")
                net_in_bytes = outstr.replace("BytesReceivedPersec=","")
                print(net_in_bytes)
                
                rr = win.run_cmd('wmic path Win32_PerfFormattedData_Tcpip_NetworkInterface where Name="%s" get BytesSentPersec /format:list' %(nic))
                outstr = str(rr.std_out.decode()).replace("\r","").replace("\n","")
                net_out_bytes = outstr.replace("BytesSentPersec=","")
                print(net_out_bytes)
        
                net_in_bytes_total = net_in_bytes_total + int(net_in_bytes)
                net_out_bytes_total = net_out_bytes_total + int(net_out_bytes)
                ##################### insert data to mysql server#############################
                sql = "insert into os_net(ip,tags,if_descr,in_bytes,out_bytes) values(%s,%s,%s,%s,%s);"
                param = (ip, tags, nic, net_in_bytes, net_out_bytes)
                func.mysql_exec(sql,param) 
        #net end
            
        

            
        ##################### insert data to mysql server#############################
        func.mysql_exec("insert into os_status_his SELECT *,DATE_FORMAT(sysdate(),'%%Y%%m%%d%%H%%i%%s') from os_status where ip = '%s';" %(ip),'')
        func.mysql_exec("delete from os_status where ip = '%s';" %(ip),'')
        sql = "insert into os_status(ip,connect,tags,hostname,kernel,system_date,system_uptime,process,load_1,load_5,load_15,cpu_user_time,cpu_system_time,cpu_idle_time,swap_total,swap_avail,mem_total,mem_avail,mem_free,mem_shared,mem_buffered,mem_cached,mem_usage_rate,mem_available,disk_io_reads_total,disk_io_writes_total,net_in_bytes_total,net_out_bytes_total) values(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
        param = (ip,1,tags, hostname, kernel, system_date,system_uptime,process,load_1,load_5,load_15,cpu_user_time,cpu_system_time,cpu_idle_time,swap_total,swap_avail,mem_total,mem_avail,mem_free,mem_shared,mem_buffered,mem_cached,mem_usage_rate,mem_available,disk_io_reads_total,disk_io_writes_total,net_in_bytes_total,net_out_bytes_total)
        func.mysql_exec(sql,param) 
        
        # generate OS alert
        alert.gen_alert_os_status(ip)    
        alert.gen_alert_os_disk(ip)    
        alert.gen_alert_os_network(ip)   
        
        mail.send_alert_mail(0, ip)      
    except Exception, e:
        print e.message
        logger.error("%s:%s statspack error: %s"%(dbhost,dbport,e))
    finally:
        pass     


######################################################################################################
# function clean_invalid_os_status
######################################################################################################   
def clean_invalid_os_status():
    try:
        func.mysql_exec("insert into os_status_his SELECT *,sysdate() from os_status where ip not in(select host from db_cfg_os);",'')
        func.mysql_exec('delete from os_status where ip not in(select host from  db_cfg_os);','')
        
        func.mysql_exec("insert into os_disk_his SELECT *,sysdate() from os_disk where ip not in(select host from db_cfg_os);",'')
        func.mysql_exec('delete from os_disk where ip not in(select host from  db_cfg_os);','')
        
        func.mysql_exec("insert into os_diskio_his SELECT *,sysdate() from os_diskio where ip not in(select host from db_cfg_os);",'')
        func.mysql_exec('delete from os_diskio where ip not in(select host from  db_cfg_os);','')
        
        func.mysql_exec("insert into os_net_his SELECT *,sysdate() from os_net where ip not in(select host from db_cfg_os);",'')
        func.mysql_exec('delete from os_net where ip not in(select host from  db_cfg_os);','')
        
        func.mysql_exec("delete from db_status where db_type = 'os' and host not in(select host from db_cfg_os);",'')
        
    except Exception, e:
        logger.error(e)
    finally:
        pass
               
               
def main():
    #get os servers list
    servers=func.mysql_query("select host,protocol,port,username,password,filter_os_disk,tags from db_cfg_os where is_delete=0 and monitor=1;")
    
    logger.info("check os controller started.")
    if servers:
         plist = []
         for row in servers:
             host=row[0]
             protocol=row[1]
             port=row[2]
             username=row[3]
             password=row[4]
             filter_os_disk=row[5]
             tags=row[6]
             if host <> '' :
                 #thread.start_new_thread(check_os, (host,community,filter_os_disk,tags))
                 #time.sleep(1)
                 if protocol == 'snmp':
                     p = Process(target = check_os_snmp, args=(host,filter_os_disk,tags))
                     plist.append(p)
                     p.start()
                 elif protocol == 'winrm':
                     p = Process(target = check_os_winrm, args=(host,'5985', username,password,filter_os_disk,tags))
                     plist.append(p)
                     p.start()

         for p in plist:
             p.join()

    else: 
         logger.warning("check os: not found any servers")

    logger.info("check os controller finished.")

    logger.info("Clean invalid os status start.")   
    clean_invalid_os_status()
    logger.info("Clean invalid os status finished.")       
    
if __name__=='__main__':
     main()
