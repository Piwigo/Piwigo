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
  header( 'Request-URI: '.$url );  
  header( 'Content-Location: '.$url );  
  header( 'Location: '.$url );
  exit();
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
      'NB_SUBCATS'=>$category['nb_sub_categories'],
      'TOTAL_CAT'=>$category['nb_images'],
      'CAT_ICON'=>get_icon($category['date_last']),
      
      'U_LINK' => add_session_id($url))
    );
  
  if ( $user['expand'] or $category['nb_sub_categories'] == 0 )
  {
    $template->assign_block_vars(
      'category.bulletnolink',
      array('BULLET_IMAGE' => $user['lien_collapsed'])
      );
  }
  else
  {
    $url = PHPWG_ROOT_PATH.'category.php';
    if (isset($page['cat']))
    {
      $url .='?cat='.$page['cat'];
    }
    
    if ( $category['expanded'] )
    {
      $img=$user['lien_expanded'];
    }
    else
    {
      $img=$user['lien_collapsed'];
    }
    
    $template->assign_block_vars(
      'category.bulletlink',
      array(
        'BULLET_IMAGE' =>  $img,
        'U_BULLET_LINK'=>  add_session_id($url))
      );
  }
  
  // recursive call
  if ( $category['expanded'] )
  {
    foreach ( $category['subcats'] as $subcat ) 
	{
      $template->assign_block_vars('category.subcat', array());
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
$cat_title = $lang['no_category'];
if ( isset ( $page['cat'] ) )
{
  if ( is_numeric( $page['cat'] ) )
  {
    $cat_title = get_cat_display_name( $page['cat_name'], ' - ');
  }
  else
  {
    if ( $page['cat'] == 'search' )
    {
      $page['title'].= ' : <span style="font-style:italic;">';
      $page['title'].= $_GET['search']."</span>";
    }
    $page['title'] = replace_space( $page['title'] );
  }
}

$template->assign_vars(array(
  'NB_PICTURE' => count_user_total_images(),
  'TITLE' => $cat_title,
  'USERNAME' => $user['username'],
  'TOP_VISITED'=>$conf['top_number'],

  'L_CATEGORIES' => $lang['categories'],
  'L_HINT_CATEGORY' => $lang['hint_category'],
  'L_SUBCAT' => $lang['sub-cat'],
  'L_IMG_AVAILABLE' => $lang['images_available'],
  'L_TOTAL' => $lang['total'],
  'L_FAVORITE_HINT' => $lang['favorite_cat_hint'],
  'L_FAVORITE' => $lang['favorite_cat'],
  'L_STATS' => $lang['stats'],
  'L_MOST_VISITED_HINT' => $lang['most_visited_cat_hint'],
  'L_MOST_VISITED' => $lang['most_visited_cat'],
  'L_RECENT_HINT' => $lang['recent_cat_hint'],
  'L_RECENT' => $lang['recent_cat'],
  'L_SUMMARY' => $lang['title_menu'],
  'L_UPLOAD' => $lang['upload_picture'],
  'L_COMMENT' => $lang['comments'],
  'L_NB_IMG' => $lang['nb_image_category'],
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
  'T_SHORT'=>get_icon( time() ),
  'T_LONG'=>get_icon( time() - ( $user['short_period'] * 24 * 60 * 60 + 1 ) ),

  'U_HOME' => add_session_id( PHPWG_ROOT_PATH.'category.php' ),
  'U_FAVORITE' => add_session_id( PHPWG_ROOT_PATH.'category.php?cat=fav' ),
  'U_MOST_VISITED'=>add_session_id( PHPWG_ROOT_PATH.'category.php?cat=most_visited' ),
  'U_RECENT'=>add_session_id( PHPWG_ROOT_PATH.'category.php?cat=recent' ),
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

//------------------------------------------------------------------ thumbnails
if ( isset( $page['cat'] ) && $page['cat_nb_images'] != 0 )
{
  $array_cat_directories = array();
  
  $query = 'SELECT distinct(id),file,date_available,tn_ext,name,filesize';
  $query.= ',storage_category_id';
  $query.= ' FROM '.IMAGES_TABLE.' AS i';
  $query.=' INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id=ic.image_id';
  $query.= $page['where'];
  $query.= $conf['order_by'];
  $query.= ' LIMIT '.$page['start'].','.$page['nb_image_page'];
  $query.= ';';
  $result = mysql_query( $query );

  $template->assign_block_vars('thumbnails', array());

  // iteration counter to use a new <tr> every "$nb_image_line" pictures
  $cell_number = 0;
  // iteration counter to be sure not to create too much lines in the table
  $line_number = 0;

  while ( $row = mysql_fetch_array( $result ) )
  {
    // retrieving the storage dir of the picture
    if ( !isset($array_cat_directories[$row['storage_category_id']]))
    {
      $array_cat_directories[$row['storage_category_id']] =
        get_complete_dir( $row['storage_category_id'] );
    }
    $cat_directory = $array_cat_directories[$row['storage_category_id']];

    $file = get_filename_wo_extension( $row['file'] );
    // name of the picture
    if ( isset( $row['name'] ) and $row['name'] != '' ) $name = $row['name'];
    else $name = str_replace( '_', ' ', $file );

    if ( $page['cat'] == 'search' )
    {
      $name = replace_search( $name, $_GET['search'] );
    }
    // thumbnail url
    $thumbnail_url = $cat_directory;
    $thumbnail_url.= 'thumbnail/'.$conf['prefix_thumbnail'];
    $thumbnail_url.= $file.'.'.$row['tn_ext'];
    // message in title for the thumbnail
    $thumbnail_title = $row['file'];
    if ( $row['filesize'] == '' )
      $poids = floor( filesize( $cat_directory.$row['file'] ) / 1024 );
    else
      $poids = $row['filesize'];
    $thumbnail_title .= ' : '.$poids.' KB';
    // url link on picture.php page
    $url_link = PHPWG_ROOT_PATH.'picture.php?cat='.$page['cat'];
    $url_link.= '&amp;image_id='.$row['id'];
    if ( $page['cat'] == 'search' )
    {
      $url_link.= '&amp;search='.$_GET['search'].'&amp;mode='.$_GET['mode'];
    }
    // date of availability for creation icon
    list( $year,$month,$day ) = explode( '-', $row['date_available'] );
    $date = mktime( 0, 0, 0, $month, $day, $year );
    
	// sending vars to display
	if (!$cell_number && ( $line_number< $user['nb_line_page']))
    {
      $template->assign_block_vars('thumbnails.line', array());
      $cell_number = 0;
	  $line_number++;
    }
	if ( $cell_number++ == $user['nb_image_line'] -1) $cell_number = 0;
	
	$template->assign_block_vars('thumbnails.line.thumbnail', array(
	  'IMAGE'=>$thumbnail_url,
	  'IMAGE_ALT'=>$row['file'],
	  'IMAGE_TITLE'=>$thumbnail_title,
	  'IMAGE_NAME'=>$name,
	  'IMAGE_TS'=>get_icon( $date ),

	  'U_IMG_LINK'=>add_session_id( $url_link )
	  ));
	  
    if ( $conf['show_comments'] && $user['show_nb_comments'] )
    {
      $query = 'SELECT COUNT(*) AS nb_comments';
      $query.= ' FROM '.COMMENTS_TABLE.' WHERE image_id = '.$row['id'];
      $query.= " AND validated = 'true'";
      $query.= ';';
      $row = mysql_fetch_array( mysql_query( $query ) );
      $template->assign_block_vars( 'thumbnails.line.thumbnail.nb_comments', 
	    array('NB_COMMENTS'=>$row['nb_comments']) );
    }
  }
}
//-------------------------------------------------------------- empty category
else
{
  $subcats=array();
  if (isset($page['cat'])) $subcats = get_non_empty_subcat_ids( $page['cat'] );
  else                     $subcats = get_non_empty_subcat_ids( '' );
  $cell_number = 0;
  $i = 0;
  
  $template->assign_block_vars('thumbnails', array());
  
  foreach ( $subcats as $subcat_id => $non_empty_id ) 
  {
    $name = '<img src="'.$user['lien_collapsed'].'" style="border:none;"';
    $name.= ' alt="&gt;"/> ';
    $name.= '[ <span style="font-weight:bold;">';
    $name.= $page['plain_structure'][$subcat_id]['name'];
    $name.= '</span> ]';

    // searching the representative picture of the category
    $query = 'SELECT representative_picture_id';
    $query.= ' FROM '.CATEGORIES_TABLE.' WHERE id = '.$non_empty_id;
    $query.= ';';
    $row = mysql_fetch_array( mysql_query( $query ) );
    
    $query = 'SELECT file,tn_ext,storage_category_id';
    $query.= ' FROM '.IMAGES_TABLE.', '.IMAGE_CATEGORY_TABLE;
    $query.= ' WHERE category_id = '.$non_empty_id;
    $query.= ' AND id = image_id';
    // if the category has a representative picture, this is its thumbnail
    // that will be displayed !
    if ( isset( $row['representative_picture_id'] ) )
      $query.= ' AND id = '.$row['representative_picture_id'];
    else
      $query.= ' ORDER BY RAND()';
    $query.= ' LIMIT 0,1';
    $query.= ';';
    $image_result = mysql_query( $query );
    $image_row    = mysql_fetch_array( $image_result );

    $file = get_filename_wo_extension( $image_row['file'] );

    // creating links for thumbnail and associated category
    $thumbnail_link = get_complete_dir( $image_row['storage_category_id'] );
    $thumbnail_link.= 'thumbnail/'.$conf['prefix_thumbnail'];
    $thumbnail_link.= $file.'.'.$image_row['tn_ext'];

    $thumbnail_title = $lang['hint_category'];

    $url_link = PHPWG_ROOT_PATH.'category.php?cat='.$subcat_id;

    $date = $page['plain_structure'][$subcat_id]['date_last'];

    // sending vars to display
	if (!$cell_number && $i < count( $subcats ))
    {
      $template->assign_block_vars('thumbnails.line', array());
      $cell_number = 0;
	  $i++;
    }
	if ( $cell_number++ == $user['nb_image_line'] -1) $cell_number = 0;
	
	$template->assign_block_vars('thumbnails.line.thumbnail', array(
	  'IMAGE'=>$thumbnail_link,
	  'IMAGE_ALT'=>$image_row['file'],
	  'IMAGE_TITLE'=>$thumbnail_title,
	  'IMAGE_NAME'=>$name,
	  'IMAGE_TS'=>get_icon( $date ),

	  'U_IMG_LINK'=>add_session_id( $url_link )
	  ));  
  }
}
//------------------------------------------------------- category informations
if ( isset ( $page['cat'] ) )
{
  // upload a picture in the category
  if ( is_numeric( $page['cat']) && $page['cat_site_id'] == 1
       && $conf['upload_available'] && $page['cat_uploadable'] )
  {
    $url = PHPWG_ROOT_PATH.'upload.php?cat='.$page['cat'];
	$template->assign_block_vars('upload',array('U_UPLOAD'=>add_session_id( $url )));
  }
  
  $template->assign_block_vars('cat_infos',array('NB_IMG_CAT' => $page['cat_nb_images']));

  // navigation bar
  if ( $page['navigation_bar'] != '' )
  { 
    $template->assign_block_vars('cat_infos.navigation',array('NAV_BAR' => $page['navigation_bar']));
  }
  // category comment
  if ( isset( $page['comment'] ) and $page['comment'] != '' )
  {
    $template->assign_block_vars('cat_infos.navigation',array('COMMENTS' => $page['cat_comment']));
  }
}
//------------------------------------------------------------ log informations
pwg_log( 'category', $page['title'] );
mysql_close();

$template->pparse('category');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
