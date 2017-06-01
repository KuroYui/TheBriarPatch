#!/bin/bash

function rotate()
{
logrotate /etc/logrotate.d/suricata
}

function myfunc()
{
#checks to see if running
ourcheck=$(sudo ps aux | grep "/opt/suricata/bin/suricata" | awk '{print $14}' | grep yaml)
#echo $ourcheck
if [ "$?" == "0" ]; then
echo "Suricata is running. continuing..."
#kill suricata
echo "killing Suricata"
sudo killall "/opt/suricata/bin/suricata"
myfunc
else
echo "Suricata is NOT running..."
echo "Starting suricata back up again"
#locating monitoring interface
monint=$(grep -m 1 in_iface /var/log/suricata/eve.json | awk -F: '{print $6}' | awk -F',' '{print $1}')
monint=$(echo "$monint" | tr -d '"')
#echo $monint
sudo ethtool -K $monint tx off rx off sg off gso off gro off 2>/dev/null
sudo /opt/suricata/bin/suricata -c /opt/suricata/etc/suricata/suricata.yaml --af-packet=$monint &
exit
fi
}

rotate
myfunc
