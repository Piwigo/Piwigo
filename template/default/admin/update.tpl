<!-- BEGIN introduction -->
<div class="admin">{L_UPDATE_TITLE}</div>
<ul class="menu">
  <li><a href="{U_CAT_UPDATE}">{L_CAT_UPDATE}</a></li>
  <li><a href="{U_ALL_UPDATE}">{L_ALL_UPDATE}</a></li>
</ul>
<!-- END introduction -->
<!-- BEGIN update -->
<div class="admin">{L_RESULT_UPDATE}</div>
<br /><span style="color:#9cb4ce;">{update.NEW_CAT} {L_NEW_CATEGORY}</span>
<br /><span style="color:#ffe1e1;">{update.DEL_CAT} {L_DEL_CATEGORY}</span>
<!-- BEGIN sync_metadata -->
<br />[ <a href="{update.sync_metadata.U_URL}">{L_UPDATE_SYNC_METADATA_QUESTION}</a> ]
<!-- END sync_metadata -->
{update.CATEGORIES}
<!-- END update -->
<!-- BEGIN remote_update -->
  <table>
  <tr>
    <th>{#remote_site}</th>
  </tr>
  <tr>
    <td>
      <div class="retrait">
        <span style="font-weight:bold;color:navy;">{#url}</span><br /><br />
        <!-- update.php generates itself HTML code for categories  -->
        {#categories}
        <br /><span style="color:blue;">{#count_new} {#update_research_conclusion}</span>
        <br /><span style="color:red;">{#count_deleted} {#update_deletion_conclusion}</span>
      </div>
    </td>
  </tr>
  </table>
  <!-- END remote_update -->
