
<!-- menu menu bar -->
<dt>{$section.NAME|@translate}</dt>
<dd>
  {if isset($section.ITEMS.qsearch) and $section.ITEMS.qsearch=='y'}
    <form action="{$ROOT_URL}qsearch.php" method="get" id="quicksearch">
      <p>
        <input type="text" name="q" id="qsearchInput" onfocus="if (value==qsearch_prompt) value='';" onblur="if (value=='') value=qsearch_prompt;" />
      </p>
    </form>
    <script type="text/javascript">var qsearch_prompt="{'qsearch'|@translate|@escape:'javascript'}"; document.getElementById('qsearchInput').value=qsearch_prompt;</script>
  {/if}

  <ul>
  {foreach from=$section.ITEMS item=sum}
    {if is_array($sum)}
      <li><a href="{$sum.U_SUMMARY}" title="{$sum.TITLE}" {if isset($sum.REL)}{$sum.REL}{/if}>{$sum.NAME}</a></li>
    {/if}
  {/foreach}
  </ul>
</dd>
