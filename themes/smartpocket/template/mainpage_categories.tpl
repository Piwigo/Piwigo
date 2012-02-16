{define_derivative name='derivative_params' width=80 height=80 crop=true}

<ul data-role="listview" data-inset="true">
{foreach from=$category_thumbnails item=cat}
	<li>
		<a href="{$cat.URL}">
		<img src="{$pwg->derivative_url($derivative_params, $cat.representative.src_image)}">
    <h3>{$cat.NAME}</h3>
    {if isset($cat.INFO_DATES) }
		<p class="dates">{$cat.INFO_DATES}</p>
		{/if}
		<p class="Nb_images">{$cat.CAPTION_NB_IMAGES}</p>
		{if not empty($cat.DESCRIPTION)}
		<p>{$cat.DESCRIPTION}</p>
		{/if}
		</a>
  </li>
{/foreach}
</ul>

