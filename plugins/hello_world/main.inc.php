<?php /*
Plugin Name: Hello World
Version: 1.8
Description: This example plugin changes the page banner for the administration page.
Plugin URI: http://www.phpwebgallery.net
Author: PhpWebGallery team
Author URI: http://www.phpwebgallery.net
*/

add_event_handler('loc_begin_page_header', 'hello_world_begin_header' );

function hello_world_begin_header()
{
  global $page;
  if ( isset($page['body_id']) and $page['body_id']=='theAdminPage')
  {
    $hellos = array( 'Aloha', 'Ahoy', 'Guten tag', 'Hello', 'Hoi', 'Hola', 'Salut', 'Yo' );
    shuffle($hellos);
    $page['page_banner'] = $hellos[0];
    // just as an example we modify it a little bit later
    add_event_handler('loc_end_page_header', 'hello_world_end_header');
  }
}


function hello_world_end_header()
{
  global $template, $page;
  $template->assign( 'PAGE_BANNER',
    '<h1>"'.$page['page_banner'].'" from PhpWebGallery plugin!</h1>');
}

?>