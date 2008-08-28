
<!-- links menu bar -->
{if $block->get_title() !="" }
  <dt>{$block->get_title()}</dt>
{/if}
<dd>
  <ul {if $block->data.icons=='y'}style="padding-left:4px;list-style:none;"{/if}>
    {foreach from=$block->data.LINKS item=link}
      <li>
        {if $block->data.icons=='y'}<img src='{$link.icon}' style="position:relative;top:3px;"/>{/if}
        <a href="{$link.url}"
          {if $link.mode == 0} target = '_blank' {/if}>{$link.label}</a>
      </li>
    {/foreach}
  </ul>
</dd>