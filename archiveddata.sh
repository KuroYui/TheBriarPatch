#!/bin/bash

if [ "$2" == "Apple" ]; then
grep $1 /var/log/suricata/http.log | grep -e 'iPhone' -e 'Macintosh' -e 'iPad' >> $3.txt
else if [ "$2" == "Linux" ]; then
grep $1 /var/log/suricata/http.log | grep -e "Linux" -e "CrOS" >> $3.txt
else
grep $1 /var/log/suricata/http.log | grep "$2" >> $3.txt
fi
fi
