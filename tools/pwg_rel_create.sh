#!/bin/bash

# +--------------------------------------------------------------------------+
# |                            pwg_rel_create.sh                             |
# +--------------------------------------------------------------------------+
# | author        : Pierrick LE GALL <http://le-gall.net/pierrick>           |
# | project       : Piwigo                                                   |
# +--------------------------------------------------------------------------+

if [ $# -lt 1 ]
then
  echo
  echo 'usage : '$(basename $0)' <version number> [<sha>]'
  echo
  exit 1
fi

version=$1

sha=$2

name=piwigo-$version

cd /tmp

if [ -e $version ]
then
  rm -rf $version
fi
mkdir $version
cd $version

git clone https://github.com/Piwigo/Piwigo.git piwigo
cd piwigo

if [ $# -eq 2 ]
then
  git checkout $2
fi

cd plugins
git clone https://github.com/Piwigo/TakeATour.git
git clone https://github.com/Piwigo/AdminTools.git
git clone https://github.com/Piwigo/LocalFilesEditor.git
git clone https://github.com/Piwigo/LanguageSwitch.git

rm -rf /tmp/$version/piwigo/.git
rm -rf /tmp/$version/piwigo/plugins/*/.git

cd /tmp/$version

mkdir piwigo/upload
mkdir piwigo/_data
touch piwigo/_data/dummy.txt

zip -r $name-nochmod.zip piwigo

chmod -R a+w piwigo/local
chmod a+w piwigo/_data
chmod a+w piwigo/upload
chmod a+w piwigo/plugins
chmod a+w piwigo/themes

zip -r $name.zip piwigo

echo cd /tmp/$version
