
<!-- main menu bar -->

{if isset($sections) and count($sections)}
  <div id="menubar">
    {foreach from=$sections key=name item=section}
      {if not(empty($section.ITEMS))}
        <dl id="{$section.ID}">
         
          {include file=$section.TEMPLATE section=$section}
        </dl>
      {/if}
    {/foreach}
  </div>
{/if}