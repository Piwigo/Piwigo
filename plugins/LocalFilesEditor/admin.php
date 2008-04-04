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

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');
include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
include_once(LOCALEDIT_PATH.'functions.inc.php');
load_language('plugin.lang', LOCALEDIT_PATH);
$my_base_url = get_admin_plugin_menu_link(__FILE__);

// +-----------------------------------------------------------------------+
// |                            Tabssheet
// +-----------------------------------------------------------------------+
if (!isset($_GET['tab']))
    $page['tab'] = 'localconf';
else
    $page['tab'] = $_GET['tab'];

$tabsheet = new tabsheet();
$tabsheet->add('localconf',
               l10n('locfiledit_onglet_localconf'),
               $my_base_url.'&amp;tab=localconf');
$tabsheet->add('css',
               l10n('locfiledit_onglet_css'),
               $my_base_url.'&amp;tab=css');
$tabsheet->add('tpl',
               l10n('locfiledit_onglet_tpl'),
               $my_base_url.'&amp;tab=tpl');
$tabsheet->add('lang',
               l10n('locfiledit_onglet_lang'),
               $my_base_url.'&amp;tab=lang');
$tabsheet->add('plug',
               l10n('locfiledit_onglet_plug'),
               $my_base_url.'&amp;tab=plug');
$tabsheet->select($page['tab']);
$tabsheet->assign();


// +-----------------------------------------------------------------------+
// |                            Variables init
// +-----------------------------------------------------------------------+
$edited_file = '';
$content_file = '';
$new_file['localconf'] = "<?php\n\n".l10n('locfiledit_newfile')."\n\n\n\n\n?>";
$new_file['css'] = l10n('locfiledit_newfile') . "\n\n";
$new_file['lang'] = "<?php\n\n" . l10n('locfiledit_newfile') . "\n\n\n\n\n?>";
$new_file['plug'] = "<?php\n/*
Plugin Name: " . l10n('locfiledit_onglet_plug') . "
Version: 1.0
Description: " . l10n('locfiledit_onglet_plug') . "
Plugin URI: http://www.phpwebgallery.net
Author:
Author URI:
*/\n\n\n\n\n?>";

// Editarea options
$editarea = array(
  'start_highlight' => true,
  'language' => substr($user['language'], 0, 2),
  'toolbar' => 'search,fullscreen, |,select_font, |, undo, redo, change_smooth_selection, highlight, reset_highlight, |, help');
if (isset($conf['editarea_options']) and is_array($conf['editarea_options']))
{
  $editarea = array_merge($editarea, $conf['editarea_options']);
}

// Edit selected file for CSS, template and language
if ((isset($_POST['edit'])) and !is_numeric($_POST['file_to_edit']))
{
  $edited_file = $_POST['file_to_edit'];
  $content_file = file_exists($edited_file) ? 
    file_get_contents($edited_file) : $new_file[$page['tab']];
}


// +-----------------------------------------------------------------------+
// |                            Process tabsheet
// +-----------------------------------------------------------------------+
$options[] = l10n('locfiledit_choose_file');
$selected = 0; 

switch ($page['tab'])
{
  case 'localconf':
    $edited_file = PHPWG_ROOT_PATH . "include/config_local.inc.php";
    $content_file = file_exists($edited_file) ?
      file_get_contents($edited_file) : $new_file['localconf'];
	
    $template->assign('show_default' , array(
        array('SHOW_DEFAULT' => LOCALEDIT_PATH
                . 'show_default.php?file=include/config_default.inc.php',
              'FILE' => 'config_default.inc.php')));
    $editarea['syntax'] = 'php';
    break;

  case 'css':
    $template_dir = PHPWG_ROOT_PATH . 'template';
    $options[] = '----------------------';
    $value = PHPWG_ROOT_PATH . "template-common/local-layout.css";
    $options[$value] = 'template-common / local-layout.css';
    if ($edited_file == $value) $selected = $value;
	
    foreach (get_dirs($template_dir) as $pwg_template)
    {
      $options[] = '----------------------';
      $value = $template_dir . '/' . $pwg_template . '/local-layout.css';
      $options[$value] = $pwg_template . ' / local-layout.css';
      if ($edited_file == $value) $selected = $value;
      $options[] = '----------------------';
      foreach (get_dirs($template_dir.'/'.$pwg_template.'/theme') as $theme)
      {
        $value = $template_dir.'/'.$pwg_template.'/theme/'.$theme.'/theme.css';
        $options[$value] = $pwg_template . ' / ' . $theme . ' / theme.css';
        if ($edited_file == $value) $selected = $value;
      }
    }
    $template->assign('css_lang_tpl', array(
        'OPTIONS' => $options,
        'SELECTED' => $selected));
    $editarea['syntax'] = 'css';
    break;
  
  case 'tpl':
    $template_dir = PHPWG_ROOT_PATH . 'template';
    foreach (get_dirs($template_dir) as $pwg_template)
    {
      $dir = $template_dir . '/' . $pwg_template . '/';
      $options[] = '----------------------';
      if (is_dir($dir) and $content = opendir($dir))
      {
        while ($node = readdir($content))
        {
          if (is_file($dir . $node)
            and strtolower(get_extension($node)) == 'tpl'
            and !strpos($node , '.bak.tpl'))
          {
            $value = $dir . $node;
            $options[$value] = $pwg_template . ' / ' . $node;
            if ($edited_file == $value) $selected = $value;
          }
        }
      }
    }
    $template->assign('css_lang_tpl', array(
        'OPTIONS' => $options,
        'SELECTED' => $selected));
    $editarea['syntax'] = 'html';
    break;

  case 'lang':
    $options[] = '----------------------';
    foreach (get_languages() as $language_code => $language_name)
    {
      $value = PHPWG_ROOT_PATH.'language/'.$language_code.'/local.lang.php';
      if ($edited_file == $value)
      {
        $selected = $value;
        $template->assign('show_default', array(
          array('SHOW_DEFAULT' => LOCALEDIT_PATH
                  . 'show_default.php?file='
                  . 'language/'.$language_code.'/common.lang.php',
                'FILE' => 'common.lang.php'),
          array('SHOW_DEFAULT' => LOCALEDIT_PATH
                  . 'show_default.php?file='
                  . 'language/'.$language_code.'/admin.lang.php',
                'FILE' => 'admin.lang.php')));
      }
      $options[$value] = $language_name;
    }
    $template->assign('css_lang_tpl', array(
        'OPTIONS' => $options,
        'SELECTED' => $selected));
    $editarea['syntax'] = 'php';
    break;
    
  case 'plug':
    $edited_file = PHPWG_PLUGINS_PATH . "PersonalPlugin/main.inc.php";
    $content_file = file_exists($edited_file) ?
      file_get_contents($edited_file) : $new_file['plug'];
    $editarea['syntax'] = 'php';
    break;
}


// +-----------------------------------------------------------------------+
// |                           Load backup file
// +-----------------------------------------------------------------------+
if (isset($_POST['restore']) and !is_adviser())
{
  $edited_file = $_POST['edited_file'];
  $content_file = file_get_contents(
      substr_replace($edited_file , '.bak' , strrpos($edited_file ,'.') , 0));

  array_push($page['infos'],
             l10n('locfiledit_bak_loaded1'),
             l10n('locfiledit_bak_loaded2'));
}


// +-----------------------------------------------------------------------+
// |                            Save file
// +-----------------------------------------------------------------------+
if (isset($_POST['submit']) and !is_adviser())
{
  $edited_file = $_POST['edited_file'];
  $content_file = stripslashes($_POST['text']);
  if (get_extension($edited_file) == 'php')
	{
    $content_file = eval_syntax($content_file);
  }
  if ($content_file === false)
	{
    array_push($page['errors'], l10n('locfiledit_syntax_error'));
  }
	else
	{
    if ($page['tab'] == 'plug'
      and !is_dir(PHPWG_PLUGINS_PATH . 'PersonalPlugin'))
    {
      @mkdir(PHPWG_PLUGINS_PATH . "PersonalPlugin");
    }
    if (file_exists($edited_file))
    {
      @copy($edited_file,
        substr_replace($edited_file,
                       '.bak',
                       strrpos($edited_file , '.'),
                       0)
      );
    }
    
    if ($file = @fopen($edited_file , "w"))
		{
      @fwrite($file , $content_file);
      @fclose($file);
      array_push($page['infos'],
        l10n('locfiledit_save_config'),
        sprintf(l10n('locfiledit_saved_bak'),
           substr(substr_replace($edited_file,
                      '.bak',
                      strrpos($edited_file , '.'),
                      0),
                  2)));
    }
		else
    {
      array_push($page['errors'], l10n('locfiledit_cant_save'));
    }
  }
}


// +-----------------------------------------------------------------------+
// |                            template initialization
// +-----------------------------------------------------------------------+
$template->set_filenames(array(
    'plugin_admin_content' => dirname(__FILE__) . '/admin.tpl'));

if (!empty($edited_file))
{
  if (!empty($page['errors']))
	{
    $content_file = stripslashes($_POST['text']);
  }
  $template->assign('zone_edit',
    array('EDITED_FILE' => $edited_file,
          'CONTENT_FILE' => htmlspecialchars($content_file),
          'FILE_NAME' => trim($edited_file, './\\')));
  if (file_exists(
        substr_replace($edited_file ,'.bak',strrpos($edited_file , '.'),0)))
  {
    $template->assign('restore', true);
  }
}

// Editarea
if (!isset($conf['editarea_options']) or $conf['editarea_options'] !== false)
{
  $template->assign('editarea', array(
    'URL' => LOCALEDIT_PATH . 'editarea/edit_area_full.js',
    'OPTIONS' => $editarea));
}

$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');

?>