
<!-- links menu bar -->

<dt>{$section.NAME|@translate}</dt>
<dd>
  <ul>
    {foreach from=$section.ITEMS item=link}
      <li>
        <a href="{$link.URL}"
          {if isset($link.new_window) }onclick="window.open(this.href, '{$link.new_window.NAME|@escape:'javascript'}','{$link.new_window.FEATURES|@escape:'javascript'}'); return false;"{/if}
        >
        {$link.LABEL}
        </a>
      </li>
    {/foreach}
  </ul>
</dd>