<?php
$themeconf = array(
  'template' => 'yoga',
  'theme' => 'wipi',
  'template_dir' => 'template/yoga',
  'icon_dir' => 'template/yoga/icon',
  'admin_icon_dir' => 'template/yoga/icon/admin',
  'mime_icon_dir' => 'template/yoga/icon/mimetypes/',
  'local_head' => '<!-- no theme specific head content -->',
);
if ( !isset($lang['Theme: wipi']) )
{
  $lang['Theme: wipi'] = 'The site is displayed with wipi theme based ' .
  ' on yoga template, a standard template/theme of PhpWebgallery.';
}
?>
