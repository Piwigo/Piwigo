<!-- $Id$ -->
<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{lang:Maintenance}</h2>
</div>

<ul>
  <li><a href="{U_MAINT_CATEGORIES}" {TAG_INPUT_ENABLED}>{lang:update categories informations}</a></li>
  <li><a href="{U_MAINT_IMAGES}" {TAG_INPUT_ENABLED}>{lang:update images informations}</a></li>
  <li><a href="{U_MAINT_HISTORY}" {TAG_INPUT_ENABLED}>{lang:purge history}</a></li>
  <li><a href="{U_MAINT_SESSIONS}" {TAG_INPUT_ENABLED}>{lang:purge sessions}</a></li>
  <li><a href="{U_MAINT_FEEDS}" {TAG_INPUT_ENABLED}>{lang:purge never used notification feeds}</a></li>
</ul>
