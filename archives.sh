#!/bin/bash

thedate=$(date +%m/%d/%Y)
awk '{print $1}' ORS="," /var/log/suricata/http.log | grep -oE [0-9]{2}/[0-9]{2}/[0-9]{4} | grep -v "$thedate" | uniq
