#!/bin/env python
#-*-coding:utf-8-*-

import MySQLdb
import string
import sys 
reload(sys) 
sys.setdefaultencoding('utf8')
import ConfigParser

def get_item(data_dict,item):
    try:
       item_value = data_dict[item]
       return item_value
    except:
       pass


def get_parameters(conn):
    try:
        curs=conn.cursor()
        data=curs.execute('select name,value from v$parameter');
        data_list=curs.fetchall()
        parameters={}
        for item in data_list:
            parameters[item[0]] = item[1]

    except Exception,e:
        print e

    finally:
        curs.close()

    return parameters


def get_sysstat(conn):
    try:
        curs=conn.cursor()
        data=curs.execute('select name,value value from v$sysstat');
        data_list=curs.fetchall()
        sysstat={}
        for item in data_list:
            sysstat[item[0]] = item[1]

    except Exception,e:
        print e

    finally:
        curs.close()

    return sysstat


def get_instance(conn,field):
    try:
        curs=conn.cursor()
        curs.execute("select %s from v$instance" %(field) );
        result = curs.fetchone()[0]

    except Exception,e:
        result = ''
        print e

    finally:
        curs.close()

    return result


def get_database(conn,field):
    try:
        curs=conn.cursor()
        curs.execute("select %s from v$database" %(field) );
        result = curs.fetchone()[0]

    except Exception,e:
        result = ''
        print e

    finally:
        curs.close()

    return result


def get_version(conn):
    try:
        curs=conn.cursor()
        curs.execute("select product,version from product_component_version where product like '%Database%'");
        result = curs.fetchone()[1]

    except Exception,e:
        print e

    finally:
        curs.close()

    return result


def get_sessions(conn):
    try:
        curs=conn.cursor()
        curs.execute("select count(*) from v$session");
        result = curs.fetchone()[0]
        return result

    except Exception,e:
        return null    
        print e

    finally:
        curs.close()



def get_actives(conn):
    try:
        curs=conn.cursor()
        curs.execute("select count(*) from v$session where username not in('SYS','SYSTEM') and username is not null and STATUS='ACTIVE'");
        result = curs.fetchone()[0]
        return result

    except Exception,e:
        return null
        print e

    finally:
        curs.close()


def get_waits(conn):
    try:
        curs=conn.cursor()
        curs.execute("select count(*) from v$session where event like 'library%' or event like 'cursor%' or event like 'latch%'  or event like 'enq%' or event like 'log file%'");
        result = curs.fetchone()[0]
        return result

    except Exception,e:
        return null
        print e

    finally:
        curs.close()


def get_dg_stats(conn):
    try:
        curs=conn.cursor()
        curs.execute("SELECT substr((SUBSTR(VALUE,5)),0,2)*3600 + substr((SUBSTR(VALUE,5)),4,2)*60 + substr((SUBSTR(VALUE,5)),7,2) AS seconds,VALUE FROM v$dataguard_stats a WHERE NAME ='apply lag'");
        list = curs.fetchone()
        if list:
            result = 1
        else:
            result = 0
        return result

    except Exception,e:
        return null
        print e

    finally:
        curs.close()



def get_dg_delay(conn):
    try:
        curs=conn.cursor()
        curs.execute("SELECT substr((SUBSTR(VALUE,5)),0,2)*3600 + substr((SUBSTR(VALUE,5)),4,2)*60 + substr((SUBSTR(VALUE,5)),7,2) AS seconds,VALUE FROM v$dataguard_stats a WHERE NAME ='apply lag'");
        list = curs.fetchone()
        if list:
            result = list[0] 
        else:
            result = '---'
        return result

    except Exception,e:
        return null
        print e

    finally:
        curs.close()



def get_dg_p_info(conn, dest_id):
    try:
        curs=conn.cursor()
        curs.execute("""select *
                            from (select dest_id,
                                        thread#,
                                        sequence#,
                                        archived,
                                        applied,
                                        current_scn,
                                        to_char(scn_to_timestamp(current_scn), 'yyyy-mm-dd hh24:mi:ss') curr_db_time,
                                        row_number() over(partition by thread# order by sequence# desc) rn
                                    from v$archived_log t, v$database d
                                    where t.dest_id = %s)
                            where rn = 1; """ %(dest_id));
        result = curs.fetchone()
        
        return result
    except Exception,e:
        print e

    finally:
        curs.close()



def get_dg_s_ms(conn):
    try:
        curs=conn.cursor()
        curs.execute("""select ms.thread#,
                               ms.sequence#,
                               ms.block#,
                               ms.delay_mins
                          from v$managed_standby  ms
                         where ms.process in ('MRP0')
                           and ms.sequence# <> 0 """);
        result = curs.fetchone()

        return result
    except Exception,e:
        return null
        print e

    finally:
        curs.close()


def get_dg_s_al(conn):
    try:
        curs=conn.cursor()
        curs.execute(""" select thread#,
                                max(sequence#) sequence#
                           from v$archived_log t
                          where t.dest_id = 1
                            and t.applied = 'YES'
                          group by thread# """);
        result = curs.fetchone()

        return result
    except Exception,e:
        return null
        print e

    finally:
        curs.close()


def get_dg_s_rate(conn):
    try:
        curs=conn.cursor()
        curs.execute("""select rp.sofar avg_apply_rate
                          from v$recovery_progress rp
                         where rp1.item = 'Average Apply Rate' """);
        result = curs.fetchone()

        return result
    except Exception,e:
        return null
        print e

    finally:
        curs.close()


def get_dg_s_lar(conn):
    try:
        curs=conn.cursor()
        curs.execute("""select replace(rp.comments, 'SCN: ', '') current_scn,
                               to_char(rp.timestamp, 'yyyy-mm-dd hh24:mi:ss') curr_db_time
                          from v$recovery_progress rp
                         where rp.item = 'Last Applied Redo' """);
        result = curs.fetchone()

        return result
    except Exception,e:
        return null
        print e

    finally:
        curs.close()


def get_tablespace(conn):
    try:
        curs=conn.cursor()
        curs.execute("select df.tablespace_name ,totalspace total_size, (totalspace-freespace) used_size,freespace avail_size ,round((1-freespace/totalspace)*100) || '%' as used_ratio from (select tablespace_name,round(sum(bytes)/1024/1024) totalspace from dba_data_files group by tablespace_name) df,(select tablespace_name,round(sum(bytes)/1024/1024) freespace from dba_free_space group by tablespace_name) fs where df.tablespace_name=fs.tablespace_name and df.tablespace_name not like 'UNDOTBS%'");
        list = curs.fetchall()
        return list

    except Exception,e:
        return null
        print e

    finally:
        curs.close()
