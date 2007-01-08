<?php /*
Plugin Name: Admin Advices !
Version: 1.0.0
Author: PhpWebGallery team
Description: Give you an advice on the administration page.
*/

add_event_handler('loc_begin_page_tail', 'set_admin_advice' );


function set_admin_advice()
{
  global $page, $user, $template, $conf;  

  // This Plugin works only on the Admin page
  if ( isset($page['body_id']) and $page['body_id']=='theAdminPage'
    and isset($page['page']['name']) and $page['page']['name'] == 'intro' 
    and isset($page['page']['type']) and $page['page']['type'] == 'standard'
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
//  $template->set_filenames( array( 'advice' => 'admin_advices.tpl' ));    
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
        $template->assign_block_vars(
          'thumbnail',
           array(
             'IMAGE'              => get_thumbnail_url($row),
             'IMAGE_ALT'          => $row['file'],
             'IMAGE_TITLE'        => $row['name'],
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
