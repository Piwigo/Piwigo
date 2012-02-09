<img src="{$current.selected_derivative->get_url()}" {$current.selected_derivative->get_size_htm()} alt="{$ALT_IMG}" id="theMainImage" usemap="#map{$current.selected_derivative->get_type()}"
{if isset($COMMENT_IMG)}
	title="{$COMMENT_IMG|@strip_tags:false|@replace:'"':' '}" {else} title="{$current.TITLE|@replace:'"':' '} - {$ALT_IMG}"
{/if}>
{if count($current.unique_derivatives)>1}
{footer_script}{literal}
function changeImgSrc(url,typeSave,typeMap,typeDisplay)
{
	var theImg = document.getElementById("theMainImage");
	if (theImg)
	{
		theImg.removeAttribute("width");theImg.removeAttribute("height");
		theImg.src = url;
		var elt = document.getElementById("derivativeSwitchLink");
		if (elt) elt.innerHTML = typeDisplay;
		theImg.useMap = "#map"+typeMap;
	}
	document.cookie = 'picture_deriv='+typeSave+';path={/literal}{$COOKIE_PATH}{literal}';
}

function toggleDerivativeSwitchBox()
{
	var elt = document.getElementById("derivativeSwitchBox"),
		ePos = document.getElementById("derivativeSwitchLink");
	if (elt.style.display==="none")
	{
		elt.style.position = "absolute";
		elt.style.left = (ePos.offsetLeft+10)+"px";
		elt.style.top = (ePos.offsetTop+ePos.offsetHeight)+"px";
		elt.style.display="";
	}
	else
		elt.style.display="none";
}
{/literal}{/footer_script}
<a id="derivativeSwitchLink" href="javascript:toggleDerivativeSwitchBox()">{$current.selected_derivative->get_type()|@translate}</a>
<div id="derivativeSwitchBox" onclick="toggleDerivativeSwitchBox()" style="display:none">
{foreach from=$current.unique_derivatives item=derivative key=derivative_type}
<a href="javascript:changeImgSrc('{$derivative->get_url()|@escape:javascript}','{$derivative_type}','{$derivative->get_type()}','{$derivative->get_type()|@translate|@escape:javascript}')">{$derivative->get_type()|@translate} ({$derivative->get_size_hr()})</a><br>
{/foreach}
{if isset($U_ORIGINAL)}
<a href="javascript:phpWGOpenWindow('{$U_ORIGINAL}','xxx','scrollbars=yes,toolbar=no,status=no,resizable=yes')" rel="nofollow">{'Original'|@translate}</a>
{/if}
</div>
{/if}

{foreach from=$current.unique_derivatives item=derivative key=derivative_type}{strip}
<map name="map{$derivative->get_type()}" id="map{$derivative->get_type()}">
{assign var='size' value=$derivative->get_size()}
{if isset($previous)}
<area shape=rect coords="0,0,{$size[0]/4|@intval},{$size[1]}" href="{$previous.U_IMG}" title="{'Previous'|@translate} : {$previous.TITLE}" alt="{$previous.TITLE}">
{/if}
<area shape=rect coords="{$size[0]/4|@intval},0,{$size[0]/1.34|@intval},{$size[1]/4|@intval}" href="{$U_UP}" title="{'Thumbnails'|@translate}" alt="{'Thumbnails'|@translate}">
{if isset($next)}
<area shape=rect coords="{$size[0]/1.33|@intval},0,{$size[0]},{$size[1]}" href="{$next.U_IMG}" title="{'Next'|@translate} : {$next.TITLE}" alt="{$next.TITLE}">
{/if}
</map>
{/strip}{/foreach}
