#!/bin/bash

function checkeve()
{
grep -q in_iface /var/log/suricata/eve.json
if [ $? -eq 1 -o $? -eq 2 ] ; then  
echo "<br><br><b style='background:LawnGreen'>eve.json does not exist or monitoring interface not added yet.<br>"
echo "please collect some more packets before running this log rotation script</b>"
exit
else
:
fi
}

function rotate()
{
sudo killall /opt/suricata/bin/suricata
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

checkeve
rotate
myfunc
