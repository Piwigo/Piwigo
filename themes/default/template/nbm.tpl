
<div id="content" class="content">
	<div class="titrePage">
		<ul class="categoryActions">
			<li><a href="{$U_HOME}" title="{'Home'|@translate}" class="pwg-state-default pwg-button">
				<span class="pwg-icon pwg-icon-home">&nbsp;</span><span class="pwg-button-text">{'Home'|@translate}</span>
			</a></li>
		</ul>
		<h2>{'Notification'|@translate}</h2>
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
