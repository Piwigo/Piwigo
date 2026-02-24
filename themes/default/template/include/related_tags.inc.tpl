<h3 class="related-tags-title">{'Related tags'|translate}</h3>

{foreach from=$COMBINABLE_TAGS item=tag}
<span class="related-tags {if isset($RELATED_TAGS_DISPLAY) and $RELATED_TAGS_DISPLAY == false} hide{/if}">{strip}
  <a class="tagLevel {if isset($tag.level)}{$tag.level}{/if}" href=
  {if isset($tag.U_ADD)}
    "{$tag.U_ADD}" title="{$tag.counter|@translate_dec:'%d photo is also linked to current tags':'%d photos are also linked to current tags'}" rel="nofollow">
  {else}
    "{$tag.URL}" title="{'display photos linked to this tag'|@translate}">
  {/if}
    + {$tag.name}<div class="tag-counter">{$tag.counter}</div></a></span>{/strip}
{/foreach}