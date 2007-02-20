<?php /*
Plugin Name: Admin Advices !
Version: 1.0.0
Author: PhpWebGallery team
Description: Give you an advice on the administration page.
Plugin URI: http://www.phpwebgallery.net
*/

add_event_handler('loc_end_page_header', 'set_admin_advice_add_css' );

// Add a XHTML tag in HEAD section
function set_admin_advice_add_css()
{
  global $template, $page;
  if ( isset($page['body_id']) and $page['body_id']=='theAdminPage'
    and $page['page'] == 'intro'
    )
  {// This Plugin works only on the Admin page
    $template->assign_block_vars(
      'head_element',
       array(
         'CONTENT' => '<link rel="stylesheet" type="text/css" ' 
                    . 'href="'.PHPWG_PLUGINS_PATH.'admin_advices/default-layout.css">',
       )
     );
    add_event_handler('loc_begin_page_tail', 'set_admin_advice' );
  }
}

// Build an advice on the Admin Intro page
function set_admin_advice()
{
  global $page, $user, $template, $conf;

// Setup Advice Language (Maybe there is already a variable)
  $advlang = ( isset($user['language']) ) ?
    $user['language'] : $conf['default_language']; // en_UK.iso-8859-1
  $my_path = dirname(__FILE__).'/';
  $adv = array();
  if ( !@file_exists($my_path."$advlang/lang.adv.php") )
  {
    $advlang = 'en_UK.iso-8859-1';
  }
//  Include language advices
  @include_once( $my_path."$advlang/lang.adv.php" );

//  If there is an advice
  if ( $cond )
  {
    $template->set_filenames(array(
      'admin_advice' => $my_path.'admin_advices.tpl')
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
?>
