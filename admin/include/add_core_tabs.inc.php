<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

add_event_handler('tabsheet_before_select', 'add_core_tabs', 0);

function add_core_tabs($sheets, $tab_id)
{
  switch($tab_id)
  {
    case 'admin_home':
      $sheets[''] = array('caption' => l10n('Administration Home'), 'url' => 'admin.php');
      break;

    case 'tags':
      global $my_base_url;
      $sheets[''] = array('caption' => '<span class="icon-tags"></span>'.l10n('Tags'), 'url' => $my_base_url.'tags');
      break;

    case 'album':
      global $admin_album_base_url;
      $sheets['properties'] = array('caption' => '<span class="icon-pencil"></span>'.l10n('Properties'), 'url' => $admin_album_base_url.'-properties');
      $sheets['sort_order'] = array('caption' => '<span class="icon-shuffle"></span>'.l10n('Manage photo ranks'), 'url' => $admin_album_base_url.'-sort_order');
      $sheets['permissions'] = array('caption' => '<span class="icon-lock"></span>'.l10n('Permissions'), 'url' => $admin_album_base_url.'-permissions');
      $sheets['notification'] = array('caption' => '<span class="icon-mail-alt"></span>'.l10n('Notification'), 'url' => $admin_album_base_url.'-notification');
      break;

    case 'albums':
      global $my_base_url;
      $sheets['list'] = array('caption' => '<span class="icon-menu"></span>'.l10n('List'), 'url' => $my_base_url.'cat_list');
      $sheets['move'] = array('caption' => '<span class="icon-move"></span>'.l10n('Move'), 'url' => $my_base_url.'cat_move');
      $sheets['permalinks'] = array('caption' => '<span class="icon-link-1"></span>'.l10n('Permalinks'), 'url' => $my_base_url.'permalinks');
      break;

    case 'batch_manager':
      global $manager_link;
      $sheets['global'] = array('caption' => '<span class="icon-th"></span>'.l10n('global mode'), 'url' => $manager_link.'global');
      $sheets['unit'] = array('caption' => '<span class="icon-th-list"></span>'.l10n('unit mode'), 'url' => $manager_link.'unit');
      break;

    case 'cat_options':
      global $link_start, $conf;
      $sheets['status'] = array('caption' => '<span class="icon-lock"></span>'.l10n('Public / Private'), 'url' => $link_start.'cat_options&amp;section=status');
      $sheets['visible'] = array('caption' => '<span class="icon-block"></span>'.l10n('Lock'), 'url' => $link_start.'cat_options&amp;section=visible');
      if ($conf['activate_comments'])
        $sheets['comments'] = array('caption' => '<span class="icon-chat"></span>'.l10n('Comments'), 'url' => $link_start.'cat_options&amp;section=comments');
      if ($conf['allow_random_representative'])
        $sheets['representative'] = array('caption' => l10n('Representative'), 'url' => $link_start.'cat_options&amp;section=representative');
      break;

    case 'comments':
      global $my_base_url;
      $sheets[''] = array('caption' => l10n('User comments'), 'url' => $my_base_url.'comments');
      break;

    case 'users':
      global $my_base_url;
      $sheets[''] = array('caption' => '<span class="icon-users"> </span>'.l10n('User list'), 'url' => $my_base_url.'user_list');
      break;

    case 'groups':
      global $my_base_url;
      $sheets[''] = array('caption' => '<span class="icon-group"> </span>'.l10n('Groups'), 'url' => $my_base_url.'group_list');
      break;

    case 'configuration':
      global $conf_link;
      $sheets['main'] = array('caption' => l10n('General'), 'url' => $conf_link.'main');
      $sheets['sizes'] = array('caption' => l10n('Photo sizes'), 'url' => $conf_link.'sizes');
      $sheets['watermark'] = array('caption' => l10n('Watermark'), 'url' => $conf_link.'watermark');
      $sheets['display'] = array('caption' => l10n('Display'), 'url' => $conf_link.'display');
      $sheets['comments'] = array('caption' => l10n('Comments'), 'url' => $conf_link.'comments');
      // $sheets['default'] = array('caption' => l10n('Guest Settings'), 'url' => $conf_link.'default');
      break;

    case 'help':
      global $help_link;
      $sheets['add_photos'] = array('caption' => l10n('Add Photos'), 'url' => $help_link.'add_photos');
      $sheets['permissions'] = array('caption' => l10n('Permissions'), 'url' => $help_link.'permissions');
      $sheets['groups'] = array('caption' => l10n('Groups'), 'url' => $help_link.'groups');
      $sheets['virtual_links'] = array('caption' => l10n('Virtual Links'), 'url' => $help_link.'virtual_links');
      $sheets['misc'] = array('caption' => l10n('Miscellaneous'), 'url' => $help_link.'misc');
      break;

    case 'history':
      global $link_start;
      $sheets['stats'] = array('caption' => '<span class="icon-signal"></span>'.l10n('Statistics'), 'url' => $link_start.'stats');
      $sheets['history'] = array('caption' => '<span class="icon-search"></span>'.l10n('Search'), 'url' => $link_start.'history');
      break;

    case 'languages':
      global $my_base_url;
      $sheets['installed'] = array('caption' => '<span class="icon-language"></span>'.l10n('Installed Languages'), 'url' => $my_base_url.'&amp;tab=installed');
      $sheets['update'] = array('caption' => '<span class="icon-arrows-cw"></span>'.l10n('Check for updates'), 'url' => $my_base_url.'&amp;tab=update');
      $sheets['new'] = array('caption' => '<span class="icon-plus-circled"></span>'.l10n('Add New Language'), 'url' => $my_base_url.'&amp;tab=new');
      break;

    case 'menus':
      global $my_base_url;
      $sheets[''] = array('caption' => '<span class="icon-menu"></span>'.l10n('Menu Management'), 'url' => $my_base_url.'menubar');
      break;

    case 'nbm':
      global $base_url;
      $sheets['param'] = array('caption' => l10n('Parameter'), 'url' => $base_url.'?page=notification_by_mail&amp;mode=param');
      $sheets['subscribe'] = array('caption' => l10n('Subscribe'), 'url' => $base_url.'?page=notification_by_mail&amp;mode=subscribe');
      $sheets['send'] = array('caption' => l10n('Send'), 'url' => $base_url.'?page=notification_by_mail&amp;mode=send');
      break;

    case 'photo':
      global $admin_photo_base_url;
      $sheets['properties'] = array('caption' => l10n('Properties'), 'url' => $admin_photo_base_url.'-properties');
      $sheets['coi'] = array('caption' => '<span class="icon-crop"></span>'.l10n('Center of interest'), 'url' => $admin_photo_base_url.'-coi');
      break;

    case 'photos_add':
      global $conf;
      $sheets['direct'] = array('caption' => '<span class="icon-upload"></span>'.l10n('Web Form'), 'url' => PHOTOS_ADD_BASE_URL.'&amp;section=direct');
      $sheets['applications'] = array('caption' => '<span class="icon-network"></span>'.l10n('Applications'), 'url' => PHOTOS_ADD_BASE_URL.'&amp;section=applications');
      if ($conf['enable_synchronization'])
        $sheets['ftp'] = array('caption' => '<span class="icon-exchange"></span>'.l10n('FTP + Synchronization'), 'url' => PHOTOS_ADD_BASE_URL.'&amp;section=ftp');
      break;

    case 'plugins':
      global $my_base_url;
      $sheets['installed'] = array('caption' => '<span class="icon-equalizer"></span>'.l10n('Plugin list'), 'url' => $my_base_url.'&amp;tab=installed');
      $sheets['update'] = array('caption' => '<span class="icon-arrows-cw"></span>'.l10n('Check for updates'), 'url' => $my_base_url.'&amp;tab=update');
      $sheets['new'] = array('caption' => '<span class="icon-plus-circled"></span>'.l10n('Other plugins'), 'url' => $my_base_url.'&amp;tab=new');
      break;

    case 'rating':
      $sheets['rating'] = array('caption' => l10n('Photos'), 'url' => get_root_url().'admin.php?page=rating');
      $sheets['rating_user'] = array('caption' => l10n('Users'), 'url' => get_root_url().'admin.php?page=rating_user');
      break;

    case 'themes':
      global $my_base_url;
      $sheets['installed'] = array('caption' => '<span class="icon-brush"></span>'.l10n('Installed Themes'), 'url' => $my_base_url.'&amp;tab=installed');
      $sheets['update'] = array('caption' => '<span class="icon-arrows-cw"></span>'.l10n('Check for updates'), 'url' => $my_base_url.'&amp;tab=update');
      $sheets['new'] = array('caption' => '<span class="icon-plus-circled"></span>'.l10n('Add New Theme'), 'url' => $my_base_url.'&amp;tab=new');
      break;

    case 'updates':
      global $my_base_url;
      $sheets['pwg'] = array('caption' => l10n('Piwigo Update'), 'url' => $my_base_url);
      $sheets['ext'] = array('caption' => l10n('Extensions Update'), 'url' => $my_base_url.'&amp;tab=ext');
      break;
  }

  return $sheets;
}

?>