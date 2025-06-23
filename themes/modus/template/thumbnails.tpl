{if !empty($thumbnails)}{strip}
{if $derivative_params->max_width()/$derivative_params->max_height() > 1.5 || ($derivative_params->max_height()<400 && !$derivative_params->sizing->max_crop)}
{modus_thumbs}
{else}
{if $smarty.const.IMG_SQUARE == $derivative_params->type}{assign var='SHOW_THUMBNAIL_CAPTION' value=false}{/if}
{html_style}
{*Set some sizes according to maximum thumbnail width and height*}
.thumbnails SPAN,.thumbnails .wrap2 A{
	width:{$derivative_params->max_width()+2}px
}
.thumbnails .wrap2{
	height:{$derivative_params->max_height()+3}px
}

@media {cssResolution min=1.3 max=1.7}{
	.thumbnails SPAN,.thumbnails .wrap2 A{
		width:{($derivative_params->max_width()/1.5+2)|intval}px
	}
	.thumbnails .wrap2{
		height:{($derivative_params->max_height()/1.5+3)|intval}px
	}
	.thumbnails .wrap2 IMG{
		max-width:{($derivative_params->max_width()/1.5)|intval}px;
		max-height:{($derivative_params->max_height()/1.5)|intval}px;
	}
}

@media {cssResolution min=1.7 max=2.5}{
	.thumbnails SPAN,.thumbnails .wrap2 A{
		width:{($derivative_params->max_width()/2+2)|intval}px
	}
	.thumbnails .wrap2{
		height:{($derivative_params->max_height()/2+3)|intval}px
	}
	.thumbnails .wrap2 IMG{
		max-width:{($derivative_params->max_width()/2)|intval}px;
		max-height:{($derivative_params->max_height()/2)|intval}px;
	}
}

@media {cssResolution min=2.5}{
	.thumbnails SPAN,.thumbnails .wrap2 A{
		width:{($derivative_params->max_width()/3+2)|intval}px
	}
	.thumbnails .wrap2{
		height:{($derivative_params->max_height()/3+3)|intval}px
	}
	.thumbnails .wrap2 IMG{
		max-width:{($derivative_params->max_width()/3)|intval}px;
		max-height:{($derivative_params->max_height()/3)|intval}px;
	}
}

{*=== If we cannot fit two images per width (1 comes from border, 7 comes from css li margin)===*}
@media
{$maxw=(2+2*($derivative_params->max_width()+1+7))|intval}
{cssResolution base='(max-width:'|cat:$maxw|cat:'px)' max=1},
{$maxw=(2+2*($derivative_params->max_width()/1.5+1+7))|intval}
{cssResolution base='(max-width:'|cat:$maxw|cat:'px)' min=1.3 max=1.7},
{$maxw=(2+2*($derivative_params->max_width()/2+1+7))|intval}
{cssResolution base='(max-width:'|cat:$maxw|cat:'px)' min=1.7 max=2.5},
{$maxw=(2+2*($derivative_params->max_width()/3+1+7))|intval}
{cssResolution base='(max-width:'|cat:$maxw|cat:'px)' min=2.5}{
	.thumbnails .wrap2{
		height:auto;
		border:0
	}
	.thumbnails .thumbLegend{
		height:auto;
		min-height:4em;
		overflow:visible;
	}
}

{*=== If we cannot fit one image per width===*}
@media
{$maxw=(2+($derivative_params->max_width()+1+7))|intval}
{cssResolution base='(max-width:'|cat:$maxw|cat:'px)' max=1},
{$maxw=(2+($derivative_params->max_width()/1.5+1+7))|intval}
{cssResolution base='(max-width:'|cat:$maxw|cat:'px)' min=1.3 max=1.7},
{$maxw=(2+($derivative_params->max_width()/2+1+7))|intval}
{cssResolution base='(max-width:'|cat:$maxw|cat:'px)' min=1.7 max=2.5},
{$maxw=(2+($derivative_params->max_width()/3+1+7))|intval}
{cssResolution base='(max-width:'|cat:$maxw|cat:'px)' min=2.5}{
	.thumbnails .wrap1{
		margin:0 0 5px
	}
	.thumbnails .wrap2{
		display:block
	}
	.thumbnails SPAN, .thumbnails .wrap2 A{
		max-width:99.8%
	}
	.thumbnails .wrap2 IMG{
		max-width:100%
	}
}
{if $derivative_params->max_width() > 400}
.thumbLegend {ldelim}font-size: 110%}
{else}
.thumbLegend {ldelim}font-size: 90%}
{/if}
{/html_style}
{foreach from=$thumbnails item=thumbnail}
	<li>
	<span class="wrap1">
		<span class="wrap2">
		<a href="{$thumbnail.URL}">
{assign var=derivative value=$pwg->derivative($derivative_params, $thumbnail.src_image)}
			<img src="{$derivative->get_url()}"{* {$derivative->get_size_htm()}*} alt="{$thumbnail.TN_ALT}" title="{$thumbnail.TN_TITLE}">
		</a>
		</span>
		{if $SHOW_THUMBNAIL_CAPTION }
		<div class="thumbLegend">
		{$thumbnail.NAME}
		{if !empty($thumbnail.icon_ts)}
		<img title="{$thumbnail.icon_ts.TITLE}" src="{$ROOT_URL}{$themeconf.icon_dir}/recent.png" alt="(!)">
		{/if}
		{if isset($thumbnail.NB_COMMENTS)}
		<span class="{if 0==$thumbnail.NB_COMMENTS}zero {/if}nb-comments">
		<br>
		{$thumbnail.NB_COMMENTS|@translate_dec:'%d comment':'%d comments'}
		</span>
		{/if}

		{if isset($thumbnail.NB_HITS)}
		<span class="{if 0==$thumbnail.NB_HITS}zero {/if}nb-hits">
		<br>
		{$thumbnail.NB_HITS|@translate_dec:'%d hit':'%d hits'}
		</span>
		{/if}
		<span class="thumbDesc"><br>{$thumbnail.DESCRIPTION}</span>
		</div>
		{/if}
	</span>
	</li>
{/foreach}
{/if}
{/strip}{/if}
