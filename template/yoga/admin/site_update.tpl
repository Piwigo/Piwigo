{* $Id$ *}

<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{$U_HELP}" onclick="popuphelp(this.href); return false;" title="{'Help'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{'title_update'|@translate}: <a href="{$SITE_URL}">{$SITE_URL}</a></h2>
</div>

{if isset($update_result)}
<h3>{$L_RESULT_UPDATE}</h3>
<ul>
  <li class="update_summary_new">{$update_result.NB_NEW_CATEGORIES} {'update_nb_new_categories'|@translate}</li>
  <li class="update_summary_new">{$update_result.NB_NEW_ELEMENTS} {'update_nb_new_elements'|@translate}</li>
  <li class="update_summary_del">{$update_result.NB_DEL_CATEGORIES} {'update_nb_del_categories'|@translate}</li>
  <li class="update_summary_del">{$update_result.NB_DEL_ELEMENTS} {'update_nb_del_elements'|@translate}</li>
  <li>{$update_result.NB_UPD_ELEMENTS} {'update_nb_upd_elements'|@translate}</li>
  <li class="update_summary_err">{$update_result.NB_ERRORS} {'update_nb_errors'|@translate}</li>
</ul>
{/if}

{if isset($metadata_result)}
<h3>{$L_RESULT_METADATA}</h3>
<ul>
  <li>{$metadata_result.NB_ELEMENTS_DONE} {'update_nb_elements_metadata_sync'|@translate}</li>
  <li>{$metadata_result.NB_ELEMENTS_CANDIDATES} {'update_nb_elements_metadata_available'|@translate}</li>
  <li>{'update_used_metadata'|@translate} : {$METADATA_LIST}</li>
</ul>
{/if}


{if not empty($sync_errors)}
<h3>{'update_error_list_title'|@translate}</h3>
<div class="errors">
<ul>
  {foreach from=$sync_errors item=error}
  <li>[{$error.ELEMENT}] {$error.LABEL}</li>
  {/foreach}
</ul>
</div>
<h3>{'update_errors_caption'|@translate}</h3>
<ul>
  {foreach from=$sync_error_captions item=caption}
  <li><strong>{$caption.TYPE}</strong>: {$caption.LABEL}</li>
  {/foreach}
</ul>
{/if}

{if not empty($sync_infos)}
<h3>{'update_infos_title'|@translate}</h3>
<div class="infos">
<ul>
  {foreach from=$sync_infos item=info}
  <li>[{$info.ELEMENT}] {$info.LABEL}</li>
  {/foreach}
</ul>
</div>
{/if}

{if isset($introduction)}
<h3>{'update_default_title'|@translate}</h3>
<form action="" method="post" id="update">

  <fieldset id="syncFiles">
    <legend>{'update_sync_files'|@translate}</legend>
    <ul>
      <li><label><input type="radio" name="sync" value="dirs" {if 'dirs'==$introduction.sync}checked="checked"{/if}/> {'update_sync_dirs'|@translate}</label></li>
      <li><label><input type="radio" name="sync" value="files" {if 'files'==$introduction.sync}checked="checked"{/if}/> {'update_sync_all'|@translate}</label></li>
      <li><label><input type="checkbox" name="display_info" value="1" {if $introduction.display_info}checked="checked"{/if}/> {'update_display_info'|@translate}</label></li>
      <li><label><input type="checkbox" name="add_to_caddie" value="1" {if $introduction.add_to_caddie}checked="checked"{/if}/> {'add new elements to caddie'|@translate}</label></li>
    </ul>
  </fieldset>

  <fieldset id="syncMetadata">
    <legend>{'update_sync_metadata'|@translate}</legend>
    {'update_used_metadata'|@translate} : {$METADATA_LIST}.<br/>
    <ul>
      <li><label><input type="radio" name="sync" value="metadata_new" {if 'metadata_new'==$introduction.sync}checked="checked"{/if}/> {'update_sync_metadata_new'|@translate}</label></li>
      <li><label><input type="radio" name="sync" value="metadata_all" {if 'metadata_all'==$introduction.sync}checked="checked"{/if}/> {'update_sync_metadata_all'|@translate}</label></li>
    </ul>
  </fieldset>

  <fieldset id="syncSimulate">
    <legend></legend>
    <ul><li><label><input type="checkbox" name="simulate" value="1" checked="checked" {$TAG_INPUT_ENABLED} /> {'update_simulate'|@translate}</label></li></ul>
  </fieldset>

  <fieldset id="catSubset">
    <legend>{'update_cats_subset'|@translate}</legend>
    <ul>
    <li>
    <select class="categoryList" name="cat" size="10">
      {html_options options=$category_options selected=$category_options_selected}
    </select>
    </li>

    <li><label><input type="checkbox" name="subcats-included" value="1" {if $introduction.subcats_included}checked="checked"{/if}/> {'search_subcats_included'|@translate}</label></li>
    </ul>
  </fieldset>

  <p class="bottomButtons">
    <input class="submit" type="submit" value="{'Submit'|@translate}" name="submit" />
    <input class="submit" type="reset"  value="{'Reset'|@translate}"  name="reset"  />
  </p>
</form>
{/if}{*isset $introduction*}

<p><a href="{$U_SITE_MANAGER}">{'Site manager'|@translate}</a></p>
