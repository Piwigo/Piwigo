<?php /*
Plugin Name: Multi view
Version: 1.0
Description: Allows administrators to view gallery as guests and/or change the language and/or theme on the fly. Practical to debug changes ...
Plugin URI: http://www.phpwebgallery.net
Author: PhpWebGallery team
Author URI: http://www.phpwebgallery.net
*/

add_event_handler('user_init', 'multiview_user_init' );

function multiview_user_init()
{
  if (!is_admin())
    return;
  include_once( dirname(__FILE__).'/is_admin.inc.php' );
}

?>
