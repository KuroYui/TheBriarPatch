#!/bin/bash

thedate=$(date +%m/%d/%Y)
echo "$thedate"
#echo $1

if [ "$1" != "Linux" ]; then
grep $thedate /var/log/suricata/http.log | grep "$1" >> $2.txt
else
grep $thedate /var/log/suricata/http.log | grep -e "Linux" -e "CrOS" >> $2.txt
fi
