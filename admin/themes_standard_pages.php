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

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

if (!is_webmaster())
{
  $page['warnings'][] = str_replace('%s', l10n('user_status_webmaster'), l10n('%s status is required to edit parameters.'));
}

// +-----------------------------------------------------------------------+
// | Update standard pages configuration                                   |
// +-----------------------------------------------------------------------+

$std_pgs_logo_options = array(
  "piwigo_logo",
  "custom_logo",
  "gallery_title",
  "none",
);

$std_pgs_skin_options = array(
  "default",
  "cadmium",
  "cobalt",
  "fuchsia",
  "green",
  "lime",
  "purple",
  "red",
  "sienna",
  "silver",
  "teal",
);

if (isset($_POST['submit']) and !empty($_POST) and is_webmaster())
{
  check_pwg_token();

  //use_standard_pages or not
  conf_update_param('use_standard_pages', !empty($_POST['use_standard_pages']), true);
  
  //save selected logo
  if(isset($_POST['std_pgs_display_logo']) and in_array($_POST['std_pgs_display_logo'], $std_pgs_logo_options))
  {
    conf_update_param('standard_pages_selected_logo', $_POST['std_pgs_display_logo'], true);
  }

  //save selected skin
  if(isset($_POST['std_pgs_selected_skin']) and in_array($_POST['std_pgs_selected_skin'], $std_pgs_skin_options))
  {
    conf_update_param('standard_pages_selected_skin', $_POST['std_pgs_selected_skin'], true);
  }
};

//Handle logo upload, allow png, jpg and svg
if (isset($_FILES['std_pgs_logo']) and !empty($_FILES['std_pgs_logo']['tmp_name']))
{
  $finfo = finfo_open(FILEINFO_MIME_TYPE);
  $mime_type = finfo_file($finfo, $_FILES['std_pgs_logo']['tmp_name']);
  finfo_close($finfo);

  // Allowed MIME types
  $allowed_mimes = array(
    'image/png' => 'png',
    'image/jpeg' => 'jpg',
    'image/svg+xml' => 'svg',
    'image/svg' => 'svg',
    'image/webp' => 'webp',
  );

  if (!in_array($mime_type, array_keys($allowed_mimes)))
  {
    $template->assign(
      array(
        'save_error' => 'Invalid image file.',
      )
    );
  }
  else
  {
    $upload_dir = PHPWG_ROOT_PATH . PWG_LOCAL_DIR . 'logo';
    if (mkgetdir($upload_dir, MKGETDIR_DEFAULT & ~MKGETDIR_DIE_ON_ERROR))
    {
      $pathinfo = pathinfo($_FILES['std_pgs_logo']['name']);

      $file_path = $upload_dir . '/' . str2url($pathinfo['filename']) . '.' . $allowed_mimes[ $mime_type ];

      conf_update_param('standard_pages_selected_logo_path', $file_path, true);

      if (move_uploaded_file($_FILES['std_pgs_logo']['tmp_name'], $file_path))
      {
        $logo['file'] = substr($file_path, strlen(PHPWG_ROOT_PATH));
      }
      else
      {
        $template->assign(
          array(
            'save_error' => "$file_path " . l10n('no write access'),
          )
        );
      }
    }
    else
    {
      $template->assign(
        array(
          'save_error' => sprintf(
            l10n('Add write access to the "%s" directory'),
            $upload_dir
          ),
        )
      );
    }
  }
}

//We want to now if any themes use standard pages and which ones
include_once(PHPWG_ROOT_PATH.'admin/include/themes.class.php');
$themes = new themes();
$themes->get_fs_themes();

$is_standard_pages_used = false;
$standard_pages_used_by = array();

foreach ($themes->fs_themes as $theme)
{
  if (isset($theme['use_standard_pages']) and $theme['use_standard_pages'])
  {
    $is_standard_pages_used = true;
    array_push($standard_pages_used_by, $theme['name']);
  }
}

// +-----------------------------------------------------------------------+
// |                          template output                              |
// +-----------------------------------------------------------------------+

//Send all info to template
$template->assign(
  array(
    'use_standard_pages' => conf_get_param('use_standard_pages', true),
    'std_pgs_selected_logo' => conf_get_param('standard_pages_selected_logo', 'piwigo_logo'),
    'std_pgs_logo_options' => $std_pgs_logo_options,
    'std_pgs_selected_skin' => conf_get_param('standard_pages_selected_skin', 'default'),
    'std_pgs_skin_options' => $std_pgs_skin_options,
    'is_standard_pages_used' => $is_standard_pages_used,
    'standard_pages_used_by' => $standard_pages_used_by,
    'std_pgs_selected_logo_path' => conf_get_param('standard_pages_selected_logo_path', null),
    'PWG_TOKEN' => get_pwg_token(),
  )
);

$template->assign('isWebmaster', (is_webmaster()) ? 1 : 0);

$template->set_filenames(array('themes' => 'themes_standard_pages.tpl'));

$template->assign('ADMIN_PAGE_TITLE', l10n('Themes'));

$template->assign_var_from_handle('ADMIN_CONTENT', 'themes');

?>