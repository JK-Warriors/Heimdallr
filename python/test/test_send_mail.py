#!/bin/env python
#-*-coding:utf-8-*-

import string
import sys 
reload(sys) 
sys.setdefaultencoding('utf8')
import ConfigParser
import logging
import logging.config
import smtplib
from email.mime.text import MIMEText
from email.message import Message
from email.header import Header


mail_host = "smtp.exmail.qq.com"
mail_port = 465
mail_user = "service@hzywy.cn"
mail_pass = "ywykj321#HZ"
mail_send_from = "service@hzywy.cn"


def send_mail(to_list,sub,content):
    '''
    to_list:发给谁
    sub:主题
    content:内容
    send_mail("aaa@126.com","sub","content")
    '''
    #me=mail_user+"<</span>"+mail_user+"@"+mail_postfix+">"
    me=mail_send_from
    msg = MIMEText(content, _subtype='html', _charset='utf8')
    msg['Subject'] = Header(sub,'utf8')
    msg['From'] = Header(me,'utf8')
    msg['To'] = ";".join(to_list)
    try:
        #smtp = smtplib.SMTP()
        #smtp.connect(mail_host,mail_port)
        smtp = smtplib.SMTP_SSL(mail_host, mail_port)
        smtp.login(mail_user,mail_pass)
        smtp.sendmail(me,to_list, msg.as_string())
        smtp.close()
        print "content: %s send to %s success" %(content,to_list)
        return True
    except Exception, e:
        print "Subject %s send to %s error: %s" %(sub,to_list, e)
        return False

##############################################################################
# function main  
##############################################################################
def main():
    # send mail and sms
    to_list = '29036548@qq.com'
    sub = 'kevin test title'
    content = 'kevin test content'
    send_mail(to_list, sub, content)

if __name__ == '__main__':
    main()
