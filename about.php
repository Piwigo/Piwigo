<?php
/***************************************************************************
 *                                about.php                                *
 *                            ------------------                           *
 *   application   : PhpWebGallery 1.3 <http://phpwebgallery.net>          *
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

//----------------------------------------------------------- include
$phpwg_root_path = './';
include_once( $phpwg_root_path.'common.php' );
//----------------------------------------------------- template initialization
//
// Start output of page
//
$title= $lang['about_page_title'];
include('include/page_header.php');

$template->set_filenames(array('about'=>'about.tpl'));
initialize_template();

$template->assign_vars(array(
	'PAGE_TITLE' => $title,
	'L_ABOUT' => $lang['about_message'],
	'L_RETURN' =>  $lang['about_return'], 
	'U_RETURN' => add_session_id('./category.php?'.$_SERVER['QUERY_STRING'])
	)
	);

$template->pparse('about');
include('include/page_tail.php');
?>