{* $Id$ *}

{if !empty($thumbnails)}
<ul class="thumbnails">
{foreach from=$thumbnails item=thumbnail}
	<li class="{$thumbnail.CLASS}">
	<span class="wrap1">
		<span class="wrap2">
		<a href="{$thumbnail.U_IMG_LINK}">
			<img class="thumbnail" src="{$thumbnail.IMAGE}"
		alt="{$thumbnail.IMAGE_ALT}"
		title="{$thumbnail.IMAGE_TITLE}">
		</a>
		</span>
		<span class="thumbLegend">

		{if !empty($thumbnail.ELEMENT_NAME)}{$thumbnail.ELEMENT_NAME}{/if}
		{if !empty($thumbnail.CATEGORY_NAME)}{$thumbnail.CATEGORY_NAME}{/if}
		{if !empty($thumbnail.IMAGE_TS)}{$thumbnail.IMAGE_TS}{/if}
		
		{if !empty($thumbnail.nb_comments)}
		<span class="{$thumbnail.nb_comments.CLASS} nb-comments">
		<br />
		{$pwg->l10n_dec('%d comment', '%d comments',$thumbnail.nb_comments.NB_COMMENTS)}
		</span>
		{/if}
		
		{if !empty($thumbnail.nb_hits)}
		<span class="{$thumbnail.nb_hits.CLASS} nb-hits">
		<br />
		{$pwg->l10n_dec('%d hit', '%d hits',$thumbnail.nb_hits.HITS)}
		</span>
		{/if}
		</span>
	</span>
	</li>
{/foreach}
</ul>
{/if}


