<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2004 PhpWebGallery Team - http://phpwebgallery.net |
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

//--------------------------------------- validating page and creation of title
$page_valide = false;
$title = '';
if (isset( $_GET['page'] ))
switch ( $_GET['page'] )
{
 case 'user_list':
   $title = $lang['title_liste_users'];   $page_valide = true; break;
 case 'user_modify':
   $title = $lang['title_modify'];        $page_valide = true; break;
 case 'user_search':
   $username='';
   if (isset($_POST['username'])) $username=$_POST['username'];
   $title = $lang['title_user_perm'].' '.$username;
   $page_valide = true; break;
 case 'group_list' :
   $title = $lang['title_groups'];        $page_valide = true; break;
 case 'group_perm' :
   if ( !is_numeric( $_GET['group_id'] ) ) $_GET['group_id'] = -1;
   $query = 'SELECT name FROM '.GROUPS_TABLE;
   $query.= ' WHERE id = '.$_GET['group_id'];
   $query.= ';';
   $result = pwg_query( $query );
   if ( mysql_num_rows( $result ) > 0 )
   {
     $row = mysql_fetch_array( $result );
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
 case 'admin_phpinfo':
   $title = $lang['phpinfos']; $page_valide = true; break;
 case 'help':
   $title = $lang['title_instructions'];  $page_valide = true; break;
 case 'cat_perm':
   $title = $lang['title_cat_perm'];
   if ( isset( $_GET['cat_id'] ) )
   {
     check_cat_id( $_GET['cat_id'] );
     if ( isset( $page['cat'] ) and is_numeric( $page['cat'] ) )
     {
       $result = get_cat_info( $page['cat'] );
       $name = get_cat_display_name( $result['name'],' &gt; ', '' );
       $title.= ' "'.$name.'"';
     }
   }
   $page_valide = true;
   break;
 case 'cat_list':
   $title = $lang['title_categories'];    $page_valide = true; break;
 case 'cat_modify':
   $title = $lang['title_edit_cat'];      $page_valide = true; break;
 case 'admin_upload':
   $title = $lang['upload'];      $page_valide = true; break;
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
 default:
   $title = $lang['title_default']; break;
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
  'L_PHPINFO'=>$lang['phpinfos'],
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
  'L_AUTH'=>$lang['permissions'],
  'L_UPDATE'=>$lang['update'],
  'L_UPLOAD'=>$lang['cat_options_upload_menu'],
  'L_COMMENTS'=>$lang['cat_options_comments_menu'],
  'L_VISIBLE'=>$lang['cat_options_visible_menu'],
  'L_STATUS'=>$lang['cat_options_status_menu'],

  'U_CONFIG_GENERAL'=>add_session_id($conf_link.'general' ),
  'U_CONFIG_COMMENTS'=>add_session_id($conf_link.'comments' ),
  'U_CONFIG_DISPLAY'=>add_session_id($conf_link.'default' ),
  'U_CONFIG_UPLOAD'=>add_session_id($conf_link.'upload' ),
  'U_CONFIG_SESSION'=>add_session_id($conf_link.'session' ),
  'U_CONFIG_METADATA'=>add_session_id($conf_link.'metadata' ),
  'U_SITES'=>add_session_id($link_start.'remote_site'),
  'U_PHPINFO'=>add_session_id($link_start.'admin_phpinfo' ),
  'U_USERS'=>add_session_id($link_start.'user_search' ),
  'U_GROUPS'=>add_session_id($link_start.'group_list' ),
  'U_CATEGORIES'=>add_session_id($link_start.'cat_list' ),
  'U_UPLOAD'=>add_session_id($opt_link.'upload'),
  'U_COMMENTS'=>add_session_id($opt_link.'comments'),
  'U_VISIBLE'=>add_session_id($opt_link.'visible'),
  'U_STATUS'=>add_session_id($opt_link.'status'),
  'U_WAITING'=>add_session_id($link_start.'waiting' ),
  'U_COMMENTS'=>add_session_id($link_start.'comments' ),
  'U_CAT_UPDATE'=>add_session_id($link_start.'update'),
  'U_THUMBNAILS'=>add_session_id($link_start.'thumbnail' ),
  'U_HISTORY'=>add_session_id($link_start.'stats' ),
  'U_FAQ'=>add_session_id($link_start.'help' ),
  'U_CAT_OPTIONS'=>add_session_id($link_start.'cat_options'),
  'U_RETURN'=>add_session_id(PHPWG_ROOT_PATH.'category.php')
  ));

//--------------------------------------------------------------------- summary
$link_start = PHPWG_ROOT_PATH.'admin.php?page=';
//------------------------------------------------------------- content display
if ( $page_valide )
{
  if ($_GET['page']=='comments') include ( PHPWG_ROOT_PATH.'comments.php');
  else include ( PHPWG_ROOT_PATH.'admin/'.$_GET['page'].'.php' );
}
else
{
  $template->assign_vars(array ('ADMIN_CONTENT'=> '<div style="text-align:center">'.$lang['default_message'].'</div>') );
}
$template->pparse('admin');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
