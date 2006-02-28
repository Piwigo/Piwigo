<!-- $Id: update.tpl 980 2005-12-10 15:24:53Z chrisaga $ -->

<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{lang:title_update}: <a href="{SITE_URL}" target="_blank">{SITE_URL}</a></h2>
</div>

<!-- BEGIN update_result -->
<h3>{L_RESULT_UPDATE}</h3>
<ul>
  <li class="update_summary_new">{update_result.NB_NEW_CATEGORIES} {lang:update_nb_new_categories}</li>
  <li class="update_summary_new">{update_result.NB_NEW_ELEMENTS} {lang:update_nb_new_elements}</li>
  <li class="update_summary_del">{update_result.NB_DEL_CATEGORIES} {lang:update_nb_del_categories}</li>
  <li class="update_summary_del">{update_result.NB_DEL_ELEMENTS} {lang:update_nb_del_elements}</li>
  <li class="update_summary_err">{update_result.NB_ERRORS} {lang:update_nb_errors}</li>
</ul>
<!-- END update_result -->

<!-- BEGIN metadata_result -->
<h3>{L_RESULT_METADATA}</h3>
<ul>
  <li>{metadata_result.NB_ELEMENTS_DONE} {lang:update_nb_elements_metadata_sync}</li>
  <li>{metadata_result.NB_ELEMENTS_CANDIDATES} {lang:update_nb_elements_metadata_available}</li>
  <li>{lang:update_used_metadata} : {METADATA_LIST}</li>
</ul>
<!-- END metadata_result -->


<!-- BEGIN sync_errors -->
<h3>{lang:update_error_list_title}</h3>
<div class="errors">
<ul>
  <!-- BEGIN error -->
  <li>[{sync_errors.error.ELEMENT}] {sync_errors.error.LABEL}</li>
  <!-- END error -->
</ul>
</div>
<h3>{lang:update_errors_caption}</h3>
<ul>
  <!-- BEGIN error_caption -->
  <li><strong>{sync_errors.error_caption.TYPE}</strong>: {sync_errors.error_caption.LABEL}</li>
  <!-- END error_caption -->
</ul>
<!-- END sync_errors -->

<!-- BEGIN sync_infos -->
<h3>{lang:update_infos_title}</h3>
<div class="infos">
<ul>
  <!-- BEGIN info -->
  <li>[{sync_infos.info.ELEMENT}] {sync_infos.info.LABEL}</li>
  <!-- END sync_infos -->
</ul>
</div>
<!-- END infos -->

<!-- BEGIN introduction -->
<h3>{lang:update_default_title}</h3>
<form action="{F_ACTION}" method="post" id="update">

  <fieldset>
    <legend>{lang:update_sync_files}</legend>
    <ul>
      <li><label><input type="radio" name="sync" value="dirs" {SYNC_DIRS_CHECKED} /> {lang:update_sync_dirs}</label></li>
      <li><label><input type="radio" name="sync" value="files" {SYNC_ALL_CHECKED} /> {lang:update_sync_all}</label></li>
      <li><label><input type="checkbox" name="display_info" value="1" {DISPLAY_INFO_CHECKED} /> {lang:update_display_info}</label></li>
    </ul>
  </fieldset>

  <fieldset>
    <legend>{lang:update_sync_metadata}</legend>
    {lang:update_used_metadata} : {METADATA_LIST}.<br/>
    <ul>
      <li><label><input type="radio" name="sync" value="metadata_new" {SYNC_META_NEW_CHECKED} /> {lang:update_sync_metadata_new}</label></li>
      <li><label><input type="radio" name="sync" value="metadata_all" {SYNC_META_ALL_CHECKED} /> {lang:update_sync_metadata_all}</label></li>
    </ul>
  </fieldset>

  <fieldset>
    <legend></legend>
    <ul><li><label><input type="checkbox" name="simulate" value="1" checked="checked" /> {lang:update_simulate}</label></li></ul>
  </fieldset>
  
  <fieldset>
    <legend>{lang:update_cats_subset}</legend>
    <ul>
    <li> 
    <select style="width:500px" name="cat" size="10">
      <!-- BEGIN category_option -->
      <option {introduction.category_option.SELECTED} value="{introduction.category_option.VALUE}">{introduction.category_option.OPTION}</option>
      <!-- END category_option -->
    </select>
    </li>
  
    <li><label><input type="checkbox" name="subcats-included" value="1" {SUBCATS_INCLUDED_CHECKED} /> {lang:search_subcats_included}</label></li>
    </ul>
  </fieldset>

  <p class="bottomButtons">
    <input type="submit" value="{lang:submit}" name="submit" />
    <input type="reset"  value="{lang:reset}"  name="reset"  />
  </p>
</form>
<!-- END introduction -->

<a href="{U_SITE_MANAGER}">{lang:Site manager}</a>
