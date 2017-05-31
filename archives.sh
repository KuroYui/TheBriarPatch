#!/bin/bash

####################
#deprecated
#thedate=$(date +%m/%d/%Y)
#awk '{print $1}' ORS="," /var/log/suricata/http.log | grep -oE [0-9]{2}/[0-9]{2}/[0-9]{4} | grep -v "$thedate" | uniq
####################

#thedate=$(date +%Y)
ls /var/log/suricata/ | grep "http.log-" | uniq
ls /var/log/suricata/ | grep "fast.log-" | uniq
