#!/bin/bash

script_abspath=$(perl -e 'use Cwd "abs_path"; print abs_path(@ARGV[0])' -- "$0")
scriptdir=$(dirname $script_abspath)

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

git clone git@github.com:Piwigo/Piwigo.git piwigo
cd piwigo

if [ $# -eq 2 ]
then
  git checkout $2
fi

# remove Git metadata
rm -rf /tmp/$version/piwigo/.git

# +--------------------------------------------------------------------------+
# | plugins                                                                  |
# +--------------------------------------------------------------------------+

cd plugins

for plugin in TakeATour AdminTools LocalFilesEditor LanguageSwitch
do
  cd /tmp/$version/piwigo/plugins

  plugin_dir=$plugin
  if [ $plugin = "LanguageSwitch" ]
  then
    plugin_dir=language_switch
  fi

  # clone repo
  git clone git@github.com:Piwigo/${plugin}.git $plugin_dir
  cd /tmp/$version/piwigo/plugins/$plugin_dir

  # change version
  perl $scriptdir/replace_version.pl --file=main.inc.php --version=$version

  # register metadata in dedicated file
  echo https://github.com/Piwigo/${plugin}.git > pem_metadata.txt
  git log -n 1 --pretty=format:"%H %ad" --date=iso8601 >> pem_metadata.txt

  # remove Git metadata
  rm -rf .git
done

# +--------------------------------------------------------------------------+
# | themes                                                                   |
# +--------------------------------------------------------------------------+

for theme in modus elegant smartpocket
do
  cd /tmp/$version/piwigo/themes

  git_url=git@github.com:Piwigo/piwigo-${theme}.git

  # clone repo
  git clone $git_url $theme

  # enter theme directory for next actions
  cd $theme

  # change version
  perl $scriptdir/replace_version.pl --file=themeconf.inc.php --version=$version

  # register metadata in dedicated file
  echo $git_url > pem_metadata.txt
  git log -n 1 --pretty=format:"%H %ad" --date=iso8601 >> pem_metadata.txt

  # remove Git metadata
  rm -rf .git
done

# +--------------------------------------------------------------------------+
# | languages                                                                |
# +--------------------------------------------------------------------------+

cd /tmp/$version/piwigo/language
for languagefile in $(ls */common.lang.php)
do
  # change version
  perl $scriptdir/replace_version.pl --file=$languagefile --version=$version
done

# +--------------------------------------------------------------------------+
# | data directories + zip 1                                                 |
# +--------------------------------------------------------------------------+

# create "data" directories
cd /tmp/$version

mkdir piwigo/upload
mkdir piwigo/_data
touch piwigo/_data/dummy.txt

zip -q -r $name-nochmod.zip piwigo

# +--------------------------------------------------------------------------+
# | permissions + zip 2                                                      |
# +--------------------------------------------------------------------------+

chmod -R a+w piwigo/local
chmod a+w piwigo/_data
chmod a+w piwigo/upload
chmod a+w piwigo/plugins
chmod a+w piwigo/themes

zip -q -r $name.zip piwigo

echo cd /tmp/$version
