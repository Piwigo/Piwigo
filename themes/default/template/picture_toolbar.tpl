<div class="pictureToolBar">
	<div class="actionButtons" style="float:left">
{if isset($U_SLIDESHOW_START)}
		<a href="{$U_SLIDESHOW_START}" title="{'slideshow'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-slideshow">&nbsp;</span><span class="pwg-button-text">{'slideshow'|@translate}</span>
		</a>
{/if}
{if isset($U_METADATA)}
		<a href="{$U_METADATA}" title="{'Show file metadata'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-camera-info">&nbsp;</span><span class="pwg-button-text">{'Show file metadata'|@translate}</span>
		</a>
{/if}
{if isset($current.U_DOWNLOAD)}
		<a href="{$current.U_DOWNLOAD}" title="{'download this file'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-save">&nbsp;</span><span class="pwg-button-text">{'download'|@translate}</span>
		</a>
{/if}
{if isset($PLUGIN_PICTURE_ACTIONS)}{$PLUGIN_PICTURE_ACTIONS}{/if}
{if isset($favorite)}
		<a href="{$favorite.U_FAVORITE}" title="{if $favorite.IS_FAVORITE}{'delete this image from your favorites'|@translate}{else}{'add this image to your favorites'|@translate}{/if}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-favorite-{if $favorite.IS_FAVORITE}del{else}add{/if}">&nbsp;</span><span class="pwg-button-text">{'Favorites'|@translate}</span>
		</a>
{/if}
{if isset($U_SET_AS_REPRESENTATIVE)}
		<a href="{$U_SET_AS_REPRESENTATIVE}" title="{'set as category representative'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-representative">&nbsp;</span><span class="pwg-button-text">{'representative'|@translate}</span>
		</a>
{/if}
{if isset($U_ADMIN)}
		<a href="{$U_ADMIN}" title="{'Modify information'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-edit">&nbsp;</span><span class="pwg-button-text">{'edit'|@translate}</span>
		</a>
{/if}
{if isset($U_CADDIE)}{*caddie management BEGIN*}
<script type="text/javascript">
{literal}function addToCadie(aElement, rootUrl, id)
{
if (aElement.disabled) return;
aElement.disabled=true;
var y = new PwgWS(rootUrl);

y.callService(
	"pwg.caddie.add", {image_id: id} ,
	{
		onFailure: function(num, text) { alert(num + " " + text); document.location=aElement.href; },
		onSuccess: function(result) { aElement.disabled = false; }
	}
	);
}{/literal}
</script>
		<a href="{$U_CADDIE}" onclick="addToCadie(this, '{$ROOT_URL|@escape:'javascript'}', {$current.id}); return false;" title="{'add to caddie'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-caddie-add">&nbsp;</span><span class="pwg-button-text">{'caddie'|@translate}</span>
		</a>
{/if}{*caddie management END*}
	</div>   

	<div class="navigationButtons" style="float:right">
{if $DISPLAY_NAV_BUTTONS or isset($slideshow)}
{if isset($slideshow)}
  {if isset($slideshow.U_INC_PERIOD)}
		<a href="{$slideshow.U_INC_PERIOD}" title="{'Accelerate diaporama speed'|@translate}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-clock-minus">&nbsp;</span><span class="pwg-button-text">{'Accelerate diaporama speed'|@translate}</span>
		</a>
	{else}
		<span class="pwg-state-disabled pwg-button">
			<span class="pwg-icon pwg-icon-clock-minus">&nbsp;</span><span class="pwg-button-text">{'Accelerate diaporama speed'|@translate}</span>
		</span>
	{/if}
  {if isset($slideshow.U_DEC_PERIOD)}
		<a href="{$slideshow.U_DEC_PERIOD}" title="{'Accelerate diaporama speed'|@translate}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-clock-plus">&nbsp;</span><span class="pwg-button-text">{'Accelerate diaporama speed'|@translate}</span>
		</a>
	{else}
		<span class="pwg-state-disabled pwg-button">
			<span class="pwg-icon pwg-icon-clock-plus">&nbsp;</span><span class="pwg-button-text">{'Accelerate diaporama speed'|@translate}</span>
		</span>
	{/if}
{/if}
{if isset($slideshow.U_START_REPEAT)}
		<a href="{$slideshow.U_START_REPEAT}" title="{'Repeat the slideshow'|@translate}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-repeat-play">&nbsp;</span><span class="pwg-button-text">{'Repeat the slideshow'|@translate}</span>
		</a>
{/if}
{if isset($slideshow.U_STOP_REPEAT)}
		<a href="{$slideshow.U_STOP_REPEAT}" title="{'Not repeat the slideshow'|@translate}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-repeat-stop">&nbsp;</span><span class="pwg-button-text">{'Not repeat the slideshow'|@translate}</span>
		</a>
{/if}

{if isset($first)}
		<a href="{$first.U_IMG}" title="{'First'|@translate} : {$first.TITLE}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-arrowstop-w">&nbsp;</span><span class="pwg-button-text">{'First'|@translate}</span>
		</a>
{else}
		<span class="pwg-state-disabled pwg-button">
			<span class="pwg-icon pwg-icon-arrowstop-w">&nbsp;</span><span class="pwg-button-text">{'First'|@translate}</span>
		</span>
{/if}
{if isset($previous)}
		<a href="{$previous.U_IMG}" title="{'Previous'|@translate} : {$previous.TITLE}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-arrow-w">&nbsp;</span><span class="pwg-button-text">{'Previous'|@translate}</span>
		</a>
{else}
		<span class="pwg-state-disabled pwg-button">
			<span class="pwg-icon pwg-icon-arrow-w">&nbsp;</span><span class="pwg-button-text">{'Previous'|@translate}</span>
		</span>
{/if}
{if isset($slideshow.U_START_PLAY)}
		<a href="{$slideshow.U_START_PLAY}" title="{'Play of slideshow'|@translate}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-play">&nbsp;</span><span class="pwg-button-text">{'Play of slideshow'|@translate}</span>
		</a>
{/if}
{if isset($slideshow.U_STOP_PLAY)}
		<a href="{$slideshow.U_STOP_PLAY}" title="{'Pause of slideshow'|@translate}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-pause">&nbsp;</span><span class="pwg-button-text">{'Pause of slideshow'|@translate}</span>
		</a>
{/if}
{if isset($U_UP) and !isset($slideshow)}
		<a href="{$U_UP}" title="{'Thumbnails'|@translate}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-arrow-n">&nbsp;</span><span class="pwg-button-text">{'Thumbnails'|@translate}</span>
		</a>
{/if}
{if isset($next)}
		<a href="{$next.U_IMG}" title="{'Next'|@translate} : {$next.TITLE}" class="pwg-state-default pwg-button pwg-button-icon-right">
			<span class="pwg-icon pwg-icon-arrow-e">&nbsp;</span><span class="pwg-button-text">{'Next'|@translate}</span>
		</a>
{else}
		<span class="pwg-state-disabled pwg-button pwg-button-icon-right">
			<span class="pwg-icon pwg-icon-arrow-e">&nbsp;</span><span class="pwg-button-text">{'Next'|@translate}</span>
		</span>
{/if}
{if isset($last)}
		<a href="{$last.U_IMG}" title="{'Last'|@translate} : {$last.TITLE}" class="pwg-state-default pwg-button pwg-button-icon-right">
			<span class="pwg-icon pwg-icon-arrowstop-e"></span><span class="pwg-button-text">{'Last'|@translate}</span>
		</a>
{else}
		<span class="pwg-state-disabled pwg-button pwg-button-icon-right">
			<span class="pwg-icon pwg-icon-arrowstop-e">&nbsp;</span><span class="pwg-button-text">{'Last'|@translate}</span>
		</span>
{/if}
{/if}
	</div>
</div>
 