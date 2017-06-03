#!/bin/bash

function rotate()
{
sudo killall /opt/suricata/bin/suricata
logrotate /etc/logrotate.d/suricata
}

function myfunc()
{

#locating monitoring interface
liveip=$(ifconfig | grep "inet addr" | grep -v "127.0.0.1" | awk -F: '{print $2}' | awk '{print $1}')
#check if mon int is eth0
ifconfig eth0 | grep $liveip &> /dev/null
if [ "$?" != "1" ]; then
monint=eth0
else
#check if mon int is eth1
ifconfig eth1 | grep $liveip &> /dev/null
if [ "$?" != "1" ]; then
monint=eth1
else
#check if mon int is wlan0
ifconfig wlan0 | grep $liveip &> /dev/null
if [ "$?" != "1" ]; then
monint=wlan0
else
#check if mon int is wlan1
ifconfig wlan1 | grep $liveip &> /dev/null
if [ "$?" != "1" ]; then
monint=wlan1
fi
fi
fi
fi

echo "setting monitoring int to: $monint"

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
sudo ethtool -K $monint tx off rx off sg off gso off gro off 2>/dev/null
sudo /opt/suricata/bin/suricata -c /opt/suricata/etc/suricata/suricata.yaml --af-packet=$monint &
exit
fi
}

rotate
myfunc
