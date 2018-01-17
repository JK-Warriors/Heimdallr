#-*- coding: utf-8 -*-

############ Script Bilgisi ##########

# Versiyon 	    : 1

# Gelistirici  	    : Fatih Acar

# E-Mail   	    : fatih@fatihacar.com

# Aciklama : Bu Script RAC veya Single Instance olarak calisan 
# Oracle veritabaninda meydana gelen bozulma durumunda 
# bu sisteme bagli calisan Physical Standby Oracle Data Guard veritabaninin 
# ayaga kalkip prod sistem gibi hizmet verebilmesini saglayacak
# olan islemleri yapmaktadir. 

# Anahtar Kelimeler : Oracle Data Guard Failover, Create Virtual IP, 
# Oracle Data Guard Open, Oracle Failover, Physical Standby Database

############ Kutuphaneler #############

import os
import sys
import commands

############# Degiskenler #############

# Virtual ve Scan IP parametrelerine PROD veritabaninin Virtual ve Scan IP degerleri yazilmalidir.

dg_ip = '192.168.27.146' # Mevcut Data Guard sunucusunun IP adresi
virtual_ip_1 = '192.168.27.143' # Bos birakilirsa IP atamasi yapilmaz.
virtual_ip_2 = '192.168.27.142' # Bos birakilirsa IP atamasi yapilmaz.
scan_ip_1 = '192.168.27.141' # Bos birakilirsa IP atamasi yapilmaz.
scan_ip_2 = '192.168.27.140' # Bos birakilirsa IP atamasi yapilmaz.
scan_ip_3 = '192.168.27.139' # Bos birakilirsa IP atamasi yapilmaz.

ethernet_adaptor = 'eth0' # Data Guard sunucusu ethernet adaptor bilgisi

prod_service_name = 'DWH' # Prod veritabaninin service_names degeri yazilmalidir.

#######################################

def failover_db():
	
	try:
		print "\033[92mSUCCESS : /tmp/failover_query.sql dosyasi olusturuluyor.\033[0m"
		file = open('/tmp/failover_query.sql','w')
		file.write('ALTER DATABASE RECOVER MANAGED STANDBY DATABASE FINISH;\n')
		file.write('ALTER DATABASE ACTIVATE STANDBY DATABASE;\n')
        	file.write('ALTER DATABASE OPEN;\n')
		file.write('ALTER SYSTEM SET SERVICE_NAMES=' +prod_service_name+ ';\n')
		file.write('ALTER SYSTEM REGISTER;\n')
		file.write('SELECT OPEN_MODE FROM V$DATABASE;\n')
		file.write('EXIT;')
		file.close()
		os.system('chown oracle.oinstall /tmp/failover_query.sql')
		print "\033[92mSUCCESS : /tmp/failover_query.sql dosyasi olusturuldu.\033[0m"
	except:
		print "\033[91mERROR : /tmp/failover_query.sql dosyasi olusturulamadi !\033[0m"
	try:
		if os.path.isfile("/tmp/failover_query.sql"):
			print "\033[92mSUCCESS : Failover islemleri basliyor.\033[0m"
			try:
				os.system('su - oracle -c "sqlplus / as sysdba @/tmp/failover_query.sql"')
				print "\033[92mSUCCESS : Failover islemleri tamamlandi.\033[0m"
			except:
				print "\033[91mERROR : Failover islemleri sirasinda hata alindi.\033[0m"

			print "\033[92mSUCCESS : /tmp/failover_query.sql dosyasi siliniyor.\033[0m"	
			try:    
	                	os.system('rm -f /tmp/failover_query.sql')
				print "\033[92mSUCCESS : /tmp/failover_query.sql dosyasi silindi.\033[0m"
        		except: 
				print "\033[91mERROR : /tmp/failover_query.sql dosyasi silinirken hata alindi.\033[0m"

	except:
			print "\033[91mERROR : Failover islemleri sirasinda hata alindi.\033[0m"

        print ('\033[93mWARNING : Eger failover islemleri sirasinda READ WRITE durumu gozukmediyse failover islem adimlarinda problem meydana gelmistir !\033[0m')

	print "\033[92mSUCCESS : IP alma ve failover islemleri tamamlandi.\033[0m"

def take_virtual_ip():

	print "\033[92mSUCCESS : Virtual IPler aliniyor..\033[0m"
	if virtual_ip_1 <> '':
		try:
			os.system('ifconfig '+ethernet_adaptor+':0 ' +virtual_ip_1)
			print ('\033[92mSUCCESS : ' +virtual_ip_1+ ' alindi.\033[0m')
		except:
			print ('\033[91mERROR : ' +virtual_ip_1+ ' alinamadi !\033[0m')
	else:
		print ('\033[93mWARNING : virtual_ip_1 parametresi bos oldugu icin IP alinamadi.\033[0m')
	if virtual_ip_2 <> '':
		try:
			os.system('ifconfig '+ethernet_adaptor+':1 '+virtual_ip_2)
			print ('\033[92mSUCCESS : ' +virtual_ip_2+ ' alindi.\033[0m')
		except:
			print ('\033[91mERROR : ' +virtual_ip_2+ ' alinamadi !\033[0m')
	else:
		print ('\033[93mWARNING : virtual_ip_2 parametresi bos oldugu icin IP alinamadi.\033[0m')
	if scan_ip_1 <> '':
		try:
			os.system('ifconfig '+ethernet_adaptor+':2 '+scan_ip_1)
			print ('\033[92mSUCCESS : ' +scan_ip_1+ ' alindi.\033[0m')
		except:
			print ('\033[91mERROR : ' +scan_ip_1+ ' alinamadi !\033[0m')
	else:
                print ('\033[93mWARNING : scan_ip_1 parametresi bos oldugu icin IP alinamadi.\033[0m')
	if scan_ip_2 <> '':
                try:
			os.system('ifconfig '+ethernet_adaptor+':3 '+scan_ip_2)
			print ('\033[92mSUCCESS : ' +scan_ip_2+ ' alindi.\033[0m')
		except:
                        print ('\033[91mERROR : ' +scan_ip_2+ ' alinamadi !\033[0m')
        else:
                print ('\033[93mWARNING : scan_ip_2 parametresi bos oldugu icin IP alinamadi.\033[0m')
	if scan_ip_3 <> '':
                try:
			os.system('ifconfig '+ethernet_adaptor+':4 '+scan_ip_3)
			print ('\033[92mSUCCESS : ' +scan_ip_3+ ' alindi.\033[0m')
		except:
                        print ('\033[91mERROR : ' +scan_ip_3+ ' alinamadi !\033[0m')
        else:
                print ('\033[93mWARNING : scan_ip_3 parametresi bos oldugu icin IP alinamadi.\033[0m')

	print "IP adreslerinin son durumu listeleniyor.."
	print os.system('ifconfig')
	print ('\033[93mWARNING : IP adresleri ethernet adaptorlerinde gozukmuyorsa diger yontemleri kullanarak IP adreslerini almayi deneyiniz !\033[0m')


def release_virtual_ip():

	print "\033[92mSUCCESS : Virtual IPler serbest birakiliyor..\033[0m"
	os.system('ifconfig '+ethernet_adaptor+':0 down')
	os.system('ifconfig '+ethernet_adaptor+':1 down')
	os.system('ifconfig '+ethernet_adaptor+':2 down')
	os.system('ifconfig '+ethernet_adaptor+':3 down')
	os.system('ifconfig '+ethernet_adaptor+':4 down')
	print os.system('ifconfig')
	print "\033[92mSUCCESS : Virtual IPler serbest birakildi..\033[0m"

def take_permanent_virtual_ip():
	
	try:		
		print "\033[92mSUCCESS : Kalici olarak Virtual IPler aliniyor.\033[0m"
		if virtual_ip_1 <> '':
			file = open('/etc/sysconfig/network-scripts/ifcfg-'+ethernet_adaptor+':0','w')
			file.write('DEVICE="'+ethernet_adaptor+':0"\n')
			file.write('NAME="System '+ethernet_adaptor+':0"\n')
			file.write('NM_CONTROLLED="yes"\n')
			file.write('TYPE="Ethernet"\n')
			file.write('ONBOOT="yes"\n')
			file.write('IPADDR='+virtual_ip_1+'\n')
			file.close()
			os.system('/sbin/ifup '+ethernet_adaptor+':0')
			print ('\033[92mSUCCESS : ' +virtual_ip_1+ ' IP si icin ethernet olusturuldu.\033[0m')
		else:
			print ('\033[93mWARNING : virtual_ip_1 parametresi bos oldugu icin IP alinamadi.\033[0m')
	
		if virtual_ip_2 <> '':
			file = open('/etc/sysconfig/network-scripts/ifcfg-'+ethernet_adaptor+':1','w')
                	file.write('DEVICE="'+ethernet_adaptor+':1"\n')
			file.write('NAME="System '+ethernet_adaptor+':1"\n') 
			file.write('NM_CONTROLLED="yes"\n')
			file.write('TYPE="Ethernet"\n')
                	file.write('ONBOOT="yes"\n')
                	file.write('IPADDR='+virtual_ip_2+'\n')
                	file.close()
			os.system('/sbin/ifup '+ethernet_adaptor+':1')
			print ('\033[92mSUCCESS : ' +virtual_ip_2+ ' IP si icin ethernet olusturuldu.\033[0m')
		else:
                        print ('\033[93mWARNING : virtual_ip_2 parametresi bos oldugu icin IP alinamadi.\033[0m')

                if scan_ip_1 <> '':
                        file = open('/etc/sysconfig/network-scripts/ifcfg-'+ethernet_adaptor+':2','w')
                        file.write('DEVICE="'+ethernet_adaptor+':2"\n')
                        file.write('NAME="System '+ethernet_adaptor+':2"\n')
                        file.write('NM_CONTROLLED="yes"\n')
                        file.write('TYPE="Ethernet"\n')
                        file.write('ONBOOT="yes"\n')
                        file.write('IPADDR='+scan_ip_1+'\n')
                        file.close()
                        os.system('/sbin/ifup '+ethernet_adaptor+':2')
                        print ('\033[92mSUCCESS : ' +scan_ip_1+ ' IP si icin ethernet olusturuldu.\033[0m')
                else:
                        print ('\033[93mWARNING : scan_ip_1 parametresi bos oldugu icin IP alinamadi.\033[0m')
 		
		if scan_ip_2 <> '':
                        file = open('/etc/sysconfig/network-scripts/ifcfg-'+ethernet_adaptor+':3','w')
                        file.write('DEVICE="'+ethernet_adaptor+':3"\n')
                        file.write('NAME="System '+ethernet_adaptor+':3"\n')
                        file.write('NM_CONTROLLED="yes"\n')
                        file.write('TYPE="Ethernet"\n')
                        file.write('ONBOOT="yes"\n')
                        file.write('IPADDR='+scan_ip_2+'\n')
                        file.close()
                        os.system('/sbin/ifup '+ethernet_adaptor+':3')
                        print ('\033[92mSUCCESS : ' +scan_ip_2+ ' IP si icin ethernet olusturuldu.\033[0m')
                else:
                        print ('\033[93mWARNING : scan_ip_2 parametresi bos oldugu icin IP alinamadi.\033[0m')

		if scan_ip_3 <> '':
                        file = open('/etc/sysconfig/network-scripts/ifcfg-eth0:4','w')
                        file.write('DEVICE="'+ethernet_adaptor+':4"\n')
                        file.write('NAME="System '+ethernet_adaptor+':4"\n')
                        file.write('NM_CONTROLLED="yes"\n')
                        file.write('TYPE="Ethernet"\n')
                        file.write('ONBOOT="yes"\n')
                        file.write('IPADDR='+scan_ip_3+'\n')
                        file.close()
                        os.system('/sbin/ifup '+ethernet_adaptor+':4')
                        print ('\033[92mSUCCESS : ' +scan_ip_3+ ' IP si icin ethernet olusturuldu.\033[0m')
                else:
                        print ('\033[93mWARNING : scan_ip_3 parametresi bos oldugu icin IP alinamadi.\033[0m')
		
		print "\033[92mSUCCESS : Network servisi restart ediliyor..\033[0m"
		os.system('service network restart')
		print "\033[92mSUCCESS : Network servisi restart edildi.\033[0m"
	except:
		print "\033[91mERROR : Virtual Adaptor dosyalari olustulurken hata olustu !\033[0m"
	print "IP adreslerinin son durumu listeleniyor.."
        print os.system('ifconfig')
        print ('\033[93mWARNING : IP adresleri ethernet adaptorlerinde gozukmuyorsa diger yontemleri kullanarak IP adreslerini almayi deneyiniz !\033[0m')

def take_virtual_ip_as_service():

	print "\033[92mSUCCESS : /etc/init.d/take_virtual_ip dosyasi olusturuluyor.\033[0m"
	file = open('/etc/init.d/take_virtual_ip','w')
	file.write('#!/bin/bash\n')
	file.write('# chkconfig: 56 20 80\n')
	file.write('# description: Take virtual IP\n\n')
	file.write('# Source fuction library.\n')
	file.write('. /etc/init.d/functions\n\n')
	file.write('start() {\n')
	file.write('	ifconfig '+ethernet_adaptor+' '+dg_ip+' netmask 255.255.255.0\n')
	if virtual_ip_1 <> '':
		file.write('	ifconfig '+ethernet_adaptor+':0 '+virtual_ip_1+' netmask 255.255.255.0\n')
	if virtual_ip_2 <> '':
		file.write('    ifconfig '+ethernet_adaptor+':1 '+virtual_ip_2+' netmask 255.255.255.0\n')
	if scan_ip_1 <> '':
		file.write('    ifconfig '+ethernet_adaptor+':2 '+scan_ip_1+' netmask 255.255.255.0\n')
	if scan_ip_2 <> '':	
		file.write('    ifconfig '+ethernet_adaptor+':3 '+scan_ip_2+' netmask 255.255.255.0\n')
	if scan_ip_3 <> '':
		file.write('    ifconfig '+ethernet_adaptor+':4 '+scan_ip_3+' netmask 255.255.255.0\n')	
	file.write('}\n\n')
	file.write('case "$1" in\n')
	file.write('	start)\n')
	file.write('		start\n')
	file.write('		;;\n')
	file.write('	stop)\n')
	file.write('		stop\n')
	file.write('		;;\n')
	file.write('	restart)\n')
	file.write('		stop\n')
	file.write('		start\n')
	file.write('		;;\n')
	file.write('	status)\n')
	file.write('		;;\n')
	file.write('	*)\n')
	file.write('		echo "Usage: $0 {start|stop|status|restart}"\n')
	file.write('esac\n\n')
	file.write('exit 0\n')
	file.close()
	if os.path.exists('/etc/init.d/take_virtual_ip'):
                print "\033[92mSUCCESS : /etc/init.d/take_virtual_ip dosyasi olusturuldu.\033[0m"
		try:
			os.system('chmod +x /etc/init.d/take_virtual_ip')
        		os.system('chkconfig --add /etc/init.d/take_virtual_ip')
        		os.system('chkconfig --level 56 take_virtual_ip on')
			print "\033[92mSUCCESS : take_virtual_ip servisi olusturuldu.\033[0m"
			try:
				os.system('service take_virtual_ip start')
				print "\033[92mSUCCESS : take_virtual_ip servisi calistirildi.\033[0m"
			except:
				print "\033[91mERROR : take_virtual_ip servisi calistirilirken hata olustu !\033[0m"
		except:
			print "\033[91mERROR : take_virtual_ip servisi olusturulamadi !\033[0m"
	else:
		print "\033[91mERROR : /etc/init.d/take_virtual_ip dosyasi olustururken hata olustu !\033[0m"
	print "IP adreslerinin son durumu listeleniyor.."
	print os.system('ifconfig')
	print ('\033[93mWARNING : IP adresleri ethernet adaptorlerinde gozukmuyorsa diger yontemleri kullanarak IP adreslerini almayi deneyiniz !\033[0m')

def main():

#	take_virtual_ip()
#	release_virtual_ip()
	print "\n\n ###################### Data Guard Failover ###################### \n\n"
	print "DIKKAT ! Bu uygulama Oracle Data Guard veritabanini PROD veritabani olarak aktif hale getirir ! \n"
	print "###### Parametreler ######\n"
	print ('dg_ip = ' +dg_ip)
	print ('virtual_ip_1 = ' +virtual_ip_1)
	print ('virtual_ip_2 = ' +virtual_ip_2)
	print ('scan_ip_1 = ' +scan_ip_1)
	print ('scan_ip_2 = ' +scan_ip_2)
	print ('scan_ip_3 = ' +scan_ip_3)
	print ('ethernet_adaptor = ' +ethernet_adaptor)
	print ('prod_service_name = ' +prod_service_name)
	print ('\n#########################\n')
	if raw_input("Parametreler dogru ise devam etmek icin evet yazip enter tusuna basiniz : ") == 'evet': 
		
		secim1 = raw_input("Sadace IP alma islemi icin 1 e, hem IP alma hem de failover islemleri icin 2 ye basiniz : ")
		if secim1 == '1':
                        print "Virtual IP alma islemi nasil yapilsin ?"
                        print "1 - Service olusturarak kalici olarak olustur (Onerilen)"
                        print "2 - Virtual Ethernet Adaptorleri olusturarak kalici olustur"
                        print "3 - Gecici olarak Virtual IP olustur (Sistem veya Network Restart edildiginde IP ler kaybedilir)"
                        secim = raw_input("Seciminiz (1,2,3) : ")
                        if secim == '1':
                                take_virtual_ip_as_service()
                        elif secim == '2':
                                take_permanent_virtual_ip()
                        elif secim == '3':
                                take_virtual_ip()
                        else:
                                print "Dogru secim yapmadiniz !"
                                print "Uygulama calistirilmadan sonlandirildi."		

		elif secim1 == '2':
			print "Virtual IP alma islemi nasil yapilsin ?"
                        print "1 - Service olusturarak kalici olarak olustur (Onerilen)"
			print "2 - Virtual Ethernet Adaptorleri olusturarak kalici olustur"
			print "3 - Gecici olarak Virtual IP olustur (Sistem veya Network Restart edildiginde IP ler kaybedilir)"
                        secim = raw_input("Seciminiz (1,2,3) : ")
                        if secim == '1':
				take_virtual_ip_as_service()	
			elif secim == '2':
                                take_permanent_virtual_ip()
                        elif secim == '3':
                                take_virtual_ip()
                        else:
                		print "Dogru secim yapmadiniz !"
                                print "Uygulama calistirilmadan sonlandirildi."		

			print "\033[92mSUCCESS : Failover islemi basliyor..\n\033[0m"
						
			failover_db()

			print ('\033[93mWARNING : Failover islemlerinin ardindan RMAN ile FULL yedek alinmasi onerilir ! \033[0m')
		else:
			print "Virtual IP olusturma ve failover islemleri yapilmadi !"
	else:
		print "Uygulama calistirilmadan sonlandirildi."

main()
