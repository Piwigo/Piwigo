<!-- $Id$ -->

<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="template/cclear/theme/help.png" alt="(?)"></a></li>
  </ul>
  <h2>{lang:title_update}</h2>
</div>

<!-- BEGIN update -->
<h3>{L_RESULT_UPDATE}</h3>
<ul>
  <li class="update_summary_new">{update.NB_NEW_CATEGORIES} {L_NB_NEW_CATEGORIES}</li>
  <li class="update_summary_new">{update.NB_NEW_ELEMENTS} {L_NB_NEW_ELEMENTS}</li>
  <li class="update_summary_del">{update.NB_DEL_CATEGORIES} {L_NB_DEL_CATEGORIES}</li>
  <li class="update_summary_del">{update.NB_DEL_ELEMENTS} {L_NB_DEL_ELEMENTS}</li>
  <li class="update_summary_err">{update.NB_ERRORS} {L_UPDATE_NB_ERRORS}</li>
</ul>
<!-- BEGIN update_errors -->
<h3>{L_UPDATE_ERROR_LIST_TITLE}</h3>
<ul>
  <!-- BEGIN update_error -->
  <li>[{update.update_errors.update_error.ELEMENT}] {update.update_errors.update_error.LABEL}</li>
  <!-- END update_error -->
</ul>
<h3>{L_UPDATE_ERRORS_CAPTION}</h3>
<ul>
  <li><strong>PWG-UPDATE-1</strong> : {L_UPDATE_WRONG_DIRNAME_INFO}</li>
  <li><strong>PWG-UPDATE-2</strong> : {L_UPDATE_MISSING_TN_INFO} {{PICTURE_EXT_LIST}}</li>
</ul>
<!-- END update_errors -->
<!-- BEGIN update_infos -->
<h3>{L_UPDATE_INFOS_TITLE}</h3>
<ul>
  <!-- BEGIN update_info -->
  <li>[{update.update_infos.update_info.ELEMENT}] {update.update_infos.update_info.LABEL}</li>
  <!-- END update_info -->
</ul>
<!-- END update_infos -->
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

  <fieldset>
    <legend>{L_UPDATE_SYNC_FILES}</legend>

    <ul>
      <li><label><input type="radio" name="sync" value="dirs" {SYNC_DIRS_CHECKED} /> {L_UPDATE_SYNC_DIRS}</label></li>
      <li><label><input type="radio" name="sync" value="files" {SYNC_ALL_CHECKED} /> {L_UPDATE_SYNC_ALL}</label></li>
      <li><label><input type="checkbox" name="display_info" value="1" {DISPLAY_INFO_CHECKED} /> {L_UPDATE_DISPLAY_INFO}</label></li>
      <li><label><input type="checkbox" name="simulate" value="1" checked="checked" /> {L_UPDATE_SIMULATE}</label></li>
    </ul>
  </fieldset>

  <fieldset>
    <legend>{L_UPDATE_SYNC_METADATA}</legend>
    <p> {L_USED_METADATA} : {METADATA_LIST}.</p>
    <ul>
      <li><label><input type="radio" name="sync" value="metadata_new" /> {L_UPDATE_SYNC_METADATA_NEW}</label></li>
      <li><label><input type="radio" name="sync" value="metadata_all" /> {L_UPDATE_SYNC_METADATA_ALL}</label></li>
    </ul>
  </fieldset>
  
  <fieldset>
    <legend>{L_UPDATE_CATS_SUBSET}</legend>
  
    <select style="width:500px" name="cat" size="10">
      <!-- BEGIN category_option -->
      <option {introduction.category_option.SELECTED} value="{introduction.category_option.VALUE}">{introduction.category_option.OPTION}</option>
      <!-- END category_option -->
    </select>
  
    <label><input type="checkbox" name="subcats-included" value="1" {SUBCATS_INCLUDED_CHECKED} /> {L_SEARCH_SUBCATS_INCLUDED}</label>
  </fieldset>

  <p class="bottomButtons">
    <input type="submit" value="{L_SUBMIT}" name="submit" />
    <input type="reset"  value="{L_RESET}"  name="reset"  />
  </p>

</form>
<!-- END introduction -->
