{if isset($MENUBAR)}{$MENUBAR}{/if}
<div id="content" class="content{if isset($MENUBAR)} contentWithMenu{/if}">

<div class="titrePage">
	<ul class="categoryActions">
{if $display_mode == 'letters'}
		<li><a href="{$U_CLOUD}" title="{'show tag cloud'|@translate}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-cloud">&nbsp;</span><span class="pwg-button-text">{'cloud'|@translate}</span>
		</a></li>
{/if}
{if $display_mode == 'cloud'}
		<li><a href="{$U_LETTERS}" title="{'group by letters'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-letters">&nbsp;</span><span class="pwg-button-text">{'letters'|@translate}</span>
		</a></li>
{/if}
	</ul>
	<h2><a href="{$U_HOME}">{'Home'|@translate}</a>{$LEVEL_SEPARATOR}{'Tags'|@translate}</h2>
</div>

{include file='infos_errors.tpl'}


{if $display_mode == 'cloud' and isset($tags)}
<div id="fullTagCloud">
	{foreach from=$tags item=tag}
	<span><a href="{$tag.URL}" class="tagLevel{$tag.level}" title="{$tag.counter|@translate_dec:'%d photo':'%d photos'}">{$tag.name}</a></span>
	{/foreach}
</div>
{/if}

{if $display_mode == 'letters' and isset($letters)}
<table>
	<tr>
		<td valign="top">
	{foreach from=$letters item=letter}
<fieldset class="tagLetter">
	<legend class="tagLetterLegend">{$letter.TITLE}</legend>
	<table class="tagLetterContent">
		{foreach from=$letter.tags item=tag}
		<tr class="tagLine">
			<td><a href="{$tag.URL}" title="{$tag.name}">{$tag.name}</a></td>
			<td class="nbEntries">{$tag.counter|@translate_dec:'%d photo':'%d photos'}</td>
		</tr>
		{/foreach}
	</table>
</fieldset>
		{if isset($letter.CHANGE_COLUMN) }
		</td>
		<td valign="top">
		{/if}
	{/foreach}
		</td>
	</tr>
</table>
{/if}

</div> <!-- content -->
