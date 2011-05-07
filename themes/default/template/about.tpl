{if isset($MENUBAR)}{$MENUBAR}{/if}
<div id="content" class="content">
  <div class="titrePage">
    <ul class="categoryActions"></ul>
    <h2>{'About'|@translate}</h2>
  </div>
  <div id="piwigoAbout">
  {$ABOUT_MESSAGE}
  {if isset($THEME_ABOUT) }
  <ul>
   <li>{$THEME_ABOUT}</li>
  </ul>
  {/if}
  {if not empty($about_msgs)}
    {foreach from=$about_msgs item=elt}
    {$elt}
    {/foreach}
  {/if}
  </div>
</div>
