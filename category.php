<?php
// +-----------------------------------------------------------------------+
// |                             category.php                              |
// +-----------------------------------------------------------------------+
// | application   : PhpWebGallery <http://phpwebgallery.net>              |
// | branch        : BSF (Best So Far)                                     |
// +-----------------------------------------------------------------------+
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

//--------------------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
//---------------------------------------------------------------------- logout
if ( isset( $_GET['act'] )
     and $_GET['act'] == 'logout'
     and isset( $_COOKIE['id'] ) )
{
  // cookie deletion if exists
  setcookie( 'id', '', 0, cookie_path() );
  $url = 'category.php';
  redirect( $url );
}
//-------------------------------------------------- access authorization check
if ( isset( $_GET['cat'] ) ) check_cat_id( $_GET['cat'] );
check_login_authorization();
if ( isset( $page['cat'] ) and is_numeric( $page['cat'] ) )
{
  check_restrictions( $page['cat'] );
}

//-------------------------------------------------------------- initialization
function display_category( $category, $indent )
{
  global $user,$template,$page;
  
  $url = PHPWG_ROOT_PATH.'category.php?cat='.$category['id'];

  $style = '';
  if ( isset( $page['cat'] )
       and is_numeric( $page['cat'] )
       and $category['id'] == $page['cat'] )
  {
    $style = 'font-weight:normal;color:yellow;';
  }
  
  $name = $category['name'];
  if (empty($name)) $name = str_replace( '_', ' ', $category['dir'] );
  
  $template->assign_block_vars('category', array(
      'T_NAME' => $style,
      'LINK_NAME' => $name,
      'INDENT' => $indent,
      'U_LINK' => add_session_id($url),
      'BULLET_IMAGE' => $user['lien_collapsed'])
    );
  
  if ( $category['nb_images'] >  0 )
  {
    $template->assign_block_vars(
      'category.infocat',
      array(
        'TOTAL_CAT'=>$category['nb_images'],
        'CAT_ICON'=>get_icon($category['date_last'])
        ));
  }
  
  // recursive call
  if ( $category['expanded'] )
  {
    foreach ( $category['subcats'] as $subcat ) {
      display_category( $subcat, $indent.str_repeat( '&nbsp;', 2 ));
    }
  }
}

// detection of the start picture to display
if ( !isset( $_GET['start'] )
     or !is_numeric( $_GET['start'] )
     or ( is_numeric( $_GET['start'] ) and $_GET['start'] < 0 ) )
  $page['start'] = 0;
else
  $page['start'] = $_GET['start'];

initialize_category();

// creation of the array containing the cat ids to expand in the menu
// $page['tab_expand'] contains an array with the category ids
// $page['expand'] contains the string to display in URL with comma
$page['tab_expand'] = array();
if ( isset( $page['cat'] ) and is_numeric( $page['cat'] ) )
{
  // the category displayed (in the URL cat=23) must be seen in the menu ->
  // parent categories must be expanded
  $uppercats = explode( ',', $page['uppercats'] );
  foreach ( $uppercats as $uppercat ) {
    array_push( $page['tab_expand'], $uppercat );
  }
}
// in case of expanding all authorized cats $page['tab_expand'] is empty
if ( $user['expand'] )
{
  $page['tab_expand'] = array();
}

// Sometimes, a "num" is provided in the URL. It is the number
// of the picture to show. This picture must be in the thumbnails page.
// We have to find the right $page['start'] that show the num picture
// in this category
if ( isset( $_GET['num'] )
     and is_numeric( $_GET['num'] )
     and $_GET['num'] >= 0 )
{
  $page['start'] = floor( $_GET['num'] / $user['nb_image_page'] );
  $page['start']*= $user['nb_image_page'];
}
// creating the structure of the categories (useful for displaying the menu)
// creating the plain structure : array of all the available categories and
// their relative informations, see the definition of the function
// get_user_plain_structure for further details.
$page['plain_structure'] = get_user_plain_structure();
$page['structure'] = create_user_structure( '' );
$page['structure'] = update_structure( $page['structure'] );

//----------------------------------------------------- template initialization

//
// Start output of page
//
$title = $page['title'];
include(PHPWG_ROOT_PATH.'include/page_header.php');

$template->set_filenames( array('category'=>'category.tpl') );

//-------------------------------------------------------------- category title
if ( !isset( $page['title'] ) )
{
  $page['title'] = $lang['no_category'];
}
$template_title = $page['title'];
if ( isset( $page['cat_nb_images'] ) and $page['cat_nb_images'] > 0 )
{
  $template_title.= ' ['.$page['cat_nb_images'].']';
}

$icon_short = get_icon( date( 'Y-m-d' ) );

$template->assign_vars(array(
  'NB_PICTURE' => count_user_total_images(),
  'TITLE' => $template_title,
  'USERNAME' => $user['username'],
  'TOP_VISITED'=>$conf['top_number'],

  'L_CATEGORIES' => $lang['categories'],
  'L_HINT_CATEGORY' => $lang['hint_category'],
  'L_SUBCAT' => $lang['sub-cat'],
  'L_IMG_AVAILABLE' => $lang['images_available'],
  'L_TOTAL' => $lang['total'],
  'L_FAVORITE_HINT' => $lang['favorite_cat_hint'],
  'L_FAVORITE' => $lang['favorite_cat'],
  'L_SPECIAL_CATEGORIES' => $lang['special_categories'],
  'L_MOST_VISITED_HINT' => $lang['most_visited_cat_hint'],
  'L_MOST_VISITED' => $lang['most_visited_cat'],
  'L_RECENT_PICS_HINT' => $lang['recent_pics_cat_hint'],
  'L_RECENT_PICS' => $lang['recent_pics_cat'],
  'L_RECENT_CATS_HINT' => $lang['recent_cats_cat_hint'],
  'L_RECENT_CATS' => $lang['recent_cats_cat'],
  'L_CALENDAR' => $lang['calendar'],
  'L_CALENDAR_HINT' => $lang['calendar_hint'],
  'L_SUMMARY' => $lang['title_menu'],
  'L_UPLOAD' => $lang['upload_picture'],
  'L_COMMENT' => $lang['comments'],
  'L_IDENTIFY' => $lang['ident_title'],
  'L_SUBMIT' => $lang['menu_login'],
  'L_USERNAME' => $lang['login'],
  'L_PASSWORD' => $lang['password'],
  'L_HELLO' => $lang['hello'],
  'L_LOGOUT' => $lang['logout'],
  'L_ADMIN' => $lang['admin'],
  'L_ADMIN_HINT' => $lang['hint_admin'],
  'L_PROFILE' => $lang['customize'],
  'L_PROFILE_HINT' => $lang['hint_customize'],
  
  'F_IDENTIFY' => add_session_id( PHPWG_ROOT_PATH.'identification.php' ),
  
  'T_COLLAPSED' => $user['lien_collapsed'],
  'T_SHORT' => $icon_short,
  'T_LONG'=>get_icon(date( 'Y-m-d',time()-($user['short_period']*24*60*60+1))),

  'U_HOME' => add_session_id( PHPWG_ROOT_PATH.'category.php' ),
  'U_FAVORITE' => add_session_id( PHPWG_ROOT_PATH.'category.php?cat=fav' ),
  'U_MOST_VISITED'=>add_session_id( PHPWG_ROOT_PATH.'category.php?cat=most_visited' ),
  'U_RECENT_PICS'=>add_session_id( PHPWG_ROOT_PATH.'category.php?cat=recent_pics' ),
  'U_RECENT_CATS'=>add_session_id( PHPWG_ROOT_PATH.'category.php?cat=recent_cats' ),
  'U_CALENDAR'=>add_session_id( PHPWG_ROOT_PATH.'category.php?cat=calendar' ),
  'U_LOGOUT' => PHPWG_ROOT_PATH.'category.php?act=logout',
  'U_ADMIN'=>add_session_id( PHPWG_ROOT_PATH.'admin.php' ),
  'U_PROFILE'=>add_session_id(PHPWG_ROOT_PATH.'profile.php?'.str_replace( '&', '&amp;', $_SERVER['QUERY_STRING'] ))
  )
);

foreach ( $page['structure'] as $category ) {
  // display category is a function relative to the template
  display_category( $category, '&nbsp;');
}

// authentification mode management
if ( !$user['is_the_guest'] )
{
  // searching the number of favorite picture
  $query = 'SELECT COUNT(*) AS count';
  $query.= ' FROM '.FAVORITES_TABLE.' WHERE user_id = '.$user['id'].';';
  $result = mysql_query( $query );
  $row = mysql_fetch_array( $result );
  $template->assign_block_vars('favorites', array ('NB_FAV'=>$row['count']) );
  $template->assign_block_vars('username', array());
}
//--------------------------------------------------------------------- summary

if ( !$user['is_the_guest'] )
{
  $template->assign_block_vars('logout',array());
  // administration link
  if ( $user['status'] == 'admin' )
  {
    $template->assign_block_vars('logout.admin', array());
  }
}
else
{
  $template->assign_block_vars('login',array());
}

// search link
$template->assign_block_vars('summary', array(
'TITLE'=>$lang['hint_search'],
'NAME'=>$lang['search'],
'U_SUMMARY'=>add_session_id( 'search.php' ),
));

// comments link
$template->assign_block_vars('summary', array(
'TITLE'=>$lang['hint_comments'],
'NAME'=>$lang['comments'],
'U_SUMMARY'=>add_session_id( 'comments.php' ),
));

// about link
$template->assign_block_vars('summary', array(
'TITLE'=>$lang['hint_about'],
'NAME'=>$lang['about'],
'U_SUMMARY'=>add_session_id( 'about.php?'.str_replace( '&', '&amp;', $_SERVER['QUERY_STRING'] ) )
));

//------------------------------------------------------ main part : thumbnails
if (isset($page['cat']) and $page['cat_nb_images'] != 0)
{
  include(PHPWG_ROOT_PATH.'include/category_default.inc.php');
}
elseif (isset($page['cat']) and $page['cat'] == 'calendar')
{
  include(PHPWG_ROOT_PATH.'include/category_calendar.inc.php');
}
elseif (isset($page['cat']) and $page['cat'] == 'recent_cats')
{
  include(PHPWG_ROOT_PATH.'include/category_recent_cats.inc.php');
}
else
{
  include(PHPWG_ROOT_PATH.'include/category_subcats.inc.php');
}
//------------------------------------------------------- category informations
if ( isset ( $page['cat'] ) )
{
  // upload a picture in the category
  if ( is_numeric( $page['cat'] )
       and $page['cat_site_id'] == 1
       and $conf['upload_available']
       and $page['cat_uploadable'] )
  {
    $url = PHPWG_ROOT_PATH.'upload.php?cat='.$page['cat'];
    $template->assign_block_vars(
      'upload',
      array('U_UPLOAD'=>add_session_id( $url ))
      );
  }

  if ( $page['navigation_bar'] != ''
       or ( isset( $page['comment'] ) and $page['comment'] != '' ) )
  {
    $template->assign_block_vars('cat_infos',array());
  }
  
  // navigation bar
  if ( $page['navigation_bar'] != '' )
  { 
    $template->assign_block_vars(
      'cat_infos.navigation',
      array('NAV_BAR' => $page['navigation_bar'])
      );
  }
  // category comment
  if ( isset( $page['comment'] ) and $page['comment'] != '' )
  {
    $template->assign_block_vars(
      'cat_infos.comment',
      array('COMMENTS' => $page['comment'])
      );
  }
}
//------------------------------------------------------------ log informations
pwg_log( 'category', $page['title'] );
mysql_close();

$template->pparse('category');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
