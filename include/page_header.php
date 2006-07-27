<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
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
    'PAGE_TITLE' => strip_tags($title),
    'LANG'=>$lang_info['code'],
    'DIR'=>$lang_info['direction'],

    'TAG_INPUT_ENABLED' => ((is_adviser()) ? 'disabled onclick="return false;"' : '')
    ));

header('Content-Type: text/html; charset='.$lang_info['charset']);
$template->parse('header');
?>
