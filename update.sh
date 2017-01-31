#!/bin/bash
echo "Checking for updates..."
git remote show origin | grep "out of date"
if [ $? == 0 ] ; then
   echo "An update is available! Updating now!"
   git pull
   exit
   else
   echo "All good here.  No updates available at this time"
   fi

grep -q broretention.sh /etc/sudoers
if [ $? == 0 ] ; then
sudo sed -i -e '/retention.sh/,+1 d' /etc/sudoers
sudo sed -i -e '/update.sh/,+1 d' /etc/sudoers
fi

grep -q archiveddata /etc/sudoers
if [ $? != 0 ] ; then
sudo sed -i -e '$awww-data ALL = (root) NOPASSWD: /bin/cp /var/log/suricata/http.log /var/www/html/TheBriarPatch/archiveddata' /etc/sudoers
sudo sed -i -e '$awww-data ALL = (root) NOPASSWD: /var/www/html/TheBriarPatch/' /etc/sudoers
fi
