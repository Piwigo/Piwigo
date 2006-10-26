<?php /*
Plugin Name: Hello World !
Author: PhpWebGallery team
Description: This example plugin changes the page banner for the administration page
*/

add_event_handler('page_banner', 'hello_world_banner' );

function hello_world_banner($banner)
{
  global $page;
  if ( isset($page['body_id']) and $page['body_id']=='theAdminPage')
  {
    return '<h1>Hello world from PhpWebGallery plugin!</h1>';
  }
  return $banner;
}
?>
