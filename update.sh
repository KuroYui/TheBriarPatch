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
