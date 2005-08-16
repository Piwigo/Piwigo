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

//--------------------------------------- validating page and creation of title
$page_valide = false;
$title = '';
$username='';
if (isset($_POST['username']))
{
  $username = $_POST['username'];
}
else if (isset($_POST['userid']))
{
  $username = get_username($_POST['userid']);
}
else if (isset($_GET['user_id']))
{
  $username = get_username($_GET['user_id']);
}

$_GET['page'] = isset($_GET['page']) ? $_GET['page'] : 'intro';

switch ( $_GET['page'] )
{
  case 'user_list' :
  {
    $title = $lang['title_liste_users'];
    $page_valide = true;
    break;
  }
  case 'profile' :
  {
    $title = $lang['title_user_modify'];
    $page_valide = true; 
    break;
  }
 case 'user_perm':
   $title = $lang['title_user_perm'].' '.$username;
   $page_valide = true; break;
 case 'group_list' :
   $title = $lang['title_groups'];        $page_valide = true; break;
 case 'group_perm' :
   if (!is_numeric($_GET['group_id']))
   {
     $_GET['group_id'] = -1;
   }
   $query = '
SELECT name
  FROM '.GROUPS_TABLE.'
  WHERE id = '.$_GET['group_id'].'
;';
   $result = pwg_query($query);
   if (mysql_num_rows($result) > 0 )
   {
     $row = mysql_fetch_array($result);
     $title = $lang['title_group_perm'].' "'.$row['name'].'"';
     $page_valide = true;
   }
   else
   {
     $page_valide = false;
   }
   break;
 case 'stats':
   $title = $lang['title_history'];       $page_valide = true; break;
 case 'update':
   $title = $lang['title_update'];        $page_valide = true; break;
 case 'configuration':
   $title = $lang['title_configuration']; $page_valide = true; break;
 case 'help':
   $title = $lang['title_instructions'];  $page_valide = true; break;
 case 'cat_perm':
   $title = $lang['title_cat_perm'];
   if ( isset( $_GET['cat'] ) )
   {
     check_cat_id( $_GET['cat'] );
     if ( isset( $page['cat'] ) and is_numeric( $page['cat'] ) )
     {
       $result = get_cat_info( $page['cat'] );
       $name = get_cat_display_name($result['name'], '');
       $title.= ' "'.$name.'"';
     }
   }
   $page_valide = true;
   break;
 case 'cat_list':
   $title = $lang['title_categories'];    $page_valide = true; break;
 case 'cat_modify':
   $title = $lang['title_edit_cat'];      $page_valide = true; break;
 case 'infos_images':
   $title = $lang['title_info_images'];   $page_valide = true; break;
 case 'waiting':
   $title = $lang['title_waiting'];       $page_valide = true; break;
 case 'thumbnail':
   $title = $lang['title_thumbnails'];
   if ( isset( $_GET['dir'] ) )
   {
     $title.= ' '.$lang['title_thumbnails_2'].' <span class="titreImg">';
     // $_GET['dir'] contains :
     // ./galleries/vieux_lyon ou
     // ./galleries/vieux_lyon/visite ou
     // ./galleries/vieux_lyon/visite/truc ...
     $dir = explode( "/", $_GET['dir'] );
     $title.= $dir[2];
     for ( $i = 2; $i < sizeof( $dir ) - 1; $i++ )
     {
       $title.= ' &gt; '.$dir[$i+1];
     }
     $title.= "</span>";
   }
   $page_valide = true;
   break;
 case 'comments' :
   $title = $lang['title_comments'];
   $page_valide = true;
   break;
 case 'picture_modify' :
   $title = $lang['title_picmod'];
   $page_valide = true;
   break;
 case 'remote_site' :
 {
   $title = $lang['remote_sites'];
   $page_valide = true;
   break;
 }
 case 'cat_options' :
 {
   $title = $lang['title_cat_options'];
   $page_valide = true;
   break;
 }
 case 'element_set' :
 {
   $title = 'batch management';
   $page_valide = true;
   break;
 }
 case 'maintenance' :
 {
   $title = l10n('Maintenance');
   $page_valide = true;
   break;
 }
 case 'representative' :
 {
   $title = l10n('Representative');
   $page_valide = true;
   break;
 }
//  case 'element_set_unit' :
//  {
//    $title = 'batch management';
//    $page_valide = true;
//    break;
//  }
 case 'intro' :
 {
   $_GET['page'] = 'intro';
   $title = $lang['title_default'];
   $page_valide = true;
   break;
 }
 default :
 {
   break;
 }
}
if ( $title == '' ) $title = $lang['title_default'];

// waiting
$query = 'SELECT id FROM '.WAITING_TABLE;
$query.= " WHERE validated='false'";
$query.= ';';
$result = pwg_query( $query );
$nb_waiting = '';
if ( mysql_num_rows( $result ) > 0 )
{
  $nb_waiting =  ' [ '.mysql_num_rows( $result ).' ]';
}
// comments
$query = 'SELECT id FROM '.COMMENTS_TABLE;
$query.= " WHERE validated='false'";
$query.= ';';
$result = pwg_query( $query );
$nb_comments = '';
if ( mysql_num_rows( $result ) > 0 )
{
  $nb_comments =  ' [ '.mysql_num_rows( $result ).' ]';
}

$link_start = PHPWG_ROOT_PATH.'admin.php?page=';
$conf_link = $link_start.'configuration&amp;section=';
$opt_link = $link_start.'cat_options&amp;section=';
//----------------------------------------------------- template initialization
include(PHPWG_ROOT_PATH.'include/page_header.php');
$template->set_filenames( array('admin'=>'admin.tpl') );

$template->assign_vars(array(
  'L_TITLE'=>$lang['admin_panel'],
  'L_LINKS'=>$lang['links'],
  'L_GALLERY_INDEX'=>$lang['home'],
  'L_GENERAL'=>$lang['general'],
  'L_DEFAULT'=>$lang['gallery_default'],
  'L_HISTORY'=>$lang['history'],
  'L_FAQ'=>$lang['instructions'],
  'L_CONFIGURATION'=>$lang['config'],
  'L_CONFIG_GENERAL'=>$lang['general'],
  'L_CONFIG_COMMENTS'=>$lang['comments'],
  'L_CONFIG_DISPLAY'=>$lang['conf_default'],
  'L_CONFIG_UPLOAD'=>$lang['upload'],
  'L_CONFIG_SESSION'=>$lang['conf_cookie'],
  'L_CONFIG_METADATA'=>$lang['metadata'],
  'L_SITES'=>$lang['remote_sites'],
  'L_CATEGORIES'=>$lang['categories'],
  'L_MANAGE'=>$lang['manage'],
  'L_IMAGES'=>$lang['pictures'],
  'L_WAITING'=>$lang['waiting'].$nb_waiting,
  'L_COMMENTS'=>$lang['comments'].$nb_comments,
  'L_THUMBNAILS'=>$lang['thumbnails'],
  'L_IDENTIFY'=>$lang['identification'],
  'L_USERS'=>$lang['users'],
  'L_GROUPS'=>$lang['groups'],
  'L_UPDATE'=>$lang['update'],
  'L_CAT_UPLOAD'=>$lang['upload'],
  'L_CAT_COMMENTS'=>$lang['comments'],
  'L_CAT_VISIBLE'=>$lang['lock'],
  'L_CAT_STATUS'=>$lang['cat_security'],

  'U_HISTORY'=>add_session_id($link_start.'stats' ),
  'U_FAQ'=>add_session_id($link_start.'help' ),
  'U_SITES'=>add_session_id($link_start.'remote_site'),
  'U_MAINTENANCE'=>add_session_id($link_start.'maintenance'),
  'U_CONFIG_GENERAL'=>add_session_id($conf_link.'general' ),
  'U_CONFIG_COMMENTS'=>add_session_id($conf_link.'comments' ),
  'U_CONFIG_DISPLAY'=>add_session_id($conf_link.'default' ),
  'U_CONFIG_UPLOAD'=>add_session_id($conf_link.'upload' ),
  'U_CONFIG_SESSION'=>add_session_id($conf_link.'session' ),
  'U_CONFIG_METADATA'=>add_session_id($conf_link.'metadata' ),
  'U_CATEGORIES'=>add_session_id($link_start.'cat_list' ),
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
  ));

if ($conf['allow_random_representative'])
{
  $template->assign_block_vars(
    'representative',
    array(
      'URL' => add_session_id($opt_link.'representative')
      )
    );
}
  
//--------------------------------------------------------------------- summary
$link_start = PHPWG_ROOT_PATH.'admin.php?page=';
//------------------------------------------------------------- content display
$page['errors'] = array();
$page['infos'] = array();

if ($page_valide)
{
  switch ($_GET['page'])
  {
    case 'comments' :
    {
      include(PHPWG_ROOT_PATH.'comments.php');
      break;
    }
    case 'profile' :
    {
      include(PHPWG_ROOT_PATH.'profile.php');
      break;
    }
    default :
    {
      include(PHPWG_ROOT_PATH.'admin/'.$_GET['page'].'.php');
    }
  }
}
else
{
  $template->assign_vars(
    array(
      'ADMIN_CONTENT'
      =>'<div style="text-align:center">'.$lang['default_message'].'</div>'
      )
    );
}
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
