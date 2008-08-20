{* $Id$ *}
<!-- This is a sample of template extensions -->
<div id="imageHeaderBar">
  <div class="browsePath">
    <a href="{$U_HOME}" rel="home">{'home'|@translate}</a>
    {$LEVEL_SEPARATOR}{$SECTION_TITLE}
    {$LEVEL_SEPARATOR}{$current.TITLE}
  </div>
</div>

{if !empty($PLUGIN_PICTURE_BEFORE)}{$PLUGIN_PICTURE_BEFORE}{/if}
<div id="imageToolBar">
  <div class="randomButtons">
    {if isset($PLUGIN_PICTURE_ACTIONS)}{$PLUGIN_PICTURE_ACTIONS}{/if}
  </div>
  {include file=$FILE_PICTURE_NAV_BUTTONS}
</div> <!-- imageToolBar -->

<div id="theImage">
{$ELEMENT_CONTENT}

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
