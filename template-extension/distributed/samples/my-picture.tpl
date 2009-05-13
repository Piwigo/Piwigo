
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
    <a href="{$U_HOME}" rel="home">{'home'|@translate}</a>
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
  <div class="randomButtons">
    {if isset($U_SLIDESHOW_START) }
      <a href="{$U_SLIDESHOW_START}" title="{'slideshow'|@translate}" rel="nofollow"><img src="{$ROOT_URL}{$themeconf.icon_dir}/start_slideshow.png" class="button" alt="{'slideshow'|@translate}"></a>
    {/if}
    {if isset($U_SLIDESHOW_STOP) }
      <a href="{$U_SLIDESHOW_STOP}" title="{'slideshow_stop'|@translate}" rel="nofollow"><img src="{$ROOT_URL}{$themeconf.icon_dir}/stop_slideshow.png" class="button" alt="{'slideshow_stop'|@translate}"></a>
    {/if}
      <a href="{$U_METADATA}" title="{'picture_show_metadata'|@translate}" rel="nofollow"><img src="{$ROOT_URL}{$themeconf.icon_dir}/metadata.png" class="button" alt="metadata"></a>
    {if isset($current.U_DOWNLOAD) }
      <a href="{$current.U_DOWNLOAD}" title="{'download_hint'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/save.png" class="button" alt="{'download'|@translate}"></a>
    {/if}
    {if isset($PLUGIN_PICTURE_ACTIONS)}{$PLUGIN_PICTURE_ACTIONS}{/if}
    {if isset($favorite) }
      <a href="{$favorite.U_FAVORITE}" title="{$favorite.FAVORITE_HINT}"><img src="{$favorite.FAVORITE_IMG}" class="button" alt="favorite" title="{$favorite.FAVORITE_HINT}"></a>
    {/if}
    {if !empty($U_SET_AS_REPRESENTATIVE) }
      <a href="{$U_SET_AS_REPRESENTATIVE}" title="{'set as category representative'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/representative.png" class="button" alt="{'representative'|@translate}"></a>
    {/if}
    {if isset($U_ADMIN) }
      <a href="{$U_ADMIN}" title="{'link_info_image'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/preferences.png" class="button" alt="{'edit'|@translate}"></a>
    {/if}
    {if isset($U_CADDIE) }{*caddie management BEGIN*}
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
      <a href="{$U_CADDIE}" onclick="addToCadie(this, '{$ROOT_URL|@escape:'javascript'}', {$current.id}); return false;" title="{'add to caddie'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/caddie_add.png" class="button" alt="{'caddie'|@translate}"></a>
    {/if}{*caddie management END*}
  </div>
  {include file='picture_nav_buttons.tpl'|@get_extent:'picture_nav_buttons'}
</div> <!-- imageToolBar -->

<div id="theImage">
{$ELEMENT_CONTENT}

{if isset($COMMENT_IMG)}
<p>{$COMMENT_IMG}</p>
{/if}

{if isset($U_SLIDESHOW_STOP) }
<p>
  [ <a href="{$U_SLIDESHOW_STOP}">{'slideshow_stop'|@translate}</a> ]
</p>
{/if}

</div>

{if isset($previous) }
<a class="navThumb" id="linkPrev" href="{$previous.U_IMG}" title="{'previous_page'|@translate} : {$previous.TITLE}" rel="prev">
  <img src="{$previous.THUMB_SRC}" alt="{$previous.TITLE}">
</a>
{/if}
{if isset($next) }
<a class="navThumb" id="linkNext" href="{$next.U_IMG}" title="{'next_page'|@translate} : {$next.TITLE}" rel="next">
  <img src="{$next.THUMB_SRC}" alt="{$next.TITLE}">
</a>
{/if}

{if !empty($PLUGIN_PICTURE_AFTER)}{$PLUGIN_PICTURE_AFTER}{/if}
