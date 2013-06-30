{include file='infos_errors.tpl'}
<div data-role="content">
  <ul data-role="listview" data-inset="true">
    <li data-role="list-divider">{'About'|@translate}</li>
    <li>{$ABOUT_MESSAGE}</li>
    {if isset($THEME_ABOUT) }
    <li>{$THEME_ABOUT}</li>
    {/if}
    {if not empty($about_msgs)}
    {foreach from=$about_msgs item=elt}
    <li>{$elt}</li>
    {/foreach}
    {/if}
  </ul>
</div>
