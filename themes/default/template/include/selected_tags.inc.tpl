<span id="selected-tags-container">

{foreach $SELECT_RELATED_TAGS as $TAG}
<span class="selected-related-tag {if 1 == count($SELECT_RELATED_TAGS)}unique-tag{/if}">
    <a href="{$TAG.index_url}" title="{'display photos linked to this tag'|translate}">
      {$TAG.tag_name}
    </a>
    {if count($SELECT_RELATED_TAGS) > 1}
    <a class="selected-related-tag-remove" href="{$TAG.remove_url}" style="border:none;" title="{'remove this tag from the list'|translate}">
      <i class="gallery-icon-cancel"></i>
    </a>
    {/if}
  </span>
{/foreach}

</span>
