<img src="{$current.selected_derivative->get_url()}" {$current.selected_derivative->get_size_htm()} alt="{$ALT_IMG}" id="theMainImage"
{if isset($COMMENT_IMG)}
	title="{$COMMENT_IMG|@strip_tags:false|@replace:'"':' '}" {else} title="{$current.TITLE|@replace:'"':' '} - {$ALT_IMG}"
{/if}>
{if count($current.unique_derivatives)>1}
{footer_script}{literal}
function changeImgSrc(url,type,display)
{
	var theImg = document.getElementById("theMainImage");
	if (theImg)
	{
		theImg.removeAttribute("width");theImg.removeAttribute("height");
		theImg.src = url;
		var elt = document.getElementById("derivativeSwitchLink");
		if (elt) elt.innerHTML = display;
	}
	document.cookie = 'picture_deriv=' + type;
}

function toggleDerivativeSwitchBox()
{
	var elt = document.getElementById("derivativeSwitchBox"),
		ePos = document.getElementById("derivativeSwitchLink");
	if (elt.style.display==="none")
	{
		elt.style.position = "absolute";
		elt.style.left = (ePos.offsetLeft + 10) + "px";
		elt.style.top = (ePos.offsetTop + ePos.offsetHeight) + "px";
		elt.style.display="";
	}
	else
		elt.style.display="none";
}
{/literal}{/footer_script}
<a id="derivativeSwitchLink" href="javascript:toggleDerivativeSwitchBox()">{$current.selected_derivative->get_type()|@translate}</a>
<div id="derivativeSwitchBox" onclick="toggleDerivativeSwitchBox()" style="display:none">
{foreach from=$current.unique_derivatives item=derivative key=derivative_type}
<a href="javascript:changeImgSrc('{$derivative->get_url()|@escape:javascript}', '{$derivative_type}', '{$derivative->get_type()|@translate|@escape:javascript}')" style="cursor:pointer">{$derivative->get_type()|@translate} ({$derivative->get_size_hr()})</a><br>
{/foreach}
{if isset($U_ORIGINAL)}
<a href="javascript:phpWGOpenWindow('{$U_ORIGINAL}','xxx','scrollbars=yes,toolbar=no,status=no,resizable=yes')" title="{'Click on the photo to see it in high definition'|@translate}">{'original'|@translate}</a>
{/if}
</div>
{/if}