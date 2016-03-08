<?php
/*
Theme Name: Sylvia
Version: auto
Description: Dark background, flora and pink decorations.
Theme URI: http://piwigo.org/ext/extension_view.php?eid=368
Author: Piwigo team
Author URI: http://piwigo.org
*/
$themeconf = array(
  'name'         => 'Sylvia',
  'parent'        => 'default',
  'icon_dir'      => 'themes/Sylvia/icon',
  'mime_icon_dir' => 'themes/Sylvia/icon/mimetypes/',
);
/************************************ mainpage_categories.tpl ************************************/
add_event_handler('loc_end_index_category_thumbnails', 'Sylvia_album');
function Sylvia_album($tpl_thumbnails_var)
{
    global $template;
    $template->set_prefilter('index_category_thumbnails', 'Sylvia_album_prefilter');
    return $tpl_thumbnails_var;
}
function Sylvia_album_prefilter($content, &$smarty)
{
  $search = '#\{html_style\}#';
  $replacement = '{html_style}
.thumbnailCategory .description .text{ldelim}
	height: {$derivative_params->max_height()-30}px;
}';
  $content = preg_replace($search, $replacement, $content);
  $search = '#\.thumbnailCategory[\t ]*.description\{ldelim\}[\s]*height:[\t ]*\{\$derivative_params->max_height\(\)\+5#';
  $replacement = '.thumbnailCategory .description{ldelim}
	height: {$derivative_params->max_height()+15';
  $content = preg_replace($search, $replacement, $content);
  return $content;
}
?>
