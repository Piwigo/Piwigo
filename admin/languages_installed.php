<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
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

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/languages.class.php');

$template->set_filenames(array('languages' => 'languages_installed.tpl'));

$base_url = get_root_url().'admin.php?page='.$page['page'];

$languages = new languages();
$languages->get_db_languages();

//--------------------------------------------------perform requested actions
if (isset($_GET['action']) and isset($_GET['language']))
{
  $page['errors'] = $languages->perform_action($_GET['action'], $_GET['language']);

  if (empty($page['errors']))
  {
    redirect($base_url);
  }
}

// +-----------------------------------------------------------------------+
// |                     start template output                             |
// +-----------------------------------------------------------------------+
$default_language = get_default_language();

$tpl_languages = array();

foreach($languages->fs_languages as $language_id => $language)
{
  $language['u_action'] = add_url_params($base_url, array('language' => $language_id));

  if (in_array($language_id, array_keys($languages->db_languages)))
  {
    $language['state'] = 'active';
    $language['deactivable'] = true;

    if (count($languages->db_languages) <= 1)
    {
      $language['deactivable'] = false;
      $language['deactivate_tooltip'] = l10n('Impossible to deactivate this language, you need at least one language.');
    }

    if ($language_id == $default_language)
    {
      $language['deactivable'] = false;
      $language['deactivate_tooltip'] = l10n('Impossible to deactivate this language, first set another language as default.');
    }
  }
  else
  {
    $language['state'] = 'inactive';
  }

  if ($language_id == $default_language)
  {
    $language['is_default'] = true;
    array_unshift($tpl_languages, $language);
  }
  else
  {
    $language['is_default'] = false;
    array_push($tpl_languages, $language);
  }
}

$template->assign(
  array(
    'languages' => $tpl_languages,
    )
  );
$template->append('language_states', 'active');
$template->append('language_states', 'inactive');


$missing_language_ids = array_diff(
    array_keys($languages->db_languages),
    array_keys($languages->fs_languages)
  );

foreach($missing_language_ids as $language_id)
{
  $query = '
UPDATE '.USER_INFOS_TABLE.'
  SET language = \''.get_default_language().'\'
  WHERE language = \''.$language_id.'\'
;';
  pwg_query($query);

  $query = '
DELETE
  FROM '.LANGUAGES_TABLE.'
  WHERE id= \''.$language_id.'\'
;';
  pwg_query($query);
}

$template->assign_var_from_handle('ADMIN_CONTENT', 'languages');
?>
