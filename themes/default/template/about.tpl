<div id="content" class="content">
  <div class="titrePage">
    <ul class="categoryActions">
			<li><a href="{$U_HOME}" title="{'Home'|@translate}" class="pwg-state-default pwg-button">
				<span class="pwg-icon pwg-icon-home">&nbsp;</span><span class="pwg-button-text">{'Home'|@translate}</span>
			</a></li>
    </ul>
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
