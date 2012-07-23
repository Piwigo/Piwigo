<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

add_event_handler('tabsheet_before_select', 'add_core_tabs', 0, 2);

function add_core_tabs($sheets, $tab_id)
{
  switch($tab_id)
  {
    case 'album':
      global $admin_album_base_url;
      $sheets['properties'] = array('caption' => l10n('Properties'), 'url' => $admin_album_base_url.'-properties');
      $sheets['sort_order'] = array('caption' => l10n('Manage photo ranks'), 'url' => $admin_album_base_url.'-sort_order');
      $sheets['permissions'] = array('caption' => l10n('Permissions'), 'url' => $admin_album_base_url.'-permissions');
      $sheets['notification'] = array('caption' => l10n('Notification'), 'url' => $admin_album_base_url.'-notification');
      break;
      
    case 'albums':
      global $my_base_url;
      $sheets['list'] = array('caption' => l10n('List'), 'url' => $my_base_url.'cat_list');
      $sheets['move'] = array('caption' => l10n('Move'), 'url' => $my_base_url.'cat_move');
      $sheets['permalinks'] = array('caption' => l10n('Permalinks'), 'url' => $my_base_url.'permalinks');
      break;
      
    case 'batch_manager':
      global $manager_link;
      $sheets['global'] = array('caption' => l10n('global mode'), 'url' => $manager_link.'global');
      $sheets['unit'] = array('caption' => l10n('unit mode'), 'url' => $manager_link.'unit');
      break;
      
    case 'cat_options':
      global $link_start, $conf;
      $sheets['status'] = array('caption' => l10n('Public / Private'), 'url' => $link_start.'cat_options&amp;section=status');
      $sheets['visible'] = array('caption' => l10n('Lock'), 'url' => $link_start.'cat_options&amp;section=visible');
      if ($conf['activate_comments'])
        $sheets['comments'] = array('caption' => l10n('Comments'), 'url' => $link_start.'cat_options&amp;section=comments');
      if ($conf['allow_random_representative'])
        $sheets['representative'] = array('caption' => l10n('Representative'), 'url' => $link_start.'cat_options&amp;section=representative');
      break;
      
    case 'comments':
      $sheets[''] = array('caption' => l10n('User comments validation'), 'url' => '');
      break;
      
    case 'configuration':
      global $conf_link;
      $sheets['main'] = array('caption' => l10n('Main'), 'url' => $conf_link.'main');
      $sheets['sizes'] = array('caption' => l10n('Photo sizes'), 'url' => $conf_link.'sizes');
      $sheets['watermark'] = array('caption' => l10n('Watermark'), 'url' => $conf_link.'watermark');
      $sheets['display'] = array('caption' => l10n('Display'), 'url' => $conf_link.'display');
      $sheets['comments'] = array('caption' => l10n('Comments'), 'url' => $conf_link.'comments');
      $sheets['default'] = array('caption' => l10n('Guest Settings'), 'url' => $conf_link.'default');
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
      $sheets['stats'] = array('caption' => l10n('Statistics'), 'url' => $link_start.'stats');
      $sheets['history'] = array('caption' => l10n('Search'), 'url' => $link_start.'history');
      break;
      
    case 'languages':
      global $my_base_url;
      $sheets['installed'] = array('caption' => l10n('Installed Languages'), 'url' => $my_base_url.'&amp;tab=installed');
      $sheets['update'] = array('caption' => l10n('Check for updates'), 'url' => $my_base_url.'&amp;tab=update');
      $sheets['new'] = array('caption' => l10n('Add New Language'), 'url' => $my_base_url.'&amp;tab=new');
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
      $sheets['coi'] = array('caption' => l10n('Center of interest'), 'url' => $admin_photo_base_url.'-coi');
      break;
      
    case 'photos_add':
      global $conf;
      $sheets['direct'] = array('caption' => l10n('Web Form'), 'url' => PHOTOS_ADD_BASE_URL.'&amp;section=direct');
      $sheets['applications'] = array('caption' => l10n('Applications'), 'url' => PHOTOS_ADD_BASE_URL.'&amp;section=applications');
      if ($conf['enable_synchronization'])
        $sheets['ftp'] = array('caption' => l10n('FTP + Synchronization'), 'url' => PHOTOS_ADD_BASE_URL.'&amp;section=ftp');
      break;
      
    case 'plugins':
      global $my_base_url;
      $sheets['installed'] = array('caption' => l10n('Plugin list'), 'url' => $my_base_url.'&amp;tab=installed');
      $sheets['update'] = array('caption' => l10n('Check for updates'), 'url' => $my_base_url.'&amp;tab=update');
      $sheets['new'] = array('caption' => l10n('Other plugins'), 'url' => $my_base_url.'&amp;tab=new');
      break;
      
    case 'rating':
      $sheets['rating'] = array('caption' => l10n('Photos'), 'url' => get_root_url().'admin.php?page=rating');
      $sheets['rating_user'] = array('caption' => l10n('Users'), 'url' => get_root_url().'admin.php?page=rating_user');
      break;
      
    case 'themes':
      global $my_base_url;
      $sheets['installed'] = array('caption' => l10n('Installed Themes'), 'url' => $my_base_url.'&amp;tab=installed');
      $sheets['update'] = array('caption' => l10n('Check for updates'), 'url' => $my_base_url.'&amp;tab=update');
      $sheets['new'] = array('caption' => l10n('Add New Theme'), 'url' => $my_base_url.'&amp;tab=new');
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