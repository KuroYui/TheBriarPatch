#!/bin/bash

echo "packet capture testing utility"
echo "[ diag test #1 ]"
echo "please browse to speedtest.net on any device on your network"
echo "if packet capture is working effectively, you should see the speedtest entry show up below"
sudo timeout 20 tcpdump -i eth0 | grep "speedtest.net"

if [ "$?" == "0" ]; then
clear
echo "packets appear to be coming in to your monitoring interface from other devices on your network"
else
clear
echo "packets do NOT appear to be coming into your monitoring interface from other devices on the network"
fi

echo "starting diag test #2 in 10 seconds"
sleep 10
clear
echo "[ diag test #2 ]"
echo "make sure suricata is running"
echo "hit [enter] when ready"
read
clear
echo "now browse to ipcheck.info on another device on your network"
sudo timeout 20 tail -f /var/log/suricata/http.log | grep "ipcheck.info"

if [ "$?" == "0" ]; then
clear
echo "suricata is collecting packets as expected"
else
clear
echo "suricata does NOT appear to be collecting packets"
fi

echo "starting diag test #3 in 10 seconds"
sleep 10
clear
echo "[ diag test #3 ]"
echo "make sure suricata is running"
echo "hit [enter] when ready"
read
clear
echo "testing for ascii logs in suricata folder instead of binary"
echo "sometimes an unexpected shutdown can cause logs to get corrupted"
file --mime-encoding /var/log/suricata/*.log | grep binary
sleep 3
if [ "$?" == "1" ]; then
clear
echo "logs appear to be in-tact and in ascii formatting"
else
clear
echo "logs could be corrpupted. recommend deleting all logs in /var/log/suricata folder"
echo "you can do this by issuing the following command: sudo rm /var/log/suricata/*.log"
fi

