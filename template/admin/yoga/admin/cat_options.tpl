<!-- $Id: cat_options.tpl 1244 2006-04-21 21:07:19Z nikrou $ -->
<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{lang:title_cat_options}</h2>
</div>

<form method="post" action="{F_ACTION}" id="cat_options">
  <fieldset>
    <legend>{L_SECTION}</legend>
    {DOUBLE_SELECT}
  </fieldset>
</form>

<p class="information">{L_CAT_OPTIONS_INFO}</p>

