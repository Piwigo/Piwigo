<div data-role="content">
{if !empty($CATEGORIES)}{$CATEGORIES}{/if}
{if !empty($THUMBNAILS)}{$THUMBNAILS}{/if}
{if !empty($CONTENT_DESCRIPTION)}
<div class="additional_info">
	{$CONTENT_DESCRIPTION}
</div>
{/if}
{if !empty($CONTENT)}{$CONTENT}{/if}
</div>

