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
$template->set_filenames(array('tail'=>'footer.tpl'));

$template->assign_vars(
  array(
    'VERSION' => PHPWG_VERSION,
    'MAIL'=>$conf['mail_webmaster'],
    
    'L_GEN_TIME' => $lang['generation_time'],
    'L_SQL_QUERIES_IN' => $lang['sql_queries_in'],
    'L_SEND_MAIL' => $lang['send_mail'],
    'L_TITLE_MAIL' => $lang['title_send_mail'],
    'L_WEBMASTER'=>$lang['webmaster'],
    ));
//------------------------------------------------------------- generation time
if ($conf['show_gt'])
{
  $time = get_elapsed_time($t2, get_moment());

  if (!isset($page['count_queries']))
  {
    $page['count_queries'] = 0;
    $page['queries_time'] = 0;
  }
  
  $template->assign_block_vars(
    'debug',
    array('TIME' => $time,
          'NB_QUERIES' => $page['count_queries'],
          'SQL_TIME' => number_format($page['queries_time'],3,'.',' ').' s'));
}

//
// Generate the page
//

$template->pparse('tail');
?>
