{* $Id$ *}
<div id="imageHeaderBar">
  <div class="browsePath">
    {if isset($U_SLIDESHOW_STOP) }
    [ <a href="{$U_SLIDESHOW_STOP}">{'slideshow_stop'|@translate}</a> ]
    {/if}
  </div>
  <div class="imageNumber">{$PHOTO}</div>
  {if $SHOW_PICTURE_NAME_ON_TITLE }
  <h2 class="showtitle">{$current.TITLE}</h2>
  {/if}
</div>

<div id="imageToolBar">
  {include file='picture_nav_buttons.tpl'|@get_extent:'picture_nav_buttons'}
</div>

<div id="theImage">
  {$ELEMENT_CONTENT}
  {if isset($COMMENT_IMG)}
  <p class="showlegend">{$COMMENT_IMG}</p>
  {/if}
</div>
