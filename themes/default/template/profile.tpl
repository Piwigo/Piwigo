{if isset($MENUBAR)}{$MENUBAR}{/if}
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
	<ul class="categoryActions"></ul>
	<h2>{'Profile'|@translate}</h2>
</div>

{$PROFILE_CONTENT}
</div> <!-- content -->
