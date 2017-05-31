#!/bin/bash

if [ "$2" == "Apple" ]; then
#deprecated
#grep $1 /var/log/suricata/http.log | grep -e 'iPhone' -e 'Macintosh' -e 'iPad' >> $3.txt
zcat /var/log/suricata/$1 | grep -e 'iPhone' -e 'Macintosh' -e 'iPad' >> $3.txt
else if [ "$2" == "Linux" ]; then
zcat /var/log/suricata/$1 | grep -e 'armv7l' -e 'Android' -e 'Tizen' -e 'X11;' -e 'CrOS' >> $3.txt
else if [ "$2" == "Exploits" ]; then
zcat /var/log/suricata/$1 >> $3.txt
else 
#Windows OS
zcat /var/log/suricata/$1 >> $3.txt
fi
fi
fi
