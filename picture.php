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
// retrieving the number of the picture in its category (in order)
$query = '
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  '.$page['where'].'
  '.$conf['order_by'].'
;';
$result = mysql_query( $query );
$page['num'] = 0;
$belongs = false;
while ($row = mysql_fetch_array($result))
{
  if ($row['id'] == $_GET['image_id'])
  {
    $belongs = true;
    break;
  }
  $page['num']++;
}
// if this image_id doesn't correspond to this category, an error message is
// displayed, and execution is stopped
if (!$belongs)
{
  echo '<div style="text-align:center;">'.$lang['access_forbiden'].'<br />';
  echo '<a href="'.add_session_id( PHPWG_ROOT_PATH.'category.php' ).'">';
  echo $lang['thumbnails'].'</a></div>';
  exit();
}
//------------------------------------- prev, current & next picture management
$picture = array();

if ($page['num'] == 0)
{
  $has_prev = false;
}
else
{
  $has_prev = true;
}

if ($page['num'] == $page['cat_nb_images'] - 1)
{
  $has_next = false;
}
else
{
  $has_next = true;
}

$query = '
SELECT *
  FROM '.IMAGES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id=ic.image_id
  '.$page['where'].'
  '.$conf['order_by'].'
  ';

if ( !$has_prev )
{
  $query.= ' LIMIT 0,2';
}
else
{
  $query.= ' LIMIT '.($page['num'] - 1).',3';
}
$query.= ';';

$result = mysql_query( $query );
$indexes = array('prev', 'current', 'next');

foreach (array('prev', 'current', 'next') as $i)
{
  if ($i == 'prev' and !$has_prev)
  {
    continue;
  }
  if ($i == 'next' and !$has_next)
  {
    break;
  }

  $row = mysql_fetch_array($result);
  foreach (array_keys($row) as $key)
  {
    if (!is_numeric($key))
    {
      $picture[$i][$key] = $row[$key];
    }
  }

  $picture[$i]['is_picture'] = false;
  if (in_array(get_extension($row['file']), $conf['picture_ext']))
  {
    $picture[$i]['is_picture'] = true;
  }
  
  if ( !isset($array_cat_directories[$row['storage_category_id']]))
  {
    $array_cat_directories[$row['storage_category_id']] =
      get_complete_dir( $row['storage_category_id'] );
  }
  $cat_directory = $array_cat_directories[$row['storage_category_id']];
  $file_wo_ext = get_filename_wo_extension($row['file']);

  $icon = './template/'.$user['template'].'/mimetypes/';
  $icon.= strtolower(get_extension($row['file'])).'.png';

  if (isset($row['representative_ext']) and $row['representative_ext'] =! '')
  {
    $picture[$i]['src'] = $cat_directory.'representative/';
    $picture[$i]['src'].= $file_wo_ext.'.'.$row['representative_ext'];
  }
  else
  {
    $picture[$i]['src'] = $icon;
  }
  // special case for picture files
  if ($picture[$i]['is_picture'])
  {
    $picture[$i]['src'] = $cat_directory.$row['file'];
  }

  // if picture is not a file, we need the download link
  if (!$picture[$i]['is_picture'])
  {
    $picture[$i]['download'] = $cat_directory.$row['file'];
  }

  if (isset($row['tn_ext']) and $row['tn_ext'] != '')
  {
    $picture[$i]['thumbnail'] = $cat_directory.'thumbnail/';
    $picture[$i]['thumbnail'].= $conf['prefix_thumbnail'].$file_wo_ext;
    $picture[$i]['thumbnail'].= '.'.$row['tn_ext'];
  }
  else
  {
    $picture[$i]['thumbnail'] = $icon;
  }
  
  if ( !empty( $row['name'] ) )
  {
    $picture[$i]['name'] = $row['name'];
  }
  else
  {
    $picture[$i]['name'] = str_replace('_', ' ', $file_wo_ext);
  }

  $picture[$i]['url'] = PHPWG_ROOT_PATH.'picture.php?image_id='.$row['id'];
  $picture[$i]['url'].= '&amp;cat='.$page['cat'];
  if ( $page['cat'] == 'search' )
  {
    $picture[$i]['url'].= '&amp;search='.$_GET['search'];
  }
  if (isset($_GET['show_metadata']))
  {
    $picture[$i]['url'].= '&amp;show_metadata=1';
  }
}

$url_home = PHPWG_ROOT_PATH.'category.php?cat='.$page['cat'].'&amp;';
$url_home.= 'num='.$page['num']; 
if ( $page['cat'] == 'search' )
{
  $url_home.= "&amp;search=".$_GET['search'];
}

$url_admin = PHPWG_ROOT_PATH.'admin.php?page=picture_modify';
$url_admin.= '&amp;cat_id='.$page['cat'];
$url_admin.= '&amp;image_id='.$_GET['image_id'];
  
//--------------------------------------------------------- favorite management
if ( isset( $_GET['add_fav'] ) )
{
  $query = 'DELETE FROM '.FAVORITES_TABLE;
  $query.= ' WHERE user_id = '.$user['id'];
  $query.= ' AND image_id = '.$picture['current']['id'];
  $query.= ';';
  $result = mysql_query( $query );
  
  if ( $_GET['add_fav'] == 1 )
  {
    $query = 'INSERT INTO '.FAVORITES_TABLE;
    $query.= ' (image_id,user_id) VALUES';
    $query.= ' ('.$picture['current']['id'].','.$user['id'].')';
    $query.= ';';
    $result = mysql_query( $query );
  }
  if ( !$_GET['add_fav'] and $page['cat'] == 'fav' )
  {
    if (!$has_prev and $mysql_num_rows == 1)
    {
      // there is no favorite picture anymore we redirect the user to the
      // category page
      $url = add_session_id( $url_home );
      redirect( $url );
    }
    else if (!$has_prev)
    {
      $url = str_replace( '&amp;', '&', $picture['next']['url'] );
      $url = add_session_id( $url, true);
    }
    else
    {
      $url = str_replace('&amp;', '&', $picture['prev']['url'] );
      $url = add_session_id( $url, true);
    }
    redirect( $url );
  }
}

//------------------------------------------------------  comment registeration
if ( isset( $_POST['content'] ) && !empty($_POST['content']) )
{
  $register_comment = true;
  $author = !empty($_POST['author'])?$_POST['author']:$lang['guest'];
  // if a guest try to use the name of an already existing user, he must be
  // rejected
  if ( $author != $user['username'] )
  {
    $query = 'SELECT COUNT(*) AS user_exists';
    $query.= ' FROM '.USERS_TABLE;
    $query.= " WHERE username = '".$author."'";
    $query.= ';';
    $row = mysql_fetch_array( mysql_query( $query ) );
    if ( $row['user_exists'] == 1 )
    {
      $template->assign_block_vars(
        'information',
        array('INFORMATION'=>$lang['comment_user_exists']));
      $register_comment = false;
    }
  }
  
  if ( $register_comment )
  {
    // anti-flood system
    $reference_date = time() - $conf['anti-flood_time'];
    $query = 'SELECT id FROM '.COMMENTS_TABLE;
    $query.= ' WHERE date > FROM_UNIXTIME('.$reference_date.')';
    $query.= " AND author = '".$author."'";
    $query.= ';';
    if ( mysql_num_rows( mysql_query( $query ) ) == 0
         or $conf['anti-flood_time'] == 0 )
    {
      $query = 'INSERT INTO '.COMMENTS_TABLE;
      $query.= ' (author,date,image_id,content,validated) VALUES (';
      $query.= "'".$author."'";
      $query.= ',NOW(),'.$_GET['image_id'];
      $query.= ",'".htmlspecialchars( $_POST['content'], ENT_QUOTES)."'";
      if ( !$conf['comments_validation'] or $user['status'] == 'admin' )
      {        
        $query.= ",'true'";
      }
      else
      {
        $query.= ",'false'";
      }
      $query.= ');';
      mysql_query( $query );
      // information message
      $message = $lang['comment_added'];
      if ( $conf['comments_validation'] and $user['status'] != 'admin' )
      {
        $message.= '<br />'.$lang['comment_to_validate'];
      }
      $template->assign_block_vars('information',
                                   array('INFORMATION'=>$message));
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
      $template->assign_block_vars(
        'information',
        array('INFORMATION'=>$lang['comment_anti-flood']));
    }
  }
}
// comment deletion
if ( isset( $_GET['del'] )
     and is_numeric( $_GET['del'] )
     and $user['status'] == 'admin' )
{
  $query = 'DELETE FROM '.COMMENTS_TABLE;
  $query.= ' WHERE id = '.$_GET['del'];
  $query.= ';';
  mysql_query( $query );
}

//
// Start output of page
//

$title =  $picture['current']['name'];
$refresh = 0;
if ( isset( $_GET['slideshow'] ) and $next )
{
  $refresh= $_GET['slideshow'];
  $url_link = $picture['next']['url'].'&amp;slideshow='.$refresh;
}

$title_img = $picture['current']['name'];
$title_nb = '';
if (is_numeric( $page['cat'] )) 
{
  $title_img = replace_space(get_cat_display_name( $page['cat_name'], " &gt; "));
  $n = $page['num'] + 1;
  $title_nb = "Photo".' '.$n.'/';
  $title_nb.= $page['cat_nb_images'];
  //$title_img.= $picture['current']['name'];
}
else if ( $page['cat'] == 'search' )
{
  $title_img = replace_search( $title_img, $_GET['search'] );
}

// calculation of width and height
if (empty($picture['current']['width']))
{
  $taille_image = @getimagesize($picture['current']['src']);
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
				  
include(PHPWG_ROOT_PATH.'include/page_header.php');
$template->set_filenames(array('picture'=>'picture.tpl'));

$template->assign_vars(array(
  'CATEGORY' => $title_img,
  'PHOTO' => $title_nb,
  'TITLE' => $picture['current']['name'],
  'SRC_IMG' => $picture['current']['src'],
  'ALT_IMG' => $picture['current']['file'],
  'WIDTH_IMG' => $picture_size[0],
  'HEIGHT_IMG' => $picture_size[1],

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
  'L_COMMENT' =>$lang['comment'],
  'L_DOWNLOAD' => $lang['download'],
  'L_DOWNLOAD_HINT' => $lang['download_hint'],
  'L_PICTURE_SHOW_METADATA' => $lang['picture_show_metadata'],
  'L_PICTURE_HIDE_METADATA' => $lang['picture_hide_metadata'],
  
  'T_DEL_IMG' =>PHPWG_ROOT_PATH.'template/'.$user['template'].'/theme/delete.gif',
  
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

if ($has_prev)
{
  $template->assign_block_vars(
    'previous',
    array(
      'TITLE_IMG' => $picture['prev']['name'],
      'IMG' => $picture['prev']['thumbnail'],
      'U_IMG' => add_session_id($picture['prev']['url'])
      ));
}

if ($has_next)
{
  $template->assign_block_vars(
    'next',
    array(
      'TITLE_IMG' => $picture['next']['name'],
      'IMG' => $picture['next']['thumbnail'],
      'U_IMG' => add_session_id($picture['next']['url'])
      ));
}

//--------------------------------------------------------- picture information
// legend
if (isset($picture['current']['comment'])
    and !empty($picture['current']['comment']))
{
  $template->assign_block_vars(
    'legend',
    array(
        'COMMENT_IMG' => $picture['current']['comment']
      ));
}
// download link if file is not a picture
if (!$picture['current']['is_picture'])
{
  $template->assign_block_vars(
    'download',
    array(
        'U_DOWNLOAD' => $picture['current']['download']
      ));
}

// author
if ( !empty($picture['current']['author']) )
{
  $template->assign_block_vars(
    'info_line',
    array(
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
if ($picture['current']['is_picture'])
{
  if ($original_width != $picture_size[0]
      or $original_height != $picture_size[1])
  {
    $content = '[ <a href="'.$picture['current']['url'].'" ';
    $content.= ' title="'.$lang['true_size'].'">';
    $content.= $original_width.'*'.$original_height.'</a> ]';
  }
  else
  {
    $content = $original_width.'*'.$original_height;
  }
  $template->assign_block_vars(
    'info_line',
    array(
      'INFO'=>$lang['size'],
      'VALUE'=>$content 
      ));
}
// file
$template->assign_block_vars('info_line', array(
	  'INFO'=>$lang['file'],
	  'VALUE'=>$picture['current']['file'] 
	  ));
// filesize
if (empty($picture['current']['filesize']))
{
  if (!$picture[$i]['is_picture'])
  {
    $filesize = floor(filesize($picture['current']['download'])/1024);
  }
  else
  {
    $filesize = floor(filesize($picture['current']['src'])/1024);
  }
}
else
{
  $filesize = $picture['current']['filesize'];
}

$template->assign_block_vars('info_line', array(
	  'INFO'=>$lang['filesize'],
	  'VALUE'=>$filesize.' KB'
	  ));
// keywords
if ( !empty($picture['current']['keywords']))
{
  $keywords = explode( ',', $picture['current']['keywords'] );
  $content = '';
  $url = PHPWG_ROOT_PATH.'category.php?cat=search';
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
//-------------------------------------------------------------------- metadata
if ($conf['show_exif'] or $conf['show_iptc'])
{
  $metadata_showable = true;
}
else
{
  $metadata_showable = false;
}

if ($metadata_showable and !isset($_GET['show_metadata']))
{
  $url = PHPWG_ROOT_PATH.'picture.php?'.$_SERVER['QUERY_STRING'];
  $url.= '&amp;show_metadata=1';
  $template->assign_block_vars('show_metadata', array('URL' => $url));
}

if ($metadata_showable and isset($_GET['show_metadata']))
{
  $url = PHPWG_ROOT_PATH.'picture.php';
  
  $str = $_SERVER['QUERY_STRING'];
  parse_str($str, $get_vars);
  $is_first = true;
  foreach ($get_vars as $key => $value)
  {
    if ($key != 'show_metadata')
    {
      if ($is_first)
      {
        $url.= '?';
        $is_first = false;
      }
      else
      {
        $url.= '&amp;';
      }
      $url.= $key.'='.$value;
    }
  }
  
  $template->assign_block_vars('hide_metadata', array('URL' => $url));
  
  include_once(PHPWG_ROOT_PATH.'/include/functions_metadata.inc.php');
  
  $template->assign_block_vars('metadata', array());
  
  if ($conf['show_exif'])
  {
    if ($exif = @read_exif_data($picture['current']['src']))
    {
      $template->assign_block_vars(
        'metadata.headline',
        array('TITLE' => 'EXIF Metadata')
        );
      
      foreach ($conf['show_exif_fields'] as $field)
      {
        if (strpos($field, ';') === false)
        {
          if (isset($exif[$field]))
          {
            $key = $field;
            if (isset($lang['exif_field_'.$field]))
            {
              $key = $lang['exif_field_'.$field];
            }
            
            $template->assign_block_vars(
              'metadata.line',
              array(
                'KEY' => $key,
                'VALUE' => $exif[$field]
                )
              );
          }
        }
        else
        {
          $tokens = explode(';', $field);
          if (isset($exif[$tokens[0]][$tokens[1]]))
          {
            $key = $tokens[1];
            if (isset($lang['exif_field_'.$tokens[1]]))
            {
              $key = $lang['exif_field_'.$tokens[1]];
            }
            
            $template->assign_block_vars(
              'metadata.line',
              array(
                'KEY' => $key,
                'VALUE' => $exif[$tokens[0]][$tokens[1]]
                )
              );
          }
        }
      }
    }
  }

  if ($conf['show_iptc'])
  {
    $iptc = get_iptc_data($picture['current']['src'],
                          $conf['show_iptc_mapping']);

    if (count($iptc) > 0)
    {
      $template->assign_block_vars(
        'metadata.headline',
        array('TITLE' => 'IPTC Metadata')
        );
    }
    
    foreach ($iptc as $field => $value)
    {
      $key = $field;
      if (isset($lang[$field]))
      {
        $key = $lang[$field];
      }
      
      $template->assign_block_vars(
        'metadata.line',
        array(
          'KEY' => $key,
          'VALUE' => $value
          )
        );
    }
  }
}
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
    $url = PHPWG_ROOT_PATH.'picture.php?cat='.$page['cat'].'&amp;image_id='.$_GET['image_id'];
    $url.='&amp;add_fav=1';
    if ( $page['cat'] == 'search' )
    {
      $url.= '&amp;search='.$_GET['search'].'&amp;mode='.$_GET['mode'];
    }
	$template->assign_block_vars('favorite', array(
      'FAVORITE_IMG' => PHPWG_ROOT_PATH.'template/'.$user['template'].'/theme/favorite.gif',
	  'FAVORITE_HINT' =>$lang['add_favorites_hint'],
	  'FAVORITE_ALT' =>'[ '.$lang['add_favorites_alt'].' ]',
      'U_FAVORITE'=> add_session_id( $url )
    ));
  }
  else
  {
    $url = PHPWG_ROOT_PATH.'picture.php?cat='.$page['cat'].'&amp;image_id='.$_GET['image_id'];
    $url.= '&amp;add_fav=0';
	$template->assign_block_vars('favorite', array(
      'FAVORITE_IMG' => PHPWG_ROOT_PATH.'template/'.$user['template'].'/theme/del_favorite.gif',
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
  // number of comment for this picture
  $query = 'SELECT COUNT(*) AS nb_comments';
  $query.= ' FROM '.COMMENTS_TABLE.' WHERE image_id = '.$_GET['image_id'];
  $query.= " AND validated = 'true'";
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );
  
  // navigation bar creation
  $url = PHPWG_ROOT_PATH.'picture.php?cat='.$page['cat'].'&amp;image_id='.$_GET['image_id'];
  if ( $page['cat'] == 'search' )
  {
    $url.= '&amp;search='.$_GET['search'];
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
    'COMMENT_DATE'=>format_date( $row['date'], 'mysql_datetime', true ),
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
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
