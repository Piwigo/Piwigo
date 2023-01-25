<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

if (!is_webmaster())
{
  $page['warnings'][] = str_replace('%s', l10n('user_status_webmaster'), l10n('%s status is required to edit parameters.'));
}

include_once(PHPWG_ROOT_PATH.'admin/include/languages.class.php');

$template->set_filenames(array('languages' => 'languages_installed.tpl'));

$base_url = get_root_url().'admin.php?page='.$page['page'];

$languages = new languages();
$languages->get_db_languages();

//--------------------------------------------------perform requested actions
check_input_parameter('action', $_GET, false, '/^(activate|deactivate|set_default|delete)$/');
check_input_parameter('language', $_GET, false, '/^('.join('|', array_keys($languages->fs_languages)).')$/');

if (isset($_GET['action']) and isset($_GET['language']) and is_webmaster())
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
    $tpl_languages[] = $language;
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

$template->assign('isWebmaster', (is_webmaster()) ? 1 : 0);
$template->assign('ADMIN_PAGE_TITLE', l10n('Languages'));
$template->assign('CONF_ENABLE_EXTENSIONS_INSTALL', $conf['enable_extensions_install']);

$template->assign_var_from_handle('ADMIN_CONTENT', 'languages');
?>
