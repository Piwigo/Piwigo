<?php

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

if ((isset($_POST['edit'])) and !is_numeric($_POST['file_to_edit']))
{
  $edited_file = $_POST['file_to_edit'];
}
elseif (isset($_POST['edited_file']))
{
  $edited_file = $_POST['edited_file'];
}
elseif (isset($_GET['theme']) and in_array($_GET['theme'], array_keys(get_pwg_themes(true))))
{
  $edited_file = PHPWG_ROOT_PATH.PWG_LOCAL_DIR . 'css/'.$_GET['theme'].'-rules.css';
}
else
{
  $edited_file = PHPWG_ROOT_PATH.PWG_LOCAL_DIR . 'css/'.get_default_theme().'-rules.css';
}

if (file_exists($edited_file))
{
  $content_file = file_get_contents($edited_file);
}
else
{
  $content_file = "/* " . l10n('locfiledit_newfile') . " */\n\n";
}

$selected = 0; 
// $options[] = l10n('locfiledit_choose_file');
// $options[] = '----------------------';
$value = PHPWG_ROOT_PATH.PWG_LOCAL_DIR . "css/rules.css";

$options[$value] = (file_exists($value) ? '&#x2714;' : '&#x2718;').' local / css / rules.css';
if ($edited_file == $value)
{
  $selected = $value;
}

// themes are displayed in the same order as on screen
// [Administration > Configuration > Themes]

include_once(PHPWG_ROOT_PATH.'admin/include/themes.class.php');
$themes = new themes();
$themes->sort_fs_themes();
$default_theme = get_default_theme();
$db_themes = $themes->get_db_themes();

$db_theme_ids = array();
foreach ($db_themes as $db_theme)
{
  array_push($db_theme_ids, $db_theme['id']);
}

$active_themes = array();
$inactive_themes = array();

foreach ($themes->fs_themes as $theme_id => $fs_theme)
{
  if ($theme_id == 'default')
  {
    continue;
  }

  if (in_array($theme_id, $db_theme_ids))
  {
    if ($theme_id == $default_theme)
    {
      array_unshift($active_themes, $fs_theme);
    }
    else
    {
      array_push($active_themes, $fs_theme);
    }
  }
  else
  {
    array_push($inactive_themes, $fs_theme);
  }
}

$options[] = '';
$options[] = '----- '.l10n('Active Themes').' -----';
$options[] = '';
foreach ($active_themes as $theme)
{
  $value = PHPWG_ROOT_PATH.PWG_LOCAL_DIR . 'css/'.$theme['id'].'-rules.css';

  $options[$value] = (file_exists($value) ? '&#x2714;' : '&#x2718;').' '.$theme['name'];

  if ($default_theme == $theme['id'])
  {
    $options[$value].= ' ('.l10n('default').')';
  }
  
  if ($edited_file == $value)
  {
    $selected = $value;
  }
}

$options[] = '';
$options[] = '----- '.l10n('Inactive Themes').' -----';
$options[] = '';
foreach ($inactive_themes as $theme)
{
  $value = PHPWG_ROOT_PATH.PWG_LOCAL_DIR . 'css/'.$theme['id'].'-rules.css';

  $options[$value] = (file_exists($value) ? '&#x2714;' : '&#x2718;').' '.$theme['name'];
  
  if ($edited_file == $value)
  {
    $selected = $value;
  }
}

$template->assign('css_lang_tpl', array(
  'OPTIONS' => $options,
  'SELECTED' => $selected
  )
);

$codemirror_mode = 'text/css';

?>