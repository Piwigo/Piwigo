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
    $content_file = "<?php\n\n/* ".l10n('locfiledit_newfile')." */\n\n\n\n\n?>";
  }
}

$selected = 0; 
$options[] = l10n('locfiledit_choose_file');
$options[] = '----------------------';
foreach (get_languages() as $language_code => $language_name)
{
  $value = PHPWG_ROOT_PATH.PWG_LOCAL_DIR.'language/'.$language_code.'.lang.php';
  if ($edited_file == $value)
  {
    $selected = $value;
    $template->assign('show_default', array(
      array(
        'URL' => LOCALEDIT_PATH.'show_default.php?file=language/'.$language_code.'/common.lang.php',
        'FILE' => 'common.lang.php'
        ),
      array(
        'URL' => LOCALEDIT_PATH.'show_default.php?file=language/'.$language_code.'/admin.lang.php',
        'FILE' => 'admin.lang.php'
        )
      )
    );
  }
  $options[$value] = $language_name;
}

$template->assign('css_lang_tpl', array(
    'OPTIONS' => $options,
    'SELECTED' => $selected
    )
  );

$codemirror_mode = 'application/x-httpd-php';

?>