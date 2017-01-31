#!/bin/bash

grep $1 /var/log/suricata/http.log | grep "$2" >> $3.txt
