<img src="{$current.selected_derivative->get_url()}" {$current.selected_derivative->get_size_htm()} alt="{$ALT_IMG}" id="theMainImage"
{if isset($COMMENT_IMG)}
	title="{$COMMENT_IMG|@strip_tags:false|@replace:'"':' '}" {else} title="{$current.TITLE|@replace:'"':' '} - {$ALT_IMG}"
{/if}>
{if count($current.available_derivative_types)>1}
{footer_script}{literal}
function changeImgSrc(url,type)
{
	var theImg = document.getElementById("theMainImage");
	if (theImg)
	{
		theImg.removeAttribute("width");theImg.removeAttribute("height");
		theImg.src = url;
	}
	document.cookie = 'picture_deriv=' + type;
}
{/literal}{/footer_script}
<p>
{foreach from=$current.available_derivative_types item=derivative_type}
<a onclick="changeImgSrc('{$current.derivatives[$derivative_type]->get_url()|@escape:javascript}', '{$derivative_type}')" title="{$current.derivatives[$derivative_type]->get_size_hr()}">{$derivative_type}</a>
{/foreach}
</p>
{/if}