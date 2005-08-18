<!-- BEGIN update -->
<h3>{L_RESULT_UPDATE}</h3>
<ul>
  <li class="update_summary_new">{update.NB_NEW_CATEGORIES} {L_NB_NEW_CATEGORIES}</li>
  <li class="update_summary_new">{update.NB_NEW_ELEMENTS} {L_NB_NEW_ELEMENTS}</li>
  <li class="update_summary_del">{update.NB_DEL_CATEGORIES} {L_NB_DEL_CATEGORIES}</li>
  <li class="update_summary_del">{update.NB_DEL_ELEMENTS} {L_NB_DEL_ELEMENTS}</li>
  <li class="update_summary_err">{update.NB_ERRORS} {L_UPDATE_NB_ERRORS}</li>
</ul>
<!-- BEGIN errors -->
<h3>{L_UPDATE_ERROR_LIST_TITLE}</h3>
<ul>
  <!-- BEGIN error -->
  <li>[{update.errors.error.ELEMENT}] {update.errors.error.LABEL}</li>
  <!-- END error -->
</ul>
<h3>{L_UPDATE_ERRORS_CAPTION}</h3>
<ul>
  <li><strong>PWG-UPDATE-1</strong> : {L_UPDATE_WRONG_DIRNAME_INFO}</li>
  <li><strong>PWG-UPDATE-2</strong> : {L_UPDATE_MISSING_TN_INFO} {{PICTURE_EXT_LIST}}</li>
</ul>
<!-- END errors -->
<!-- BEGIN infos -->
<h3>{L_UPDATE_INFOS_TITLE}</h3>
<ul>
  <!-- BEGIN info -->
  <li>[{update.infos.info.ELEMENT}] {update.infos.info.LABEL}</li>
  <!-- END info -->
</ul>
<!-- END infos -->
<!-- END update -->

<!-- BEGIN metadata_result -->
<h3>{L_RESULT_METADATA}</h3>
<ul>
  <li>{metadata_result.NB_ELEMENTS} {L_ELEMENTS_METADATA_SYNC}</li>
  <li>{L_USED_METADATA} : {METADATA_LIST}</li>
</ul>
<!-- END metadata_result -->

<!-- BEGIN introduction -->
<h3>{L_UPDATE_TITLE}</h3>
<form action="{F_ACTION}" method="post" id="update">
  <ul>
    <li>
      {L_UPDATE_SYNC_FILES}
      <ul>
        <li><input type="radio" name="sync" value="dirs" {SYNC_DIRS_CHECKED} /> {L_UPDATE_SYNC_DIRS}</li>
        <li><input type="radio" name="sync" value="files" {SYNC_ALL_CHECKED} /> {L_UPDATE_SYNC_ALL}</li>
        <li><input type="checkbox" name="display_info" value="1" {DISPLAY_INFO_CHECKED} /> {L_UPDATE_DISPLAY_INFO}</li>
        <li><input type="checkbox" name="simulate" value="1" checked="checked" /> {L_UPDATE_SIMULATE}</li>
      </ul>
    </li>
    <li>
      {L_UPDATE_SYNC_METADATA}. {L_USED_METADATA} : {METADATA_LIST}.
      <ul>
        <li><input type="radio" name="sync" value="metadata_new" /> {L_UPDATE_SYNC_METADATA_NEW}</li>
        <li><input type="radio" name="sync" value="metadata_all" /> {L_UPDATE_SYNC_METADATA_ALL}</li>
      </ul>
    </li>
    <li>
      {L_UPDATE_CATS_SUBSET}<br />
      <select style="width:500px" name="cat" size="10">
        <!-- BEGIN category_option -->
        <option {introduction.category_option.SELECTED} value="{introduction.category_option.VALUE}">{introduction.category_option.OPTION}</option>
        <!-- END category_option -->
      </select>
      <input type="checkbox" name="subcats-included" value="1" {SUBCATS_INCLUDED_CHECKED} /> {L_SEARCH_SUBCATS_INCLUDED}
    </li>
  </ul>
  <p>
    <input type="submit" value="{L_SUBMIT}" name="submit" class="bouton" />
    <input type="reset"  value="{L_RESET}"  name="reset"  class="bouton" />
  </p>
</form>
<!-- END introduction -->
