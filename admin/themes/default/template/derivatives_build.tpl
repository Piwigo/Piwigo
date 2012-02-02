{html_head}{literal}
<style type="text/css">
TABLE {
	font-size: larger;
}
</style>
{/literal}{/html_head}

<p>
	<select id="types" name="types[]" multiple="multiple">
	{foreach from=$derivatives item=type}
	<option value="{$type}" selected="selected">{$type|@translate}</option>
	{/foreach}
	</select>
	<input id="startLink" value="{'Start'|@translate}" onclick="start()" type="button">
	<input id="pauseLink" value="{'Pause'|@translate}" onclick="pause()" type="button" disabled="disbled">
	<input id="stopLink" value="{'Stop'|@translate}" onclick="stop()" type="button" disabled="disbled">
</p>
<hr/>
<p>
<table>
	<tr>
		<td>Errors</td>
		<td id="errors">0</td>
	</tr>
	<tr>
		<td>Loaded</td>
		<td id="loaded">0</td>
	</tr>
	<tr>
		<td>Remaining</td>
		<td id="remaining">0</td>
	</tr>
</table>
<div id="feedbackWrap" style="height:320px; min-height:320px;">
<img id="feedbackImg">
</div>
</p>

<div id="errorList">
</div>

{combine_script id='iloader' load='footer' path='themes/default/js/image.loader.js'}

{footer_script require='jquery.effects.slide'}{literal}

var loader = new ImageLoader( {onChanged: loaderChanged, maxRequests:1 } )
	, pending_next_page = null
	, last_image_show_time = 0
	, allDoneDfd, urlDfd;

function start() {
	allDoneDfd = jQuery.Deferred();
	urlDfd = jQuery.Deferred();

	allDoneDfd.always( function() {
			jQuery("#startLink").attr('disabled', false).css("opacity", 1);
			jQuery("#pauseLink,#stopLink").attr('disabled', true).css("opacity", 0.5);
		} );

	urlDfd.always( function() {
		if (loader.remaining()==0)
			allDoneDfd.resolve();
		} );

	jQuery("#startLink").attr('disabled', true).css("opacity", 0.5);
	jQuery("#pauseLink,#stopLink").attr('disabled', false).css("opacity", 1);

	loader.pause(false);
	updateStats();
	getUrls();
}

function pause() {
	loader.pause( !loader.pause() );
}

function stop() {
	loader.clear();
	urlDfd.resolve();
}

function getUrls(page_token) {
	data = {max_urls: 500, types: []};
	jQuery.each(jQuery("#types").serializeArray(), function(i, t) {
			data.types.push( t.value );
		} );

	if (page_token)
		data['prev_page'] = page_token;
	jQuery.post( '{/literal}{$ROOT_URL}{literal}ws.php?format=json&method=pwg.getMissingDerivatives',
		data, wsData, "json").fail( wsError );
}

function wsData(data) {
	if (!data.stat || data.stat != "ok") {
		wsError();
		return;
	}
	loader.add( data.result.urls );
	if (data.result.next_page) {
		if (loader.pause() || loader.remaining() > 100) {
			pending_next_page = data.result.next_page;
		}
		else {
			getUrls(data.result.next_page);
		}
	}
}

function wsError() {
	urlDfd.reject();
}

function updateStats() {
	jQuery("#loaded").text( loader.loaded );
	jQuery("#errors").text( loader.errors );
	jQuery("#remaining").text( loader.remaining() );
}

function loaderChanged(type, img) {
	updateStats();
	if (img) {
		if (type==="load") {
			var now = jQuery.now();
			if (now - last_image_show_time > 3000) {
				last_image_show_time = now;
				var h=img.height, url=img.src;
				jQuery("#feedbackWrap").hide("slide", {direction:'down'}, function() {
					last_image_show_time = jQuery.now();
					if (h > 300 )
						jQuery("#feedbackImg").attr("height", 300);
					else
						jQuery("#feedbackImg").removeAttr("height");
					jQuery("#feedbackImg").attr("src", url);
					jQuery("#feedbackWrap").show("slide", {direction:'up'} );
					} );
			}
		}
		else {
			jQuery("#errorList").prepend( '<a href="'+img.src+'">'+img.src+'</a>' + "<br>");
		}
	}
	if (pending_next_page && 100 > loader.remaining() )	{
		getUrls(pending_next_page);
		pending_next_page = null;
	}
	else if (loader.remaining() == 0 && (urlDfd.isResolved() || urlDfd.isRejected()))	{
		allDoneDfd.resolve();
	}
}
{/literal}{/footer_script}