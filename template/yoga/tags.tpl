{* $Id$ *}
<div id="content">

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{$U_HOME}" title="{'return to homepage'|@translate}"><img src="{$themeconf.icon_dir}/home.png" class="button" alt="{'home'|@translate}"/></a></li>
    </ul>
    <h2>{'Tags'|@translate}</h2>
  </div>

  {if isset($tags)}
  <ul id="fullTagCloud">
    {foreach from=$tags item=tag}
    <li><a href="{$tag.URL}" class="{$tag.CLASS}" title="{$tag.TITLE}">{$tag.NAME}</a></li>
    {/foreach}
  </ul>
  {/if}

</div> <!-- content -->
