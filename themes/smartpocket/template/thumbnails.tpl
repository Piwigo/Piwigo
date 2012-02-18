{if !empty($thumbnails)}
{combine_script id='klass' path='themes/smartpocket/js/klass.min.js'}
{combine_script id='photoswipe' path='themes/smartpocket/js/code.photoswipe.jquery.min.js' require='klass,jquery.mobile'}

{define_derivative name='derivative_params_thumb' width=120 height=120 crop=true}
{define_derivative name='derivative_params_full' type='large'}

{footer_script}{literal}
(function(window, $, PhotoSwipe){
  $(document).ready(function(){
    var options = {
      jQueryMobile: true,
      imageScaleMethod: "fitNoUpscale"
    };
    $(".thumbnails a").photoSwipe(options);
    $(document).bind('orientationchange', set_thumbnails_width);
    set_thumbnails_width();
  });
}(window, window.jQuery, window.Code.PhotoSwipe));

function set_thumbnails_width() {
  nb_thumbs = Math.max(3, Math.ceil($('.thumbnails').width() / 130));
  width = Math.floor(1000000 / nb_thumbs) / 10000;
  $('.thumbnails li').css('width', width+'%');
}

{/literal}{/footer_script}
<ul class="thumbnails">
{foreach from=$thumbnails item=thumbnail}{strip}
{if isset($page_selection[$thumbnail.id])}
	<li>
	  <a href="{$pwg->derivative_url($derivative_params_full, $thumbnail.src_image)}" rel="external">
      <img src="{$pwg->derivative_url($derivative_params_thumb, $thumbnail.src_image)}" alt="{$thumbnail.TN_ALT}">
    </a>
  </li>
{else}
	<li style="display:none;">
	  <a href="{$pwg->derivative_url($derivative_params_full, $thumbnail.src_image)}" rel="external"></a>
  </li>
{/if}
{/strip}{/foreach}
</ul>
{/if}
