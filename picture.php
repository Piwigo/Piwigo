<?php
// +-----------------------------------------------------------------------+
// |                              picture.php                              |
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
//----------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );    
//-------------------------------------------------- access authorization check
check_cat_id( $_GET['cat'] );
check_login_authorization();
if ( isset( $page['cat'] ) and is_numeric( $page['cat'] ) )
{
  check_restrictions( $page['cat'] );
}
//---------------------------------------- incrementation of the number of hits
$query = 'UPDATE '.IMAGES_TABLE.' SET hit=hit+1';
$query.= ' WHERE id='.$_GET['image_id'];
$query.= ';';
@mysql_query( $query );
//-------------------------------------------------------------- initialization
initialize_category( 'picture' );

// if this image_id doesn't correspond to this category, an error message is
// displayed, and execution is stopped
if ( 0 )
{
  echo '<div style="text-align:center;">'.$lang['access_forbiden'].'<br />';
  echo '<a href="'.add_session_id( './category.php' ).'">';
  echo $lang['thumbnails'].'</a></div>';
  exit();
}

// retrieving the number of the picture in its category (in order)
$query = 'SELECT DISTINCT(id)';
$query.= ' FROM '.IMAGES_TABLE;
$query.= ' INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic';
$query.= ' ON id = ic.image_id';
$query.= $page['where'];
$query.= $conf['order_by'];
$query.= ';';
$result = mysql_query( $query );
$page['num'] = 0;
$row = mysql_fetch_array( $result );
while ( $row['id'] != $_GET['image_id'] )
{
  $page['num']++;
  $row = mysql_fetch_array( $result );
}

//---------------------------------------- prev, current & next picture management
$picture=array();
$picture['prev']['name']='';
$picture['next']['name']='';
$picture['prev']['thumbnail']='';
$picture['next']['thumbnail']='';
$picture['prev']['url']='';
$picture['next']['url']='';

$next = $page['num'] + 1;
$prev = $page['num'] - 1;

if ( $page['num'] == $page['cat_nb_images']-1)
{
  $next = 0;
}

$query = 'SELECT * FROM '.IMAGES_TABLE;
$query.= ' INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic';
$query.= ' ON id=ic.image_id';
$query.= $page['where'];
$query.= $conf['order_by'];

if ($prev <0)
  $query.= ' LIMIT 0,2';
else
  $query.= ' LIMIT '.$prev.',3';
  
$query.= ';';

$result = mysql_query( $query );
$nb_row = mysql_num_rows($result);
$index = array('prev','current','next');
for ($i=0; $i<$nb_row;$i++)
{
  $j=($prev<0)?$index[$i+1]:$index[$i];
  $row = mysql_fetch_array($result);
  $picture[$j] = $row;
  
  if ( !isset($array_cat_directories[$row['storage_category_id']]))
  {
    $array_cat_directories[$row['storage_category_id']] =
      get_complete_dir( $row['storage_category_id'] );
  }
  $cat_directory = $array_cat_directories[$row['storage_category_id']];
  $file = substr ( $row['file'], 0, strrpos ( $row['file'], ".") );
  $picture[$j]['src'] = $cat_directory.$row['file'];
  $picture[$j]['thumbnail'] = $cat_directory.'thumbnail/';
  $picture[$j]['thumbnail'].= $conf['prefix_thumbnail'].$file.".".$row['tn_ext'];
  
  if (!empty($row['name']))
  {
    $picture[$j]['name'] = $row['name'];
  }
  else
  {
    $picture[$j]['name'] = str_replace( "_", " ",$file);
  }

  $picture[$j]['url'] = PHPWG_ROOT_PATH.'picture.php?image_id='.$row['id'];
  $picture[$j]['url'].= '&amp;cat='.$page['cat'];
  if ( isset( $_GET['expand'] ) )
    $picture[$j]['url'].= '&amp;expand='.$_GET['expand'];
  if ( $page['cat'] == 'search' )
  {
    $picture[$j]['url'].= "&amp;search=".$_GET['search'].'&amp;mode='.$_GET['mode'];
  }
}

$url_home = './category.php?cat='.$page['cat'].'&amp;';
$url_home.= 'num='.$page['num']; 
if (isset($_GET['expand']))
	$url_home.='&amp;expand='.$_GET['expand'];
if ( $page['cat'] == 'search' )
{
  $url_home.= "&amp;search=".$_GET['search'].'&amp;mode='.$_GET['mode'];
}

$url_admin = PHPWG_ROOT_PATH.'admin.php?page=picture_modify&amp;cat_id='.$page['cat'];
$url_admin.= '&amp;image_id='.$_GET['image_id'];
  
//--------------------------------------------------------- favorite management
if ( isset( $_GET['add_fav'] ) )
{
  $query = 'DELETE FROM '.FAVORITES_TABLE.' WHERE user_id = '.$user['id'];
  $query.= ' AND image_id = '.$picture['current']['id'].';';
  $result = mysql_query( $query );
  
  if ( $_GET['add_fav'] == 1 )
  {
    $query = 'INSERT INTO '.FAVORITES_TABLE.' (image_id,user_id) VALUES';
    $query.= ' ('.$picture['current']['id'].','.$user['id'].');';
	$result = mysql_query( $query );
  }
  if ( !$_GET['add_fav'] && $page['cat']=='fav')
  {
    if ( $prev < 0 && $nb_row==1 )
    {
      // there is no favorite picture anymore
      // we redirect the user to the category page
      $url = add_session_id( $url_home );
      header( 'Request-URI: '.$url );
      header( 'Content-Location: '.$url );  
      header( 'Location: '.$url );
      exit();
    }
	else if ( $prev < 0 )
	{
	  $url = add_session_id( str_replace('&amp;','&',$picture['next']['url']), true);
	}
	else
	{
	  $url = add_session_id( str_replace('&amp;','&',$picture['prev']['url']), true);
	}
	header( 'Request-URI: '.$url );
	header( 'Content-Location: '.$url );  
	header( 'Location: '.$url );
	exit();
  }
}

//
// Start output of page
//

$title =  $picture['current']['name'];
$refresh = 0;
if ( isset( $_GET['slideshow'] ) && $next) 
{
	$refresh= $_GET['slideshow'];
	$url_link = $picture['next']['url'];
}

$title_img = $picture['current']['name'];
if (is_numeric( $page['cat'] )) 
{
  $title_img = get_cat_display_name( $page['cat_name'], " - ","font-style:italic;" );
  $n = $page['num'] + 1;
  $title_img = replace_space( $title_img." - " ).$n.'/';
  $title_img.= $page['cat_nb_images']."<br />";
  $title_img.= $picture['current']['name'];
}
else if ( $page['cat'] == 'search' )
{
  $title_img = replace_search( $title_img, $_GET['search'] );
}

// calculation of width and height
if ( empty($picture['current']['width']))
{
  $taille_image = @getimagesize( $lien_image );
  $original_width = $taille_image[0];
  $original_height = $taille_image[1];
}
else
{
  $original_width = $picture['current']['width'];
  $original_height = $picture['current']['height'];
}

$picture_size = get_picture_size( $original_width, $original_height,
				  $user['maxwidth'], $user['maxheight'] );
				  
include('include/page_header.php');
$template->set_filenames(array('picture'=>'picture.tpl'));
initialize_template();

$template->assign_vars(array(
  'TITLE' => $title_img,
  'PREV_TITLE_IMG' => $picture['prev']['name'],
  'NEXT_TITLE_IMG' => $picture['next']['name'],
  'PREV_IMG' => $picture['prev']['thumbnail'],
  'NEXT_IMG' => $picture['next']['thumbnail'],
  'SRC_IMG' => $picture['current']['src'],
  'ALT_IMG' => $picture['current']['file'],
  'WIDTH_IMG' => $picture_size[0],
  'HEIGHT_IMG' => $picture_size[1],
  'COMMENT_IMG' => $picture['current']['comment'],

  'L_SLIDESHOW' => $lang['slideshow'],
  'L_TIME' => $lang['period_seconds'],
  'L_STOP_SLIDESHOW' => $lang['slideshow_stop'],
  'L_PREV_IMG' =>$lang['previous_image'].' : ',
  'L_ADMIN' =>$lang['link_info_image'],
  'L_BACK' =>$lang['back'],
  'L_COMMENT_TITLE' =>$lang['comments_title'],
  'L_ADD_COMMENT' =>$lang['comments_add'],
  'L_DELETE_COMMENT' =>$lang['comments_del'],
  'L_DELETE' =>$lang['delete'],
  'L_SUBMIT' =>$lang['submit'],
  'L_AUTHOR' =>$lang['author'],
  
  'T_DEL_IMG' =>'./template/'.$user['template'].'/theme/delete.gif',
  
  'U_PREV_IMG' => add_session_id($picture['prev']['url']),
  'U_NEXT_IMG' => add_session_id($picture['next']['url']),
  'U_HOME' => add_session_id($url_home),
  'U_ADMIN' => add_session_id($url_admin),
  'U_ADD_COMMENT' => add_session_id(str_replace( '&', '&amp;', $_SERVER['REQUEST_URI'] ))
  )
);

//-------------------------------------------------------- slideshow management
if ( isset( $_GET['slideshow'] ) )
{
  if ( !is_numeric( $_GET['slideshow'] ) ) $_GET['slideshow'] = $conf['slideshow_period'][0];
	
  $template->assign_block_vars('stop_slideshow', array(
  'U_SLIDESHOW'=>add_session_id( $picture['current']['url'] )
  ));
}
else
{
  $template->assign_block_vars('start_slideshow', array());
  foreach ( $conf['slideshow_period'] as $option ) 
  {
    $template->assign_block_vars('start_slideshow.second', array(
	  'SLIDESHOW_SPEED'=>$option,
	  'U_SLIDESHOW'=>add_session_id( $picture['current']['url'].'&amp;slideshow='.$option)
	  ));
  }
}

if ($prev>=0) $template->assign_block_vars('previous', array());
if ($next) $template->assign_block_vars('next', array());

//--------------------------------------------------------- picture information
// author
if ( !empty($picture['current']['author']) )
{
  $template->assign_block_vars('info_line', array(
	  'INFO'=>$lang['author'],
	  'VALUE'=>$picture['current']['author']
	  ));
}
// creation date
if ( !empty($picture['current']['date_creation']) )
{
  $template->assign_block_vars('info_line', array(
	  'INFO'=>$lang['creation_date'],
	  'VALUE'=>format_date( $picture['current']['date_creation'] ) 
	  ));
}
// date of availability
$template->assign_block_vars('info_line', array(
	  'INFO'=>$lang['registration_date'],
	  'VALUE'=>format_date( $picture['current']['date_available'] ) 
	  ));
// size in pixels
if ( $original_width != $picture_size[0] or $original_height != $picture_size[1] )
{
  $content = '[ <a href="'.$picture['current']['url'].'" title="'.$lang['true_size'].'">';
  $content.= $original_width.'*'.$original_height.'</a> ]';
}
else
{
  $content = $original_width.'*'.$original_height;
}
$template->assign_block_vars('info_line', array(
	  'INFO'=>$lang['size'],
	  'VALUE'=>$content 
	  ));
// file
$template->assign_block_vars('info_line', array(
	  'INFO'=>$lang['file'],
	  'VALUE'=>$picture['current']['file'] 
	  ));
// filesize
if ( empty($picture['current']['filesize']))
{
  $poids = floor ( filesize( $picture['current']['url'] ) / 1024 );
}
else
{
  $poids = $picture['current']['filesize'];
}

$template->assign_block_vars('info_line', array(
	  'INFO'=>$lang['filesize'],
	  'VALUE'=>$poids.' KB'
	  ));
// keywords
if ( !empty($picture['current']['keywords']))
{
  $keywords = explode( ',', $picture['current']['keywords'] );
  $content = '';
  $url = './category.php?cat=search';
  if ( isset( $_GET['expand'] ) ) $url.= '&amp;expand='.$_GET['expand'];
  $url.= '&amp;mode=OR&amp;search=';
  foreach ( $keywords as $i => $keyword ) {
    $local_url = add_session_id( $url.$keyword );
    if ( $i > 0 ) $content.= ',';
    $content.= '<a href="'.$local_url.'">'.$keyword.'</a>';
  }
  $template->assign_block_vars('info_line', array(
    'INFO'=>$lang['keywords'],
    'VALUE'=>$content
    ));
}
// number of visits
$template->assign_block_vars('info_line', array(
    'INFO'=>$lang['visited'],
    'VALUE'=>$picture['current']['hit'].' '.$lang['times']
    ));

//------------------------------------------------------- favorite manipulation
if ( !$user['is_the_guest'] )
{
  // verify if the picture is already in the favorite of the user
  $query = 'SELECT COUNT(*) AS nb_fav';
  $query.= ' FROM '.FAVORITES_TABLE.' WHERE image_id = '.$_GET['image_id'];
  $query.= ' AND user_id = '.$user['id'].';';
  $result = mysql_query( $query );
  $row = mysql_fetch_array( $result );
  if (!$row['nb_fav'])
  {
    $url = './picture.php?cat='.$page['cat'].'&amp;image_id='.$_GET['image_id'];
    if (isset($_GET['expand']))
      $url.= '&amp;expand='.$_GET['expand'];
    $url.='&amp;add_fav=1';
    if ( $page['cat'] == 'search' )
    {
      $url.= '&amp;search='.$_GET['search'].'&amp;mode='.$_GET['mode'];
    }
	$template->assign_block_vars('favorite', array(
      'FAVORITE_IMG' => './template/'.$user['template'].'/theme/favorite.gif',
	  'FAVORITE_HINT' =>$lang['add_favorites_hint'],
	  'FAVORITE_ALT' =>'[ '.$lang['add_favorites_alt'].' ]',
      'U_FAVORITE'=> add_session_id( $url )
    ));
  }
  else
  {
    $url = './picture.php?cat='.$page['cat'].'&amp;image_id='.$_GET['image_id'];
    $url.= '&amp;expand='.$_GET['expand'].'&amp;add_fav=0';
	$template->assign_block_vars('favorite', array(
      'FAVORITE_IMG' => './template/'.$user['template'].'/theme/del_favorite.gif',
	  'FAVORITE_HINT' =>$lang['del_favorites_hint'],
	  'FAVORITE_ALT' =>'[ '.$lang['del_favorites_alt'].' ]',
      'U_FAVORITE'=> add_session_id( $url )
    ));
  }
}
//------------------------------------ admin link for information modifications
if ( $user['status'] == 'admin' )
{
  $template->assign_block_vars('modification', array());
}

//---------------------------------------------------- users's comments display
if ( $conf['show_comments'] )
{
  // comment registeration
  if ( isset( $_POST['content'] ) && !empty($_POST['content']) )
  {
    $register_comment = true;
	$author = !empty($_POST['author'])?$_POST['author']:$lang['guest'];
    // if a guest try to use the name of an already existing user, he must
    // be rejected
    if ( $author != $user['username'] )
    {
      $query = 'SELECT COUNT(*) AS user_exists';
      $query.= ' FROM '.USERS_TABLE;
      $query.= " WHERE username = '".$author."'";
      $query.= ';';
      $row = mysql_fetch_array( mysql_query( $query ) );
      if ( $row['user_exists'] == 1 )
      {
	    $template->assign_block_vars('information', array('INFORMATION'=>$lang['comment_user_exists']));
        $register_comment = false;
      }
    }

    if ( $register_comment )
    {
      // anti-flood system
      $reference_date = time() - $conf['anti-flood_time'];
      $query = 'SELECT id FROM '.COMMENTS_TABLE;
      $query.= ' WHERE date > '.$reference_date;
      $query.= " AND author = '".$author."'";
      $query.= ';';
      if ( mysql_num_rows( mysql_query( $query ) ) == 0
           || $conf['anti-flood_time'] == 0 )
      {
        $query = 'INSERT INTO '.COMMENTS_TABLE;
        $query.= ' (author,date,image_id,content,validated) VALUES (';
		$query.= "'".$author."'";
        $query.= ','.time().','.$_GET['image_id'];
        $query.= ",'".htmlspecialchars( $_POST['content'], ENT_QUOTES)."'";
        if ( !$conf['comments_validation'] || $user['status'] == 'admin' )
          $query.= ",'true'";
        else
          $query.= ",'false'";
        $query.= ');';
        mysql_query( $query );
        // information message
        $message = $lang['comment_added'];
        if ( $conf['comments_validation'] and $user['status'] != 'admin' )
        {
          $message.= '<br />'.$lang['comment_to_validate'];
        }
        $template->assign_block_vars('information', array('INFORMATION'=>$message));
        // notification to the administrators
        if ( $conf['mail_notification'] )
        {
          $cat_name = get_cat_display_name( $page['cat_name'], ' > ', '' );
          $cat_name = strip_tags( $cat_name );
          notify( 'comment', $cat_name.' > '.$picture['current']['name']);
        }
      }
      else
      {
        // information message
        $template->assign_block_vars('information', array('INFORMATION'=>$lang['comment_anti-flood']));
      }
    }
  }
  // comment deletion
  if ( isset( $_GET['del'] )
       && is_numeric( $_GET['del'] )
       && $user['status'] == 'admin' )
  {
    $query = 'DELETE FROM '.COMMENTS_TABLE.' WHERE id = '.$_GET['del'].';';
    mysql_query( $query );
  }
  // number of comment for this picture
  $query = 'SELECT COUNT(*) AS nb_comments';
  $query.= ' FROM '.COMMENTS_TABLE.' WHERE image_id = '.$_GET['image_id'];
  $query.= " AND validated = 'true'";
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );
  
  // navigation bar creation
  $url = './picture.php?cat='.$page['cat'].'&amp;image_id='.$_GET['image_id'];
  if (isset($_GET['expand']))
  	$url.= '&amp;expand='.$_GET['expand'];
  if ( $page['cat'] == 'search' )
  {
    $url.= '&amp;search='.$_GET['search'].'&amp;mode='.$_GET['mode'];
  }
  if( !isset( $_GET['start'] )
      or !is_numeric( $_GET['start'] )
      or ( is_numeric( $_GET['start'] ) and $_GET['start'] < 0 ) )
  {
    $page['start'] = 0;
  }
  else
  {
    $page['start'] = $_GET['start'];
  }
  $page['navigation_bar'] = create_navigation_bar( $url, $row['nb_comments'],
                                                   $page['start'],
                                                   $conf['nb_comment_page'],
                                                   '' );
  $template->assign_block_vars('comments', array(
    'NB_COMMENT'=>$row['nb_comments'],
    'NAV_BAR'=>$page['navigation_bar']));

  $query = 'SELECT id,author,date,image_id,content';
  $query.= ' FROM '.COMMENTS_TABLE.' WHERE image_id = '.$_GET['image_id'];
  $query.= " AND validated = 'true'";
  $query.= ' ORDER BY date ASC';
  $query.= ' LIMIT '.$page['start'].', '.$conf['nb_comment_page'].';';
  $result = mysql_query( $query );
                
  while ( $row = mysql_fetch_array( $result ) )
  {
    $content = nl2br( $row['content'] );

    // replace _word_ by an underlined word
    $pattern = '/_([^\s]*)_/';
    $replacement = '<span style="text-decoration:underline;">\1</span>';
    $content = preg_replace( $pattern, $replacement, $content );

    // replace *word* by a bolded word
    $pattern = '/\*([^\s]*)\*/';
    $replacement = '<span style="font-weight:bold;">\1</span>';
    $content = preg_replace( $pattern, $replacement, $content );

    // replace /word/ by an italic word
    $pattern = '/\/([^\s]*)\//';
    $replacement = '<span style="font-style:italic;">\1</span>';
    $content = preg_replace( $pattern, $replacement, $content );
	
    $template->assign_block_vars('comments.comment', array(
    'COMMENT_AUTHOR'=>empty($row['author'])?$lang['guest']:$row['author'],
    'COMMENT_DATE'=>format_date( $row['date'], 'unix', true ),
	'COMMENT'=>$content
	));
	
    if ( $user['status'] == 'admin' )
    {
	  $template->assign_block_vars('comments.comment.delete', array('U_COMMENT_DELETE'=>add_session_id( $url.'&amp;del='.$row['id'] )));
    }
  }

  if ( !$user['is_the_guest']||( $user['is_the_guest'] and $conf['comments_forall'] ) )
  {
    $template->assign_block_vars('comments.add_comment', array());
    // display author field if the user is not logged in
    if ( !$user['is_the_guest'] )
    {
      $template->assign_block_vars('comments.add_comment.author_known', array('KNOWN_AUTHOR'=>$user['username']));
	}
    else
    {
      $template->assign_block_vars('comments.add_comment.author_field', array());
    }
  }
}
//------------------------------------------------------------ log informations
pwg_log( 'picture', $title_img, $picture['current']['file'] );
mysql_close();

$template->pparse('picture');
include('include/page_tail.php');
?>
