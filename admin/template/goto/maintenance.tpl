{* $Id$ *}
<div class="titrePage">
  <h2>{'Maintenance'|@translate}</h2>
</div>

<ul>
  <li><a href="{$U_MAINT_CATEGORIES}" {$TAG_INPUT_ENABLED}>{'update categories informations'|@translate}</a></li>
  <li><a href="{$U_MAINT_IMAGES}" {$TAG_INPUT_ENABLED}>{'update images informations'|@translate}</a></li>
  <li><a href="{$U_MAINT_DATABASE}" {$TAG_INPUT_ENABLED}>{'repair and optimize database'|@translate}</a></li>
</ul>

<ul>
  <li><a href="{$U_MAINT_HISTORY_DETAIL}" onclick="return confirm('{'Are you sure?'|@translate}');" {$TAG_INPUT_ENABLED}>{'purge history detail'|@translate}</a></li>
  <li><a href="{$U_MAINT_HISTORY_SUMMARY}" onclick="return confirm('{'Are you sure?'|@translate}');" {$TAG_INPUT_ENABLED}>{'purge history summary'|@translate}</a></li>
  <li><a href="{$U_MAINT_SESSIONS}" {$TAG_INPUT_ENABLED}>{'purge sessions'|@translate}</a></li>
  <li><a href="{$U_MAINT_FEEDS}" {$TAG_INPUT_ENABLED}>{'purge never used notification feeds'|@translate}</a></li>
  <li><a href="{$U_MAINT_SEARCH}"onclick="return confirm('{'Are you sure?'|@translate}');" {$TAG_INPUT_ENABLED}>{'Purge search history'|@translate}</a></li>
  <li><a href="{$U_MAINT_COMPILED_TEMPLATES}" {$TAG_INPUT_ENABLED}>{'Purge compiled templates'|@translate}</a></li>
</ul>

<ul>
  <li><a href="{$U_MAINT_C13Y}" {$TAG_INPUT_ENABLED}>{'c13y_maintenance'|@translate}</a></li>
</ul>
