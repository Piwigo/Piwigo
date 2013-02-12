<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

include_once(PHPWG_ROOT_PATH.'admin/include/themes.class.php');
$themes = new themes();

if (isset($_POST['edit']))
{
  $_POST['theme'] = $_POST['theme_select'];
}

if (isset($_POST['theme']) and '~common~' == $_POST['theme'])
{
  $page['theme'] = $_POST['theme'];
  $edited_file = PHPWG_ROOT_PATH.PWG_LOCAL_DIR.'css/rules.css';
}
else
{
  if (isset($_GET['theme']))
  {
    $page['theme'] = $_GET['theme'];
  }
  elseif (isset($_POST['theme']))
  {
    $page['theme'] = $_POST['theme'];
  }
  
  if (!isset($page['theme']) or !in_array($page['theme'], array_keys($themes->fs_themes)))
  {
    $page['theme'] = get_default_theme();
  }
  
  $edited_file = PHPWG_ROOT_PATH.PWG_LOCAL_DIR . 'css/'.$page['theme'].'-rules.css';
}

$template->assign('theme', $page['theme']);

if (file_exists($edited_file))
{
  $content_file = file_get_contents($edited_file);
}
else
{
  $content_file = "/* " . l10n('locfiledit_newfile') . " */\n\n";
}

$selected = 0; 
$value = '~common~';
$file = PHPWG_ROOT_PATH.PWG_LOCAL_DIR . 'css/rules.css';

$options[$value] = (file_exists($file) ? '&#x2714;' : '&#x2718;').' local / css / rules.css';
if ($page['theme'] == $value)
{
  $selected = $value;
}

// themes are displayed in the same order as on screen
// [Administration > Configuration > Themes]

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

$active_theme_options = array();
foreach ($active_themes as $theme)
{
  $file = PHPWG_ROOT_PATH.PWG_LOCAL_DIR . 'css/'.$theme['id'].'-rules.css';

  $label = (file_exists($file) ? '&#x2714;' : '&#x2718;').' '.$theme['name'];

  if ($default_theme == $theme['id'])
  {
    $label.= ' ('.l10n('default').')';
  }

  $active_theme_options[$theme['id']] = $label;
  
  if ($theme['id'] == $page['theme'])
  {
    $selected = $theme['id'];
  }
}

if (count($active_theme_options) > 0)
{
  $options[l10n('Active Themes')] = $active_theme_options;
}

$inactive_theme_options = array();
foreach ($inactive_themes as $theme)
{
  $file = PHPWG_ROOT_PATH.PWG_LOCAL_DIR . 'css/'.$theme['id'].'-rules.css';

  $inactive_theme_options[$theme['id']] = (file_exists($file) ? '&#x2714;' : '&#x2718;').' '.$theme['name'];
  
  if ($theme['id'] == $page['theme'])
  {
    $selected = $theme['id'];
  }
}

if (count($inactive_theme_options) > 0)
{
  $options[l10n('Inactive Themes')] = $inactive_theme_options;
}

$template->assign(
  'css_lang_tpl',
  array(
    'SELECT_NAME' => 'theme_select',
    'OPTIONS' => $options,
    'SELECTED' => $selected
    )
);

$codemirror_mode = 'text/css';
?>