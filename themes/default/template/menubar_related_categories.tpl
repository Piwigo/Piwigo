<dt>
	{'Related albums'|@translate}
</dt>
<dd>
{assign var='ref_level' value=0}
{foreach from=$block->data.MENU_CATEGORIES item=cat}
  {if $cat.LEVEL > $ref_level}
  <ul>
  {else}
    </li>
    {'</ul></li>'|@str_repeat:($ref_level-$cat.LEVEL)}
  {/if}
    <li>
  {if isset($cat.url)}
      <a href="{$cat.url}" title="{$cat.TITLE}">{$cat.name}</a>
  {else}
      {$cat.name}
  {/if}
  {if $cat.count_images > 0}
      <span class="badge" title="{$cat.count_images|translate_dec:'%d photo':'%d photos'}">{$cat.count_images}</span>
  {/if}
  {if $cat.count_categories > 0}
      <span class="badge badgeCategories" title="{'sub-albums'|translate}">{$cat.count_categories}</span>
  {/if}
  {assign var='ref_level' value=$cat.LEVEL}
{/foreach}
{'</li></ul>'|@str_repeat:$ref_level}
</dd>
