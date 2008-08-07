
<!-- personalised menu bar -->
{if $section.NAME!=""}
  <dt>{$section.NAME|@translate}</dt>
{/if}
<dd>
    {if $section.ITEMS.CONTENT!=""}{$section.ITEMS.CONTENT}{/if}
</dd>