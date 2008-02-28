{* $Id$ *}
<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{$U_HELP}" onclick="popuphelp(this.href); return false;" title="{'Help'|@translate}"><img src="{$themeconf.icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{'cat_options_title'|@translate} {$TABSHEET_TITLE}</h2>
  {include file='admin/tabsheet.tpl'}
</div>

<form method="post" action="{$F_ACTION}" id="cat_options">
  <fieldset>
    <legend>{$L_SECTION}</legend>
    {$DOUBLE_SELECT}
  </fieldset>
</form>

