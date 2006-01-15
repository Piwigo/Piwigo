<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
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
  die ("Hacking attempt!");
}
include_once(PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php');

// +-----------------------------------------------------------------------+
// |                                actions                                |
// +-----------------------------------------------------------------------+

// Check for upgrade : code inspired from punbb
if (isset($_GET['action']) and 'check_upgrade' == $_GET['action'])
{
  if (!ini_get('allow_url_fopen'))
  {
    array_push(
      $page['errors'],
      l10n('Unable to check for upgrade since allow_url_fopen is disabled.')
      );
  }
  else
  {
    $versions = array('current' => PHPWG_VERSION);
    $lines = @file('http://www.phpwebgallery.net/latest_version');
    
    // if the current version is a BSF (development branch) build, we check
    // the first line, for stable versions, we check the second line
    if (preg_match('/^BSF/', $versions{'current'}))
    {
      $versions{'latest'} = trim($lines[0]);

      // because integer are limited to 4,294,967,296 we need to split BSF
      // versions in date.time
      foreach ($versions as $key => $value)
      {
        $versions{$key} =
          preg_replace('/BSF_(\d{8})(\d{4})/', '$1.$2', $value);
      }
    }
    else
    {
      $versions{'latest'} = trim($lines[1]);
    }

    if ('' == $versions{'latest'})
    {
      array_push(
        $page['errors'],
        l10n('Check for upgrade failed for unknown reasons.')
        );
    }
    else if ('%PWGVERSION%' == $versions{'current'})
    {
      array_push(
        $page['infos'],
        l10n('You are running on development sources, no check possible.')
        );
    }
    else if (version_compare($versions{'current'}, $versions{'latest'}) < 0)
    {
      array_push(
        $page['infos'],
        l10n('A new version of PhpWebGallery is available.')
        );
    }
    else
    {
      array_push(
        $page['infos'],
        l10n('You are running the latest version of PhpWebGallery.')
        );
    }
  }
}
// Show phpinfo() output
else if (isset($_GET['action']) and 'phpinfo' == $_GET['action'])
{
  phpinfo();
  exit();
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(array('intro' => 'admin/intro.tpl'));

list($mysql_version) = mysql_fetch_row(pwg_query('SELECT VERSION();'));

$query = '
SELECT COUNT(*)
  FROM '.IMAGES_TABLE.'
;';
list($nb_elements) = mysql_fetch_row(pwg_query($query));

$query = '
SELECT COUNT(*)
  FROM '.CATEGORIES_TABLE.'
;';
list($nb_categories) = mysql_fetch_row(pwg_query($query));

$query = '
SELECT COUNT(*)
  FROM '.CATEGORIES_TABLE.'
  WHERE dir IS NULL
;';
list($nb_virtual) = mysql_fetch_row(pwg_query($query));

$query = '
SELECT COUNT(*)
  FROM '.CATEGORIES_TABLE.'
  WHERE dir IS NOT NULL
;';
list($nb_physical) = mysql_fetch_row(pwg_query($query));

$query = '
SELECT COUNT(*)
  FROM '.USERS_TABLE.'
;';
list($nb_users) = mysql_fetch_row(pwg_query($query));

$query = '
SELECT COUNT(*)
  FROM '.GROUPS_TABLE.'
;';
list($nb_groups) = mysql_fetch_row(pwg_query($query));

$query = '
SELECT COUNT(*)
  FROM '.COMMENTS_TABLE.'
;';
list($nb_comments) = mysql_fetch_row(pwg_query($query));

$template->assign_vars(
  array(
    'PWG_VERSION' => PHPWG_VERSION,
    'OS' => PHP_OS,
    'PHP_VERSION' => phpversion(),
    'MYSQL_VERSION' => $mysql_version,
    'DB_ELEMENTS' => sprintf(l10n('%d elements'), $nb_elements),
    'DB_CATEGORIES' =>
      sprintf(
        l10n('%d categories including %d physical and %d virtual'),
        $nb_categories,
        $nb_physical,
        $nb_virtual
        ),
    'DB_USERS' => sprintf(l10n('%d users'), $nb_users),
    'DB_GROUPS' => sprintf(l10n('%d groups'), $nb_groups),
    'DB_COMMENTS' => sprintf(l10n('%d comments'), $nb_comments),
    'U_CHECK_UPGRADE' => PHPWG_ROOT_PATH.'admin.php?action=check_upgrade',
    'U_PHPINFO' => PHPWG_ROOT_PATH.'admin.php?action=phpinfo'
    )
  );

if ($nb_elements > 0)
{
  $query = '
SELECT MIN(date_available)
  FROM '.IMAGES_TABLE.'
;';
  list($first_date) = mysql_fetch_row(pwg_query($query));

  $template->assign_block_vars(
    'first_added',
    array(
      'DB_DATE' =>
      sprintf(
        l10n('first element added on %s'),
        format_date($first_date, 'mysql_datetime')
        )
      )
    );
}

// waiting elements
$query = '
SELECT COUNT(*)
  FROM '.WAITING_TABLE.'
  WHERE validated=\'false\'
;';
list($nb_waiting) = mysql_fetch_row(pwg_query($query));

if ($nb_waiting > 0)
{
  $template->assign_block_vars(
    'waiting',
    array(
      'URL' => PHPWG_ROOT_PATH.'admin.php?page=waiting',
      'INFO' => sprintf(l10n('%d waiting for validation'), $nb_waiting)
      )
    );
}

// unvalidated comments
$query = '
SELECT COUNT(*)
  FROM '.COMMENTS_TABLE.'
  WHERE validated=\'false\'
;';
list($nb_comments) = mysql_fetch_row(pwg_query($query));

if ($nb_comments > 0)
{
  $template->assign_block_vars(
    'unvalidated',
    array(
      'URL' => PHPWG_ROOT_PATH.'admin.php?page=comments',
      'INFO' => sprintf(l10n('%d waiting for validation'), $nb_comments)
      )
    );
}

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'intro');

?>