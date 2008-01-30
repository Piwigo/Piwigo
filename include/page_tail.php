<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
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
$template->set_filenames(array('tail'=>'footer.tpl'));

trigger_action('loc_begin_page_tail');

$template->assign_vars(
  array(
    'VERSION' => $conf['show_version'] ? PHPWG_VERSION : '',
    'PHPWG_URL' => PHPWG_URL,

    'L_TITLE_MAIL' => urlencode(l10n('title_send_mail')),
    ));

//--------------------------------------------------------------------- contact

if (!is_a_guest())
{
  $template->assign_block_vars(
    'contact',
    array(
      'MAIL' => get_webmaster_mail_address()
      )
    );
}

//------------------------------------------------------------- generation time
$debug_vars = array();
if ($conf['show_gt'])
{
  $time = get_elapsed_time($t2, get_moment());

  if (!isset($page['count_queries']))
  {
    $page['count_queries'] = 0;
    $page['queries_time'] = 0;
  }

  $debug_vars = array_merge($debug_vars,
    array('TIME' => $time,
          'NB_QUERIES' => $page['count_queries'],
          'SQL_TIME' => number_format($page['queries_time'],3,'.',' ').' s')
          );
}

if ($conf['show_queries'])
{
  $debug_vars = array_merge($debug_vars, array('QUERIES_LIST' => $debug) );
}

if ( !empty($debug_vars) )
{
  $template->assign_block_vars('debug',$debug_vars );
}

trigger_action('loc_end_page_tail');
//
// Generate the page
//
$template->parse('tail');

$template->p();
?>
