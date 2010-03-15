<div class="titrePage">
  <h2>{'Install New Theme'|@translate}</h2>
</div>

{if isset($themes)}
<ul>
{foreach from=$new_themes item=theme name=themes_loop}
  <li>
    <img src="{$theme.src}"> {$theme.name} <a href="{$theme.install_url}">Install</a>
  </li>
{/foreach}
</ul>
{/if}