{if isset($MENUBAR)}{$MENUBAR}{/if}
<div id="content" class="content{if isset($MENUBAR)} contentWithMenu{/if}">

<div class="titrePage">
	<h2><a href="{$U_HOME}">{'Home'|@translate}</a>{$LEVEL_SEPARATOR}{'Profile'|@translate}</h2>
</div>

{include file='infos_errors.tpl'}

{$PROFILE_CONTENT}
</div> <!-- content -->
