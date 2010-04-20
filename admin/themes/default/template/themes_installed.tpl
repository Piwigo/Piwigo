<div class="titrePage">
  <h2>{'Installed Themes'|@translate}</h2>
</div>

<div id="themesContent">

<fieldset>
<legend>{'Active Themes'|@translate}</legend>
{if isset($active_themes)}
<div class="themeBoxes">
{foreach from=$active_themes item=theme}
  <div class="themeBox{if $theme.is_default} themeDefault{/if}">
    <div class="themeName">{$theme.name}{if $theme.is_default} <em>({'default'|@translate})</em>{/if}</div>
    <div class="themeShot"><img src="{$theme.screenshot}" alt=""></div>
    <div class="themeActions">
      <div>
{if $theme.deactivable}
      <a href="{$deactivate_baseurl}{$theme.id}" title="{'Forbid this theme to users'|@translate}">{'Deactivate'|@translate}</a>
{else}
      <span title="{$theme.deactivate_tooltip}">{'Deactivate'|@translate}</span>
{/if}
      
{if not $theme.is_default}
      | <a href="{$set_default_baseurl}{$theme.id}" title="{'Set as default theme for unregistered and new users'|@translate}">{'Default'|@translate}</a>
{/if}
{if isset($theme.admin_uri)}
      <br><a href="{$theme.admin_uri}" title="{'Configuration'|@translate}">{'Configuration'|@translate}</a>
{/if}
      </div>
    </div> <!-- themeActions -->
  </div>
{/foreach}
</div> <!-- themeBoxes -->
{/if}
</fieldset>

{if isset($inactive_themes)}
<fieldset>
<legend>{'Inactive Themes'|@translate}</legend>
<div class="themeBoxes">
{foreach from=$inactive_themes item=theme}
  <div class="themeBox">
    <div class="themeName">{$theme.name}</div>
    <div class="themeShot"><img src="{$theme.screenshot}" alt=""></div>
    <div class="themeActions">
      <div>
  {if $theme.activable}
      <a href="{$activate_baseurl}{$theme.id}" title="{'Make this theme available to users'|@translate}">{'Activate'|@translate}</a>
  {else}
      <span title="{$theme.activate_tooltip}">{'Activate'|@translate}</span>
  {/if}

      |

  {if $theme.deletable}
      <a href="{$delete_baseurl}{$theme.id}" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');" title="{'Delete this theme'|@translate}">{'Delete'|@translate}</a>
  {else}
      <span title="{$theme.delete_tooltip}">{'Delete'|@translate}</span>
  {/if}
      </div>
    </div>
    
  </div>
{/foreach}
</div> <!-- themeBoxes -->
</fieldset>
{/if}

</div> <!-- themesContent -->
