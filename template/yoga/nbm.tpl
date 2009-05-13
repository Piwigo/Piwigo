{* $Id: /piwigo/trunk/template/yoga/nbm.tpl 7025 2009-03-09T19:41:45.898712Z nikrou  $ *}

<div id="content" class="content">
  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{$U_HOME}" title="{'Go through the gallery as a visitor'|@translate}"><img src="{$themeconf.icon_dir}/home.png" class="button" alt="{'home'|@translate}"></a></li>
    </ul>
    <h2>{'nbm_item_notification'|@translate}</h2>
  </div>

  {if not empty($errors)}
  <div class="errors">
    <ul>
      {foreach from=$errors item=error}
      <li>{$error}</li>
      {/foreach}
    </ul>
  </div>
  {/if}

  {if not empty($infos)}
  <div class="infos">
    <ul>
      {foreach from=$infos item=info}
      <li>{$info}</li>
      {/foreach}
    </ul>
  </div>
  {/if}

</div>
