{if !empty($thumbnails)}{strip}
{*define_derivative name='derivative_params' width=160 height=90 crop=true*}
{html_style}
{*Set some sizes according to maximum thumbnail width and height*}
.thumbnails SPAN,
.thumbnails .wrap2 A,
.thumbnails LABEL{ldelim}
	width: {$derivative_params->max_width()+2}px;
}

.thumbnails .wrap2{ldelim}
	height: {$derivative_params->max_height()+3}px;
}
{if $derivative_params->max_width() > 600}
.thumbLegend {ldelim}font-size: 130%}
{else}
{if $derivative_params->max_width() > 400}
.thumbLegend {ldelim}font-size: 110%}
{else}
.thumbLegend {ldelim}font-size: 90%}
{/if}
{/if}
{/html_style}
{foreach from=$thumbnails item=thumbnail}
{assign var=derivative value=$pwg->derivative($derivative_params, $thumbnail.src_image)}
{if !$derivative->is_cached() and !$js_loaded}
{combine_script id='jquery.ajaxmanager' path='themes/default/js/plugins/jquery.ajaxmanager.js' load='footer'}
{*combine_script id='thumbnails.loader' path='themes/default/js/thumbnails.loader.js' require='jquery.ajaxmanager' load='footer'*}
{footer_script}{literal}
var thumbnails_queue = jQuery.manageAjax.create('queued', {
  queue: true,  
  cacheResponse: false,
  maxRequests: {/literal}{$maxRequests}{literal},
  preventDoubleRequests: false
});

function add_thumbnail_to_queue(img, loop) {
  thumbnails_queue.add({
    type: 'GET', 
    url: img.data('src'), 
    data: { ajaxload: 'true' },
    dataType: 'json',
    beforeSend: function(){jQuery('.loader').show()},
    success: function(result) {
      img.attr('src', result.url);
      jQuery('.loader').hide();
    },
    error: function() {
      if (loop < 3)
        add_thumbnail_to_queue(img, ++loop); // Retry 3 times
      img.attr('src', {/literal}"{$ROOT_URL}{$themeconf.icon_dir}/errors_small.png"{literal});
      jQuery('.loader').hide();
    }
  }); 
}

function pwg_ajax_thumbnails_loader() {
  jQuery('img[data-src]').each(function() {
    add_thumbnail_to_queue(jQuery(this), 0);
  });
}
jQuery(document).ready(pwg_ajax_thumbnails_loader);
{/literal}{/footer_script}
{assign var=js_loaded value=true}
{/if}
<li>
	<span class="wrap1">
		<span class="wrap2">
		<a href="{$thumbnail.URL}">
			<img class="thumbnail" {if $derivative->is_cached()}src="{$derivative->get_url()}"{else}src="{$ROOT_URL}{$themeconf.icon_dir}/img_small.png" data-src="{$derivative->get_url()}"{/if} alt="{$thumbnail.TN_ALT}" title="{$thumbnail.TN_TITLE}">
		</a>
		</span>
		{if $SHOW_THUMBNAIL_CAPTION }
		<span class="thumbLegend">
		<span class="thumbName">{$thumbnail.NAME}</span>
		{if !empty($thumbnail.icon_ts)}
		<img title="{$thumbnail.icon_ts.TITLE}" src="{$ROOT_URL}{$themeconf.icon_dir}/recent.png" alt="(!)">
		{/if}
		{if isset($thumbnail.NB_COMMENTS)}
		<span class="{if 0==$thumbnail.NB_COMMENTS}zero {/if}nb-comments">
		<br>
		{$pwg->l10n_dec('%d comment', '%d comments',$thumbnail.NB_COMMENTS)}
		</span>
		{/if}

		{if isset($thumbnail.NB_HITS)}
		<span class="{if 0==$thumbnail.NB_HITS}zero {/if}nb-hits">
		<br>
		{$pwg->l10n_dec('%d hit', '%d hits',$thumbnail.NB_HITS)}
		</span>
		{/if}
		</span>
		{/if}
	</span>
	</li>
{/foreach}{/strip}
{/if}
