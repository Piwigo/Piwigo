<h2>{lang:User comments validation}</h2>

<form method="post" action="{F_ACTION}" id="comment">

  <input type="hidden" name="list" value="{LIST}" />

  <!-- BEGIN comment -->
  <div class="comment">
    <a class="illustration" href="{comment.U_PICTURE}"><img src="{comment.TN_SRC}" /></a>
    <p class="commentHeader"><strong>{comment.AUTHOR}</strong> - <em>{comment.DATE}</em></p>
    <blockquote>{comment.CONTENT}</blockquote>
    <ul class="actions">
      <li><label><input type="radio" name="action-{comment.ID}" value="reject" />{lang:Reject}</label></li>
      <li><label><input type="radio" name="action-{comment.ID}" value="validate" />{lang:Validate}</label></li>
    </ul>
  </div>
  <!-- END comment -->

  <p class="bottomButtons">
    <input type="submit" name="submit" value="{lang:Submit}" />
    <input type="submit" name="validate-all" value="{lang:Validate All}" />
    <input type="submit" name="reject-all" value="{lang:Reject All}" />
    <input type="reset" value="{lang:Reset}" />
  </p>

</form>
