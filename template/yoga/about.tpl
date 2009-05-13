{* $Id: /piwigo/trunk/template/yoga/about.tpl 7025 2009-03-09T19:41:45.898712Z nikrou  $ *}
<div id="content" class="content">
  <div class="titrePage">
    <ul class="categoryActions">
      <li>
        <a href="{$U_HOME}" title="{'return to homepage'|@translate}">
          <img src="{$themeconf.icon_dir}/home.png" class="button" alt="{'home'|@translate}">
        </a>
      </li>
    </ul>
    <h2>{'About'|@translate}</h2>
  </div>
  <ul>
  {$ABOUT_MESSAGE}
  {if isset($THEME_ABOUT) }
  <li>{$THEME_ABOUT}</li>
  {/if}
  </ul>
</div>
