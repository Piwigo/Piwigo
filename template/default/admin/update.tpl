<!-- BEGIN introduction -->
<div class="admin">{L_UPDATE_TITLE}</div>
<form action="{F_ACTION}" method="post">
  <ul class="menu">
    <li>
      {L_UPDATE_SYNC_FILES}
      <ul class="menu">
        <li><input type="radio" name="sync" value="dirs" checked="checked" /> {L_UPDATE_SYNC_DIRS}</li>
        <li><input type="radio" name="sync" value="files" /> {L_UPDATE_SYNC_ALL}</li>
      </ul>
    </li>
    <li>
      {L_UPDATE_SYNC_METADATA}
      <ul class="menu">
        <li><input type="radio" name="sync" value="metadata_new" /> {L_UPDATE_SYNC_METADATA_NEW}</li>
        <li><input type="radio" name="sync" value="metadata_all" /> {L_UPDATE_SYNC_METADATA_ALL}</li>
      </ul>
    </li>
    <li>
      {L_UPDATE_CATS_SUBSET}
    </li>
  </ul>
  <select style="width:500px" name="cat" size="10">
    <!-- BEGIN category_option -->
    <option {introduction.category_option.SELECTED} value="{introduction.category_option.VALUE}">{introduction.category_option.OPTION}</option>
    <!-- END category_option -->
  </select>
  <input type="checkbox" name="subcats-included" value="1" checked="checked" /> {L_SEARCH_SUBCATS_INCLUDED}
  <p style="text-align:center;"><input type="submit" value="{L_SUBMIT}" name="submit" class="bouton" /></p>
</form>
<!-- END introduction -->
<!-- BEGIN update -->
<div class="admin">{L_RESULT_UPDATE}</div>
<ul style="text-align:left;">
  <li class="update_summary_new">{update.NB_NEW_CATEGORIES} {L_NB_NEW_CATEGORIES}</li>
  <li class="update_summary_new">{update.NB_NEW_ELEMENTS} {L_NB_NEW_ELEMENTS}</li>
  <li class="update_summary_del">{update.NB_DEL_CATEGORIES} {L_NB_DEL_CATEGORIES}</li>
  <li class="update_summary_del">{update.NB_DEL_ELEMENTS} {L_NB_DEL_ELEMENTS}</li>
</ul>
{update.CATEGORIES}
<!-- END update -->
