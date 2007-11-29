#!/bin/bash

# +--------------------------------------------------------------------------+
# |                            pwg_rel_create.sh                             |
# +--------------------------------------------------------------------------+
# | author        : Pierrick LE GALL <http://le-gall.net/pierrick>           |
# | project       : PhpWebGallery                                            |
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

name=phpwebgallery-$version

cd /tmp
if [ -e $name ]
then
  rm -rf $name
fi

# cvs export -r $tag -d $name phpwebgallery
svn export http://svn.gna.org/svn/phpwebgallery/tags/$tag $name
# creating mysql.inc.php empty and writeable
touch $name/include/mysql.inc.php
chmod a+w $name/include/mysql.inc.php

# find $name -name "*.php" \
#   | xargs grep -l 'branch 1.7' \
#   | xargs perl -pi -e "s/branch 1.7/${version}/g"

for ext in zip tar.gz tar.bz2
do
  file=$name.$ext
  if [ -f $file ]
  then
    rm $name
  fi
done

zip -r   $name.zip     $name
tar -czf $name.tar.gz  $name
tar -cjf $name.tar.bz2 $name
