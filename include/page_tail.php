<?php
// +-----------------------------------------------------------------------+
// |                             page_tail.php                             |
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
$template->set_filenames(array('tail'=>'footer.tpl'));

//------------------------------------------------------------- generation time

$time = get_elapsed_time( $t2, get_moment() );

$template->assign_vars(
  array(
    'TIME' =>  $time,
    'VERSION' => $conf['version'],
    'WEBMASTER'=>$conf['webmaster'],
    'MAIL'=>$conf['mail_webmaster'],
    
    'L_GEN_TIME' => $lang['generation_time'],
    'L_SEND_MAIL' => $lang['send_mail'],
    'L_TITLE_MAIL' => $lang['title_send_mail'],
    
    'U_SITE' => $conf['site_url']
    ));
    
if (DEBUG)
{
  $template->assign_block_vars('debug', array());
}

//
// Generate the page
//

$template->pparse('tail');
?>
