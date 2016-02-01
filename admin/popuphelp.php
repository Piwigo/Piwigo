<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 Piwigo Team                  http://piwigo.org |
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

// +-----------------------------------------------------------------------+
// |                           initialization                              |
// +-----------------------------------------------------------------------+

define('PHPWG_ROOT_PATH', '../');
define('PWG_HELP', true);
define('IN_ADMIN', true);
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

$page['body_id'] = 'thePopuphelpPage';
$title = l10n('Piwigo Help');
$page['page_banner'] = '<h1>'.$title.'</h1>';
$page['meta_robots']=array('noindex'=>1, 'nofollow'=>1);
include(PHPWG_ROOT_PATH.'include/page_header.php');

if
  (
    isset($_GET['page'])
    and preg_match('/^[a-z_]*$/', $_GET['page'])
  )
{
  $help_content = load_language(
    'help/'.$_GET['page'].'.html',
    '',
    array(
      'force_fallback' => 'en_UK',
      'return' => true,
      )
    );

  if ($help_content == false)
  {
    $help_content = '';
  }

  $help_content = trigger_change('get_popup_help_content', $help_content, $_GET['page']);
}
else
{
  die('Hacking attempt!');
}

$template->set_filename('popuphelp','popuphelp.tpl');

$template->assign(
  array
  (
    'HELP_CONTENT' => $help_content
  ));

// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+

$template->pparse('popuphelp');

include(PHPWG_ROOT_PATH.'include/page_tail.php');

?>