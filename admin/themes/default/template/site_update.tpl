
{include file='include/resize.inc.tpl'}

<div class="titrePage">
  <h2>{'Database synchronization with files'|@translate}: <a href="{$SITE_URL}">{$SITE_URL}</a></h2>
</div>

{if isset($update_result)}
<h3>{$L_RESULT_UPDATE}</h3>
<ul>
  <li class="update_summary_new">{$update_result.NB_NEW_CATEGORIES} {'categories added in the database'|@translate}</li>
  <li class="update_summary_new">{$update_result.NB_NEW_ELEMENTS} {'elements added in the database'|@translate}</li>
  <li class="update_summary_del">{$update_result.NB_DEL_CATEGORIES} {'categories deleted in the database'|@translate}</li>
  <li class="update_summary_del">{$update_result.NB_DEL_ELEMENTS} {'elements deleted in the database'|@translate}</li>
  <li>{$update_result.NB_UPD_ELEMENTS} {'elements updated in the database'|@translate}</li>
  <li class="update_summary_err">{$update_result.NB_ERRORS} {'errors during synchronization'|@translate}</li>
</ul>
{/if}

{if isset($metadata_result)}
<h3>{$L_RESULT_METADATA}</h3>
<ul>
  <li>{$metadata_result.NB_ELEMENTS_DONE} {'elements informations synchronized with files metadata'|@translate}</li>
  <li>{$metadata_result.NB_ELEMENTS_CANDIDATES} {'images candidates for metadata synchronization'|@translate}</li>
  <li>{'Used metadata'|@translate} : {$METADATA_LIST}</li>
</ul>
{/if}


{if not empty($sync_errors)}
<h3>{'Error list'|@translate}</h3>
<div class="errors">
<ul>
  {foreach from=$sync_errors item=error}
  <li>[{$error.ELEMENT}] {$error.LABEL}</li>
  {/foreach}
</ul>
</div>
<h3>{'Errors caption'|@translate}</h3>
<ul>
  {foreach from=$sync_error_captions item=caption}
  <li><strong>{$caption.TYPE}</strong>: {$caption.LABEL}</li>
  {/foreach}
</ul>
{/if}

{if not empty($sync_infos)}
<h3>{'Detailed informations'|@translate}</h3>
<div class="infos">
<ul>
  {foreach from=$sync_infos item=info}
  <li>[{$info.ELEMENT}] {$info.LABEL}</li>
  {/foreach}
</ul>
</div>
{/if}

{if isset($introduction)}
<h4>{'Choose an option'|@translate}</h4>
<form action="" method="post" id="update">

	<fieldset id="syncFiles">
		<legend>{'synchronize files structure with database'|@translate}</legend>
		<ul>
			<li><label><input type="radio" name="sync" value="" {if empty($introduction.sync)}checked="checked"{/if}> {'nothing'|@translate}</label></li>
			<li><label><input type="radio" name="sync" value="dirs" {if 'dirs'==$introduction.sync}checked="checked"{/if}> {'only directories'|@translate}</label></li>

			<li><label><input type="radio" name="sync" value="files" {if 'files'==$introduction.sync}checked="checked"{/if}> {'directories + files'|@translate}</label>
				<ul style="padding-left:3em">
					<li><label><input type="checkbox" name="display_info" value="1" {if $introduction.display_info}checked="checked"{/if}> {'display maximum informations (added categories and elements, deleted categories and elements)'|@translate}</label></li>
					<li><label><input type="checkbox" name="add_to_caddie" value="1" {if $introduction.add_to_caddie}checked="checked"{/if}> {'add new elements to caddie'|@translate}</label></li>
					<li><label>{'Who can see these photos?'|@translate} <select name="privacy_level">{html_options options=$introduction.privacy_level_options selected=$introduction.privacy_level_selected}</select></label></li>
				</ul>
			</li>
		</ul>
	</fieldset>

	<fieldset id="syncMetadata">
		<legend>{'synchronize files metadata with database elements informations'|@translate}</legend>
		<label><input type="checkbox" name="sync_meta" {if $introduction.sync_meta}checked="checked"{/if}> {'synchronize metadata'|@translate} ({$METADATA_LIST})</label>
		<ul style="padding-left:3em">
	  		<li>
	  			<label><input type="checkbox" name="meta_all" {if $introduction.meta_all}checked="checked"{/if}> {'even already synchronized elements'|@translate}</label>
	  		</li>
	  		<li>
	  			<label><input type="checkbox" name="meta_empty_overrides" {if $introduction.meta_empty_overrides}checked="checked"{/if}> {'overrides existing values with empty ones'|@translate}</label>
	  		</li>
		</ul>
	</fieldset>

  <fieldset id="syncSimulate">
    <legend></legend>
    <ul><li><label><input type="checkbox" name="simulate" value="1" checked="checked" {$TAG_INPUT_ENABLED}> {'only perform a simulation (no change in database will be made)'|@translate}</label></li></ul>
  </fieldset>

  <fieldset id="catSubset">
    <legend>{'reduce to single existing categories'|@translate}</legend>
    <ul>
    <li>
    <select class="categoryList" name="cat" size="10">
      {html_options options=$category_options selected=$category_options_selected}
    </select>
    </li>

    <li><label><input type="checkbox" name="subcats-included" value="1" {if $introduction.subcats_included}checked="checked"{/if}> {'Search in sub-albums'|@translate}</label></li>
    </ul>
  </fieldset>

  <p class="bottomButtons">
    <input class="submit" type="submit" value="{'Submit'|@translate}" name="submit">
    <input class="submit" type="reset"  value="{'Reset'|@translate}"  name="reset">
  </p>
</form>
{/if}{*isset $introduction*}

<p><a href="{$U_SITE_MANAGER}">{'Site manager'|@translate}</a></p>
