<?php
// +-----------------------------------------------------------------------+
// |                               about.php                               |
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

//----------------------------------------------------------- include
$phpwg_root_path = './';
include_once( $phpwg_root_path.'include/common.inc.php' );
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
