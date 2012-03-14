<ul data-role="listview" data-inset="true">
{foreach from=$category_thumbnails item=cat}
	<li>
		<a href="{$cat.URL}">
		<img src="{$pwg->derivative_url($thumbnail_derivative_params, $cat.representative.src_image)}">
    <h3>{$cat.NAME}</h3>
		<p class="Nb_images">{$cat.CAPTION_NB_IMAGES}</p>
		</a>
  </li>
{/foreach}
</ul>

