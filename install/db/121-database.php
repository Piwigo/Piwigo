<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

// see http://piwigo.org/doc/doku.php?id=user_documentation:htaccess_and_hotlink_in_2.4

if (!isset($page['warnings'])) $page['warnings'] = array();

$upgrade_description = 'add/append htaccess for hotlinks';
$warning_message = 'Failed to modify <b>.htaccess</b> file, a manual intervention is needed, <a href="http://piwigo.org/doc/doku.php?id=user_documentation:htaccess_and_hotlink_in_2.4" target="_blank">click here for more information</a>';

$htaccess = PHPWG_ROOT_PATH.'/.htaccess';
$writable = true;
if (file_exists($htaccess))
{
  if (!is_readable($htaccess) || !is_writable($htaccess))
  {
    $writable = false;
  }
}
else
{
  $writable = is_writable(PHPWG_ROOT_PATH);
}

if (!$writable)
{
  array_push($page['warnings'], $warning_message);
  $upgrade_description.= ': failed';
}
else
{
  $content = file_exists($htaccess) ? file_get_contents($htaccess) : null;
  if (strpos($content, 'RewriteEngine off') !== false)
  {
    array_push($page['warnings'], $warning_message);
    $upgrade_description.= ': failed';
  }
  else
  {
    if (strpos($content, 'RewriteEngine on') === false)
    {
      $content.='
RewriteEngine on';
    }
    
    if (!isset($conf['prefix_thumbnail']))
    {
      $conf['prefix_thumbnail'] = 'TN-';
    }

    if (!isset($conf['dir_thumbnail']))
    {
      $conf['dir_thumbnail'] = 'thumbnail';
    }
    
    $content.= '
## redirect <2.4 thumbnails hotlinks to i.php
RewriteRule ^upload/(.*)/'.preg_quote($conf['dir_thumbnail']).'/'.preg_quote($conf['prefix_thumbnail']).'(.*)\.([a-z0-9]{3,4})$ i.php?/upload/$1/$2-th.$3 [L]
RewriteRule ^galleries/(.*)/'.preg_quote($conf['dir_thumbnail']).'/'.preg_quote($conf['prefix_thumbnail']).'(.*)\.([a-z0-9]{3,4})$ i.php?/galleries/$1/$2-th.$3 [L]

## redirect <2.4 high-def hotlinks to original file
RewriteRule ^upload/(.*)/pwg_high/(.*)\.([a-z0-9]{3,4})$ upload/$1/$2.$3 [L]
RewriteRule ^galleries/(.*)/pwg_high/(.*)\.([a-z0-9]{3,4})$ galleries/$1/$2.$3 [L]

## redirect <2.4 low-def hotlinks to i.php
RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?'.preg_quote($_SERVER['SERVER_NAME']).'/.*$ [NC]
RewriteRule ^upload/(.*)/(.*)\.([a-z0-9]{3,4})$ i.php?/upload/$1/$2-me.$3 [L]
RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?'.preg_quote($_SERVER['SERVER_NAME']).'/.*$ [NC]
RewriteRule ^galleries(.*)/(.*)\.([a-z0-9]{3,4})$ i.php?/galleries/$1/$2-me.$3 [L]';
    
    file_put_contents($htaccess, $content);
  }
}


echo
"\n"
. $upgrade_description
."\n"
;
?>