<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

$edited_file = '';

if (isset($_POST['edit']))
{
  $_POST['template'] = $_POST['file_to_edit'];
}

if (!empty($_POST['template']))
{
  if (preg_match('#\.\./#', $_POST['template']))
  {
    die('Hacking attempt! template extension must be in template-extension directory');
  }

  if (!preg_match('#\.tpl$#', $_POST['template']))
  {
    die('Hacking attempt! template extension must be a *.tpl file');
  }

  $template->assign('template', $_POST['template']);
  
  $edited_file = './template-extension/'.$_POST['template'];
}

$content_file = '';
if (file_exists($edited_file))
{
  $content_file = file_get_contents($edited_file);
}

$newfile_page = isset($_GET['newfile']);

// Edit new tpl file
if (isset($_POST['create_tpl']))
{
  $filename = $_POST['tpl_name'];
  if (empty($filename))
  {
    $page['errors'][] = l10n('locfiledit_empty_filename');
  }
  if (get_extension($filename) != 'tpl')
  {
    $filename .= '.tpl';
  }
  if (!preg_match('/^[a-zA-Z0-9-_.]+$/', $filename))
  {
    $page['errors'][] = l10n('locfiledit_filename_error');
  }
  if (is_numeric($_POST['tpl_model']) and $_POST['tpl_model'] != '0')
  {
    $page['errors'][] = l10n('locfiledit_model_error');
  }
  if (file_exists($_POST['tpl_parent'] . '/' . $filename))
  {
    $page['errors'][] = l10n('locfiledit_file_already_exists');
  }
  if (!empty($page['errors']))
  {
    $newfile_page = true;
  }
  else
  {
    $template->assign('template', $filename);
    $edited_file = $_POST['tpl_parent'] . '/' . $filename;
    $content_file = ($_POST['tpl_model'] == '0') ? '' : file_get_contents($_POST['tpl_model']);
  }
}

if ($newfile_page)
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
    'PARENT_SELECTED' => $selected['parent']
    )
  );
}
else
{
  // List existing template extensions
  $selected = 0; 
  $options[] = l10n('locfiledit_choose_file');
  $options[] = '----------------------';
  foreach (get_extents() as $pwg_template)
  {
    $value = $pwg_template;
    $options[$value] =  str_replace('/', ' / ', $pwg_template);
    if ($edited_file == $value) $selected = $value;
  }
  if ($selected == 0 and !empty($edited_file))
  {
    $options[$edited_file] =  str_replace(array('./template-extension/', '/'), array('', ' / '), $edited_file);
    $selected = $edited_file;
  }
  $template->assign(
    'css_lang_tpl',
    array(
      'SELECT_NAME' => 'file_to_edit',
      'OPTIONS' => $options,
      'SELECTED' => $selected,
      'NEW_FILE_URL' => $my_base_url.'-tpl&amp;newfile',
      'NEW_FILE_CLASS' => empty($edited_file) ? '' : 'top_right'
      )
    );
}

$codemirror_mode = 'text/html';

?>