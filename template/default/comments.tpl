<!-- BEGIN title -->
<div class="titrePage">{L_COMMENT_TITLE}</div>
<!-- END title -->
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
<table class="table2">
<!-- BEGIN picture -->
<tr class="row1">
<td >
<a href="{picture.U_THUMB}" title="{picture.TITLE_IMG}">
<img src="{picture.I_THUMB}" class="thumbLink" alt="{picture.THUMB_ALT_IMG}"/>
</a>
</td>
<td class="tablecompact">
  <div class="commentTitle">{picture.TITLE_IMG}</div>
  <div class="commentsNavigationBar">{picture.NAV_BAR}</div>
  <table class="tablecompact">
  <!-- BEGIN comment -->
	<tr class="throw">
	  <td class="throw">
	  {picture.comment.COMMENT_AUTHOR}
	  </td>
	  <td class="commentDate">
	  {picture.comment.COMMENT_DATE}
	<!-- BEGIN validation -->
	<input type="checkbox" name="comment_id[]" value="{picture.comment.validation.ID}" {picture.comment.validation.CHECKED} />
	<!-- END validation -->
	  </td>
	</tr>
	<tr class="row1">
	  <td class="comment" colspan="2">{picture.comment.COMMENT}</td>
	</tr>
	<!-- END comment -->
  </table>
</td>
</tr>
<!-- END picture -->
</table>
<!-- BEGIN validation -->
<div align="center">
<input type="submit" name="validate" class="bouton" value="{L_VALIDATE}" />
<input type="submit" name="delete" class="bouton" value="{L_DELETE}" />
</div>
</form>
<!-- END validation -->