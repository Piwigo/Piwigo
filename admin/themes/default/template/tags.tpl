{include file='include/tag_selection.inc.tpl'}

{html_style}
.showInfo { text-indent:5px; }
{/html_style}

{footer_script require='jquery'}
jQuery('.showInfo').tipTip({
  'delay' : 0,
  'fadeIn' : 200,
  'fadeOut' : 200,
  'maxWidth':'300px',
  'keepAlive':true,
  'activation':'click'
});

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
    alert("{'Select at least two tags for merging'|@translate}");
    return false;
  }
});

$("#searchInput").on("keydown", function(e) {
  var $this = $(this),
      timer = $this.data("timer");

  if (timer) {
    clearTimeout(timer);
  }

  $this.data("timer", setTimeout(function() {
    var val = $this.val();
    if (!val) {
      $(".tagSelection>li").show();
      $("#filterIcon").css("visibility","hidden");
    }
    else {
      $("#filterIcon").css("visibility","visible");
      var regex = new RegExp( val.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&"), "i" );
      $(".tagSelection>li").each(function() {
        var $li = $(this),
            text = $.trim( $("label", $li).text() );
        $li.toggle(regex.test(text));
      });
    }

  }, 300) );

  if (e.keyCode == 13) { // Enter
    e.preventDefault();
  }
});
{/footer_script}


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
        <td><input type="text" name="tag_name-{$tag.ID}" value="{$tag.NAME}" size="50"></td>
      </tr>
      {/foreach}
    </table>

    <p>
      <input type="submit" name="edit_submit" value="{'Submit'|@translate}">
      <input type="submit" name="edit_cancel" value="{'Cancel'|@translate}">
    </p>
  </fieldset>
  {/if}

  {if isset($DUPLIC_TAGS_LIST)}
  <fieldset>
    <legend>{'Edit tags'|@translate}</legend>
    <input type="hidden" name="edit_list" value="{$DUPLIC_TAGS_LIST}">
    <table class="table2">
      <tr class="throw">
        <th>{'Source tag'|@translate}</th>
        <th>{'Name of the duplicate'|@translate}</th>
      </tr>
      {foreach from=$tags item=tag}
      <tr>
        <td>{$tag.NAME}</td>
        <td><input type="text" name="tag_name-{$tag.ID}" value="{$tag.NAME}" size="50"></td>
      </tr>
      {/foreach}
    </table>

    <p>
      <input type="submit" name="duplic_submit" value="{'Submit'|@translate}">
      <input type="submit" name="duplic_cancel" value="{'Cancel'|@translate}">
    </p>
  </fieldset>
  {/if}

  {if isset($MERGE_TAGS_LIST)}
  <fieldset id="mergeTags">
    <legend>{'Merge tags'|@translate}</legend>
    {'Select the destination tag'|@translate}

    <p>
    {foreach from=$tags item=tag name=tagloop}
    <label><input type="radio" name="destination_tag" value="{$tag.ID}"{if $smarty.foreach.tagloop.index == 0} checked="checked"{/if}> {$tag.NAME}<span class="warningDeletion"> {'(this tag will be deleted)'|@translate}</span></label><br>
    {/foreach}
    </p>

    <p>
      <input type="hidden" name="merge_list" value="{$MERGE_TAGS_LIST}">
      <input type="submit" name="merge_submit" value="{'Confirm merge'|@translate}">
      <input type="submit" name="merge_cancel" value="{'Cancel'|@translate}">
    </p>
  </fieldset>
  {/if}

  <fieldset>
    <legend>{'Add a tag'|@translate}</legend>

    <label>
      {'New tag'|@translate}
      <input type="text" name="add_tag" size="50">
    </label>

    <p><input class="submit" type="submit" name="add" value="{'Submit'|@translate}"></p>
  </fieldset>

  <fieldset>
    <legend>{'Tag selection'|@translate}</legend>

    {if count($all_tags)}
    <div><label><span class="icon-filter" style="visibility:hidden" id="filterIcon"></span>{'Search'|@translate}: <input id="searchInput" type="text" size="12"></label></div>
    {/if}

    <ul class="tagSelection">
    {foreach from=$all_tags item=tag}
      <li>
        {capture name='showInfo'}{strip}
          <b>{$tag.name}</b> ({$tag.counter|@translate_dec:'%d photo':'%d photos'})<br>
          <a href="{$tag.U_VIEW}">{'View in gallery'|@translate}</a> |
          <a href="{$tag.U_EDIT}">{'Manage photos'|@translate}</a>
          {if !empty($tag.alt_names)}<br>{$tag.alt_names}{/if}
        {/strip}{/capture}
        <a class="icon-info-circled-1 showInfo" title="{$smarty.capture.showInfo|@htmlspecialchars}"></a>
        <label>
          <input type="checkbox" name="tags[]" value="{$tag.id}"> {$tag.name}
        </label>
      </li>
    {/foreach}
    </ul>

    <p>
      <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
      <input type="submit" name="edit" value="{'Edit selected tags'|@translate}">
      <input type="submit" name="duplicate" value="{'Duplicate selected tags'|@translate}">
      <input type="submit" name="merge" value="{'Merge selected tags'|@translate}">
      <input type="submit" name="delete" value="{'Delete selected tags'|@translate}" onclick="return confirm('{'Are you sure?'|@translate}');">
    </p>
  </fieldset>

</form>
