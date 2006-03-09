<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
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

//
// Start output of page
//
$template->set_filenames(array('header'=>'header.tpl'));

$css = PHPWG_ROOT_PATH.'template/'.$user['template'];
$css.= '/'.$user['template'].'.css';

$template->assign_vars(
  array(
    'GALLERY_TITLE' =>
      isset($page['gallery_title']) ?
        $page['gallery_title'] : $conf['gallery_title'],

    'PAGE_BANNER' =>
      isset($page['page_banner']) ?
        $page['page_banner'] : $conf['page_banner'],

    'BODY_ID' =>
      isset($page['body_id']) ?
        $page['body_id'] : '',

    'CONTENT_ENCODING' => $lang_info['charset'],
    'PAGE_TITLE' => $title,
    'LANG'=>$lang_info['code'],
    'DIR'=>$lang_info['direction'],

    'T_STYLE' => $css
    ));

// refresh
if ( isset( $refresh ) and intval($refresh) >= 0 and isset( $url_link ) )
{
  $template->assign_vars(
    array(
      'REFRESH_TIME' => $refresh,
      'U_REFRESH' => $url_link
      ));
  $template->assign_block_vars('refresh', array());
}

$template->parse('header');
?>
