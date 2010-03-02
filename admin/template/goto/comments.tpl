<div class="titrePage">
  <h2>{'Waiting'|@translate} {$TABSHEET_TITLE}</h2>
</div>

<h3>{'User comments validation'|@translate}</h3>

{if !empty($comments) }
<form method="post" action="{$F_ACTION}">
  
  {foreach from=$comments item=comment}
  <div class="comment">
    <a class="illustration" href="{$comment.U_PICTURE}"><img src="{$comment.TN_SRC}"></a>
    <p class="commentHeader"><strong>{$comment.AUTHOR}</strong> - <em>{$comment.DATE}</em></p>
    <blockquote>{$comment.CONTENT}</blockquote>
  </div>
    <ul class="actions">
      <li><label><input type="radio" name="action-{$comment.ID}" value="reject">{'Reject'|@translate}</label></li>
      <li><label><input type="radio" name="action-{$comment.ID}" value="validate">{'Validate'|@translate}</label></li>
    </ul>
  {/foreach}

  <p class="bottomButtons">
    <input type="hidden" name="list" value="{$LIST}">
    <input class="submit" type="submit" name="submit" value="{'Submit'|@translate}" {$TAG_INPUT_ENABLED}>
    <input class="submit" type="submit" name="validate-all" value="{'Validate All'|@translate}" {$TAG_INPUT_ENABLED}>
    <input class="submit" type="submit" name="reject-all" value="{'Reject All'|@translate}" {$TAG_INPUT_ENABLED}>
    <input class="submit" type="reset" value="{'Reset'|@translate}">
  </p>

</form>
{/if}
