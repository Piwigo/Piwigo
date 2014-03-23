<dt>{'Menu'|@translate}</dt>
<dd>
{if isset($block->data.qsearch) and  $block->data.qsearch==true}
	<form action="{$ROOT_URL}qsearch.php" method="get" id="quicksearch" onsubmit="return this.q.value!='' && this.q.value!=qsearch_prompt;">
		<p style="margin:0;padding:0"{*this <p> is for html validation only - does not affect positioning*}>
			<input type="text" name="q" id="qsearchInput" onfocus="if (value==qsearch_prompt) value='';" onblur="if (value=='') value=qsearch_prompt;" style="width:90%"{if !empty($QUERY_SEARCH)} value="{$QUERY_SEARCH}"{/if}>
		</p>
	</form>
	<script type="text/javascript">var qsearch_prompt="{'Quick search'|@translate|@escape:'javascript'}";{if empty($QUERY_SEARCH)}document.getElementById('qsearchInput').value=qsearch_prompt;{/if}</script>
{/if}
	<ul>{strip}
	{foreach from=$block->data item=link}
		{if is_array($link)}
			<li><a href="{$link.URL}" title="{$link.TITLE}"{if isset($link.REL)} {$link.REL}{/if}>{$link.NAME}</a>{if isset($link.COUNTER)} ({$link.COUNTER}){/if}</li>
		{/if}
	{/foreach}
	{/strip}</ul>
</dd>
