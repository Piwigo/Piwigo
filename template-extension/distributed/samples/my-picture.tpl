
{if isset($errors)}
<div class="errors">
  <ul>
    {foreach from=$errors item=error}
    <li>{$error}</li>
    {/foreach}
  </ul>
</div>
{/if}

{if isset($infos)}
<div class="infos">
  <ul>
    {foreach from=$infos item=info}
    <li>{$info}</li>
    {/foreach}
  </ul>
</div>
{/if}

<div id="imageHeaderBar">
  <div class="browsePath">
    <a href="{$U_HOME}" rel="Home">{'Home'|@translate}</a>
    {if !$IS_HOME}{$LEVEL_SEPARATOR}{$SECTION_TITLE}{/if}
    {$LEVEL_SEPARATOR}{$current.TITLE}
  </div>
  <div class="imageNumber">{$PHOTO}</div>
  {if $SHOW_PICTURE_NAME_ON_TITLE }
  <h2>{$current.TITLE}</h2>
  {/if}
</div>

{if !empty($PLUGIN_PICTURE_BEFORE)}{$PLUGIN_PICTURE_BEFORE}{/if}
<div id="imageToolBar">
	<div class="actionButtons">
{if isset($U_SLIDESHOW_START)}
		<a href="{$U_SLIDESHOW_START}" title="{'slideshow'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-slideshow">&nbsp;</span><span class="pwg-button-text">{'slideshow'|@translate}</span>
		</a>
{/if}
{if isset($U_SLIDESHOW_STOP)}
		<a href="{$U_SLIDESHOW_STOP}" title="{'stop the slideshow'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-slideshow">&nbsp;</span><span class="pwg-button-text">{'stop the slideshow'|@translate}</span>
		</a>
{/if}
{if isset($U_METADATA)}
		<a href="{$U_METADATA}" title="{'Show file metadata'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-camera-info">&nbsp;</span><span class="pwg-button-text">{'Show file metadata'|@translate}</span>
		</a>
{/if}
{if isset($current.U_DOWNLOAD)}
		<a href="{$current.U_DOWNLOAD}" title="{'Download this file'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-save">&nbsp;</span><span class="pwg-button-text">{'Download'|@translate}</span>
		</a>
{/if}
{if isset($PLUGIN_PICTURE_ACTIONS)}{$PLUGIN_PICTURE_ACTIONS}{/if}
{if isset($favorite)}
		<a href="{$favorite.U_FAVORITE}" title="{if $favorite.IS_FAVORITE}{'delete this photo from your favorites'|@translate}{else}{'add this photo to your favorites'|@translate}{/if}" class="pwg-state-default pwg-button" rel="nofollow">
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
			<span class="pwg-icon pwg-icon-edit">&nbsp;</span><span class="pwg-button-text">{'Edit'|@translate}</span>
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
		<a href="{$U_CADDIE}" onclick="addToCadie(this, '{$ROOT_URL|@escape:'javascript'}', {$current.id}); return false;" title="{'Add to caddie'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
			<span class="pwg-icon pwg-icon-caddie-add">&nbsp;</span><span class="pwg-button-text">{'Caddie'|@translate}</span>
		</a>
{/if}{*caddie management END*}
	</div>   
  {include file='picture_nav_buttons.tpl'|@get_extent:'picture_nav_buttons'}
</div> <!-- imageToolBar -->

<div id="imageWrap">
{$ELEMENT_CONTENT}

{if isset($COMMENT_IMG)}
<p>{$COMMENT_IMG}</p>
{/if}

{if isset($U_SLIDESHOW_STOP) }
<p>
  [ <a href="{$U_SLIDESHOW_STOP}">{'stop the slideshow'|@translate}</a> ]
</p>
{/if}

</div>

{if isset($previous) }
<a class="navThumb" id="linkPrev" href="{$previous.U_IMG}" title="{'Previous'|@translate} : {$previous.TITLE}" rel="prev">
  <img src="{$previous.THUMB_SRC}" alt="{$previous.TITLE}">
</a>
{/if}
{if isset($next) }
<a class="navThumb" id="linkNext" href="{$next.U_IMG}" title="{'Next'|@translate} : {$next.TITLE}" rel="next">
  <img src="{$next.THUMB_SRC}" alt="{$next.TITLE}">
</a>
{/if}

{if !empty($PLUGIN_PICTURE_AFTER)}{$PLUGIN_PICTURE_AFTER}{/if}
