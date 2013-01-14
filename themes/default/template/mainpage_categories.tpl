{strip}{html_style}
.thumbnailCategory .illustration{ldelim}
	width: {$derivative_params->max_width()+5}px;
}

.content .thumbnailCategory .description{ldelim}
	height: {$derivative_params->max_height()+5}px;
}
{/html_style}{/strip}
<div class="loader" style="display: none; position: fixed; right: 0;bottom: 0;"><img src="{$ROOT_URL}{$themeconf.img_dir}/ajax_loader.gif"></div>
<ul class="thumbnailCategories">
{foreach from=$category_thumbnails item=cat name=cat_loop}
{assign var=derivative value=$pwg->derivative($derivative_params, $cat.representative.src_image)}
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
  <li class="{if $smarty.foreach.cat_loop.index is odd}odd{else}even{/if}">
		<div class="thumbnailCategory">
			<div class="illustration">
			<a href="{$cat.URL}">
				<img {if $derivative->is_cached()}src="{$derivative->get_url()}"{else}src="{$ROOT_URL}{$themeconf.icon_dir}/img_small.png" data-src="{$derivative->get_url()}"{/if} alt="{$cat.TN_ALT}" title="{$cat.NAME|@replace:'"':' '|@strip_tags:false} - {'display this album'|@translate}">
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
