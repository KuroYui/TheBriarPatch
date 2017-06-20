#!/bin/bash

function rotate()
{
#sudo pkill -F /var/run/suricata.pid 2> /dev/null
logrotate /etc/logrotate.d/suricata
}

function myfunc()
{

#locating monitoring interface
liveip=$(ifconfig | grep "inet addr" | grep -v "127.0.0.1" | awk -F: '{print $2}' | awk '{print $1}')
#check if mon int is eth0
ifconfig eth0 2> /dev/null | grep $liveip 2> /dev/null
if [ "$?" != "1" ]; then
monint=eth0
else
#check if mon int is eth1
ifconfig eth1 2> /dev/null | grep $liveip 2> /dev/null
if [ "$?" != "1" ]; then
monint=eth1
else
#check if mon int is wlan0
ifconfig wlan0 2> /dev/null | grep $liveip 2> /dev/null
if [ "$?" != "1" ]; then
monint=wlan0
else
#check if mon int is wlan1
ifconfig wlan1 2> /dev/null | grep $liveip 2> /dev/null
if [ "$?" != "1" ]; then
monint=wlan1
fi
fi
fi
fi

echo "setting monitoring int to: $monint"

#checks to see if running
ourcheck=$(ls /var/run/suricata.pid 2> /dev/null)
#echo $ourcheck
if [ "$?" == "0" ]; then
echo "Suricata is running. continuing..."
#kill suricata
echo "killing Suricata"
sudo pkill -F /var/run/suricata.pid 2> /dev/null
myfunc
else
#cleaning up tables
echo "DELETE FROM MACINTOSHOS; VACUUM;" | sqlite3 BriarPatch.db
echo "DELETE FROM LINUXOS; VACUUM;" | sqlite3 BriarPatch.db
echo "DELETE FROM WINDOWSOS; VACUUM;" | sqlite3 BriarPatch.db
echo "DELETE FROM MACINTOSHOSARCHIVES; VACUUM;" | sqlite3 BriarPatch.db
echo "DELETE FROM LINUXOSARCHIVES; VACUUM;" | sqlite3 BriarPatch.db
echo "DELETE FROM WINDOWSOSARCHIVES; VACUUM;" | sqlite3 BriarPatch.db
echo "DELETE FROM EXPLOITS; VACUUM;" | sqlite3 BriarPatch.db
echo "DELETE FROM EXPLOITARCHIVES; VACUUM;" | sqlite3 BriarPatch.db
echo "Suricata is NOT running..."
echo "Starting suricata back up again"

sudo ethtool -K $monint tx off rx off sg off gso off gro off 2>/dev/null
sudo /opt/suricata/bin/suricata -c /opt/suricata/etc/suricata/suricata.yaml --af-packet=$monint -D
exit
fi
}

rotate
myfunc
