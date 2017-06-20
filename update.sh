#!/bin/bash
echo "Performing status check for services and permissions..."
git remote show origin | grep "out of date"
if [ $? == 0 ] ; then
   echo "<br><b style='background:green'>An update is available! Updating now!</b><br>"
   git pull
   #exit
   else
   echo "<b>All good here.  <br>Either there are no updates available or no internet connection at this time</b><br>"
   fi

ps aux | grep sendmail | grep "accepting connections" &>/dev/nul

if [ $? == 0 ] ; then
:
#echo "<br>Checking if sendmail service is running: <b>Sendmail service is running!</b>"
else
echo "<br>Checking if sendmail service is running: <b style='background:red'>Sendmail service is NOT running</b>"
fi

grep -q broretention.sh /etc/sudoers
if [ $? == 0 ] ; then
sudo sed -i -e '/retention.sh/,+1 d' /etc/sudoers
sudo sed -i -e '/update.sh/,+1 d' /etc/sudoers
fi

grep -q TheBriarPatch /etc/sudoers
if [ $? != 0 ] ; then
sudo sed -i -e '$awww-data ALL = (root) NOPASSWD: /var/www/html/TheBriarPatch/' /etc/sudoers
fi

#echo "<br>Checking to see if php sqlite is installed"
sqlcheck=$(dpkg -s php5-sqlite | grep installed)
if [ "$sqlcheck" == "Status: install ok installed" ]; then
:
#echo "<br>php5-sqlite is installed!"
else
echo "<br>php5-sqlite not installed...installing now!<br>"
sudo apt-get install php5-sqlite -y
fi

#echo "<br>Checking to see if sqlite3 is installed"
sqlcheck=$(dpkg -s sqlite3 | grep installed)
if [ "$sqlcheck" == "Status: install ok installed" ]; then
:
#echo "<br>sqlite3 is installed!"
else
echo "<br>sqlite3 not installed...installing now!<br>"
sudo apt-get install sqlite3 -y
fi


#echo "<br>checking permissions for /var/www/html/TheBriarPatch"
permcheck=$(ls -g /var/www/html/ | grep TheBriarPatch | awk '{print $3}')

if [ "$permcheck" == "www-data" ]; then
:
#echo "<br>permissions look good!"
else
echo "<br>adjusting permissions for this directory..."
sudo chown www-data:www-data /var/www/html/TheBriarPatch
fi

#echo "<br>checking permissions for ExploitTraffic.txt / creating if it does not exist"
exploitcheck=$(ls ExploitTraffic.txt 2>&1 | awk '{print $2}')

if [ "$exploitcheck" != "cannot" ]; then
:
#echo "<br>file is already created and permissions look good!"
else
echo "<br>ExploitTraffic.txt does NOT exist.  Creating it now and adjusting permissions..."
sudo touch ExploitTraffic.txt
sudo chown www-data:www-data ExploitTraffic.txt
fi

#echo "<br>Checking to make sure suricata boot file is available"
suricheck=$(ls startupscan.txt 2>&1 | awk '{print $2}')
if [ "$suricheck" != "cannot" ]; then
:
#echo "<br>file is already created and permissions look good!"
else
echo "<br>startupscan.txt does NOT exist.  Creating it now and adjusting permissions..."
sudo touch startupscan.txt
sudo chown www-data:www-data startupscan.txt
sudo echo no_interface_defined_yet>> startupscan.txt
fi

#echo "<br>Checking value to see if you would like to run Suricata at boot.</b>"
bootcheck=$(head -n 1 startupscan.txt | xargs)
if [ "$bootcheck" != "no_interface_defined_yet" ]; then

#check to see if rc.local entry already exists
grepper=$(grep suricata /etc/rc.local)
if [ "$?" == "0" ]; then
:
#echo "<br><b style='background:DeepSkyBlue'>entry already exists...skipping</b>"
else 
echo "<br><b style='background:DeepSkyBlue'>Enabling Suricata to start scanning at bootup on $bootcheck!</b>"
sudo sed -i -e '$i /opt/suricata/bin/suricata -c /opt/suricata/etc/suricata/suricata.yaml --af-packet='"$bootcheck &" /etc/rc.local
fi
fi

#sort of counter-productive, but we neded to adjust the above bootup settings now :)

######################################
#add support for suricata pid files
#
######################################

grep -qF '#pid-file: /var/run/suricata.pid' /opt/suricata/etc/suricata/suricata.yaml
if [ $? == 0 ] ; then
   #clear
   echo "<br><b style='background:DeepSkyBlue'>Uncommenting pid file config...</b>"
   sudo sed -i "/#pid-file: \/var\/run\/suricata.pid/c\pid-file: \/var\/run\/suricata.pid" /opt/suricata/etc/suricata/suricata.yaml
   #zenity --info --text="Ok.  TLS_EVENTS rule has been corrected!" &> /dev/null
sudo sed -i '/console:/{n;s/.*/      enabled: no/}' /opt/suricata/etc/suricata/suricata.yaml
sudo sed -i '/- file:/{n;s/.*/      enabled: yes/}' /opt/suricata/etc/suricata/suricata.yaml
sudo sed -i '/- file:/{n;n;n;n;n;s/.*/      enabled: yes/}' /opt/suricata/etc/suricata/suricata.yaml
else
:
#echo "pid file enabled already ;)"
fi

if [ "$bootcheck" != "no_interface_defined_yet" ]; then
grepper=$(grep "$bootcheck -D" /etc/rc.local)
if [ "$?" == "0" ]; then
:
else
sed -i "/$bootcheck"' \&/s/.*/\#run suricata at boot/' /etc/rc.local
sudo sed -i -e '$i /opt/suricata/bin/suricata -c /opt/suricata/etc/suricata/suricata.yaml --af-packet='"$bootcheck -D" /etc/rc.local
fi
fi


#ethtool entry check
if [ "$bootcheck" != "no_interface_defined_yet" ]; then

grepper2=$(grep ethtool /etc/rc.local)
if [ "$?" == "0" ]; then
:
#echo "<br><b style='background:DeepSkyBlue'>ethtool entry already exists...skipping</b>"
else
echo "<br><b style='background:DeepSkyBlue'>adding ethtool functionality to Suricata at bootup!</b>"
thecount=$(wc -l /etc/rc.local | awk '{print $1}')
count=$((thecount-1))
sed -i ''$count'i ethtool -K '$bootcheck' tx off rx off sg off gso off gro off 2>/dev/null' /etc/rc.local
fi
fi

#echo "$bootcheck"
#ps aux | grep suricata | grep "$bootcheck -D" &>/dev/null
#if [ "$?" == "1" ]; then
#echo "<br><b style='background:orange;font-size:15pt'>[***A reboot is required to finalize new configuration for running suricata in daemon mode<br>Please restart at your earliest convenience. thanks!***]</b>"
#else
#:
#fi


#echo "<br><b style='background:DeepSkyBlue'>Checking to make sure manual log rotation script file is available</b>"
rotatecheck=$(ls rotatelogs.sh 2>&1 | awk '{print $2}')
if [ "$rotatecheck" == "cannot" ]; then
#echo "<br><b style='background:DeepSkyBlue'>Log rotation script does not exist, creating now...</b>"
sudo touch rotatelogs.sh
sudo chown root:root rotatelogs.sh
sudo echo '#!/bin/bash' >> rotatelogs.sh
sudo echo logrotate /etc/logrotate.d/suricata >> rotatelogs.sh
sudo chmod +x rotatelogs.sh
else
:
#echo "<br><b style='background:DeepSkyBlue'>Script exists!  skipping...</b>"
fi




#echo "<br><b style='background:DeepSkyBlue'>Checking to make sure automatic daily log rotation script file is available</b>"
rotatecheck=$(ls /etc/logrotate.d/suricata 2>&1 | awk '{print $2}')
if [ "$rotatecheck" == "cannot" ]; then
echo "<br><b style='background:DeepSkyBlue'>Automatic Daily Log rotation script does not exist, creating now...</b>"
sudo touch /etc/logrotate.d/suricata
sudo chown root:root /etc/logrotate.d/suricata
sudo echo '/var/log/suricata/*.log /var/log/suricata/*.json'>>/etc/logrotate.d/suricata
sudo echo '{'>>/etc/logrotate.d/suricata
sudo echo 'maxsize 250M'>>/etc/logrotate.d/suricata	
sudo echo 'daily'>>/etc/logrotate.d/suricata
sudo echo 'rotate 5'>>/etc/logrotate.d/suricata
sudo echo 'create'>>/etc/logrotate.d/suricata
sudo echo 'dateext'>>/etc/logrotate.d/suricata
sudo echo 'compress'>>/etc/logrotate.d/suricata
sudo echo '}'>>/etc/logrotate.d/suricata
else
:
#echo "<br><b style='background:DeepSkyBlue'>Script exists!  skipping...</b>"
fi


#clean tables
echo "DELETE FROM MACINTOSHOS; VACUUM;" | sqlite3 BriarPatch.db
echo "DELETE FROM LINUXOS; VACUUM;" | sqlite3 BriarPatch.db
echo "DELETE FROM WINDOWSOS; VACUUM;" | sqlite3 BriarPatch.db
echo "DELETE FROM MACINTOSHOSARCHIVES; VACUUM;" | sqlite3 BriarPatch.db
echo "DELETE FROM LINUXOSARCHIVES; VACUUM;" | sqlite3 BriarPatch.db
echo "DELETE FROM WINDOWSOSARCHIVES; VACUUM;" | sqlite3 BriarPatch.db
echo "DELETE FROM EXPLOITS; VACUUM;" | sqlite3 BriarPatch.db
echo "DELETE FROM EXPLOITARCHIVES; VACUUM;" | sqlite3 BriarPatch.db

#commenting out unneccesary rules

grep -qF '# - emerging-chat.rules' /opt/suricata/etc/suricata/suricata.yaml
if [ $? != 0 ] ; then
   #clear
   #echo "Commenting out AIM_SERVERS rule..."
   sed -i "/ - emerging-chat.rules/c\# - emerging-chat.rules" /opt/suricata/etc/suricata/suricata.yaml
  #zenity --info --text="Ok.  AIM_SERVERS rule has been corrected!" &> /dev/null
   fi

grep -qF '# - tls-events.rules' /opt/suricata/etc/suricata/suricata.yaml
if [ $? != 0 ] ; then
   #clear
   #echo "Commenting out TLS_EVENTS rule..."
   sed -i "/ - tls-events.rules/c\# - tls-events.rules" /opt/suricata/etc/suricata/suricata.yaml
   #zenity --info --text="Ok.  TLS_EVENTS rule has been corrected!" &> /dev/null
   fi

grep -qF '# - stream-events.rules' /opt/suricata/etc/suricata/suricata.yaml
if [ $? != 0 ] ; then
   #clear
   #echo "Commenting out STREAM_EVENTS rule..."
   sed -i "/ - stream-events.rules/c\# - stream-events.rules" /opt/suricata/etc/suricata/suricata.yaml
   #zenity --info --text="Ok.  STREAM_EVENTS rule has been corrected!" &> /dev/null
   fi

grep -qF '# - app-layer-events.rules' /opt/suricata/etc/suricata/suricata.yaml
if [ $? != 0 ] ; then
   #clear
   #echo "Commenting out APP_LAYER_EVENTS rule..."
   sed -i "/ - app-layer-events.rules/c\# - app-layer-events.rules" /opt/suricata/etc/suricata/suricata.yaml
   #zenity --info --text="Ok.  APP_LAYER_EVENTS rule has been corrected!" &> /dev/null
   fi

grep -qF '# - decoder-events.rules' /opt/suricata/etc/suricata/suricata.yaml
if [ $? != 0 ] ; then
   #clear
   #echo "Commenting out DECODER_EVENTS rule..."
   sed -i "/ - decoder-events.rules/c\# - decoder-events.rules" /opt/suricata/etc/suricata/suricata.yaml
   #zenity --info --text="Ok.  DECODER_EVENTS rule has been corrected!" &> /dev/null
   fi


echo "<br><b style='background:yellow'>All checks complete!!!</b><br><br>"

bootcheck2=$(head -n 1 'startupscan.txt')
if [ "$bootcheck2" != "no_interface_defined_yet" ]; then
:
else
echo "<u><b style='background:DeepSkyBlue'>[IMPORTANT]:</b></u><b style='background:yellow'>If you would like to run Suricata at boot,<br>please add your monitoring interface (Ex: wlan0, eth0) to this file: <u>startupscan.txt by replacing the line that reads: no_interface_defined_yet</b></u>"
fi
