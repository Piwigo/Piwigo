
{foreach from=$datas item=data}
  <span class="{$data.classname}">
  {if isset($data.url) and $data.url!=""}
  <a href="{$data.url}">{$data.label}</a>
  {else}
  {$data.label}
  {/if}
  {if isset($data.nb_images) and $data.nb_images!=""}
  ({$data.nb_images})
  {/if}
  </span>
{/foreach}