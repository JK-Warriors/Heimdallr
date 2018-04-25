#!/usr/bin/python
# -*- coding: utf-8 -*-
import netsnmp
import time
 
 
class SnmpClass(object):
    """
    SNMP
    """
    def __init__(self, version=1, destHost="localhost", community="public"):
        self.version = version
        self.destHost = destHost
        self.community = community


		# get hostname
    def get_hostname(self):
        """
        snmpwalk
        """
        hostname = ""
        try:
            result = netsnmp.snmpwalk("SNMPv2-MIB::sysName", Version=self.version, DestHost=self.destHost, Community=self.community)
            #print result
            if result:
                hostname = result[0]
        except Exception, err:
            print err
        return hostname


		# get kernel
    def get_kernel(self):
        """
        snmpwalk
        """
        kernel = ""
        try:
            result = netsnmp.snmpwalk("SNMPv2-MIB::sysDescr", Version=self.version, DestHost=self.destHost, Community=self.community)
            if result:
                kernel = result[0]
        except Exception, err:
            print err
        return kernel
        

		# get system_date
    def get_system_date(self):
        """
        snmpwalk
        """
        system_date = ""
        try:
            result = netsnmp.snmpwalk("HOST-RESOURCES-MIB::hrSystemDate", Version=self.version, DestHost=self.destHost, Community=self.community)
            if result:
                system_date = result[0]
        except Exception, err:
            print err
        return system_date


		# get system_uptime
    def get_system_uptime(self):
        """
        snmpwalk
        """
        system_date = ""
        try:
            result = netsnmp.snmpwalk("HOST-RESOURCES-MIB::hrSystemUptime", Version=self.version, DestHost=self.destHost, Community=self.community)
            if result:
                system_uptime = result[0]
        except Exception, err:
            print err
        return system_uptime     

		# get process
    def get_process(self):
        """
        snmpwalk
        """
        process = ""
        try:
            result = netsnmp.snmpwalk("HOST-RESOURCES-MIB::hrSystemProcesses", Version=self.version, DestHost=self.destHost, Community=self.community)
            if result:
                process = result[0]
        except Exception, err:
            print err
        return process     


		# get load
    def get_load(self):
        """
        snmpwalk
        """
        load_1 = ""
        load_5 = ""
        load_15 = ""
        try:
            result = netsnmp.snmpwalk("UCD-SNMP-MIB::laLoad", Version=self.version, DestHost=self.destHost, Community=self.community)
            if result:
                #print result
                load_1 = result[0]
                load_5 = result[1]
                load_15 = result[2]
        except Exception, err:
            print err
        return load_1,load_5,load_15
 
        

		# get cpu_user_time
    def get_cpu_user_time(self):
        """
        snmpwalk
        """
        cpu_user_time = ""
        try:
            result = netsnmp.snmpwalk("UCD-SNMP-MIB::ssCpuUser", Version=self.version, DestHost=self.destHost, Community=self.community)
            if result:
                cpu_user_time = result[0]
        except Exception, err:
            print err
        return cpu_user_time     


		# get cpu_system_time
    def get_cpu_system_time(self):
        """
        snmpwalk
        """
        cpu_system_time = ""
        try:
            result = netsnmp.snmpwalk("UCD-SNMP-MIB::ssCpuSystem", Version=self.version, DestHost=self.destHost, Community=self.community)
            if result:
                cpu_system_time = result[0]
        except Exception, err:
            print err
        return cpu_system_time     
        


		# get cpu_idle_time
    def get_cpu_idle_time(self):
        """
        snmpwalk
        """
        cpu_idle_time = ""
        try:
            result = netsnmp.snmpwalk("UCD-SNMP-MIB::ssCpuIdle", Version=self.version, DestHost=self.destHost, Community=self.community)
            if result:
                cpu_idle_time = result[0]
        except Exception, err:
            print err
        return cpu_idle_time     


		# get swap_total
    def get_swap_total(self):
        """
        snmpwalk
        """
        swap_total = ""
        try:
            result = netsnmp.snmpwalk("UCD-SNMP-MIB::memTotalSwap", Version=self.version, DestHost=self.destHost, Community=self.community)
            if result:
                swap_total = result[0]
        except Exception, err:
            print err
        return swap_total     
        


		# get swap_avail
    def get_swap_avail(self):
        """
        snmpwalk
        """
        swap_avail = ""
        try:
            result = netsnmp.snmpwalk("UCD-SNMP-MIB::memAvailSwap", Version=self.version, DestHost=self.destHost, Community=self.community)
            if result:
                swap_avail = result[0]
        except Exception, err:
            print err
        return swap_avail     
        


		# get mem_total
    def get_mem_total(self):
        """
        snmpwalk
        """
        mem_total = ""
        try:
            result = netsnmp.snmpwalk("UCD-SNMP-MIB::memTotalReal", Version=self.version, DestHost=self.destHost, Community=self.community)
            if result:
                mem_total = result[0]
        except Exception, err:
            print err
        return mem_total     
        


		# get mem_avail
    def get_mem_avail(self):
        """
        snmpwalk
        """
        mem_avail = ""
        try:
            result = netsnmp.snmpwalk("UCD-SNMP-MIB::memAvailReal", Version=self.version, DestHost=self.destHost, Community=self.community)
            if result:
                mem_avail = result[0]
        except Exception, err:
            print err
        return mem_avail    


		# get mem_free
    def get_mem_free(self):
        """
        snmpwalk
        """
        mem_free = ""
        try:
            result = netsnmp.snmpwalk("UCD-SNMP-MIB::memTotalFree", Version=self.version, DestHost=self.destHost, Community=self.community)
            if result:
                mem_free = result[0]
        except Exception, err:
            print err
        return mem_free  
        


		# get mem_shared
    def get_mem_shared(self):
        """
        snmpwalk
        """
        mem_shared = 0
        try:
            result = netsnmp.snmpwalk("UCD-SNMP-MIB::memShared", Version=self.version, DestHost=self.destHost, Community=self.community)
            if result:
                mem_shared = result[0]
        except Exception, err:
            print err
        finally:
            return mem_shared        
        
        

		# get mem_buffered
    def get_mem_buffered(self):
        """
        snmpwalk
        """
        mem_buffered = ""
        try:
            result = netsnmp.snmpwalk("UCD-SNMP-MIB::memBuffer", Version=self.version, DestHost=self.destHost, Community=self.community)
            if result:
                mem_buffered = result[0]
        except Exception, err:
            print err
        return mem_buffered    


		# get mem_cached
    def get_mem_cached(self):
        """
        snmpwalk
        """
        mem_cached = ""
        try:
            result = netsnmp.snmpwalk("UCD-SNMP-MIB::memCached", Version=self.version, DestHost=self.destHost, Community=self.community)
            if result:
                mem_cached = result[0]
        except Exception, err:
            print err
        return mem_cached    
        

 
                                         
def main():
    test_obj = SnmpClass(destHost="192.168.210.210")
    #print test_obj.get_hostname
 
if __name__ == '__main__':
    main()