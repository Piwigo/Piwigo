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

// +-----------------------------------------------------------------------+
// |                           initialization                              |
// +-----------------------------------------------------------------------+

define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_GUEST);

$page['body_id'] = 'thePopuphelpPage';
$title = l10n('PhpWebGallery Help');
$page['page_banner'] = '<h1>'.$title.'</h1>';
include(PHPWG_ROOT_PATH.'include/page_header.php');

if
  (
    isset($_GET['page'])
    and preg_match('/^[a-z_]*$/', $_GET['page'])
  )
{
  $help_content =
    load_language('help/'.$_GET['page'].'.html', '', '', true);

  if ($help_content == false)
  {
    $help_content = '';
  }

  $help_content = trigger_event(
    'get_popup_help_content', $help_content, $_GET['page']);
}
else
{
  die('Hacking attempt!');
}

$template->set_filenames(array('popuphelp' => 'popuphelp.tpl'));

$template->assign_vars(
  array
  (
    'HELP_CONTENT' => $help_content
  ));

// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+

$template->parse('popuphelp');

include(PHPWG_ROOT_PATH.'include/page_tail.php');

?>