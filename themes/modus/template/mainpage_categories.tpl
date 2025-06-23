{if !empty($album_thumb_size)}{* ================= modus mode ===*}
<ul class="albThumbs" id="rv-at">
{foreach from=$category_thumbnails item=item}
<li>{strip}
<a href="{$item.URL}">
	<img class=albImg{$item.MODUS_STYLE} src="{$item.modus_deriv->get_url()}" alt="{$item.TN_ALT}">
	<div class=albLegend>
		<h4>{$item.NAME}</h4>
		{if !empty($item.icon_ts)}<div class=albLegendRight><span class=albSymbol title="{if $item.icon_ts.IS_CHILD_DATE}{'Recent albums'|@translate} {$item.icon_ts.TITLE}">✻{else}{'Recent photos'|@translate} {$item.icon_ts.TITLE}">✽{/if}</span></div>{/if}
		<div>
			{if $item.nb_images}{$item.nb_images|@translate_dec:'%d photo':'%d photos'}{if $item.nb_categories}, {/if}{/if}
			{if $item.nb_categories}
			<span title="{$item.count_images|@translate_dec:'%d photo':'%d photos'} {$item.count_categories|@translate_dec:'in %d sub-album':'in %d sub-albums'}">{$item.nb_categories|@translate_dec:'%d album':'%d albums'}</span>
			{/if}
		</div>
	</div>
</a>
{/strip}</li>
{/foreach}
</ul>
{else}{* ================= standard mode ===*}
{strip}{html_style}
.thumbnailCategory .illustration{
	width:{$derivative_params->max_width()+5}px;
}

.content .thumbnailCategory .description{
	height:{$derivative_params->max_height()+5}px;
}


@media {cssResolution min=1.3}{
	.thumbnailCategory .illustration{
		width:{($derivative_params->max_width()/2+5)|intval}px;
	}

	.content .thumbnailCategory .description{
		height:{($derivative_params->max_height()/2+5)|intval}px;
	}

	.illustration IMG{
		max-width:{($derivative_params->max_width()/2)|intval}px;
		max-height:{($derivative_params->max_height()/2)|intval}px;
	}
}

@media (max-width: {(2+2*($derivative_params->max_width()+1+120))|intval}px),
{$maxw={(2+2*($derivative_params->max_width()/2+1+120))|intval}}
	{cssResolution base='(max-width:'|cat:$maxw|cat:'px)' min=1.3} {
	.thumbnailCategories LI{
		width:99%
	}
}

{/html_style}{/strip}
<ul class="thumbnailCategories">
{foreach from=$category_thumbnails item=cat name=cat_loop}
{assign var=derivative value=$pwg->derivative($derivative_params, $cat.representative.src_image)}
  <li class="{if $smarty.foreach.cat_loop.index is odd}odd{else}even{/if}">
		<div class="thumbnailCategory">
			<div class="illustration">
			<a href="{$cat.URL}">
				<img src="{$derivative->get_url()}" alt="{$cat.TN_ALT}" title="{$cat.NAME|@replace:'"':' '|@strip_tags:false} - {'display this album'|@translate}">
			</a>
			</div>
			<div class="description">
				<h3>
					<a href="{$cat.URL}">{$cat.NAME}</a>
					{if !empty($cat.icon_ts)}
					<img title="{$cat.icon_ts.TITLE}" src="{$ROOT_URL}{$themeconf.icon_dir}/recent{if $cat.icon_ts.IS_CHILD_DATE}_by_child{/if}.png" alt="(!)">
					{/if}
				</h3>
		<div class="text">
				{if isset($cat.INFO_DATES) }
				<div class="dates">{$cat.INFO_DATES}</div>
				{/if}
				<div class="Nb_images">{$cat.CAPTION_NB_IMAGES}</div>
				{if not empty($cat.DESCRIPTION)}
				<div>{$cat.DESCRIPTION}</div>
				{/if}
		</div>
			</div>
		</div>
	</li>
{/foreach}
</ul>
{/if}