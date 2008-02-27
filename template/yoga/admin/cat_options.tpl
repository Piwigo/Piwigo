<!-- DEV TAG: not smarty migrated -->
<!-- $Id$ -->
<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{lang:cat_options_title} {TABSHEET_TITLE}</h2>
  {TABSHEET}
</div>

<form method="post" action="{F_ACTION}" id="cat_options">
  <fieldset>
    <legend>{L_SECTION}</legend>
    {DOUBLE_SELECT}
  </fieldset>
</form>

<p class="information">{L_CAT_OPTIONS_INFO}</p>

