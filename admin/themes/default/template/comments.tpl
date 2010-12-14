{literal}
<script type="text/javascript">
$(document).ready(function(){
  $(".checkComment").click(function(event) {
    if (event.target.type !== 'checkbox') {
      var checkbox = $(this).children("input[type=checkbox]");
      $(checkbox).attr('checked', !$(checkbox).is(':checked'));
    }
  });

  $("#commentSelectAll").click(function () {
    $(".checkComment input[type=checkbox]").attr('checked', true);
    return false;
  });

  $("#commentSelectNone").click(function () {
    $(".checkComment input[type=checkbox]").attr('checked', false);
    return false;
  });

  $("#commentSelectInvert").click(function () {
    $(".checkComment input[type=checkbox]").each(function() {
      $(this).attr('checked', !$(this).is(':checked'));
    });
    return false;
  });

});
</script>
{/literal}

<div class="titrePage">
  <h2>{'Waiting'|@translate} {$TABSHEET_TITLE}</h2>
</div>

<h3>{'User comments validation'|@translate}</h3>

{if !empty($comments) }
<form method="post" action="{$F_ACTION}">
  
<table width="99%">
  {foreach from=$comments item=comment name=comment}
  <tr valign="top" class="{if $smarty.foreach.comment.index is odd}row2{else}row1{/if}">
    <td style="width:50px;" class="checkComment">
      <input type="checkbox" name="comments[]" value="{$comment.ID}">
    </td>
    <td>
  <div class="comment">
    <a class="illustration" href="{$comment.U_PICTURE}"><img src="{$comment.TN_SRC}"></a>
    <p class="commentHeader"><strong>{$comment.AUTHOR}</strong> - <em>{$comment.DATE}</em></p>
    <blockquote>{$comment.CONTENT}</blockquote>
  </div>
    </td>
  </tr>
  {/foreach}
</table>

  <p class="checkActions">
    {'Select:'|@translate}
    <a href="#" id="commentSelectAll">{'All'|@translate}</a>,
    <a href="#" id="commentSelectNone">{'None'|@translate}</a>,
    <a href="#" id="commentSelectInvert">{'Invert'|@translate}</a>
  </p>

  <p class="bottomButtons">
    <input class="submit" type="submit" name="validate" value="{'Validate'|@translate}">
    <input class="submit" type="submit" name="reject" value="{'Reject'|@translate}">
  </p>

</form>
{/if}
