{* $Id$ *}

<div class="titrePage">
	<h2>Menubar</h2>
</div>

<form method="post" action="{$F_ACTION}" class="properties">
<table class="table2">
	<tr class="throw">
		<td>Id</td>
		<td>{'Author'|@translate}</td>
		<td>{'Name'|@translate}</td>
		<td>{'Position'|@translate}</td>
		<td>Hide</td>
	</tr>
	{foreach from=$blocks item=block name="block_loop"}
	<tr class="{if $smarty.foreach.block_loop.index is odd}row1{else}row2{/if}">
		<td>{$block.reg->get_id()}</td>
		<td>{$block.reg->get_owner()}</td>
		<td>{$block.reg->get_name()|@translate}</td>
		<td><input type="text" name="pos_{$block.reg->get_id()}" value={math equation="abs(pos)" pos=$block.pos} size="2"></td>
		<td><input type="checkbox" name="hide_{$block.reg->get_id()}" {if $block.pos<0}checked="checked"{/if} ></td>
	</tr>
	{/foreach}
</table>
<p>
	<input type="submit" name="submit" value="{'Submit'|@translate}" {$TAG_INPUT_ENABLED}>
	<input type="submit" name="reset" value="{'Reset'|@translate}" {$TAG_INPUT_ENABLED}>
</p>
</form>
