<div class="titrePage">
  <h2>{'Install New Theme'|@translate}</h2>
</div>

{if isset($themes)}
<div id="themesBox">
{foreach from=$new_themes item=theme name=themes_loop}
  <div class="themeBox">
    <div class="themeName">{$theme.name}</div>
    <div class="themeShot"><img src="{$theme.src}"></div>
    <div class="themeActions"><a href="{$theme.install_url}">Install</a></div>
  </div>
{/foreach}
</div> <!-- themesBox -->
{/if}