<?php
/**
 * Accordion menus need to be stable (called by themeconf.inc which need it)
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
      case 'cat_perm':
      case 'permalinks':
        return 2;
      case 'element_set':
        if (isset($_GET['cat']) and is_numeric($_GET['cat']) ) {
          return 2;
        }
      case 'picture_modify':
        return 3;
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
      case 'plugins_update':
      case 'plugins_new':
      case 'plugin':
        return 5;
    }
  }
  if (isset($_GET['plgmenu'])) /* Later added plugins will add plgmenu in url */
  {
    return 5;
  }
  return 0;
}
?>