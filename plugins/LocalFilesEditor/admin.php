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

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');
include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
include_once(LOCALEDIT_PATH.'include/functions.inc.php');
load_language('plugin.lang', LOCALEDIT_PATH);
$my_base_url = get_root_url().'admin.php?page=plugin-'.basename(dirname(__FILE__));

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
               $my_base_url.'-localconf');
$tabsheet->add('css',
               l10n('locfiledit_onglet_css'),
               $my_base_url.'-css');
$tabsheet->add('tpl',
               l10n('locfiledit_onglet_tpl'),
               $my_base_url.'-tpl');
$tabsheet->add('lang',
               l10n('locfiledit_onglet_lang'),
               $my_base_url.'-lang');
$tabsheet->add('plug',
               l10n('locfiledit_onglet_plug'),
               $my_base_url.'-plug');
$tabsheet->select($page['tab']);
$tabsheet->assign();

include_once(LOCALEDIT_PATH.'include/'.$page['tab'].'.inc.php');

// +-----------------------------------------------------------------------+
// |                           Load backup file
// +-----------------------------------------------------------------------+
if (isset($_POST['restore']))
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
if (isset($_POST['submit']))
{
  if (!is_webmaster())
  {
    array_push($page['errors'], l10n('locfiledit_webmaster_only'));
  }
  else
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
        $template->delete_compiled_templates();
      }
      else
      {
        array_push($page['errors'], l10n('locfiledit_cant_save'));
      }
    }
  }
}

// +-----------------------------------------------------------------------+
// |                            template initialization
// +-----------------------------------------------------------------------+
$template->set_filenames(array(
    'plugin_admin_content' => dirname(__FILE__) . '/template/admin.tpl'));

if (!empty($edited_file))
{
  if (!empty($page['errors']))
	{
    $content_file = stripslashes($_POST['text']);
  }
  $template->assign('zone_edit',
    array(
      'EDITED_FILE' => $edited_file,
      'CONTENT_FILE' => htmlspecialchars($content_file),
      'FILE_NAME' => trim($edited_file, './\\')
    )
  );
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
  'F_ACTION' => PHPWG_ROOT_PATH.'admin.php?page=plugin-LocalFilesEditor-'.$page['tab'],
  'LOCALEDIT_PATH' => LOCALEDIT_PATH,
  'CODEMIRROR_MODE' => @$codemirror_mode
  )
);

$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');

?>