<?php
// +-----------------------------------------------------------------------+
// |                              admin_phpinfo.php                               |
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

if( !defined("PHPWG_ROOT_PATH") )
{
	die ("Hacking attempt!");
}

include_once( PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php' );

ob_start(); 
phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES | INFO_VARIABLES); 
$phpinfo = ob_get_contents(); 
ob_end_clean(); 

// Get used layout
$layout = (preg_match('#bgcolor#i', $phpinfo)) ? 'old' : 'new';
$output='';
// Here we play around a little with the PHP Info HTML to try and stylise
// it along phpBB's lines ... hopefully without breaking anything. The idea
// for this was nabbed from the PHP annotated manual
preg_match_all('#<body[^>]*>(.*)</body>#siU', $phpinfo, $output); 

switch ($layout)
{
  case 'old':
    $output = preg_replace('#<table#', '<table class="table1"', $output[1][0]);
    $output = preg_replace('# bgcolor="\#(\w){6}"#', '', $output);
    $output = preg_replace('#(\w),(\w)#', '\1, \2', $output);
    $output = preg_replace('#border="0" cellpadding="3" cellspacing="1" width="600"#', 'border="0" cellspacing="1" cellpadding="4" width="95%"', $output);
    $output = preg_replace('#<tr valign="top"><td align="left">(.*?<a .*?</a>)(.*?)</td></tr>#s', '<tr class="row1"><td style="{background-color: #9999cc;}"><table width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td style="{background-color: #9999cc;}">\2</td><td style="{background-color: #9999cc;}">\1</td></tr></table></td></tr>', $output);
    $output = preg_replace('#<tr valign="baseline"><td[ ]{0,1}><b>(.*?)</b>#', '<tr><td class="row1" nowrap="nowrap">\1', $output);
    $output = preg_replace('#<td align="(center|left)">#', '<td class="row2">', $output);
    $output = preg_replace('#<td>#', '<td class="row2">', $output);
    $output = preg_replace('#valign="middle"#', '', $output);
    $output = preg_replace('#<tr >#', '<tr>', $output);
    $output = preg_replace('#<hr(.*?)>#', '', $output);
    $output = preg_replace('#<h1 align="center">#i', '<h1>', $output);
    $output = preg_replace('#<h2 align="center">#i', '<h2>', $output);
    break;
  case 'new':
    $output = preg_replace('#<table#', '<table class="table1" align="center"', $output[1][0]);
    $output = preg_replace('#(\w),(\w)#', '\1, \2', $output);
    $output = preg_replace('#border="0" cellpadding="3" width="600"#', 'border="0" cellspacing="1" cellpadding="4" width="95%"', $output);
    $output = preg_replace('#<tr class="v"><td>(.*?<a .*?</a>)(.*?)</td></tr>#s', '<tr class="throw"><td><table width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td>\2</td><td>\1</td></tr></table></td></tr>', $output);
    $output = preg_replace('#<td>#', '<td style="background-color: #444444;text-align:center;">', $output);
	$output = preg_replace('#<th>#', '<th class="throw">', $output);
    $output = preg_replace('#class="e"#', 'class="row1" nowrap="nowrap"', $output);
    $output = preg_replace('#class="v"#', 'class="row2"', $output);
    $output = preg_replace('# class="h"#', '', $output);
    $output = preg_replace('#<hr />#', '', $output);
    preg_match_all('#<div class="center">(.*)</div>#siU', $output, $output); 
    $output = $output[1][0];
    break;
}
$template->assign_var('ADMIN_CONTENT',$output);

?>