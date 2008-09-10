{* $Id$ *}

<ul class="thumbnailCategories">
{foreach from=$category_thumbnails item=cat}
	<li>
		<div class="thumbnailCategory">
			<div class="illustration">
			<a href="{$cat.URL}">
				<img src="{$cat.TN_SRC}" alt="{$cat.TN_ALT}" title="{'hint_category'|@translate}">
			</a>
			</div>
			<div class="description">
				<h3>
					<a href="{$cat.URL}">{$cat.NAME}</a>
					{$cat.ICON_TS}
				</h3>
				{if isset($cat.INFO_DATES) }
				<p>{$cat.INFO_DATES}</p>
				{/if}
				<p>{$cat.CAPTION_NB_IMAGES}</p>
				{if not empty($cat.DESCRIPTION)}
				<p>{$cat.DESCRIPTION}</p>
				{/if}
			</div>
		</div>
	</li>
{/foreach}
</ul>

