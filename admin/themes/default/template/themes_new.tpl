<div class="titrePage">
  <h2>{'Add New Theme'|@translate}</h2>
</div>

{if not empty($new_themes)}
<div class="themeBoxes">
{foreach from=$new_themes item=theme name=themes_loop}
  <div class="themeBox">
    <div class="themeName">{$theme.name}</div>
    <div class="themeShot"><img src="{$theme.screenshot}" onerror="this.src='{$default_screenshot}'"></div>
    <div class="themeActions"><a href="{$theme.install_url}">{'Install'|@translate}</a></div>
  </div>
{/foreach}
</div> <!-- themeBoxes -->
{else}
<p>{'There is no other theme available.'|@translate}</p>
{/if}