<?php
/***************************************************************************
 *                                page_header.php                                *
 *                            ------------------                           *
 *   application   : PhpWebGallery 1.4 <http://phpwebgallery.net>          *
 *   author        : Pierrick LE GALL <pierrick@z0rglub.com>               *
 *                                                                         *
 *   $Id$
 *                                                                         *
 ***************************************************************************/

/***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/
 
//
// Start output of page
//
$template->set_filenames(array('header'=>'header.tpl'));

$template->assign_vars(array(
	'S_CONTENT_ENCODING' => $lang['charset'],
	'T_STYLE' =>  './template/'.$user['template'].'/'.$user['template'].'.css', 
	'PAGE_TITLE' => $title)
	);

// refresh
if ( isset( $refresh ) && $refresh >0 && isset($url_link))
  {
    $url = $url_link.'&amp;slideshow='.$refresh;
	$template->assign_vars(array(
	'S_REFRESH_TIME' => $refresh,
	'U_REFRESH' => add_session_id( $url )
	)
	);
  	$template->assign_block_vars('refresh', array());
  }

// Work around for "current" Apache 2 + PHP module which seems to not
// cope with private cache control setting
if (!empty($HTTP_SERVER_VARS['SERVER_SOFTWARE']) && strstr($HTTP_SERVER_VARS['SERVER_SOFTWARE'], 'Apache/2'))
{
	header ('Cache-Control: no-cache, pre-check=0, post-check=0, max-age=0');
}
else
{
	header ('Cache-Control: private, pre-check=0, post-check=0, max-age=0');
}
header ('Expires: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
header ('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

$template->pparse('header');
$vtp=new VTemplate;
?>