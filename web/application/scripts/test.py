#!/usr/bin/python
#-*- coding: utf-8 -*-
import time
import paramiko
import oracle_handle as oracle
import select
import sys
  
def sshclient_execmd(hostname, port, username, password, execmd):  
    paramiko.util.log_to_file("paramiko.log")  
      
    ssh = paramiko.SSHClient()  
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())  
      
    ssh.connect(hostname=hostname, port=port, username=username, password=password) 
    #chan = s.open_session()
    #chan = ssh.get_transport().open_channel('session')
    chan = ssh.get_transport().open_session()
    chan.settimeout(5)
    chan.get_pty()
    chan.invoke_shell()
    
    chan.send('hostname'+'\n')
    time.sleep(0.5)
    print chan.recv(1024)

    chan.send('ssh strac2'+'\n')
    time.sleep(0.5)
    print chan.recv(1024)
    
    chan.send("ps -ef | grep 'LOCAL=NO' | grep -v grep | awk '{print $2}' | xargs kill -9" + "\n")
    time.sleep(0.5)
    print chan.recv(1024)
    

        
        
    chan.close()  
    ssh.close()  
      

def exe_muitl_cmd(hostname, port, username, password):
	
    conn_str = "wlblazers/wlblazers@192.168.210.210:1522/orcl"  
    conn = oracle.ConnectOracleAsSysdba(conn_str)	
	
	
    paramiko.util.log_to_file("paramiko.log")  
    ssh = paramiko.SSHClient()  
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())  
      
    ssh.connect(hostname=hostname, port=port, username=username, password=password) 
	
    #cmd_list="""["whoami", "pwd", "ls -al"]"""
    #stdin, stdout, stderr = ssh.exec_command(cmd_list)   
    #stdin.write("Y")  # Generally speaking, the first connection, need a simple interaction.  
    #print stdout.read()    
	
    cmd_list="ps -ef | grep '(LOCAL=NO)' | grep -v grep | awk '{print $2}'"
    stdin, stdout, stderr = ssh.exec_command(cmd_list + "\n")   
    stdin.write("Y")  # Generally speaking, the first connection, need a simple interaction.  
    spid_str=stdout.read() 
    spid_str=spid_str.replace("\n", " ")
    #spid_list=spid_str.split("\n")
    #spid_list.pop()
    print type(spid_str)
    print spid_str
    print stderr.read()       
	
    ssh.close()
	
	
		
    str="select p.spid from v$session s, v$process p where s.paddr = p.addr and s.program like 'python%' and type!='BACKGROUND' "
    spid=oracle.GetMultiValue(conn, str)
    for line in spid:
        print type(line[0])
        print "line: %s" %(line)
        spid_str = spid_str.replace(line[0], " ")
    
    print spid_str
      
def main():  
      
    hostname = '192.168.210.210'  
    port = 22  
    username = 'oracle'  
    password = 'oracle'  
    execmd = "ssh strac2; hostname"  
      
    #sshclient_execmd(hostname, port, username, password, execmd)  
    exe_muitl_cmd(hostname, port, username, password)  
      
      
if __name__ == "__main__":  
    main()  
