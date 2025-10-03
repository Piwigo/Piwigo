<div class="mcs-side-results search-in-set-button" id="related-tags-toggle">
  <div>
    <p>{'Related tags'|translate}<i class="gallery-icon-up-open rotated"></i></p>
  </div>
</div>

{foreach from=$RELATED_TAGS item=tag}
<span class="related-tags {if isset($RELATED_TAGS_DISPLAY) and $RELATED_TAGS_DISPLAY == false} hide{/if}">{strip}
  <a class="tagLevel {if isset($tag.level)}{$tag.level}{/if}" href=
  {if isset($tag.U_ADD)}
    "{$tag.U_ADD}" title="{$tag.counter|@translate_dec:'%d photo is also linked to current tags':'%d photos are also linked to current tags'}" rel="nofollow">
  {else}
    "{$tag.URL}" title="{'display photos linked to this tag'|@translate}">
  {/if}
    + {$tag.name}</a><div class="tag-counter">{$tag.counter}</div></span>{/strip}
{/foreach}

{footer_script require='jquery'}

$(document).ready(function () { 
  $('#related-tags-toggle').on("click", function (e) { 
    $('.related-tags').toggle();
    $('#related-tags-toggle .gallery-icon-up-open').toggleClass('rotated'); 
  }); 
});

{/footer_script}