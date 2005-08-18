<!-- BEGIN title -->
<h2>{L_COMMENT_TITLE}</h2>
<!-- END title -->
<!-- TODO -->
<div class="admin">
      [
      <!-- BEGIN last_day_option -->
      <a class="admin" href="{last_day_option.U_OPTION}" style="{last_day_option.T_STYLE}">{last_day_option.OPTION}</a>{T_SEPARATION}
      <!-- END last_day_option -->
      {L_COMMENT_STATS}
      ]
	  <!-- BEGIN title -->
      [ <a class="admin" href="{U_HOME}" title="{L_COMMENT_RETURN_HINT}">{L_COMMENT_RETURN}</a> ]
	  <!-- END title -->
</div>
<!-- BEGIN validation -->
<form action="{F_ACTION}" method="post">
<!-- END validation -->

<!-- BEGIN picture -->
<div class="commentTitle">{picture.TITLE_IMG}</div>

<div style="margin-left:auto;margin-right:auto;text-align:center;">
  <a href="{picture.U_THUMB}" title="{picture.TITLE_IMG}"><img src="{picture.I_THUMB}" class="thumbLink" alt="{picture.THUMB_ALT_IMG}"/></a>
</div>

<!-- BEGIN comment -->
<div class="userCommentHeader">
  <!-- BEGIN validation -->
  <p class="userCommentDelete">
    <input type="checkbox" name="comment_id[]" value="{picture.comment.validation.ID}" {picture.comment.validation.CHECKED} />
  </p>
  <!-- END validation -->
  <strong>{picture.comment.COMMENT_AUTHOR}</strong> - {picture.comment.COMMENT_DATE}
</div>

<blockquote>{picture.comment.COMMENT}</blockquote>
<!-- END comment -->
<!-- END picture -->

<!-- BEGIN validation -->
<div align="center">
<input type="submit" name="validate" class="bouton" value="{L_VALIDATE}" />
<input type="submit" name="delete" class="bouton" value="{L_DELETE}" />
</div>
</form>
<!-- END validation -->
