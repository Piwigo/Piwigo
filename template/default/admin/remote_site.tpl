{REMOTE_SITE_TITLE}

<!-- BEGIN errors -->
<div class="errors">
  <ul>
    <!-- BEGIN error -->
    <li>{errors.error.ERROR}</li>
    <!-- END error -->
  </ul>
</div>
<!-- END errors -->

<!-- BEGIN confirmation -->
<div class="info">{confirmation.CONTENT}</div>
<!-- END confirmation -->

<!-- BEGIN update -->
<div class="admin">{L_RESULT_UPDATE}</div>
<ul style="text-align:left;">
  <li class="update_summary_new">{update.NB_NEW_CATEGORIES} {L_NB_NEW_CATEGORIES}</li>
  <li class="update_summary_new">{update.NB_NEW_ELEMENTS} {L_NB_NEW_ELEMENTS}</li>
  <li class="update_summary_del">{update.NB_DEL_CATEGORIES} {L_NB_DEL_CATEGORIES}</li>
  <li class="update_summary_del">{update.NB_DEL_ELEMENTS} {L_NB_DEL_ELEMENTS}</li>
</ul>
<!-- BEGIN removes -->
{L_REMOTE_SITE_REMOVED_TITLE}
<ul style="text-align:left;">
  <!-- BEGIN remote_remove -->
  <li>{update.removes.remote_remove.NAME} {L_REMOTE_SITE_REMOVED}</li>
  <!-- END remote_remove -->
</ul>
<!-- END removes -->
<!-- END update -->

<!-- BEGIN remote_output -->
<div class="remoteOutput">
  <ul>
    <!-- BEGIN remote_line -->
    <li class="{remote_output.remote_line.CLASS}">{remote_output.remote_line.CONTENT}</li>
    <!-- END remote_line -->
  </ul>
</div>
<!-- END remote_output -->

<form action="{F_ACTION}" method="post">
  {L_REMOTE_SITE_CREATE} 
  <input type="text" name="galleries_url" value="{F_GALLERIES_URL}" />
  <input class="bouton" type="submit" name="submit" value="{L_SUBMIT}" />
</form>

<table>
  <!-- BEGIN site -->
  <tr>
    <td>{site.NAME}</td>
    <td><a href="{site.U_GENERATE}" title="{L_REMOTE_SITE_GENERATE_HINT}">{L_REMOTE_SITE_GENERATE}</a></td>
    <td><a href="{site.U_UPDATE}" title="{L_REMOTE_SITE_UPDATE_HINT}">{L_REMOTE_SITE_UPDATE}</a></td>
    <td><a href="{site.U_CLEAN}" title="{L_REMOTE_SITE_CLEAN_HINT}">{L_REMOTE_SITE_CLEAN}</a></td>
    <td><a href="{site.U_DELETE}" title="{L_REMOTE_SITE_DELETE_HINT}">{L_REMOTE_SITE_DELETE}</a></td>
  </tr>
  <!-- END site -->
</table>
