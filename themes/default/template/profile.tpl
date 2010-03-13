<div id="content" class="content">

{if isset($errors)}
<div class="errors">
  <ul>
    {foreach from=$errors item=error}
    <li>{$error}</li>
    {/foreach}
  </ul>
</div>
{/if}

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{$U_HOME}" title="{'Home'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/Home.png" class="button" alt="{'Home'|@translate}"></a></li>
    </ul>
    <h2>{'Profile'|@translate}</h2>
  </div>

{$PROFILE_CONTENT}
</div> <!-- content -->
