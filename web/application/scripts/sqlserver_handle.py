#!/usr/bin/python
# -*- coding: UTF-8 -*-
###############################################################################
# SqlServer handle Script
# Created on 2019-07-12
# Author : Kevin Gideon
# Version: 1.0
# Usage:
###############################################################################
import pymssql
import time,datetime
import os
import sys

import logging
import logging.config

logging.config.fileConfig('./logging.conf')
logger = logging.getLogger('WLBlazers')
###############################################################################
# define functions
###############################################################################
# 连接数据库
def ConnectMssql(host,port,username,passwd):
    # 打开数据库连接
    try:
        return pymssql.connect(host=host,port=int(port),user=username,password=passwd,charset="utf8")
    except Exception as e:
        logger.error(e)



def CloseMssql(conn):
    # 关闭数据库连接
    if conn:
        conn.close()


def GetSingleValue(conn,str):
    res = None

    try:
        # 使用cursor()方法获取操作游标
        cur = conn.cursor()

        # 使用execute方法执行SQL语句
        cur.execute(str)

        # 使用fetchall获取数据集
        rows = cur.fetchall()
        for row in rows:
            res = row[0]
		
        cur.close()
		
        return res
    except Exception as e:
        logger.error(e)

		
def GetSingleRow(conn, str):
    res = None

    try:
        # 使用cursor()方法获取操作游标
        cur = conn.cursor()

        # 使用execute方法执行SQL语句
        cur.execute(str)

        # 使用fetchall获取数据集
        rows = cur.fetchone()
		
        cur.close()
		
        return rows
    except Exception as e:
        logger.error(e)


def GetMultiValue(conn, str):
    res = None

    try:
        # 使用cursor()方法获取操作游标
        cur = conn.cursor()

        # 使用execute方法执行SQL语句
        cur.execute(str)

        # 使用fetchall获取数据集
        rows = cur.fetchall()
		
        cur.close()
		
        return rows
    except Exception as e:
        logger.error(e)
        
        		
def ExecuteSQL(conn, str):
    res = None

    try:
        # 使用cursor()方法获取操作游标
        cur = conn.cursor()

        # 使用execute方法执行SQL语句
        cur.execute(str)
		
        cur.close()
        conn.commit()
        return 1
    except Exception as e:
        logger.error(e)
        conn.rollback()
		
###############################################################################
# main test
###############################################################################
'''
if __name__ == "__main__":
    # 建立mysql连接
    oracle_conn = ConnectOracle()
    query_str = "select sysdate from dual"

    value = GetSingleValue(oracle_conn, query_str)
    CloseOracle(oracle_conn)
    print value
'''
