{if isset($editarea)}
<script type="text/javascript" src="{$editarea.URL}"></script>
<script type="text/javascript">
editAreaLoader.init({ldelim}
	id: "text"
	{foreach from=$editarea.OPTIONS key=option item=value}
  , {$option}: {$value|editarea_quote}
  {/foreach}
{rdelim});
</script>
{/if}

<textarea rows="30" id="text" cols="90">{$DEFAULT_CONTENT}</textarea>