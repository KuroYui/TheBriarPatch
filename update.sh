#!/bin/bash
echo "Checking for updates..."
git remote show origin | grep "out of date"
if [ $? == 0 ] ; then
   echo "<b style='background:green'>An update is available! Updating now!</b><br>"
   git pull
   exit
   else
   echo "<b>All good here.  No updates available at this time</b><br>"
   fi

ps aux | grep sendmail | grep "accepting connections" &>/dev/nul

if [ $? == 0 ] ; then
echo "<br>Checking if sendmail service is running: <b>Sendmail service is running!</b>"
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

