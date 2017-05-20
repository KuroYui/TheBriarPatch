#!/bin/bash

echo "TheBriarPatch uninstallation utility"
echo "*************************************"
echo ""
echo "PLEASE RUN with sudo or root!"
echo ""
echo "This ONLY uninstalls BriarPatch specific config info"
echo "apt packages will need to manually be uninstalled"
echo "hit [enter] to continue"
read 
echo "removing sudoers entry"
sed -i '$ d' /etc/sudoers #remove sudoers BriarPatch entry
echo "removing certs"
sudo rm -rf /var/www/certs #remove certs
echo "removing passwords, username, emails, etc"
sudo rm -rf /var/www/securedfiles #remove BriarPatch login information
echo "restoring apache config..."
sudo cp /etc/apache2/sites-available/default-ssl.conf.old /etc/apache2/sites-available/default-ssl.conf #restore apache original config
echo "cleaning up sendmail..."
sudo rm -rf /etc/mail/authinfo/ #cleanup sendmail config
sudo rm -rf /etc/mail/ #cleanup sendmail continued...
echo "cleaning up hosts entry"
sed -i '$ d' /etc/hosts #cleanup hosts entry

echo "Lastly, you will need to remove TheBriarPatch manually"
echo "Simply type in: sudo rm -rf /var/www/html/TheBriarPatch"
echo "Uninstallation COMPLETE!"
