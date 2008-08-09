
<!-- links menu bar -->
{if $section.NAME!=""}
  <dt>{$section.NAME|@translate}</dt>
{/if}
<dd>
  <ul {if $section.ITEMS.icons=='y'}style="padding-left:4px;list-style:none;"{/if}>
    {foreach from=$section.ITEMS.LINKS item=link}
      <li>
        {if $section.ITEMS.icons=='y'}<img src='{$link.icon}' style="position:relative;top:3px;"/>{/if}
        <a href="{$link.url}"
          {if $link.mode == 0} target = '_blank' {/if}>{$link.label}</a>
      </li>
    {/foreach}
  </ul>
</dd>