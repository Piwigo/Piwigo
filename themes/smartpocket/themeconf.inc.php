<?php
/*
Theme Name: Smart Pocket
Version: 2.4.0
Description: Mobile theme.
Theme URI: http://piwigo.org/ext/extension_view.php?eid=599
Author: P@t
Author URI: http://piwigo.org
*/

$themeconf = array(
  'mobile' => true,
);

//Retrive all pictures on thumbnails page
add_event_handler('loc_index_thumbnails_selection', 'sp_select_all_thumbnails');

function sp_select_all_thumbnails($selection)
{
  global $page, $template;

  $template->assign('page_selection', array_flip($selection));

  return $page['items'];
}

?>
