#!/bin/bash

thedate=$(date +%m/%d/%Y)
echo "$thedate"
#echo $1

if [ "$1" == "Linux" ]; then
grep $thedate /var/log/suricata/http.log | grep -e "X11;" -e "CrOS" >> $2.txt
else if [ "$1" == "Apple" ]; then
grep $thedate /var/log/suricata/http.log | grep -e 'iPhone' -e 'Macintosh' -e 'iPad' >> $2.txt
else
grep $thedate /var/log/suricata/http.log | grep "$1" >> $2.txt
fi
fi
