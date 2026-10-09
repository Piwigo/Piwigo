#!/bin/bash
set -ex

if [[ "$1" == apache2* ]] || [ "$1" == php-fpm ]; then
  if ! [ -e index.php -a -e i.php ]; then
    echo >&2 "Piwigo not found in $PWD - copying now..."
    if [ "$(ls -A)" ]; then
      echo >&2 "WARNING: $PWD is not empty - press Ctrl+C now if this is an error!"
      ( set -x; ls -A; sleep 10 )
    fi
    tar cf - --one-file-system -C /usr/src/piwigo . | tar xf -
    echo >&2 "Complete! Piwigo has been successfully copied to $PWD"
  fi
fi

exec "$@"
