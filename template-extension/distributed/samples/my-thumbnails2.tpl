{* $Id$ *}
<!-- This is a sample of template extensions -->
{if !empty($thumbnails)}
{html_head}<link rel="stylesheet" type="text/css" href="{$smarty.template|substr:6:-3}css">{/html_head}
<ul class="thumbnails">
{foreach from=$thumbnails item=thumbnail}
	<li>
	<fieldset class="fld1">
		<legend class="thumbLegend">
		{if !empty($thumbnail.ELEMENT_NAME)}{$thumbnail.ELEMENT_NAME}{/if}
		</legend>
		<a href="{$thumbnail.U_IMG_LINK}" class="lap2">
			<span><img class="thumbnail" src="{$thumbnail.IMAGE}" alt="{$thumbnail.IMAGE_ALT}" title="{$thumbnail.IMAGE_TITLE}" /></span>
		</a>
  </fieldset>
	</li>
{/foreach}
</ul>
{/if}