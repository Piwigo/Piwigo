<img src="{$current.selected_derivative->get_url()}" {$current.selected_derivative->get_size_htm()} alt="{$ALT_IMG}" id="theMainImage" usemap="#map{$current.selected_derivative->get_type()}"
{if isset($COMMENT_IMG)}
	title="{$COMMENT_IMG|@strip_tags:false|@replace:'"':' '}" {else} title="{$current.TITLE|@replace:'"':' '} - {$ALT_IMG}"
{/if}>

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
