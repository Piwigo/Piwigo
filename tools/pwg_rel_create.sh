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
if [ -e $name ]
then
  rm -rf $name
fi

if [ -e $version ]
then
  rm -rf $version
fi
mkdir $version

# cvs export -r $tag -d $version phpwebgallery
svn export http://piwigo.org/svn/tags/$tag $name
# creating mysql.inc.php empty and writeable
touch $name/include/mysql.inc.php
chmod a+w $name/include/mysql.inc.php

# find $name -name "*.php" \
#   | xargs grep -l 'branch 1.7' \
#   | xargs perl -pi -e "s/branch 1.7/${version}/g"

cd /tmp
for ext in zip # tar.gz tar.bz2
do
  file=$version/$name.$ext
  if [ -f $file ]
  then
    rm $name
  fi
done


zip -r   $version/$name.zip     $name
# tar -czf $version/$name.tar.gz  $name
# tar -cjf $version/$name.tar.bz2 $name

cd /tmp/$version
# md5sum p* >MD5SUMS
