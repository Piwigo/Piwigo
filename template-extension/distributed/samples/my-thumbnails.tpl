{* $Id$ *}
<!-- This is a sample of template extensions -->
{if !empty($thumbnails)}
<ul class="thumbnails">
{foreach from=$thumbnails item=thumbnail}
	<li>
	<span class="wrap1">
		<span class="wrap2">
		<a href="{$thumbnail.U_IMG_LINK}">
			<img class="thumbnail" src="{$thumbnail.IMAGE}" alt="{$thumbnail.IMAGE_ALT}" title="{$thumbnail.IMAGE_TITLE}" />
		</a>
		</span>
		<span class="thumbLegend" style="color:#F36;">
    &copy; 2008 Piwigo<br />
		{if !empty($thumbnail.ELEMENT_NAME)}{$thumbnail.ELEMENT_NAME}{/if}
		{if !empty($thumbnail.IMAGE_TS)}{$thumbnail.IMAGE_TS}{/if}

		{if isset($thumbnail.NB_COMMENTS)}
		<span class="{if 0==$thumbnail.NB_COMMENTS}zero {/if}nb-comments">
		<br />
		{$pwg->l10n_dec('%d comment', '%d comments',$thumbnail.NB_COMMENTS)}
		</span>
		{/if}

		{if isset($thumbnail.NB_HITS)}
		<span class="{if 0==$thumbnail.NB_HITS}zero {/if}nb-hits">
		<br />
		{$pwg->l10n_dec('%d hit', '%d hits',$thumbnail.NB_HITS)}
		</span>
		{/if}
		</span>
	</span>
	</li>
{/foreach}
</ul>
{/if}