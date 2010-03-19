<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
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

if( !defined("PHPWG_ROOT_PATH") ) die ("Hacking attempt!");

$edit_user = build_user( $_GET['user_id'], false );

include_once(PHPWG_ROOT_PATH.'profile.php');


$errors = array();
if ( !is_adviser() )
{
  save_profile_from_post($edit_user, $errors);
}

load_profile_in_template(
  get_root_url().'admin.php?page=profile&amp;user_id='.$edit_user['id'],
  get_root_url().'admin.php?page=user_list',
  $edit_user
  );
$page['errors'] = array_merge($page['errors'], $errors);

$template->set_filename('profile', 'profile.tpl');
$template->assign_var_from_handle('ADMIN_CONTENT', 'profile');
?>
