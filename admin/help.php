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
include_once( './admin/include/isadmin.inc.php' );
//----------------------------------------------------- template initialization
$sub = $vtp->Open( './template/'.$user['template'].'/admin/help.vtp' );
$tpl = array( );
templatize_array( $tpl, 'lang', $sub );
//----------------------------------------------------- help categories display
$categories = array( 'images','thumbnails','database','remote','upload',
                     'virtual','groups','access','infos' );
foreach ( $categories as $category ) {
  $vtp->addSession( $sub, 'cat' );
  if ( $category == 'images' )
  {
    $vtp->addSession( $sub, 'illustration' );
    $vtp->setVar( $sub, 'illustration.pic_src', './admin/images/admin.png' );
    $vtp->setVar( $sub, 'illustration.pic_alt', '' );
    $vtp->setVar( $sub, 'illustration.caption', $lang['help_images_intro'] );
    $vtp->closeSession( $sub, 'illustration' );
  }
  $vtp->setVar( $sub, 'cat.name', $lang['help_'.$category.'_title'] );
  foreach ( $lang['help_'.$category] as $item ) {
    $vtp->addSession( $sub, 'item' );
    $vtp->setVar( $sub, 'item.content', $item );
    $vtp->closeSession( $sub, 'item' );
  }

  $vtp->closeSession( $sub, 'cat' );
}
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>
