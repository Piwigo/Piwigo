<?php
// +-----------------------------------------------------------------------+
// |                               help.php                                |
// +-----------------------------------------------------------------------+
// | application   : PhpWebGallery <http://phpwebgallery.net>              |
// | branch        : BSF (Best So Far)                                     |
// +-----------------------------------------------------------------------+
// | file          : $RCSfile$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
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
include_once( PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php' );
//----------------------------------------------------- template initialization
$template->set_filenames( array('help'=>'admin/help.tpl') );

//----------------------------------------------------- help categories display
$categories = array( 'images','thumbnails','database','remote','upload',
                     'virtual','groups','access','infos' );
foreach ( $categories as $category ) {
  $template->assign_block_vars('cat', array('NAME'=>$lang['help_'.$category.'_title']));
  if ( $category == 'images' )
  {
    $template->assign_block_vars('cat.illustration', array(
	  'SRC_IMG'=>PHPWG_ROOT_PATH.'admin/images/admin.png',
	  'CAPTION'=>$lang['help_images_intro']
	  ));
  }
  foreach ( $lang['help_'.$category] as $item ) {
    $template->assign_block_vars('cat.item', array('CONTENT'=>$item));
  }
}
$template->assign_var_from_handle('ADMIN_CONTENT', 'help');
?>
