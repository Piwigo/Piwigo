{* 

          Warning : This is the admin pages header only 
          don't confuse with the public page header

*}
<!DOCTYPE html>
<html lang="{$lang_info.code}" dir="{$lang_info.direction}">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$CONTENT_ENCODING}">
<meta name="generator" content="Piwigo (aka PWG), see piwigo.org">
<title>{$GALLERY_TITLE} :: {$PAGE_TITLE}</title>
<link rel="shortcut icon" type="image/x-icon" href="{$ROOT_URL}{$themeconf.icon_dir}/favicon.ico">

{get_combined_css}
{foreach from=$themes item=theme}
{if $theme.load_css}
{combine_css path="admin/themes/`$theme.id`/theme.css" order=-10}
{/if}
{if !empty($theme.local_head)}{include file=$theme.local_head load_css=$theme.load_css}{/if}
{/foreach}

<!-- BEGIN get_combined_scripts -->
{get_combined_scripts load='header'}
<!-- END get_combined_scripts -->

{combine_script id='jquery' path='themes/default/js/jquery.min.js'}

<!--[if lt IE 7]>
<script type="text/javascript" src="{$ROOT_URL}themes/default/js/pngfix.js"></script>
<![endif]-->

{if not empty($head_elements)}
{foreach from=$head_elements item=elt}
{$elt}
{/foreach}
{/if}

</head>

<body id="{$BODY_ID}">

<div id="the_page">

{if not empty($header_msgs)}
<div class="header_msgs">
  {foreach from=$header_msgs item=elt}
  {$elt}
  {/foreach}
</div>
{/if}

<div id="pwgHead">
  <h1>
    <a href="{$U_RETURN}" title="{'Visit Gallery'|@translate}">
      <img src="{$ROOT_URL}admin/themes/{$theme.id}/icon/home.png" alt="{'Home'|@translate}">
      {$GALLERY_TITLE}
    </a>
  </h1>

  <div id="headActions">
    {'Hello'|@translate} {$USERNAME} :
    <a href="{$U_RETURN}">{'Visit Gallery'|@translate}</a> |
    <a href="{$U_CHANGE_THEME}" title="{'Switch to clear or dark colors for administration'|@translate}">{'Change Admin Colors'|@translate}</a> |
    <a href="{$U_FAQ}" title="{'Instructions to use Piwigo'|@translate}">{'Help Me'|@translate}</a> |
    <a href="{$U_LOGOUT}">{'Logout'|@translate}</a>
  </div>
</div>

<div style="clear:both;"></div>

{if not empty($header_notes)}
<div class="header_notes">
  {foreach from=$header_notes item=elt}
  {$elt}
  {/foreach}
</div>
{/if}

<div id="pwgMain">