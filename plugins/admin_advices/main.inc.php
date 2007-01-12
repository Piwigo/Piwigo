<?php /*
Plugin Name: Admin Advices !
Version: 1.0.0
Author: PhpWebGallery team
Description: Give you an advice on the administration page.
*/
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $URL$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Rev$
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

add_event_handler('loc_begin_page_tail', 'set_admin_advice' );
add_event_handler('loc_end_page_header', 'set_admin_advice_add_css' );

// Add a XHTML tag in HEAD section
function set_admin_advice_add_css()
{
  global $template;
        $template->assign_block_vars(
          'head_element',
           array(
             'CONTENT' => '<link rel="stylesheet" type="text/css" ' 
                        . 'href="plugins/admin_advices/default-layout.css" />',     
           )
         );
}

// Build an advice on the Admin Intro page
function set_admin_advice()
{
  global $page, $user, $template, $conf;


  // This Plugin works only on the Admin page
  if ( isset($page['body_id']) and $page['body_id']=='theAdminPage'
    and $page['page'] == 'intro'
    )
  {
  // Setup Advice Language (Maybe there is already a variable)
    $advlang = ( isset($user['language']) ) ?
      $user['language'] : $conf['default_language']; // en_UK.iso-8859-1

    $adv = array();

//  Include language advices
    include_once( PHPWG_ROOT_PATH
      . "plugins/admin_advices/$advlang/lang.adv.php" );

//  If there is an advice
    if ( $cond )
    {
      $template->set_filenames(array(
        'admin_advice' =>
          PHPWG_ROOT_PATH.'/plugins/admin_advices/admin_advices.tpl')
        );

// Random Thumbnail
      $query = '
SELECT *
  FROM '.IMAGES_TABLE.' 
  ORDER BY RAND(NOW())
  LIMIT 0, 1
;';
      $result = pwg_query($query);
      $row = mysql_fetch_assoc($result);
      if ( is_array($row) )
      {
        $url_modify = get_root_url().'admin.php?page=picture_modify'
                    .'&amp;image_id='.$row['id'];
        $url_check = get_themeconf('icon_dir').'/';
        $url_uncheck = $url_check . 'uncheck';
        $url_check .= 'check'; 
        $picture_id = $row['id']; 
        $query = '
SELECT * FROM '.IMAGE_TAG_TABLE.' 
WHERE image_id =  ' . $picture_id .'
;';
        $tag_count = mysql_num_rows(mysql_query($query)); 
        $template->assign_block_vars(
          'thumbnail',
           array(
             'IMAGE'              => get_thumbnail_url($row),
             'IMAGE_ALT'          => $row['file'],
             'IMAGE_TITLE'        => $row['name'],
             'METADATA'           => (empty($row['date_metadata_update'])) ?
                                     $url_uncheck : $url_check,
             'NAME'               => (empty($row['name'])) ?
                                     $url_uncheck : $url_check,
             'COMMENT'            => (empty($row['comment'])) ?
                                     $url_uncheck : $url_check,
             'AUTHOR'             => (empty($row['author'])) ?
                                     $url_uncheck : $url_check,
             'CREATE_DATE'        => (empty($row['date_creation'])) ?
                                     $url_uncheck : $url_check,
             'TAGS'               => ($tag_count == 0) ?
                                     $url_uncheck : $url_check,
             'NUM_TAGS'           => (string) $tag_count,
             'U_MODIFY'           => $url_modify,     
           )
         );
      }
      $advice_text = array_shift($adv);
      $template->assign_vars(
        array(
          'ADVICE_ABOUT' => '$conf[' . "'$confk'] ",
          'ADVICE_TEXT'  => $advice_text,
           )
        );
      foreach ($adv as $advice)
      {
          $template->assign_block_vars(
            'More',
            array(
              'ADVICE' => $advice
              )
            );
      }
    $template->parse('admin_advice');
    }
  }
}
?>
