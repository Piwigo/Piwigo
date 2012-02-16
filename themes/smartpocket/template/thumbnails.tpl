{if !empty($thumbnails)}
{combine_script id='klass' path='themes/smartpocket/js/klass.min.js'}
{combine_script id='photoswipe' path='themes/smartpocket/js/code.photoswipe.jquery.min.js' require='klass,jquery.mobile'}

{define_derivative name='derivative_params_thumb' width=150 height=150 crop=true}
{define_derivative name='derivative_params_full' type='large'}

{footer_script}{literal}
(function(window, $, PhotoSwipe){
  $(document).ready(function(){
    var options = {
      jQueryMobile: true,
      //allowUserZoom: false,
      imageScaleMethod: "fitNoUpscale"
    };
    $(".thumbnails a").photoSwipe(options);
    $(".thumbnails img").load(function() { $(this).css('border', '1px solid #3c3c3c') });
  });
}(window, window.jQuery, window.Code.PhotoSwipe));
{/literal}{/footer_script}
<div data-role="content">
<ul class="thumbnails">
{foreach from=$thumbnails item=thumbnail}
	<li>
	  <a href="{$pwg->derivative_url($derivative_params_full, $thumbnail.src_image)}" rel="external">
      <img src="{$pwg->derivative_url($derivative_params_thumb, $thumbnail.src_image)}" alt="{$thumbnail.TN_ALT}">
    </a>
  </li>
{/foreach}
</ul>
</div>
{/if}
