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

if(!defined("PHPWG_ROOT_PATH"))
{
  die ("Hacking attempt!");
}
include_once(PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php');
// +-----------------------------------------------------------------------+
// |                             initialization                            |
// +-----------------------------------------------------------------------+
check_cat_id($_GET['cat_id']);
$errors = array();

if (isset($page['cat']))
{
// +-----------------------------------------------------------------------+
// |                       update individual options                       |
// +-----------------------------------------------------------------------+
  if (isset($_POST['submit']))
  {
    if (isset($_POST['associate']) and $_POST['associate'] != '')
    {
      // does the uppercat id exists in the database ?
      if (!is_numeric($_POST['associate']))
      {
        array_push($errors, $lang['cat_unknown_id']);
      }
      else
      {
        $query = 'SELECT id FROM '.CATEGORIES_TABLE;
        $query.= ' WHERE id = '.$_POST['associate'];
        $query.= ';';
        if (mysql_num_rows(pwg_query($query)) == 0)
          array_push($errors, $lang['cat_unknown_id']);
      }
    }

    $query = 'SELECT id,file FROM '.IMAGES_TABLE;
    $query.= ' INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON id = image_id';
    $query.= ' WHERE category_id = '.$page['cat'];
    $query.= ';';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      $name          = 'name-'.$row['id'];
      $author        = 'author-'.$row['id'];
      $comment       = 'comment-'.$row['id'];
      $date_creation = 'date_creation-'.$row['id'];
      $keywords      = 'keywords-'.$row['id'];
      if (isset($_POST[$name]))
      {
        $query = 'UPDATE '.IMAGES_TABLE.' SET name = ';
        if ($_POST[$name] == '')
          $query.= 'NULL';
        else
          $query.= "'".htmlentities($_POST[$name], ENT_QUOTES)."'";

        $query.= ', author = ';
        if ($_POST[$author] == '')
          $query.= 'NULL';
        else
          $query.= "'".htmlentities($_POST[$author],ENT_QUOTES)."'";

        $query.= ', comment = ';
        if ($_POST[$comment] == '')
          $query.= 'NULL';
        else
          $query.= "'".htmlentities($_POST[$comment],ENT_QUOTES)."'";

        $query.= ', date_creation = ';
        if (check_date_format($_POST[$date_creation]))
          $query.= "'".date_convert($_POST[$date_creation])."'";
        else if ($_POST[$date_creation] == '')
          $query.= 'NULL';

        $query.= ', keywords = ';

        $keywords_array = get_keywords($_POST[$keywords]);
        if (count($keywords_array) == 0) $query.= 'NULL';
        else $query.= "'".implode(',', $keywords_array)."'";

        $query.= ' WHERE id = '.$row['id'];
        $query.= ';';
        pwg_query($query);
      }
      // add link to another category
      if (isset($_POST['check-'.$row['id']])
          and isset($_POST['associate'])
          and $_POST['associate'] != '')
      {
        $query = 'INSERT INTO '.IMAGE_CATEGORY_TABLE;
        $query.= ' (image_id,category_id) VALUES';
        $query.= ' ('.$row['id'].','.$_POST['associate'].')';
        $query.= ';';
        pwg_query($query);
      }
    }
    if (isset($_POST['associate']) and $_POST['associate'] != '')
    {
      update_category(array($_POST['associate']));
    }
// +-----------------------------------------------------------------------+
// |                        update general options                         |
// +-----------------------------------------------------------------------+
    if (isset($_POST['use_common_author']))
    {
      $query = 'SELECT image_id FROM '.IMAGE_CATEGORY_TABLE;
      $query.= ' WHERE category_id = '.$page['cat'];
      $result = pwg_query($query);
      while ($row = mysql_fetch_array($result))
      {
        $query = 'UPDATE '.IMAGES_TABLE;
        if ($_POST['author_cat'] == '')
        {
          $query.= ' SET author = NULL';
        }
        else
        {
          $query.= ' SET author = ';
          $query.= "'".htmlentities($_POST['author_cat'], ENT_QUOTES)."'";
        }
        $query.= ' WHERE id = '.$row['image_id'];
        $query.= ';';
        pwg_query($query);
      }
    }
    if (isset($_POST['use_common_date_creation']))
    {
      if (check_date_format($_POST['date_creation_cat']))
      {
        $date = date_convert($_POST['date_creation_cat']);
        $query = 'SELECT image_id FROM '.IMAGE_CATEGORY_TABLE;
        $query.= ' WHERE category_id = '.$page['cat'];
        $result = pwg_query($query);
        while ($row = mysql_fetch_array($result))
        {
          $query = 'UPDATE '.IMAGES_TABLE;
          if ($_POST['date_creation_cat'] == '')
          {
            $query.= ' SET date_creation = NULL';
          }
          else
          {
            $query.= " SET date_creation = '".$date."'";
          }
          $query.= ' WHERE id = '.$row['image_id'];
          $query.= ';';
          pwg_query($query);
        }
      }
      else
      {
        array_push($errors, $lang['err_date']);
      }
    }
    if (isset($_POST['common_keywords']) and $_POST['keywords_cat'] != '')
    {
      $query = 'SELECT id,keywords FROM '.IMAGES_TABLE;
      $query.= ' INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON id = image_id';
      $query.= ' WHERE category_id = '.$page['cat'];
      $query.= ';';
      $result = pwg_query($query);
      while ($row = mysql_fetch_array($result))
      {
        if (!isset($row['keywords'])) $specific_keywords = array();
        else $specific_keywords = explode(',', $row['keywords']);
        
        $common_keywords   = get_keywords($_POST['keywords_cat']);
        // first possiblity : adding the given keywords to all the pictures
        if ($_POST['common_keywords'] == 'add')
        {
          $keywords = array_merge($specific_keywords, $common_keywords);
          $keywords = array_unique($keywords);
        }
        // second possiblity : removing the given keywords from all pictures
        // (without deleting the other specific keywords
        if ($_POST['common_keywords'] == 'remove')
        {
          $keywords = array_diff($specific_keywords, $common_keywords);
        }
        // cleaning the keywords array, sometimes, an empty value still remain
        $keywords = array_remove($keywords, '');
        // updating the picture with new keywords array
        $query = 'UPDATE '.IMAGES_TABLE.' SET keywords = ';
        if (count($keywords) == 0)
        {
          $query.= 'NULL';
        }
        else
        {
          $query.= '"';
          $i = 0;
          foreach ($keywords as $keyword) {
            if ($i++ > 0) $query.= ',';
            $query.= $keyword;
          }
          $query.= '"';
        }
        $query.= ' WHERE id = '.$row['id'];
        $query.= ';';
        pwg_query($query);
      }
    }
  }
// +-----------------------------------------------------------------------+
// |                           form initialization                         |
// +-----------------------------------------------------------------------+
  if (!isset($_GET['start'])
      or !is_numeric($_GET['start'])
      or (is_numeric($_GET['start']) and $_GET['start'] < 0))
  {
    $page['start'] = 0;
  }
  else
  {
    $page['start'] = $_GET['start'];
  }

  if (isset($_GET['num']) and is_numeric($_GET['num']) and $_GET['num'] >= 0)
  {
    $max = $conf['info_nb_elements_page'];
    $page['start'] = floor($_GET['num'] / $max) * $max;
  }
  // Navigation path
  $current_category = get_cat_info($_GET['cat_id']);
  $url = PHPWG_ROOT_PATH.'admin.php?page=infos_images&amp;cat_id=';
  $category_path = get_cat_display_name($current_category['name'], $url);
  
  $form_action = PHPWG_ROOT_PATH.'admin.php';
  $form_action.= '?page=infos_images&amp;cat_id='.$_GET['cat_id'];
  if($page['start'])
  {
    $form_action.= '&amp;start='.$_GET['start'];
  }
  
  $nav_bar = create_navigation_bar($form_action,
                                   $current_category['nb_images'],
                                   $page['start'],
                                   $conf['info_nb_elements_page'],
                                   '');
// +-----------------------------------------------------------------------+
// |                         template initialization                       |
// +-----------------------------------------------------------------------+
  $template->set_filenames(array('infos_images'=>'admin/infos_images.tpl'));
  $template->assign_vars(
    array(
      'CATEGORY'=>$category_path,
      'NAV_BAR'=>$nav_bar,
      
      'L_INFOS_TITLE'=>$lang['infoimage_general'],
      'L_AUTHOR'=>$lang['author'],
      'L_INFOS_OVERALL_USE'=>$lang['infoimage_useforall'],
      'L_INFOS_CREATION_DATE'=>$lang['infoimage_creation_date'],
      'L_KEYWORD'=>$lang['keywords'],
      'L_KEYWORD_SEPARATION'=>$lang['infoimage_keyword_separation'],
      'L_INFOS_ADDTOALL'=>$lang['infoimage_addtoall'],
      'L_INFOS_REMOVEFROMALL'=>$lang['infoimage_removefromall'],
      'L_INFOS_DETAIL'=>$lang['infoimage_detailed'],
      'L_THUMBNAIL'=>$lang['thumbnail'],
      'L_INFOS_IMG'=>$lang['infoimage_title'],
      'L_INFOS_COMMENT'=>$lang['comment'],
      'L_INFOS_ASSOCIATE'=>$lang['infoimage_associate'],
      'L_SUBMIT'=>$lang['submit'],
      
      'F_ACTION'=>add_session_id($form_action)
      ));
// +-----------------------------------------------------------------------+
// |                            errors display                             |
// +-----------------------------------------------------------------------+
  if (count($errors) != 0)
  {
    $template->assign_block_vars('errors',array());
    foreach ($errors as $error)
    {
      $template->assign_block_vars('errors.error',array('ERROR'=>$error));
    }
  }
// +-----------------------------------------------------------------------+
// |                                 form                                  |
// +-----------------------------------------------------------------------+
  $array_cat_directories = array();

  $pic_mod_base_url = PHPWG_ROOT_PATH.'admin.php';
  $pic_mod_base_url = '?page=picture_modify&amp;image_id=';
  
  $query = '
SELECT *
  FROM '.IMAGES_TABLE.' INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON id = image_id
  WHERE category_id = '.$page['cat'].'
  '.$conf['order_by'].'
  LIMIT '.$page['start'].','.$conf['info_nb_elements_page'].'
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $thumbnail_url = get_thumbnail_src($row['path'], @$row['tn_ext']);

    $template->assign_block_vars(
      'picture',
      array(
        'ID_IMG'=>$row['id'],
        'URL_IMG'=>add_session_id($pic_mod_base_url.$row['id']),
        'TN_URL_IMG'=>$thumbnail_url,
        'FILENAME_IMG'=>$row['file'],
        'DEFAULTNAME_IMG'=>get_filename_wo_extension($row['file']),
        'NAME_IMG'=>@$row['name'],
        'DATE_IMG'=>date_convert_back(@$row['date_creation']),
        'AUTHOR_IMG'=>@$row['author'],
        'KEYWORDS_IMG'=>@$row['keywords'],
        'COMMENT_IMG'=>@$row['comment']
       ));
  }
  
  // Virtualy associate a picture to a category
  $query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
;';
  display_select_cat_wrapper($query,
                             array(),
                             'associate_option',
                             true);
}
//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'infos_images');
?>
