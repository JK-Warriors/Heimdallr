# -*- coding:utf-8 -*-
import re
import os
import time


#�����ļ�
f = open(r'/wlblazers/site/web/application/scripts/readme.txt') 
#����ļ�
fw = open(r'/wlblazers/site/web/application/scripts/readme1.log','w')
#���ж��������ı�
lines = f.readlines()
num = -1
for line in lines:
    str = '@SES/%i/' %num
    line = line.replace('@SES/1/',str)
    num = num + 1
    #д���ļ�
    fw.writelines(line)
#�ر��ļ����
f.close()
fw.close()