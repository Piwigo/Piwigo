<div class="titrePage">
  <h2>{'Installed Themes'|@translate}</h2>
</div>

<div id="themesContent">

<fieldset>
<legend>Active Themes</legend>
{if isset($active_themes)}
<div class="themeBoxes">
{foreach from=$active_themes item=theme}
  <div class="themeBox{if $theme.is_default} themeDefault{/if}">
    <div class="themeName">{$theme.name}{if $theme.is_default} <em>(default)</em>{/if}</div>
    <div class="themeShot"><img src="{$theme.screenshot}"></div>
    <div class="themeActions">
      <a href="{$deactivate_baseurl}{$theme.id}" title="{'Forbid this theme to users'|@translate}">{'Deactivate'|@translate}</a>
{if not $theme.is_default}
      | <a href="{$set_default_baseurl}{$theme.id}" title="{'Set as default theme for unregistered and new users'|@translate}">{'Default'|@translate}</a>
{/if}
    </div> <!-- themeActions -->
  </div>
{/foreach}
</div> <!-- themeBoxes -->
{/if}
</fieldset>

{if isset($inactive_themes)}
<fieldset>
<legend>Inactive Themes</legend>
<div class="themeBoxes">
{foreach from=$inactive_themes item=theme}
  <div class="themeBox">
    <div class="themeName">{$theme.name}</div>
    <div class="themeShot"><img src="{$theme.screenshot}"></div>
    <div class="themeActions">
      <a href="{$activate_baseurl}{$theme.id}" title="{'Make this theme available to users'|@translate}">{'Activate'|@translate}</a>
      |
  {if $theme.deletable}
      <a href="{$delete_baseurl}{$theme.id}" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');" title="{'Delete this theme'|@translate}">{'Delete'|@translate}</a>
  {else}
      <span title="{$theme.delete_tooltip}">{'Delete'|@translate}</span>
  {/if}
    </div>
    
  </div>
{/foreach}
</div> <!-- themeBoxes -->
</fieldset>
{/if}

</div> <!-- themesContent -->