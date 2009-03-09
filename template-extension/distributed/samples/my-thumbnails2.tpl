{* $Id$ *}
<!-- This is a sample of template extensions -->
{if !empty($thumbnails)}
{html_head}<link rel="stylesheet" type="text/css" href="./template-extension/distributed/samples/my-thumbnails2.css">{/html_head}
<ul class="thumbnails">
{foreach from=$thumbnails item=thumbnail}
	<li>
	<fieldset class="fld1">
		<legend class="thumbLegend">
		{if !empty($thumbnail.NAME)}{$thumbnail.NAME}{/if}
		</legend>
		<a href="{$thumbnail.URL}" class="lap2">
			<span><img class="thumbnail" src="{$thumbnail.TN_SRC}" alt="{$thumbnail.TN_ALT}" title="{$thumbnail.TN_TITLE}"></span>
		</a>
  </fieldset>
	</li>
{/foreach}
</ul>
{/if}
