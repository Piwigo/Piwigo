{include file='include/tag_selection.inc.tpl'}

{footer_script}{literal}
jQuery(document).ready(function(){
  function displayDeletionWarnings() {
    jQuery(".warningDeletion").show();
    jQuery("input[name=destination_tag]:checked").parent("label").children(".warningDeletion").hide();
  }

  displayDeletionWarnings();

  jQuery("#mergeTags label").click(function() {
    displayDeletionWarnings();
  });

  jQuery("input[name=merge]").click(function() {
    if (jQuery("ul.tagSelection input[type=checkbox]:checked").length < 2) {
      alert("{/literal}{'Select at least two tags for merging'|@translate}{literal}");
      return false;
    }
  });
});
{/literal}{/footer_script}


<div class="titrePage">
  <h2>{'Manage tags'|@translate}</h2>
</div>

<form action="{$F_ACTION}" method="post">
  {if isset($EDIT_TAGS_LIST)}
  <fieldset>
    <legend>{'Edit tags'|@translate}</legend>
    <input type="hidden" name="edit_list" value="{$EDIT_TAGS_LIST}">
    <table class="table2">
      <tr class="throw">
        <th>{'Current name'|@translate}</th>
        <th>{'New name'|@translate}</th>
      </tr>
      {foreach from=$tags item=tag}
      <tr>
        <td>{$tag.NAME}</td>
        <td><input type="text" name="tag_name-{$tag.ID}" value="{$tag.NAME}" size="30"></td>
      </tr>
      {/foreach}
    </table>

    <p>
      <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
      <input class="submit" type="submit" name="submit" value="{'Submit'|@translate}">
      <input class="submit" type="reset" value="{'Reset'|@translate}">
    </p>
  </fieldset>
  {/if}

  {if isset($MERGE_TAGS_LIST)}
  <input type="hidden" name="merge_list" value="{$MERGE_TAGS_LIST}">

  <fieldset id="mergeTags">
    <legend>{'Merge tags'|@translate}</legend>
    {'Select the destination tag'|@translate}<br><br>
    {foreach from=$tags item=tag name=tagloop}
    <label><input type="radio" name="destination_tag" value="{$tag.ID}"{if $smarty.foreach.tagloop.index == 0} checked="checked"{/if}> {$tag.NAME}<span class="warningDeletion"> {'(this tag will be deleted)'|@translate}</span></label><br>
    {/foreach}
    <br><input type="submit" name="confirm_merge" value="{'Confirm merge'|@translate}">
  </fieldset>
  {/if}

  <fieldset>
    <legend>{'Add a tag'|@translate}</legend>

    <label>
      {'New tag'|@translate}
      <input type="text" name="add_tag" size="30">
    </label>
    
    <p><input class="submit" type="submit" name="add" value="{'Submit'|@translate}"></p>
  </fieldset>

  <fieldset>
    <legend>{'Tag selection'|@translate}</legend>
    
    {$TAG_SELECTION}

    <p>
      <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
      <input class="submit" type="submit" name="edit" value="{'Edit selected tags'|@translate}">
      <input class="submit" type="submit" name="merge" value="{'Merge selected tags'|@translate}">
      <input class="submit" type="submit" name="delete" value="{'Delete selected tags'|@translate}" onclick="return confirm('{'Are you sure?'|@translate}');">
    </p>
  </fieldset>

</form>
