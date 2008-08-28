
<!-- personalised menu bar -->
{if $block->get_title() !="" }
  <dt>{$block->get_title()}</dt>
{/if}
<dd>
    {$block->data}
</dd>