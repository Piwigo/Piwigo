<div class="titrePage">
  <h2>{'Maintenance'|@translate}</h2>
</div>

<ul>
  {foreach from=$advanced_features item=feature}
    <li><a href="{$feature.URL}" {$TAG_INPUT_ENABLED}>{$feature.CAPTION}</a></li>
  {/foreach}
</ul>

<ul>
  <li><a href="{$U_MAINT_CATEGORIES}" {$TAG_INPUT_ENABLED}>{'Update categories informations'|@translate}</a></li>
  <li><a href="{$U_MAINT_IMAGES}" {$TAG_INPUT_ENABLED}>{'Update images informations'|@translate}</a></li>
  <li><a href="{$U_MAINT_DATABASE}" {$TAG_INPUT_ENABLED}>{'Repair and optimize database'|@translate}</a></li>
</ul>

<ul>
  <li><a href="{$U_MAINT_HISTORY_DETAIL}" onclick="return confirm('{'Purge history detail'|@translate|@escape:'javascript'}');" {$TAG_INPUT_ENABLED}>{'Purge history detail'|@translate}</a></li>
  <li><a href="{$U_MAINT_HISTORY_SUMMARY}" onclick="return confirm('{'Purge history summary'|@translate|@escape:'javascript'}');" {$TAG_INPUT_ENABLED}>{'Purge history summary'|@translate}</a></li>
  <li><a href="{$U_MAINT_SESSIONS}" {$TAG_INPUT_ENABLED}>{'Purge sessions'|@translate}</a></li>
  <li><a href="{$U_MAINT_FEEDS}" {$TAG_INPUT_ENABLED}>{'Purge never used notification feeds'|@translate}</a></li>
  <li><a href="{$U_MAINT_SEARCH}"onclick="return confirm('{'Purge search history'|@translate|@escape:'javascript'}');" {$TAG_INPUT_ENABLED}>{'Purge search history'|@translate}</a></li>
  <li><a href="{$U_MAINT_COMPILED_TEMPLATES}" {$TAG_INPUT_ENABLED}>{'Purge compiled templates'|@translate}</a></li>
</ul>

<ul>
  <li><a href="{$U_MAINT_C13Y}" {$TAG_INPUT_ENABLED}>{'Reinitialize check integrity'|@translate}</a></li>
</ul>
