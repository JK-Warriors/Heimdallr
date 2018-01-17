#!/usr/bin/python
# -*- coding: UTF-8 -*-
###############################################################################
# Mysql handle Script
# Created on 2017-01-10
# Author : Kevin Gideon
# Version: 1.0
# Usage:
###############################################################################

###############################################################################
# define functions
###############################################################################
import pymysql
import time,datetime
import ConfigParser as configparser				#python 2.7以下，只能识别到ConfigParser，python3以后，用的是import configparser
import os
import sys

import logging
import logging.config

logging.config.fileConfig('./logging.conf')
logger = logging.getLogger('WLBlazers')
#####################################################################################################
# 连接数据库
def ConnectMysql():
        # 读取配置信息
        config = configparser.ConfigParser()
        curPath = os.getcwd()
        parent_path = os.path.abspath(os.path.dirname(curPath) + os.path.sep + ".")
        #print(parent_path)

        config.read(parent_path + '/scripts/param.ini')
        host_id = config.get("wlblazers", "host")
        port = config.getint("wlblazers", "port")
        username = config.get("wlblazers", "user")
        password = config.get("wlblazers", "passwd")
        dbname = config.get("wlblazers", "db")


        # 打开数据库连接
        try:
            return pymysql.connect(host=host_id, user=username, passwd=password, port=port, db=dbname, charset='utf8')
        except Exception as e:
            logger.error("Connect to wlblazers error: " + str(e))
            sys.exit(2)


def CloseMysql(conn):
    # 关闭数据库连接
    if conn:
        conn.close()


# 取单个返回值
def GetSingleValue(conn, query_str):
    res = None
    try:
        # 使用cursor()方法获取操作游标
        cur = conn.cursor()

        # 使用execute方法执行SQL语句
        cur.execute(query_str)

        # 使用fetchall获取数据集
        rows = cur.fetchall()
        for row in rows:
            res = row[0]

        return res
    except Exception as e:
        logger.error(e)


# 获取mysql库查询结果集
def GetMultiValue(conn, query_str):
    rows = None
    try:
        # 使用cursor()方法获取操作游标
        cur = conn.cursor()

        # 使用execute方法执行SQL语句
        cur.execute(query_str)

        # 使用fetchall获取数据集
        rows = cur.fetchall()

        return rows
    except Exception as e:
        logger.error(e)

		
def ExecuteSQL(conn, query_str):
    res = None
    try:
        # 使用cursor()方法获取操作游标
        cur = conn.cursor()

        # 使用execute方法执行SQL语句
        cur.execute(query_str)

        # 提交
        conn.commit()

        return 1
    except Exception as e:
        logger.error(e)
        conn.rollback()

###############################################################################
# main
###############################################################################
'''
if __name__ == "__main__":
    # 建立mysql连接
    mysql_conn = ConnectMysql()
    query_str = "select value from aop_perf_stat where inst_id = 1 and name = 'parse count (total)' order by time desc limit 1;"

    value = GetSingleValue(mysql_conn, query_str)
    CloseMysql(mysql_conn)
    print value
'''
