{* $Id$ *}
<!-- This is a sample of template extensions -->
{if !empty($thumbnails)}
<ul class="thumbnails">
{foreach from=$thumbnails item=thumbnail}
	<li>
	<fieldset class="wrap1">
		<legend class="thumbLegend">
		{if !empty($thumbnail.ELEMENT_NAME)}{$thumbnail.ELEMENT_NAME}{/if}
		</legend>
		<a href="{$thumbnail.U_IMG_LINK}" class="wrap2">
			<img class="thumbnail" src="{$thumbnail.IMAGE}" alt="{$thumbnail.IMAGE_ALT}" title="{$thumbnail.IMAGE_TITLE}" />
		</a>
  </fieldset>
	</li>
{/foreach}
</ul>
{/if}