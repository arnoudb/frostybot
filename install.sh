#!/bin/bash

# Usage: ./install-git.sh [web root path] [web server account]

SCRIPTPATH=`dirname "$(readlink -f "$0")"`
USER=`whoami`
WSDEFACC="www-data"

if [ -z "$1" ]; then
  WEBROOT="$SCRIPTPATH"
else
  WEBROOT="$1"
fi

FROSTYPATH="$WEBROOT/frostybot"

if [ -z "$2" ]; then
  WEBSERVERACCOUNT="$WSDEFACC"
else
  WEBSERVERACCOUNT="$2"
fi

if [ -d "$FROSTYPATH" ]; then
  echo "ERROR: The directory ${FROSTYPATH} already exists, cancelling installation..."
  exit 1
fi

if ! [ -x "$(command -v gits)" ]; then
  if ! [ -x "$(command -v unzip)" ]; then
    echo "ERROR: Could not find git or unzip commands, please install git or unzip. Cancelling installation."
    exit 1
  else
    echo "Installing Frostybot to $FROSTYPATH using zip package..."
    echo "Downloading zip package..."
    wget -4 --no-hsts -q  https://github.com/CryptoMF/frostybot/archive/master.zip -O "/tmp/frostybot-master.zip"
    echo "Extracting zip package..."
    if [ -d "/tmp/frostybotextract" ]; then
      rm -Rf "/tmp/frostybotextract"
    fi
    unzip -q /tmp/frostybot-master.zip -d "/tmp/frostybotextract"
    if [ -d "/tmp/frostybotextract/frostybot-master" ]; then
      mv "/tmp/frostybotextract/frostybot-master" "$FROSTYPATH"
      rm /tmp/frostybot-master.zip
    else
      echo "ERROR: Something went wrong while extracting the zip package, please try installing Frostybot manually."
      exit 1
    fi
  fi
else
  echo "Installing Frostybot to $FROSTYPATH using git..."
  echo "Cloning package..."
  git clone -4q https://github.com/CryptoMF/frostybot.git "$FROSTYPATH"
fi

if [ -d "$FROSTYPATH" ]; then
  echo "Setting permissions..."
  sudo chown -R "$USER":"$WEBSERVERACCOUNT" "$FROSTYPATH"
  sudo chmod -R 640 "$FROSTYPATH"
  sudo chmod -R 660 "$FROSTYPATH/cache"
  sudo chmod -R 660 "$FROSTYPATH/db"
  sudo chmod -R 660 "$FROSTYPATH/log"
  sudo chmod 750 "$FROSTYPATH/frostybot"
  sudo chmod -R +X "$FROSTYPATH"
  echo "Installation completed"
  exit 0
else
  echo "ERROR: Something went wrong with the install script, please try installing Frostybot manually."
  exit 1
fi
