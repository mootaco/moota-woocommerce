#!/bin/sh

if [ ! -z $1 ]
then
  TARGET=$1

  if [ ! -d "$TARGET/wp-content" ]
  then
    echo "ERROR: There is no \`wp-content\` folder inside of $TARGET/"
  else
    ln -s `pwd` "$TARGET/wp-content/plugins"
    echo "DONE"
  fi

else
  echo "Usage: ./link.sh <TARGET_DIR>"
fi
