#!/bin/bash

function rotate()
{
logrotate /etc/logrotate.d/suricata
}

function myfunc()
{
#checks to see if running
ourcheck=$(sudo ps aux | grep "/opt/suricata/bin/suricata" | awk '{print $11}')
#echo $ourcheck
if [ "$ourcheck" != "grep" ]; then
echo "Suricata is running. continuing..."
#locate monitoring interface
echo "locating monitoring interface"
monint=$(ps aux | grep -m 1 suricata | awk -F= '{print $NF}')
#grab PID of suricata
echo "grabbing PID of suricata instance"
ourpid=$(ps aux | grep -m 1 suricata | awk '{print $2}')
#kill suricata
echo "killing Suricata"
sudo kill -9 $ourpid
myfunc
else
echo "Suricata is NOT running...restarting"
echo "Starting suricata back up again"
echo $monint
sudo ethtool -K $monint tx off rx off sg off gso off gro off 2>/dev/null
sudo /opt/suricata/bin/suricata -c /opt/suricata/etc/suricata/suricata.yaml --af-packet=$monint &
exit
fi
}

rotate
myfunc
