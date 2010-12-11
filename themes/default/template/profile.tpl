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
		<li><a href="{$U_HOME}" title="{'Home'|@translate}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-home">&nbsp;</span><span class="pwg-button-text">{'Home'|@translate}</span>
		</a></li>
	</ul>
	<h2>{'Profile'|@translate}</h2>
</div>

{$PROFILE_CONTENT}
</div> <!-- content -->
