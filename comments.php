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
if (!defined('IN_ADMIN'))
{
  define('PHPWG_ROOT_PATH','./');
  include_once(PHPWG_ROOT_PATH.'include/common.inc.php');
}

if (isset($_GET['last_days']))
{
  define('MAX_DAYS', $_GET['last_days']);
}
else
{
  define('MAX_DAYS', 0);
}
$array_cat_names = array();
// +-----------------------------------------------------------------------+
// |                         comments management                           |
// +-----------------------------------------------------------------------+
// comments deletion
if (isset($_POST['delete']) and count($_POST['comment_id']) > 0)
{
  $query = '
DELETE FROM '.COMMENTS_TABLE.'
  WHERE id IN ('.implode(',', $_POST['comment_id']).')
;';
  pwg_query($query);
}
// comments validation
if (isset($_POST['validate']) and count($_POST['comment_id']) > 0)
{
  $query = '
UPDATE '.COMMENTS_TABLE.'
  SET validated = \'true\'
  WHERE id IN ('.implode(',', $_POST['comment_id']).')
;';
  pwg_query($query);
}
// +-----------------------------------------------------------------------+
// |                       page header and options                         |
// +-----------------------------------------------------------------------+
if (!defined('IN_ADMIN'))
{
  $title= $lang['title_comments'];
  include(PHPWG_ROOT_PATH.'include/page_header.php');
}

$template->set_filenames(array('comments'=>'comments.tpl'));
$template->assign_vars(
  array(
    'L_COMMENT_TITLE' => $title,
    'L_COMMENT_STATS' => $lang['stats_last_days'],
    'L_COMMENT_RETURN' => $lang['home'],
	'L_COMMENT_RETURN_HINT' => $lang['home_hint'],
    'L_DELETE' =>$lang['delete'],
    'L_VALIDATE'=>$lang['submit'],
    
    'U_HOME' => add_session_id(PHPWG_ROOT_PATH.'category.php')
    )
  );

foreach ($conf['last_days'] as $option)
{
  $url = $_SERVER['PHP_SELF'].'?last_days='.($option - 1);
  if (defined('IN_ADMIN'))
  {
    $url.= '&amp;page=comments';
  }
  $template->assign_block_vars(
    'last_day_option',
    array(
      'OPTION'=>$option,
      'T_STYLE'=>(($option == MAX_DAYS + 1)?'text-decoration:underline;':''),
      'U_OPTION'=>add_session_id($url)
      )
    );
}
// +-----------------------------------------------------------------------+
// |                        last comments display                          |
// +-----------------------------------------------------------------------+
// 1. retrieving picture ids which have comments recently added
$maxdate = date('Y-m-d', strtotime('-'.MAX_DAYS.' day'));

$query = '
SELECT DISTINCT(ic.image_id) AS image_id,ic.category_id, uppercats
  FROM '.COMMENTS_TABLE.' AS c, '.IMAGE_CATEGORY_TABLE.' AS ic
    , '.CATEGORIES_TABLE.' AS cat
  WHERE c.image_id = ic.image_id
    AND ic.category_id = cat.id
    AND date >= \''.$maxdate.'\'';
if ($user['status'] != 'admin')
{
  $query.= "
    AND validated = 'true'";
  // we must not show pictures of a forbidden category
  if ($user['forbidden_categories'] != '')
  {
    $query.= '
    AND category_id NOT IN ('.$user['forbidden_categories'].')';
  }
}
$query.= '
  GROUP BY ic.image_id
  ORDER BY ic.image_id DESC
;';
$result = pwg_query($query);
if ($user['status'] == 'admin')
{
  $template->assign_block_vars('validation', array());
}
while ($row = mysql_fetch_array($result))
{
  $category_id = $row['category_id'];
  
  // for each picture, getting informations for displaying thumbnail and
  // link to the full size picture
  $query = '
SELECT name,file,storage_category_id as cat_id,tn_ext,path
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$row['image_id'].'
;';
  $subresult = pwg_query($query);
  $subrow = mysql_fetch_array($subresult);

  // name of the picture
  $name = get_cat_display_name_cache($row['uppercats'], '', false);
  $name.= $conf['level_separator'];
  if (!empty($subrow['name']))
  {
    $name.= $subrow['name'];
  }
  else
  {
    $name.= str_replace('_',' ',get_filename_wo_extension($subrow['file']));
  }

  // source of the thumbnail picture
  $thumbnail_src = get_thumbnail_src($subrow['path'], @$subrow['tn_ext']);
  // link to the full size picture
  $url = PHPWG_ROOT_PATH.'picture.php?cat='.$category_id;
  $url.= '&amp;image_id='.$row['image_id'];
    
  $template->assign_block_vars(
    'picture',
    array(
      'TITLE_IMG'=>$name,
      'I_THUMB'=>$thumbnail_src,
      'U_THUMB'=>add_session_id($url)
      ));
    
  // for each picture, retrieving all comments
  $query = '
SELECT *
  FROM '.COMMENTS_TABLE.'
  WHERE image_id = '.$row['image_id'].'
    AND date >= \''.$maxdate.'\'';
  if ($user['status'] != 'admin')
  {
    $query.= '
    AND validated = \'true\'';
  }
  $query.= '
  ORDER BY date DESC
;';
  $handleresult = pwg_query($query);
  while ($subrow = mysql_fetch_array($handleresult))
  {
    $author = $subrow['author'];
    if (empty($subrow['author']))
    {
      $author = $lang['guest'];
    }

    $template->assign_block_vars(
      'picture.comment',
      array(
        'COMMENT_AUTHOR'=>$author,
        'COMMENT_DATE'=>format_date($subrow['date'],'mysql_datetime',true),
        'COMMENT'=>parse_comment_content($subrow['content']),
        ));
    
    if ($user['status'] == 'admin')
    {
      $template->assign_block_vars(
        'picture.comment.validation',
        array(
          'ID'=> $subrow['id'],
          'CHECKED'=>($subrow['validated']=='false')?'checked="checked"': ''
          ));
    }
  }
}
// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+
if (defined('IN_ADMIN'))
{
  $template->assign_var_from_handle('ADMIN_CONTENT', 'comments');
}
else
{
  $template->assign_block_vars('title',array());
  $template->parse('comments');
  include(PHPWG_ROOT_PATH.'include/page_tail.php');
}
?>
