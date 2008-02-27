<!-- DEV TAG: not smarty migrated -->
<!-- $Id$ -->
<div class="titrePage">
  <h2>{lang:waiting} {TABSHEET_TITLE}</h2>
  {TABSHEET}
</div>

<h3>{lang:User comments validation}</h3>

<form method="post" action="{F_ACTION}">
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
    <input type="hidden" name="list" value="{LIST}" />
    <input class="submit" type="submit" name="submit" value="{lang:Submit}" {TAG_INPUT_ENABLED}/>
    <input class="submit" type="submit" name="validate-all" value="{lang:Validate All}" {TAG_INPUT_ENABLED}/>
    <input class="submit" type="submit" name="reject-all" value="{lang:Reject All}" {TAG_INPUT_ENABLED}/>
    <input class="submit" type="reset" value="{lang:Reset}" />
  </p>

</form>
