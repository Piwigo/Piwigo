<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
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
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2008 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | file          : $Id$
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

trigger_action('loc_begin_page_header');

$template->assign(
  array(
    'GALLERY_TITLE' =>
      isset($page['gallery_title']) ?
        $page['gallery_title'] : $conf['gallery_title'],

    'PAGE_BANNER' =>
      trigger_event('render_page_banner',
        isset($page['page_banner']) ?
          $page['page_banner'] : $conf['page_banner']),

    'BODY_ID' =>
      isset($page['body_id']) ?
        $page['body_id'] : '',

    'CONTENT_ENCODING' => get_pwg_charset(),
    'PAGE_TITLE' => strip_tags($title),
    'LANG'=>$lang_info['code'],
    'DIR'=>$lang_info['direction'],

    'U_HOME' => make_index_url(),
    ));


// Header notes
if ( !empty($header_notes) )
{
  $template->assign('header_notes',$header_notes);
}

if ( !empty($page['meta_robots']) )
{
  $template->append('head_elements',
        '<meta name="robots" content="'
        .implode(',', array_keys($page['meta_robots']))
        .'">'
    );
}

// refresh
if ( isset( $refresh ) and intval($refresh) >= 0
    and isset( $url_link ) and isset( $redirect_msg ) )
{
  $template->assign(
    array(
      'REDIRECT_MSG' => $redirect_msg,
      'page_refresh' => array(
            'TIME' => $refresh,
            'U_REFRESH' => $url_link
          )
      ));
}

trigger_action('loc_end_page_header');

header('Content-Type: text/html; charset='.get_pwg_charset());
$template->parse('header');

trigger_action('loc_after_page_header');
?>