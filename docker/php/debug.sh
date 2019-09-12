#!/bin/sh

export XDEBUG_CONFIG="idekey=ANY_IDE remote_host=`route -nA inet|egrep ^0.0.0.0|tr -s ' '|cut -d' ' -f2`"
export PHP_IDE_CONFIG="serverName=community"

if [ -f $1 ]; then
  exec php "$@"
elif which $1 > /dev/null 2>&1; then
  file=$(which $1)
  shift
  exec php -dzend_extension=xdebug.so $file "$@"
fi
