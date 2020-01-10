#!/bin/bash

# This file will correctly set the file permissions for Frostybot on Ubuntu server (www-data). 
# If your web server is using an account other than www-data, replace the account name below.

BOTPATH=`dirname "$(readlink -f "$0")"`
echo "Setting correct permissions on directory $BOTPATH..."
sudo chgrp -R www-data "$BOTPATH"
sudo chmod -R 640 "$BOTPATH"
sudo chmod -R 660 "$BOTPATH/cache"
sudo chmod -R 660 "$BOTPATH/db"
sudo chmod -R 660 "$BOTPATH/log"
sudo chmod 750 "$BOTPATH/frostybot"
sudo chmod 750 "$BOTPATH/setperms.sh"
sudo chmod -R +X "$BOTPATH"
echo "Permissions set"
