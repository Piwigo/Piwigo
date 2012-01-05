<h2>{'Multiple Size'|@translate}</h2>

{html_head}{literal}
<style type="text/css">
#derviativesForm .dError {
	background-color: red;
	color: yellow;
}

#derviativesForm .dErrorDesc {
}

#derviativesForm TABLE THEAD {
	height: 3em;
}

#derviativesForm TABLE INPUT[type="text"] {
	border: 0;
	width: 5em;
}

</style>
{/literal}{/html_head}

<form method="post" id="derviativesForm">
<fieldset>
<legend>{'Watermark'|@translate}</legend>


<select name="w[file]" id="wSelect">
	{html_options options=$watermark_files selected=$watermark.file}
</select>

<p><img id="wImg"></img></p>

<label>{'Min Width'|@translate}
	<input type="text" name="w[minw]" value="{$watermark.minw}"{if isset($ferrors.watermark.minw)}class="dError"{/if}> 
</label>

<label>{'Min Height'|@translate}
	<input type="text" name="w[minh]" value="{$watermark.minh}"{if isset($ferrors.watermark.minh)}class="dError"{/if}> 
</label>

<label>{'X Position'|@translate}
	<input type="text" name="w[xpos]" value="{$watermark.xpos}"{if isset($ferrors.watermark.xpos)}class="dError"{/if}> 
%</label>

<label>{'Y Position'|@translate}
	<input type="text" name="w[ypos]" value="{$watermark.ypos}"{if isset($ferrors.watermark.ypos)}class="dError"{/if}> 
%</label>

<label>{'X Repeat'|@translate}
	<input type="text" name="w[xrepeat]" value="{$watermark.xrepeat}"{if isset($ferrors.watermark.xrepeat)}class="dError"{/if}> 
</label>

<label>{'Opacity'|@translate}
	<input type="text" name="w[opacity]" value="{$watermark.opacity}"{if isset($ferrors.watermark.opacity)}class="dError"{/if}> 
</label>

</fieldset>

<table class="table2">
	<thead>
	<tr>
		<td></td>
		<td>Enabled</td>
		<td>{'Width'|@translate}</td>
		<td>{'Height'|@translate}</td>
		<td>{'Crop'|@translate} (%)</td>
		<td>{'Min Width'|@translate}</td>
		<td>{'Min Height'|@translate}</td>
		<td>{'Sharpen'|@translate} (%)</td>
		<td>{'Quality'|@translate} (%)</td>
	</tr>
	</thead>
	{foreach from=$derivatives item=d key=type}
	<tr>
		<td>{$type|@translate}</td>
		<td>
		{if $d.must_enable}
			x
		{else}
			<input type="checkbox" name="d[{$type}][enabled]" {if $d.enabled}checked="checked"{/if}>
		{/if}
		</td>
		<td>
			<input type="text" name="d[{$type}][w]" value="{$d.w}"{if isset($ferrors.$type.w)}class="dError"{/if}>
			{if isset($ferrors.$type.w)}<span class="dErrorDesc" title="{$ferrors.$type.w}">!</span>{/if}
		</td>
		<td>{if !$d.must_square}
			<input type="text" name="d[{$type}][h]" value="{$d.h}"{if isset($ferrors.$type.h)}class="dError"{/if}>
			{if isset($ferrors.$type.h)}<span class="dErrorDesc" title="{$ferrors.$type.h}">!</span>{/if}
		{/if}</td>
		<td>{if !$d.must_square}
			<input type="text" name="d[{$type}][crop]" value="{$d.crop}"{if isset($ferrors.$type.crop)}class="dError"{/if}>
			{if isset($ferrors.$type.crop)}<span class="dErrorDesc" title="{$ferrors.$type.crop}">!</span>{/if}
		{/if}</td>
		<td>{if !$d.must_square}
			<input type="text" name="d[{$type}][minw]" value="{$d.minw}"{if isset($ferrors.$type.minw)}class="dError"{/if}>
			{if isset($ferrors.$type.minw)}<span class="dErrorDesc" title="{$ferrors.$type.minw}">!</span>{/if}
		{/if}</td>
		<td>{if !$d.must_square}
			<input type="text" name="d[{$type}][minh]" value="{$d.minh}"{if isset($ferrors.$type.minh)}class="dError"{/if}>
			{if isset($ferrors.$type.minh)}<span class="dErrorDesc" title="{$ferrors.$type.minh}">!</span>{/if}
		{/if}</td>
		<td>
			<input type="text" name="d[{$type}][sharpen]" value="{$d.sharpen}"{if isset($ferrors.$type.sharpen)}class="dError"{/if}>
			{if isset($ferrors.$type.sharpen)}<span class="dErrorDesc" title="{$ferrors.$type.sharpen}">!</span>{/if}
		</td>
		<td>
			<input type="text" name="d[{$type}][quality]" value="{$d.quality}"{if isset($ferrors.$type.quality)}class="dError"{/if}>
			{if isset($ferrors.$type.quality)}<span class="dErrorDesc" title="{$ferrors.$type.quality}">!</span>{/if}
		</td>
	</tr>
	{/foreach}
</table>
<p><input type="submit" value="{'Submit'|@translate}"></p>
</form>

{footer_script}{literal}
jQuery(".dError").bind("focus", function () {
	jQuery(this).removeClass("dError");
} );

function onWatermarkChange()
{
	var val = jQuery("#wSelect").val();
	if (val.length) {
		jQuery("#wImg").attr('src', {/literal}'{$ROOT_URL}'{literal}+val).show();
	}
	else {
		jQuery("#wImg").hide();
	}
}

onWatermarkChange();
jQuery("#wSelect").bind("change", onWatermarkChange );
{/literal}{/footer_script}