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

$rate_items = array(0,1,2,3,4,5);
//--------------------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once(PHPWG_ROOT_PATH.'include/common.inc.php');
//-------------------------------------------------- access authorization check
check_cat_id( $_GET['cat'] );

if (!isset($page['cat']))
{
  die($lang['access_forbiden']);
}

check_login_authorization();
if ( isset( $page['cat'] ) and is_numeric( $page['cat'] ) )
{
  check_restrictions( $page['cat'] );
}
//---------------------------------------- incrementation of the number of hits
$query = '
UPDATE '.IMAGES_TABLE.'
  SET hit = hit+1
  WHERE id = '.$_GET['image_id'].'
;';
@pwg_query( $query );
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
$result = pwg_query( $query );
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
//-------------------------------------------------------------- representative
if ('admin' == $user['status'] and isset($_GET['representative']))
{
  $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET representative_picture_id = '.$_GET['image_id'].'
  WHERE id = '.$page['cat'].'
;';
  pwg_query($query);

  $url =
    PHPWG_ROOT_PATH
    .'picture.php'
    .get_query_string_diff(array('representative'));
  redirect($url);
}

//-------------------------------------------------------------- caddie filling

if (isset($_GET['caddie']))
{
  fill_caddie(array($_GET['image_id']));

  $url =
    PHPWG_ROOT_PATH
    .'picture.php'
    .get_query_string_diff(array('caddie'));
  redirect($url);
}

//---------------------------------------------------------- related categories
$query = '
SELECT category_id,uppercats,commentable,global_rank
  FROM '.IMAGE_CATEGORY_TABLE.'
    INNER JOIN '.CATEGORIES_TABLE.' ON category_id = id
  WHERE image_id = '.$_GET['image_id'].'
    AND category_id NOT IN ('.$user['forbidden_categories'].')
;';
$result = pwg_query($query);
$related_categories = array();
while ($row = mysql_fetch_array($result))
{
  array_push($related_categories, $row);
}
usort($related_categories, 'global_rank_compare');
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
SELECT DISTINCT(i.id), i.*
  FROM '.IMAGES_TABLE.' AS i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON i.id = ic.image_id
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

$result = pwg_query( $query );
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
  
  $cat_directory = dirname($row['path']);
  $file_wo_ext = get_filename_wo_extension($row['file']);

  $icon = get_themeconf('mime_icon_dir');
  $icon.= strtolower(get_extension($row['file'])).'.png';

  if (isset($row['representative_ext']) and $row['representative_ext'] != '')
  {
    $picture[$i]['src'] = $cat_directory.'/pwg_representative/';
    $picture[$i]['src'].= $file_wo_ext.'.'.$row['representative_ext'];
  }
  else
  {
    $picture[$i]['src'] = $icon;
  }
  // special case for picture files
  if ($picture[$i]['is_picture'])
  {
    $picture[$i]['src'] = $row['path'];
    // if we are working on the "current" element, we search if there is a
    // high quality picture
    // FIXME : with remote pictures, this "remote fopen" takes long...
    if ($i == 'current')
    {
      if (@fopen($cat_directory.'/pwg_high/'.$row['file'], 'r'))
      {
        $picture[$i]['high'] = $cat_directory.'/pwg_high/'.$row['file'];
      }
    }
  }

  // if picture is not a file, we need the download link
  if (!$picture[$i]['is_picture'])
  {
    $picture[$i]['download'] = $row['path'];
  }

  $picture[$i]['thumbnail'] = get_thumbnail_src($row['path'], @$row['tn_ext']);
  
  if ( !empty( $row['name'] ) )
  {
    $picture[$i]['name'] = $row['name'];
  }
  else
  {
    $picture[$i]['name'] = str_replace('_', ' ', $file_wo_ext);
  }

  $picture[$i]['url'] = PHPWG_ROOT_PATH.'picture.php';
  $picture[$i]['url'].= get_query_string_diff(array('image_id','add_fav',
                                                    'slideshow','rate'));
  $picture[$i]['url'].= '&amp;image_id='.$row['id'];
}

$url_up = PHPWG_ROOT_PATH.'category.php?cat='.$page['cat'].'&amp;';
$url_up.= 'num='.$page['num']; 
if ( $page['cat'] == 'search' )
{
  $url_up.= "&amp;search=".$_GET['search'];
}
if ( $page['cat'] == 'list' )
{
  $url_up.= "&amp;list=".$_GET['list'];
}

$url_admin = PHPWG_ROOT_PATH.'admin.php?page=picture_modify';
$url_admin.= '&amp;cat_id='.$page['cat'];
$url_admin.= '&amp;image_id='.$_GET['image_id'];

$url_slide = $picture['current']['url'];
$url_slide.= '&amp;slideshow='.$conf['slideshow_period'];

//----------------------------------------------------------- rate registration
if (isset($_GET['rate'])
    and $conf['rate']
    and !$user['is_the_guest']
    and in_array($_GET['rate'], $rate_items))
{
  $query = '
DELETE
  FROM '.RATE_TABLE.'
  WHERE user_id = '.$user['id'].'
    AND element_id = '.$_GET['image_id'].'
;';
  pwg_query($query);
  $query = '
INSERT INTO '.RATE_TABLE.'
  (user_id,element_id,rate)
  VALUES
  ('.$user['id'].','.$_GET['image_id'].','.$_GET['rate'].')
;';
  pwg_query($query);

  // update of images.average_rate field
  $query = '
SELECT ROUND(AVG(rate),2) AS average_rate
  FROM '.RATE_TABLE.'
  WHERE element_id = '.$_GET['image_id'].'
;';
  $row = mysql_fetch_array(pwg_query($query));
  $query = '
UPDATE '.IMAGES_TABLE.'
  SET average_rate = '.$row['average_rate'].'
  WHERE id = '.$_GET['image_id'].'
;';
  pwg_query($query);
}
//--------------------------------------------------------- favorite management
if ( isset( $_GET['add_fav'] ) )
{
  $query = 'DELETE FROM '.FAVORITES_TABLE;
  $query.= ' WHERE user_id = '.$user['id'];
  $query.= ' AND image_id = '.$picture['current']['id'];
  $query.= ';';
  $result = pwg_query( $query );
  
  if ( $_GET['add_fav'] == 1 )
  {
    $query = 'INSERT INTO '.FAVORITES_TABLE;
    $query.= ' (image_id,user_id) VALUES';
    $query.= ' ('.$picture['current']['id'].','.$user['id'].')';
    $query.= ';';
    $result = pwg_query( $query );
  }
  if ( !$_GET['add_fav'] and $page['cat'] == 'fav' )
  {
    if (!$has_prev and !$has_next)
    {
      // there is no favorite picture anymore we redirect the user to the
      // category page
      $url = add_session_id($url_up);
      redirect($url);
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
    $query.= ' WHERE '.$conf['user_fields']['username']." = '".$author."'";
    $query.= ';';
    $row = mysql_fetch_array( pwg_query( $query ) );
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
    if ( mysql_num_rows( pwg_query( $query ) ) == 0
         or $conf['anti-flood_time'] == 0 )
    {
      list($dbnow) = mysql_fetch_row(pwg_query('SELECT NOW();'));

      $data = array();
      $data{'author'} = $author;
      $data{'date'} = $dbnow;
      $data{'image_id'} = $_GET['image_id'];
      $data{'content'} = htmlspecialchars( $_POST['content'], ENT_QUOTES);
      
      if (!$conf['comments_validation'] or $user['status'] == 'admin')
      {
        $data{'validated'} = 'true';
        $data{'validation_date'} = $dbnow;
      }
      else
      {
        $data{'validated'} = 'false';
      }
      
      include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
      $fields = array('author', 'date', 'image_id', 'content', 'validated',
                      'validation_date');
      mass_inserts(COMMENTS_TABLE, $fields, array($data));
      
      // information message
      $message = $lang['comment_added'];

      if (!$conf['comments_validation'] or $user['status'] == 'admin')
      
      if ( $conf['comments_validation'] and $user['status'] != 'admin' )
      {
        $message.= '<br />'.$lang['comment_to_validate'];
      }
      $template->assign_block_vars('information',
                                   array('INFORMATION'=>$message));
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
  pwg_query( $query );
}

//
// Start output of page
//

$title =  $picture['current']['name'];
$refresh = 0;
if ( isset( $_GET['slideshow'] ) and $has_next )
{
  $refresh= $_GET['slideshow'];
  $url_link = $picture['next']['url'].'&amp;slideshow='.$refresh;
}

$title_img = $picture['current']['name'];
$title_nb = '';
if (is_numeric( $page['cat'] )) 
{
  $title_img = replace_space(get_cat_display_name($page['cat_name']));
  $n = $page['num'] + 1;
  $title_nb = $n.'/'.$page['cat_nb_images'];
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

$picture_size = get_picture_size($original_width, $original_height,
                                 @$user['maxwidth'], @$user['maxheight']);

// metadata
if ($conf['show_exif'] or $conf['show_iptc'])
{
  $metadata_showable = true;
}
else
{
  $metadata_showable = false;
}

$url_metadata = PHPWG_ROOT_PATH.'picture.php';
$url_metadata .=  get_query_string_diff(array('add_fav', 'slideshow', 'show_metadata'));
if ($metadata_showable and !isset($_GET['show_metadata']))
{
  $url_metadata.= '&amp;show_metadata=1';
}

$page['body_id'] = 'thePicturePage';
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

  'LEVEL_SEPARATOR' => $conf['level_separator'],

  'L_HOME' => $lang['home'],
  'L_SLIDESHOW' => $lang['slideshow'],
  'L_STOP_SLIDESHOW' => $lang['slideshow_stop'],
  'L_PREV_IMG' =>$lang['previous_page'].' : ',
  'L_NEXT_IMG' =>$lang['next_page'].' : ',
  'L_ADMIN' =>$lang['link_info_image'],
  'L_COMMENT_TITLE' =>$lang['comments_title'],
  'L_ADD_COMMENT' =>$lang['comments_add'],
  'L_DELETE_COMMENT' =>$lang['comments_del'],
  'L_DELETE' =>$lang['delete'],
  'L_SUBMIT' =>$lang['submit'],
  'L_AUTHOR' =>  $lang['upload_author'],
  'L_COMMENT' =>$lang['comment'],
  'L_DOWNLOAD' => $lang['download'],
  'L_DOWNLOAD_HINT' => $lang['download_hint'],
  'L_PICTURE_METADATA' => $lang['picture_show_metadata'],
  'L_PICTURE_HIGH' => $lang['picture_high'],
  'L_UP_HINT' => $lang['home_hint'],
  'L_UP_ALT' => $lang['home'],
  
  'U_HOME' => add_session_id(PHPWG_ROOT_PATH.'category.php'),
  'U_UP' => add_session_id($url_up),
  'U_METADATA' => add_session_id($url_metadata),
  'U_ADMIN' => add_session_id($url_admin),
  'U_SLIDESHOW'=> add_session_id($url_slide),
  'U_ADD_COMMENT' => add_session_id(str_replace( '&', '&amp;', $_SERVER['REQUEST_URI'] ))
  )
);

if ($conf['show_picture_name_on_title'])
{
  $template->assign_block_vars('title', array());
}

//------------------------------------------------------- upper menu management
// download link if file is not a picture
if (!$picture['current']['is_picture'])
{
  $template->assign_block_vars(
    'download',
    array('U_DOWNLOAD' => $picture['current']['download']));
}
else
{
  $template->assign_block_vars(
    'ecard',
    array('U_ECARD' => $picture['current']['url']));
}
// display a high quality link if present
if (isset($picture['current']['high']))
{
  $full_size = @getimagesize($picture['current']['high']);
  $full_width = $full_size[0];
  $full_height = $full_size[1];
  $uuid = uniqid(rand());
  $template->assign_block_vars('high', array(
    'U_HIGH' => $picture['current']['high'],
	'UUID'=>$uuid,
	'WIDTH_IMG'=>($full_width + 40),
	'HEIGHT_IMG'=>($full_height + 40)
	));
  $template->assign_block_vars(
    'download',
    array('U_DOWNLOAD' => PHPWG_ROOT_PATH.'action.php?dwn='
          .$picture['current']['high']
    )
  );
}
// button to set the current picture as representative
if ('admin' == $user['status'] and is_numeric($page['cat']))
{
  $template->assign_block_vars(
    'representative',
    array(
      'URL' =>
        PHPWG_ROOT_PATH.'picture.php'
        .get_query_string_diff(array())
        .'&amp;representative=1'
      )
    );
}

if ('admin' == $user['status'])
{
  $template->assign_block_vars(
    'caddie',
    array(
      'URL' =>
      add_session_id(
        PHPWG_ROOT_PATH.'picture.php'
        .get_query_string_diff(array('caddie')).'&amp;caddie=1')
      )
    );
}

//------------------------------------------------------- favorite manipulation
if ( !$user['is_the_guest'] )
{
  // verify if the picture is already in the favorite of the user
  $query = 'SELECT COUNT(*) AS nb_fav';
  $query.= ' FROM '.FAVORITES_TABLE.' WHERE image_id = '.$_GET['image_id'];
  $query.= ' AND user_id = '.$user['id'].';';
  $result = pwg_query( $query );
  $row = mysql_fetch_array( $result );
  if (!$row['nb_fav'])
  {
    $url = PHPWG_ROOT_PATH.'picture.php';
    $url.= get_query_string_diff(array('rate','add_fav'));
    $url.= '&amp;add_fav=1';

    $template->assign_block_vars(
      'favorite',
      array(
        'FAVORITE_IMG' => get_themeconf('icon_dir').'/favorite.png',
        'FAVORITE_HINT' =>$lang['add_favorites_hint'],
        'FAVORITE_ALT' =>$lang['add_favorites_alt'],
        'U_FAVORITE' => $url
        ));
  }
  else
  {
    $url = PHPWG_ROOT_PATH.'picture.php';
    $url.= get_query_string_diff(array('rate','add_fav'));
    $url.= '&amp;add_fav=0';
    
    $template->assign_block_vars(
      'favorite',
      array(
        'FAVORITE_IMG' => get_themeconf('icon_dir').'/del_favorite.png',
        'FAVORITE_HINT' =>$lang['del_favorites_hint'],
        'FAVORITE_ALT' =>$lang['del_favorites_alt'],
        'U_FAVORITE'=> $url
        ));
  }
}
//------------------------------------ admin link for information modifications
if ( $user['status'] == 'admin' )
{
  $template->assign_block_vars('admin', array());
}

//-------------------------------------------------------- navigation management
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
      'COMMENT_IMG' => nl2br($picture['current']['comment'])
      ));
}

$infos = array();

// author
if (!empty($picture['current']['author']))
{
  $infos['INFO_AUTHOR'] =
    '<a href="'.
    add_session_id(
      PHPWG_ROOT_PATH.'category.php?cat=search'.
      '&amp;search=author:'.$picture['current']['author']
      ).
    '">'.$picture['current']['author'].'</a>';
}
else
{
  $infos['INFO_AUTHOR'] = l10n('N/A');
}

// creation date
if (!empty($picture['current']['date_creation']))
{
  $infos['INFO_CREATION_DATE'] =
    '<a href="'.
    add_session_id(
      PHPWG_ROOT_PATH.'category.php?cat=search'.
      '&amp;search=date_creation:'.$picture['current']['date_creation']
      ).
    '">'.format_date($picture['current']['date_creation']).'</a>';
}
else
{
  $infos['INFO_CREATION_DATE'] = l10n('N/A');
}

// date of availability
$infos['INFO_AVAILABILITY_DATE'] =
  '<a href="'.
  add_session_id(
    PHPWG_ROOT_PATH.'category.php?cat=search'.
    '&amp;search=date_available:'.
    substr($picture['current']['date_available'], 0, 10)
    ).
    '">'.
  format_date($picture['current']['date_available'], 'mysql_datetime').
  '</a>';

// size in pixels
if ($picture['current']['is_picture'])
{
  if ($original_width != $picture_size[0]
      or $original_height != $picture_size[1])
  {
    $infos['INFO_DIMENSIONS'] =
      '<a href="'.$picture['current']['src'].'" title="'.
      l10n('Original dimensions').'">'.
      $original_width.'*'.$original_height.'</a>';
  }
  else
  {
    $infos['INFO_DIMENSIONS'] = $original_width.'*'.$original_height;
  }
}
else
{
  $infos['INFO_DIMENSIONS'] = l10n('N/A');
}

// filesize
if (!empty($picture['current']['filesize']))
{
  $infos['INFO_FILESIZE'] =
    sprintf(l10n('%d Kb'), $picture['current']['filesize']);
}
else
{
  $infos['INFO_FILESIZE'] = l10n('N/A');
}

// number of visits
$infos['INFO_VISITS'] = $picture['current']['hit'];

// file
$infos['INFO_FILE'] = $picture['current']['file'];

// keywords
if (!empty($picture['current']['keywords']))
{
  $infos['INFO_KEYWORDS'] =
    preg_replace(
      '/([^,]+)/',
      '<a href="'.
      add_session_id(
        PHPWG_ROOT_PATH.'category.php?cat=search&amp;search=keywords:$1'
        ).
      '">$1</a>',
      $picture['current']['keywords']
      );
}
else
{
  $infos['INFO_KEYWORDS'] = l10n('N/A');
}

$template->assign_vars($infos);

// related categories
foreach ($related_categories as $category)
{
  $template->assign_block_vars(
    'category',
    array(
      'LINE' => count($related_categories) > 3
        ? get_cat_display_name_cache($category['uppercats'])
        : get_cat_display_name_from_id($category['category_id'])
      )
    );
}

//-------------------------------------------------------------------  metadata
if ($metadata_showable and isset($_GET['show_metadata']))
{
  include_once(PHPWG_ROOT_PATH.'/include/functions_metadata.inc.php');
  $template->assign_block_vars('metadata', array());
  if ($conf['show_exif'])
  {
    if (!function_exists('read_exif_data'))
    {
      die('Exif extension not available, admin should disable exif display');
    }
    
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
//slideshow end
if ( isset( $_GET['slideshow'] ) )
{
  if ( !is_numeric( $_GET['slideshow'] ) ) $_GET['slideshow'] = $conf['slideshow_period'];
	
  $template->assign_block_vars('stop_slideshow', array(
  'U_SLIDESHOW'=>add_session_id( $picture['current']['url'] )
  ));
}

//------------------------------------------------------------------- rating
if ($conf['rate'])
{
  $query = '
SELECT COUNT(rate) AS count
     , ROUND(AVG(rate),2) AS average
     , ROUND(STD(rate),2) AS STD
  FROM '.RATE_TABLE.'
  WHERE element_id = '.$picture['current']['id'].'
;';
  $row = mysql_fetch_array(pwg_query($query));
  if ($row['count'] == 0)
  {
    $value = $lang['no_rate'];
  }
  else
  {
    $value = sprintf(
      l10n('%.2f (rated %d times, standard deviation = %.2f)'),
      $row['average'],
      $row['count'],
      $row['STD']
      );
  }
  
  if (!$user['is_the_guest'])
  {
    $query = 'SELECT rate
    FROM '.RATE_TABLE.'
    WHERE user_id = '.$user['id'].'
    AND element_id = '.$_GET['image_id'].';';
  $result = pwg_query($query);
  if (mysql_num_rows($result) > 0)
  {
    $row = mysql_fetch_array($result);
    $sentence = $lang['already_rated'];
    $sentence.= ' ('.$row['rate'].'). ';
    $sentence.= $lang['update_rate'];
  }
  else
  {
    $sentence = $lang['never_rated'].'. '.$lang['to_rate'];
  }  
  $template->assign_block_vars(
    'rate',
    array(
      'CONTENT' => $value,
      'SENTENCE' => $sentence
      ));

  $template->assign_block_vars('info_rate', array('CONTENT' => $value));
  
  $template->assign_vars(
    array(
      'INFO_RATE' => $value
      )
    );
  
  foreach ($rate_items as $num => $mark)
  {
    if ($num > 0)
    {
      $separator = '|';
    }
    else
    {
      $separator = '';
    }

    $url = PHPWG_ROOT_PATH.'picture.php';
    $url.= get_query_string_diff(array('rate','add_fav'));
    $url.= '&amp;rate='.$mark;
    
    $template->assign_block_vars(
      'rate.rate_option',
      array(
        'OPTION' => $mark,
        'URL' => $url,
        'SEPARATOR' => $separator
        ));
    }
  }
}

//---------------------------------------------------- users's comments display

// the picture is commentable if it belongs at least to one category which
// is commentable
$page['show_comments'] = false;
foreach ($related_categories as $category)
{
  if ($category['commentable'] == 'true')
  {
    $page['show_comments'] = true;
  }
}

if ($page['show_comments'])
{
  // number of comment for this picture
  $query = 'SELECT COUNT(*) AS nb_comments';
  $query.= ' FROM '.COMMENTS_TABLE.' WHERE image_id = '.$_GET['image_id'];
  $query.= " AND validated = 'true'";
  $query.= ';';
  $row = mysql_fetch_array( pwg_query( $query ) );
  
  // navigation bar creation
  $url = PHPWG_ROOT_PATH.'picture.php';
  $url.= get_query_string_diff(array('rate','add_fav'));

  if (!isset( $_GET['start'] )
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
  $result = pwg_query( $query );
                
  while ( $row = mysql_fetch_array( $result ) )
  {
    $template->assign_block_vars(
      'comments.comment',
      array(
        'COMMENT_AUTHOR'=>empty($row['author'])?$lang['guest']:$row['author'],
        'COMMENT_DATE'=>format_date($row['date'], 'mysql_datetime', true),
	'COMMENT'=>parse_comment_content($row['content'])
	));
	
    if ( $user['status'] == 'admin' )
    {
      $template->assign_block_vars(
        'comments.comment.delete',
        array('U_COMMENT_DELETE'=>add_session_id( $url.'&amp;del='.$row['id'])
          ));
    }
  }

  if (!$user['is_the_guest']
      or ($user['is_the_guest'] and $conf['comments_forall']))
  {
    $template->assign_block_vars('comments.add_comment', array());
    // display author field if the user is not logged in
    if (!$user['is_the_guest'])
    {
      $template->assign_block_vars(
        'comments.add_comment.author_known',
        array('KNOWN_AUTHOR'=>$user['username'])
        );
    }
    else
    {
      $template->assign_block_vars(
        'comments.add_comment.author_field', array()
        );
    }
  }
}
//------------------------------------------------------------ log informations
pwg_log( 'picture', $title_img, $picture['current']['file'] );

$template->parse('picture');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
