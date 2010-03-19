<div class="titrePage">
  <h2>{'Add New Theme'|@translate}</h2>
</div>

{if isset($themes)}
<div class="themeBoxes">
{foreach from=$new_themes item=theme name=themes_loop}
  <div class="themeBox">
    <div class="themeName">{$theme.name}</div>
    <div class="themeShot"><img src="{$theme.screenshot}"></div>
    <div class="themeActions"><a href="{$theme.install_url}">{'Install'|@translate}</a></div>
  </div>
{/foreach}
</div> <!-- themeBoxes -->
{/if}