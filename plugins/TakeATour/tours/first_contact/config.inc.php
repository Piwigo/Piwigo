<?php
if ( defined('IN_ADMIN') and IN_ADMIN )
{
/* first contact */
add_event_handler('loc_end_element_set_global', 'TAT_FC_14');
add_event_handler('loc_end_picture_modify', 'TAT_FC_16');
add_event_handler('loc_end_picture_modify', 'TAT_FC_17');
add_event_handler('loc_end_cat_modify', 'TAT_FC_23');
add_event_handler('loc_end_themes_installed', 'TAT_FC_35');
}

function TAT_FC_14()
{
  global $template;
  $template->set_prefilter('batch_manager_global', 'TAT_FC_14_prefilter');
}
function TAT_FC_14_prefilter ($content, &$smarty)
{
  $search = '<span class="wrap2';
  $replacement = '{counter print=false assign=TAT_FC_14}<span {if $TAT_FC_14==1}id="TAT_FC_14"{/if} class="wrap2';
  $content = str_replace($search, $replacement, $content);
  $search = 'target="_blank">{\'Edit\'';
  $replacement = '>{\'Edit\'';
  return str_replace($search, $replacement, $content);
}
function TAT_FC_16()
{
  global $template;
  $template->set_prefilter('picture_modify', 'TAT_FC_16_prefilter');
}
function TAT_FC_16_prefilter ($content, &$smarty)
{
  $search = '<strong>{\'Linked albums\'|@translate}</strong>';
  $replacement = '<span id="TAT_FC_16"><strong>{\'Linked albums\'|@translate}</strong></span>';
  return str_replace($search, $replacement, $content);
}
function TAT_FC_17()
{
  global $template;
  $template->set_prefilter('picture_modify', 'TAT_FC_17_prefilter');
}
function TAT_FC_17_prefilter ($content, &$smarty)
{
  $search = '<strong>{\'Representation of albums\'|@translate}</strong>';
  $replacement = '<span id="TAT_FC_17"><strong>{\'Representation of albums\'|@translate}</strong></span>';
  return str_replace($search, $replacement, $content);
}
function TAT_FC_23()
{
  global $template;
  $template->set_prefilter('album_properties', 'TAT_FC_23_prefilter');
}
function TAT_FC_23_prefilter ($content, &$smarty)
{
  $search = '<strong>{\'Lock\'|@translate}</strong>';
  $replacement = '<span id="TAT_FC_23"><strong>{\'Lock\'|@translate}</strong></span>';
  return str_replace($search, $replacement, $content);
}
function TAT_FC_35()
{
  global $template;
  $template->set_prefilter('themes', 'TAT_FC_35_prefilter');
}
function TAT_FC_35_prefilter ($content, &$smarty)
{
  $search = '<a href="{$set_default_baseurl}{$theme.ID}" class="tiptip"';
  $replacement = '{counter print=false assign=TAT_FC_35}<a href="{$set_default_baseurl}{$theme.ID}" class="tiptip" {if $TAT_FC_35==1}id="TAT_FC_35"{/if}';
  return str_replace($search, $replacement, $content);
}

?>