<dt>{'Related tags'|@translate}</dt>
<dd>
	<div id="menuTagCloud">
		{foreach from=$block->data item=tag}
		<span>{strip}
			<a class="tagLevel{$tag.level}" href=
			{if isset($tag.U_ADD)}
				"{$tag.U_ADD}" title="{$tag.counter|@translate_dec:'%d photo is also linked to current tags':'%d photos are also linked to current tags'}" rel="nofollow">+
			{else}
				"{$tag.URL}" title="{'display photos linked to this tag'|@translate}">
			{/if}
				{$tag.name}</a></span>{/strip}
{* ABOVE there should be no space between text, </a> and </span> elements to avoid IE8 bug https://connect.microsoft.com/IE/feedback/ViewFeedback.aspx?FeedbackID=366567 *}
		{/foreach}
	</div>
</dd>
