{if isset($high)}
{combine_script id='core.scripts' load='async' path='themes/default/js/scripts.js'}
<a href="javascript:phpWGOpenWindow('{$high.U_HIGH}','{$high.UUID}','scrollbars=yes,toolbar=no,status=no,resizable=yes')">
{/if}
	<img src="{$SRC_IMG}" style="width:{$WIDTH_IMG}px;height:{$HEIGHT_IMG}px;" alt="{$ALT_IMG}" 
	{if isset($COMMENT_IMG)}
		title="{$COMMENT_IMG|@strip_tags:false|@replace:'"':' '}" {else} title="{$current.TITLE|@replace:'"':' '} - {$ALT_IMG}"
	{/if}>
{if isset($high) }
</a>
	<p>{'Click on the photo to see it in high definition'|@translate}</p>
{/if}
