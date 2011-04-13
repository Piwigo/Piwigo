<?php

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

$edited_file = isset($_POST['edited_file']) ? $_POST['edited_file'] : '';
$content_file = '';

if ((isset($_POST['edit'])) and !is_numeric($_POST['file_to_edit']))
{
  $edited_file = $_POST['file_to_edit'];
  if (file_exists($edited_file))
  {
    $content_file = file_get_contents($edited_file);
  }
  else
  {
    $content_file = "/* " . l10n('locfiledit_newfile') . " */\n\n";
  }
}

$selected = 0; 
$options[] = l10n('locfiledit_choose_file');
$options[] = '----------------------';
$value = PHPWG_ROOT_PATH.PWG_LOCAL_DIR . "css/rules.css";
$options[$value] = 'local / css / rules.css';
if ($edited_file == $value) $selected = $value;
$options[] = '----------------------';

foreach (get_dirs($conf['themes_dir']) as $theme_id)
{
  $value = PHPWG_ROOT_PATH.PWG_LOCAL_DIR . 'css/'.$theme_id.'-rules.css';
  $options[$value] = 'local / css / '.$theme_id.'-rules.css';
  if ($edited_file == $value)
    $selected = $value;
}

$template->assign('css_lang_tpl', array(
  'OPTIONS' => $options,
  'SELECTED' => $selected
  )
);

$codemirror_mode = 'text/css';

?>