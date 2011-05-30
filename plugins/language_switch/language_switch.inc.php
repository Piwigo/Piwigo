<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
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

if (!defined('LANGUAGE_SWITCH_PATH'))
{
  define('LANGUAGE_SWITCH_PATH' , PHPWG_PLUGINS_PATH.basename(dirname(__FILE__)).'/');
}

class language_controler
{
  static public function _switch()
  {
    global $user;
    
    if (!defined('PHPWG_ROOT_PATH'))
    {
      die('Hacking attempt!');
    }
    
    $same = $user['language'];
    
    if (isset($_GET['lang']))
    {
      if (!empty($_GET['lang']) and file_exists(PHPWG_ROOT_PATH.'language/'.$_GET['lang'].'/common.lang.php'))
      {
        if (is_a_guest() or is_generic())
        {
          pwg_set_session_var('lang_switch', $_GET['lang']);
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
    elseif ((is_a_guest() or is_generic()))
    {
      $user['language'] = pwg_get_session_var('lang_switch', $user['language']);
    }
    
    // Reload language only if it isn't the same one
    if ( $same !== $user['language'])
    {
      load_language('common.lang', '', array('language'=>$user['language']));
      
      load_language(
        'lang',
        PHPWG_ROOT_PATH.PWG_LOCAL_DIR,
        array(
          'language' => $user['language'],
          'no_fallback' => true,
          'local' => true
          )
        );
      
      if (defined('IN_ADMIN') and IN_ADMIN)
      {
        // Never currently
        load_language('admin.lang', '', array('language'=>$user['language']));
      }
    }
  }
  
  static public function _flags()
  {
    global $user, $template, $conf;
    
    $available_lang = get_languages();
    
    if (isset($conf['no_flag_languages']))
    {
      $available_lang = array_diff_key($available_lang, array_flip($conf['no_flag_languages']));
    }
    
    $url_starting = get_query_string_diff(array('lang'));
    
    foreach ($available_lang as $code => $displayname)
    {
      $qlc = array ( 
        'url' => str_replace(
          array('=&amp;','?&amp;'),
          array('&amp;','?'),
          add_url_params($url_starting, array('lang'=> $code))
          ),
        'alt' => ucwords($displayname),
        'title' => substr($displayname, 0, -4), // remove [FR] or [RU]
        'img' => get_root_url().'language/'.$code.'/'.$code.'.jpg',
        );
      
      $lsw['flags'][$code] = $qlc ;
      
      if ($code == $user['language'])
      {
        $lsw['Active'] = $qlc;
      }
    }
    
    $template->set_filename('language_flags', dirname(__FILE__) . '/flags.tpl');
    
    $lsw['side'] = ceil(sqrt(count($available_lang)));
    
    $template->assign(
      array(
        'lang_switch'=> $lsw,
        'LANGUAGE_SWITCH_PATH' => LANGUAGE_SWITCH_PATH,
        )
      );
    
    $flags = $template->parse('language_flags',true);
    $template->clear_assign('lang_switch');
    $template->concat( 'PLUGIN_INDEX_ACTIONS', $flags);
  }
}

  /* {html_head} usage function */
  /* See flags.tpl for example (due no catenation available) */
if (!function_exists('Componant_exists'))
{
  function Componant_exists($path, $file)
  {
    return file_exists( $path . $file);
  }
}

?>