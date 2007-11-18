<?php /*
Plugin Name: Language Switch
Version: 1.0
Description: Give you an advice on the administration page.
Plugin URI: http://www.phpwebgallery.net
Author: PhpWebGallery team
Author URI: http://www.phpwebgallery.net
*/
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
function language_switch()
{
global $user, $template, $conf, $lang;

if (!isset($user['status']))
{ die('You are not authorized to access this page'); };

if ( isset( $_GET['lang']) )
{
  if ( !empty($_GET['lang'] ) and 
  file_exists( PHPWG_ROOT_PATH.'language/'.$_GET['lang'].'/common.lang.php') )
  {
    if ($user['is_the_guest'] or $user['status'] == 'generic')
    {
      setcookie( 'pwg_lang_switch', $_GET['lang'], 
        time()+60*60*24*30, cookie_path() );
    }
    else
    {
        $query = '
    UPDATE '.USER_INFOS_TABLE.'
    SET language = \''.$_GET['lang'].'\'
    WHERE user_id = '.$user['id'].'
    ;';
      pwg_query($query);
    }
    $user['language'] = $_GET['lang'];
  }
}
// Users have $user['language']
// Guest or generic members will use their cookied language !

if ((is_a_guest() or is_generic())
  and isset( $_COOKIE['pwg_lang_switch'] ) )
{ $user['language'] = $_COOKIE['pwg_lang_switch']; }

load_language('common.lang', '', $user['language']);
load_language('local.lang', '', $user['language']);
if (defined('IN_ADMIN') and IN_ADMIN)
{
  load_language('admin.lang', '', $user['language']);
}
}
//if ( isset( $_GET['lang']) ) { redirect( make_index_url() ); }





function Lang_flags() 
{
global $user, $template;
$available_lang = get_languages();
foreach ( $available_lang as $code => $displayname )
{
  $qlc_url = add_url_params( make_index_url(), array( 'lang' => $code ) );
  $qlc_alt = ucwords( $displayname );
  $qlc_title =  $qlc_alt;
  $qlc_img =  PHPWG_PLUGINS_PATH.'language_switch/icons/'
       . $code . '.gif';
   // echo $code . ' '. $qlc_url .' // <br />';
   // echo $user['language'] . ' '. $qlc_img .' /// <br />';
  
    if ( $code !== $user['language'] and file_exists($qlc_img) )
  {
    $template -> concat_var( 'PLUGIN_INDEX_ACTIONS',
      '<li><a href="' . $qlc_url . '" ><img src="' . $qlc_img . '" alt="'
      . $qlc_alt . '" title="'
      . $qlc_title . '" style="border: 1px solid #000000; ' 
      . ' margin: 0px 2px;" /></a></li>');
  }
}
}
?>