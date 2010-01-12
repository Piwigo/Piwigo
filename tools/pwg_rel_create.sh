#!/bin/bash

# +--------------------------------------------------------------------------+
# |                            pwg_rel_create.sh                             |
# +--------------------------------------------------------------------------+
# | author        : Pierrick LE GALL <http://le-gall.net/pierrick>           |
# | project       : Piwigo                                                   |
# +--------------------------------------------------------------------------+

if [ $# -lt 2 ]
then
  echo
  echo 'usage : '$(basename $0)' <tag> <version number>'
  echo
  exit 1
fi

tag=$1
version=$2

name=piwigo-$version

cd /tmp

if [ -e $version ]
then
  rm -rf $version
fi
mkdir $version
cd $version

svn export http://piwigo.org/svn/tags/$tag piwigo

# creating mysql.inc.php empty and writeable
touch piwigo/include/mysql.inc.php
chmod a+w piwigo/include/mysql.inc.php

zip -r $name.zip piwigo

echo cd /tmp/$version
