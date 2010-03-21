<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
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
$edited_file = isset($_POST['edited_file']) ? $_POST['edited_file'] : '';
$content_file = '';
$new_file['localconf'] = "<?php\n\n/* ".l10n('locfiledit_newfile')." */\n\n\n\n\n?>";
$new_file['css'] = "/* " . l10n('locfiledit_newfile') . " */\n\n";
$new_file['tpl'] = "{* " . l10n('locfiledit_newfile') . " *}\n\n";
$new_file['lang'] = $new_file['localconf'];
$new_file['plug'] = "<?php\n/*
Plugin Name: " . l10n('locfiledit_onglet_plug') . "
Version: 1.0
Description: " . l10n('locfiledit_onglet_plug') . "
Plugin URI: http://piwigo.org
Author:
Author URI:
*/\n\n\n\n\n?>";
$newfile_page = isset($_GET['newfile']) ? true : false;

// Editarea options
$editarea_options = array(
  'language' => substr($user['language'], 0, 2),
  'start_highlight' => true,
  'allow_toggle' => false,
  'toolbar' => 'search,fullscreen, |,select_font, |, undo, redo, change_smooth_selection, highlight, reset_highlight, |, help');

// Edit selected file for CSS, template and language
if ((isset($_POST['edit'])) and !is_numeric($_POST['file_to_edit']))
{
  $edited_file = $_POST['file_to_edit'];
  $content_file = file_exists($edited_file) ? 
    file_get_contents($edited_file) : $new_file[$page['tab']];
}

// Edit new tpl file
if (isset($_POST['create_tpl']))
{
  $filename = $_POST['tpl_name'];
  if (empty($filename))
  {
    array_push($page['errors'], l10n('locfiledit_empty_filename'));
  }
  if (get_extension($filename) != 'tpl')
  {
    $filename .= '.tpl';
  }
  if (!preg_match('/^[a-zA-Z0-9-_.]+$/', $filename))
  {
    array_push($page['errors'], l10n('locfiledit_filename_error'));
  }
  if (is_numeric($_POST['tpl_model']) and $_POST['tpl_model'] != '0')
  {
    array_push($page['errors'], l10n('locfiledit_model_error'));
  }
  if (file_exists($_POST['tpl_parent'] . '/' . $filename))
  {
    array_push($page['errors'], l10n('locfiledit_file_already_exists'));
  }
  if (!empty($page['errors']))
  {
    $newfile_page = true;
  }
  else
  {
    $edited_file = $_POST['tpl_parent'] . '/' . $filename;
    $content_file = ($_POST['tpl_model'] == '0') ? $new_file['tpl'] : file_get_contents($_POST['tpl_model']);
  }
}

// +-----------------------------------------------------------------------+
// |                            Process tabsheet
// +-----------------------------------------------------------------------+
switch ($page['tab'])
{
  case 'localconf':
    $edited_file = PHPWG_ROOT_PATH . "local/config/config.inc.php";
    $content_file = file_exists($edited_file) ?
      file_get_contents($edited_file) : $new_file['localconf'];
	
    $template->assign('show_default' , array(
        array('SHOW_DEFAULT' => LOCALEDIT_PATH
                . 'show_default.php?file=include/config_default.inc.php',
              'FILE' => 'config_default.inc.php')));
    $editarea_options['syntax'] = 'php';
    break;

  case 'css':
    $selected = 0; 
    $options[] = l10n('locfiledit_choose_file');
    $options[] = '----------------------';
    $value = PHPWG_ROOT_PATH . "local/css/rules.css";
    $options[$value] = 'local / css / rules.css';
    if ($edited_file == $value) $selected = $value;
    $options[] = '----------------------';
	
    foreach (get_dirs($conf['themes_dir']) as $theme_id)
    {
      $value = PHPWG_ROOT_PATH . 'local/css/'.$theme_id.'-rules.css';
      $options[$value] = 'local / css / '.$theme_id.'-rules.css';
      if ($edited_file == $value) $selected = $value;
    }
    $template->assign('css_lang_tpl', array(
        'OPTIONS' => $options,
        'SELECTED' => $selected));
    $editarea_options['syntax'] = 'css';
    break;

  case 'tpl':
    // New file form creation
    if ($newfile_page and !is_adviser())
    {
      $filename = isset($_POST['tpl_name']) ? $_POST['tpl_name'] : '';
      $selected['model'] = isset($_POST['tpl_model']) ? $_POST['tpl_model'] : '0';
      $selected['parent'] = isset($_POST['tpl_parent']) ? $_POST['tpl_parent'] : PHPWG_ROOT_PATH . 'template-extension';

      // Parent directories list
      $options['parent'] = array(PHPWG_ROOT_PATH . 'template-extension' => 'template-extension');
      $options['parent'] = array_merge($options['parent'], get_rec_dirs(PHPWG_ROOT_PATH . 'template-extension'));

      $options['model'][] = l10n('locfiledit_empty_page');
      $options['model'][] = '----------------------';
      $i = 0;
      foreach (get_extents() as $pwg_template)
      {
        $value = PHPWG_ROOT_PATH . 'template-extension/' . $pwg_template;
        $options['model'][$value] =  'template-extension / ' . str_replace('/', ' / ', $pwg_template);
        $i++;
      }
      foreach (get_dirs($conf['themes_dir']) as $theme_id)
      {
        if ($i)
        {
          $options['model'][] = '----------------------';
          $i = 0;
        }
        $dir = $conf['themes_dir'] . '/' . $theme_id . '/template/';
        if (is_dir($dir) and $content = opendir($dir))
        {
          while ($node = readdir($content))
          {
            if (is_file($dir.$node) and get_extension($node) == 'tpl')
            {
              $value = $dir . $node;
              $options['model'][$value] = $theme_id . ' / ' . $node;
              $i++;
            }
          }
        }
      }
      if (end($options['model']) == '----------------------')
      {
        array_pop($options['model']);
      }
      // Assign variables to template
      $template->assign('create_tpl', array(
        'NEW_FILE_NAME' => $filename,
        'MODEL_OPTIONS' => $options['model'],
        'MODEL_SELECTED' => $selected['model'],
        'PARENT_OPTIONS' => $options['parent'],
        'PARENT_SELECTED' => $selected['parent']));
      break;
    }
    // List existing template extensions
    $selected = 0; 
    $options[] = l10n('locfiledit_choose_file');
    $options[] = '----------------------';
    foreach (get_extents() as $pwg_template)
    {
      $value = './template-extension/' . $pwg_template;
      $options[$value] =  str_replace('/', ' / ', $pwg_template);
      if ($edited_file == $value) $selected = $value;
    }
    if ($selected == 0 and !empty($edited_file))
    {
      $options[$edited_file] =  str_replace(array('./template-extension/', '/'), array('', ' / '), $edited_file);
      $selected = $edited_file;
    }
    $template->assign('css_lang_tpl', array(
      'OPTIONS' => $options,
      'SELECTED' => $selected,
      'NEW_FILE_URL' => $my_base_url.'&amp;tab=tpl&amp;newfile',
      'NEW_FILE_CLASS' => empty($edited_file) ? '' : 'top_right'));

    $editarea_options['syntax'] = 'html';
    break;

  case 'lang':
    $selected = 0; 
    $options[] = l10n('locfiledit_choose_file');
    $options[] = '----------------------';
    foreach (get_languages() as $language_code => $language_name)
    {
      $value = PHPWG_ROOT_PATH.'local/language/'.$language_code.'.lang.php';
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
    $editarea_options['syntax'] = 'php';
    break;
    
  case 'plug':
    $edited_file = PHPWG_PLUGINS_PATH . "PersonalPlugin/main.inc.php";
    $content_file = file_exists($edited_file) ?
      file_get_contents($edited_file) : $new_file['plug'];
    $editarea_options['syntax'] = 'php';
    break;
}

// +-----------------------------------------------------------------------+
// |                           Load backup file
// +-----------------------------------------------------------------------+
if (isset($_POST['restore']) and !is_adviser())
{
  $edited_file = $_POST['edited_file'];
  $content_file = file_get_contents(get_bak_file($edited_file));
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
    if ($page['tab'] == 'plug' and !is_dir(PHPWG_PLUGINS_PATH . 'PersonalPlugin'))
    {
      @mkdir(PHPWG_PLUGINS_PATH . "PersonalPlugin");
    }
    if (file_exists($edited_file))
    {
      @copy($edited_file, get_bak_file($edited_file));
      array_push($page['infos'], sprintf(l10n('locfiledit_saved_bak'), substr(get_bak_file($edited_file), 2)));
    }
    
    if ($file = @fopen($edited_file , "w"))
		{
      @fwrite($file , $content_file);
      @fclose($file);
      array_unshift($page['infos'], l10n('locfiledit_save_config'));
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
  if (file_exists(get_bak_file($edited_file)))
  {
    $template->assign('restore', true);
  }
  if (file_exists($edited_file))
  {
    $template->assign('restore_infos', true);
  }
  
}

$template->assign(array(
  'F_ACTION' => PHPWG_ROOT_PATH.'admin.php?page=plugin&amp;section=LocalFilesEditor%2Fadmin.php&amp;tab=' . $page['tab'],
  'LOCALEDIT_PATH' => LOCALEDIT_PATH,
  'LOAD_EDITAREA' => isset($conf['LocalFilesEditor']) ? $conf['LocalFilesEditor'] : 'off',
  'EDITAREA_OPTIONS' => $editarea_options));

$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');

?>