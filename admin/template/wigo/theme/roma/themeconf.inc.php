<?php
/**
 * Accordion menus need to be stable
 */
function selected_admin_menu()
{
  if (isset($_GET['page']))
  {
    switch ($_GET['page']) {
      case 'configuration':
      case 'extend_for_templates':
      case 'menubar':
        return 1;
      case 'site_manager':
      case 'site_update':
      case 'cat_list':
      case 'cat_modify':
      case 'cat_move':
      case 'cat_options':
      case 'element_set':
      case 'cat_perm':
      case 'permalinks':
      case 'picture_modify':
        if (isset($_GET['cat']) and $_GET['cat']=='caddie') {
          return 3;
        }
        return 2;
      case 'comments':
      case 'upload':
      case 'thumbnail':
      case 'rating':
      case 'tags':
        return 3;
      case 'user_list':
      case 'group_list':
      case 'notification_by_mail':
        return 4;
      case 'stats':
      case 'history':
      case 'maintenance':
      case 'advanced_feature':
      case 'plugins_list':
      case 'plugin':
        return 5;
    }
  }
  return 0;
}

$themeconf = array(
  'template' => 'wigo',
  'theme' => 'roma',
  'icon_dir' => 'template/yoga/icon',
  'admin_icon_dir' => 'template/yoga/icon/admin',
  'mime_icon_dir' => 'template/yoga/icon/mimetypes/',
  'selected_admin_menu' => selected_admin_menu(),
  'local_head' => '',
);
?>
