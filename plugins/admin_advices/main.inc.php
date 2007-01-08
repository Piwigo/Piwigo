<?php /*
Plugin Name: Admin Advices !
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
