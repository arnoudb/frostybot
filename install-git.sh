#!/bin/bash

# Usage: ./install-git.sh <web root path> [web server account]

WEBROOT="$1"
FROSTYPATH="$WEBROOT/frostybot"
USER=`whoami`
WSDEFACC="www-data"

if [ -z "$WEBROOT" ]; then
  echo "Usage: ./install-git.sh <web root path> [web server account]"
  exit 1
fi

if [ -z "$2" ]; then
  WEBSERVERACCOUNT="$WSDEFACC"
else
  WEBSERVERACCOUNT="$2"
fi

if [ -d "$FROSTYPATH" ]; then
  echo "ERROR: The directory ${FROSTYPATH} already exists, cancelling installation..."
  exit 1
fi

echo "Installing Frostybot to $FROSTYPATH using Git..."
git clone https://github.com/CryptoMF/frostybot.git "$FROSTYPATH"
echo "Setting correct permissions on directory $BOTPATH..."
sudo chown -R "$USER":"$WEBSERVERACCOUNT" "$FROSTYPATH"
sudo chmod -R 640 "$FROSTYPATH"
sudo chmod -R 660 "$FROSTYPATH/cache"
sudo chmod -R 660 "$FROSTYPATH/db"
sudo chmod -R 660 "$FROSTYPATH/log"
sudo chmod 750 "$FROSTYPATH/frostybot"
sudo chmod -R +X "$FROSTYPATH"
echo "Permissions set"
