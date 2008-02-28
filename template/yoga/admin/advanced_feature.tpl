{* $Id$ *}
<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{$U_HELP}" onclick="popuphelp(this.href); return false;" title="{'Help'|@translate}"><img src="{$themeconf.icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{'Advanced_features'|@translate}</h2>
</div>

<ul>
  {foreach from=$advanced_features item=feature}
    <li><a href="{$feature.URL}" {$TAG_INPUT_ENABLED}>{$feature.CAPTION}</a></li>
  {/foreach}
</ul>
