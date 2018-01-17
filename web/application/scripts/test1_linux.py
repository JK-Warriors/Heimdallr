# -*- coding:utf-8 -*-
import re
import os
import time


#输入文件
f = open(r'/wlblazers/site/web/application/scripts/readme.txt') 
#输出文件
fw = open(r'/wlblazers/site/web/application/scripts/readme1.log','w')
#按行读出所有文本
lines = f.readlines()
num = -1
for line in lines:
    str = '@SES/%i/' %num
    line = line.replace('@SES/1/',str)
    num = num + 1
    #写入文件
    fw.writelines(line)
#关闭文件句柄
f.close()
fw.close()