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

//----------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
define('IN_ADMIN', true);
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
include_once( PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php' );

// +-----------------------------------------------------------------------+
// |                    synchronize user informations                      |
// +-----------------------------------------------------------------------+

sync_users();

// +-----------------------------------------------------------------------+
// |                            variables init                             |
// +-----------------------------------------------------------------------+

if (isset($_GET['page'])
    and preg_match('/^[a-z_]*$/', $_GET['page'])
    and is_file(PHPWG_ROOT_PATH.'admin/'.$_GET['page'].'.php'))
{
  $page['page'] = $_GET['page'];
}
else
{
  $page['page'] = 'intro';
}

$link_start = PHPWG_ROOT_PATH.'admin.php?page=';
$conf_link = $link_start.'configuration&amp;section=';
$opt_link = $link_start.'cat_options&amp;section=';
//----------------------------------------------------- template initialization
$title = l10n('PhpWebGallery Administration'); // for include/page_header.php
$page['gallery_title'] = l10n('PhpWebGallery Administration');
$page['body_id'] = 'theAdminPage';
include(PHPWG_ROOT_PATH.'include/page_header.php');

$template->set_filenames(array('admin' => 'admin.tpl'));

$template->assign_vars(
  array(
    'U_HISTORY'=>add_session_id($link_start.'stats' ),
    'U_FAQ'=>add_session_id($link_start.'help' ),
    'U_SITES'=>add_session_id($link_start.'remote_site'),
    'U_MAINTENANCE'=>add_session_id($link_start.'maintenance'),
    'U_CONFIG_GENERAL'=>add_session_id($conf_link.'general' ),
    'U_CONFIG_COMMENTS'=>add_session_id($conf_link.'comments' ),
    'U_CONFIG_DISPLAY'=>add_session_id($conf_link.'default' ),
    'U_CATEGORIES'=>add_session_id($link_start.'cat_list' ),
    'U_MOVE'=>add_session_id($link_start.'cat_move' ),
    'U_CAT_UPLOAD'=>add_session_id($opt_link.'upload'),
    'U_CAT_COMMENTS'=>add_session_id($opt_link.'comments'),
    'U_CAT_VISIBLE'=>add_session_id($opt_link.'visible'),
    'U_CAT_STATUS'=>add_session_id($opt_link.'status'),
    'U_CAT_OPTIONS'=>add_session_id($link_start.'cat_options'),
    'U_CAT_UPDATE'=>add_session_id($link_start.'update'),
    'U_WAITING'=>add_session_id($link_start.'waiting' ),
    'U_COMMENTS'=>add_session_id($link_start.'comments' ),
    'U_CADDIE'=>add_session_id($link_start.'element_set&amp;cat=caddie'),
    'U_THUMBNAILS'=>add_session_id($link_start.'thumbnail' ),
    'U_USERS'=>add_session_id($link_start.'user_list' ),
    'U_GROUPS'=>add_session_id($link_start.'group_list' ),
    'U_RETURN'=>add_session_id(PHPWG_ROOT_PATH.'category.php')
    )
  );

if ($conf['allow_random_representative'])
{
  $template->assign_block_vars(
    'representative',
    array(
      'URL' => add_session_id($opt_link.'representative')
      )
    );
}
  
//------------------------------------------------------------- content display
$page['errors'] = array();
$page['infos']  = array();

include(PHPWG_ROOT_PATH.'admin/'.$page['page'].'.php');

// +-----------------------------------------------------------------------+
// |                            errors & infos                             |
// +-----------------------------------------------------------------------+

if (count($page['errors']) != 0)
{
  $template->assign_block_vars('errors',array());
  foreach ($page['errors'] as $error)
  {
    $template->assign_block_vars('errors.error',array('ERROR'=>$error));
  }
}

if (count($page['infos']) != 0)
{
  $template->assign_block_vars('infos',array());
  foreach ($page['infos'] as $info)
  {
    $template->assign_block_vars('infos.info',array('INFO'=>$info));
  }
}

$template->parse('admin');
include(PHPWG_ROOT_PATH.'include/page_tail.php');

// +-----------------------------------------------------------------------+
// |                     order permission refreshment                      |
// +-----------------------------------------------------------------------+

$query = '
UPDATE '.USER_CACHE_TABLE.'
  SET need_update = \'true\'
;';
pwg_query($query);
?>
