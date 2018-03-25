#!/usr/bin/python
#-*- coding: utf-8 -*-
import time
import oracle_handle as oracle
import sys
  

      
      
      
def main():  
      
    s_conn_str = "wlblazers/wlblazers@192.168.210.210:1522/orcl"  
      
    s_conn = oracle.ConnectOracleAsSysdba(s_conn_str)	
	
	
    if s_conn is None:
        print "Connect error"
    else:
        print "Connect successfully"
		
    str='select status from v$instance'
    status=oracle.GetSingleValue(s_conn, str)  
    print "current status is: %s" %(status)
    time.sleep(300)
		
if __name__ == "__main__":  
    main()  
