#!/bin/bash

basedir=""

#check basedir
########################################################
if test -z "$basedir"
then
  basedir=/usr/local/wlblazers
else
  basedir="$basedir"
fi
echo "[note] wlblazers will be install on basedir: $basedir"

#create dir
#########################################################
if [ ! -x "$basedir" ]; then 
  echo "[note] $basedir directory does not exist,will be created."
  mkdir -p "$basedir"
  echo "[note] $basedir directory created success."
else
  echo "[error] $basedir directory already exists,install exit."
  exit
fi

#copy files
########################################################
echo "[note] wait copy files......."
cp -r * $basedir

#change chmod
########################################################
echo "[note] change script permission."
chmod +x $basedir/wlblazers*

#create links
########################################################
echo "[note] create links."
ln -s $basedir/wlblazers /usr/local/sbin/
ln -s $basedir/wlblazers_monitor /usr/local/sbin/
echo "[note] install complete."
