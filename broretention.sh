#!/bin/bash

gottacatchemall=( $(ls /opt/nsm/bro/logs | grep -P "\d") )

for i in "${gottacatchemall[@]}"
do
rm -rf /opt/nsm/bro/logs/$i
done
