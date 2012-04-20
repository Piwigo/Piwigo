{strip}{html_style}
.thumbnailCategory .illustration{ldelim}
	width: {$derivative_params->max_width()+5}px;
}

.content .thumbnailCategory .description{ldelim}
	height: {$derivative_params->max_height()+5}px;
}
{/html_style}{/strip}
<ul class="thumbnailCategories">
{foreach from=$category_thumbnails item=cat name=cat_loop}
	<li class="{if $smarty.foreach.comment_loop.index is odd}odd{else}even{/if}">
		<div class="thumbnailCategory">
			<div class="illustration">
			<a href="{$cat.URL}">
				<img src="{$pwg->derivative_url($derivative_params, $cat.representative.src_image)}" alt="{$cat.TN_ALT}" title="{$cat.NAME|@replace:'"':' '|@strip_tags:false} - {'display this album'|@translate}">
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
				<p class="dates">{$cat.INFO_DATES}</p>
				{/if}
				<p class="Nb_images">{$cat.CAPTION_NB_IMAGES}</p>
				{if not empty($cat.DESCRIPTION)}
				<p>{$cat.DESCRIPTION}</p>
				{/if}
		</div>
			</div>
		</div>
	</li>
{/foreach}
</ul>
