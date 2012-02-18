{define_derivative name='derivative_params' width=120 height=120 crop=true}

<ul data-role="listview" data-inset="true">
{foreach from=$category_thumbnails item=cat}
	<li>
		<a href="{$cat.URL}">
		<img src="{$pwg->derivative_url($derivative_params, $cat.representative.src_image)}">
    <h3>{$cat.NAME}</h3>
		<p class="Nb_images">{$cat.CAPTION_NB_IMAGES}</p>
		</a>
  </li>
{/foreach}
</ul>

