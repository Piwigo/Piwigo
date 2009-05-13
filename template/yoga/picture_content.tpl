{* $Id: /piwigo/trunk/template/yoga/picture_content.tpl 6960 2009-02-04T02:30:48.228526Z rvelices  $ *}
{if isset($high) }
<a href="javascript:phpWGOpenWindow('{$high.U_HIGH}','{$high.UUID}','scrollbars=yes,toolbar=no,status=no,resizable=yes')">
{/if}
  <img src="{$SRC_IMG}" style="width:{$WIDTH_IMG}px;height:{$HEIGHT_IMG}px;" alt="{$ALT_IMG}" 
	{if isset($COMMENT_IMG)}
		title="{$COMMENT_IMG|@strip_tags:false|@replace:'"':' '}" {else} title="{$current.TITLE|@replace:'"':' '} - {$ALT_IMG}"
	{/if}>
{if isset($high) }
</a>
  <p>{'picture_high'|@translate}</p>
{/if}
