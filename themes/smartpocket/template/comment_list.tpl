<ul data-role="listview" data-inset="true">
  <li data-role="list-divider">{'User comments'|@translate}</li>
{foreach from=$comments item=comment name=comment_loop}
	<li>
		{if !isset($from) or $from!="picture"}<a href="{$comment.U_PICTURE}">
		<img src="{$pwg->derivative_url($thumbnail_derivative_params, $comment.src_image)}">{/if}
    <h3>{$comment.AUTHOR}</h3>
    <p>{$comment.CONTENT}</p>
		{if !isset($from) or $from!="comment"}</a>{/if}
  </li>
{/foreach}
</ul>
