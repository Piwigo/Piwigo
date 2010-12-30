{include file='include/tag_selection.inc.tpl'}
{include file='include/datepicker.inc.tpl'}

{footer_script}{literal}
  pwg_initialization_datepicker("#date_creation_day", "#date_creation_month", "#date_creation_year", "#date_creation_linked_date", "#date_creation_action_set");
{/literal}{/footer_script}

{combine_script id='jquery.fcbkcomplete' load='footer' require='jquery' path='themes/default/js/plugins/jquery.fcbkcomplete.js'}

{footer_script require='jquery.fcbkcomplete'}{literal}
jQuery(document).ready(function() {
  jQuery("#tags").fcbkcomplete({
    json_url: "admin.php?fckb_tags=1",
    cache: false,
    filter_case: false,
    filter_hide: true,
    firstselected: true,
    filter_selected: true,
    maxitems: 100,
    newel: true
  });
});
{/literal}{/footer_script}

{footer_script}
var nb_thumbs_page = {$nb_thumbs_page};
var nb_thumbs_set = {$nb_thumbs_set};
var applyOnDetails_pattern = "{'on the %d selected photos'|@translate}";

var selectedMessage_pattern = "{'%d of %d photos selected'|@translate}";
var selectedMessage_none = "{'No photo selected, %d photos in current set'|@translate}";
var selectedMessage_all = "{'All %d photos are selected'|@translate}";
{literal}
function str_repeat(i, m) {
        for (var o = []; m > 0; o[--m] = i);
        return o.join('');
}

function sprintf() {
        var i = 0, a, f = arguments[i++], o = [], m, p, c, x, s = '';
        while (f) {
                if (m = /^[^\x25]+/.exec(f)) {
                        o.push(m[0]);
                }
                else if (m = /^\x25{2}/.exec(f)) {
                        o.push('%');
                }
                else if (m = /^\x25(?:(\d+)\$)?(\+)?(0|'[^$])?(-)?(\d+)?(?:\.(\d+))?([b-fosuxX])/.exec(f)) {
                        if (((a = arguments[m[1] || i++]) == null) || (a == undefined)) {
                                throw('Too few arguments.');
                        }
                        if (/[^s]/.test(m[7]) && (typeof(a) != 'number')) {
                                throw('Expecting number but found ' + typeof(a));
                        }
                        switch (m[7]) {
                                case 'b': a = a.toString(2); break;
                                case 'c': a = String.fromCharCode(a); break;
                                case 'd': a = parseInt(a); break;
                                case 'e': a = m[6] ? a.toExponential(m[6]) : a.toExponential(); break;
                                case 'f': a = m[6] ? parseFloat(a).toFixed(m[6]) : parseFloat(a); break;
                                case 'o': a = a.toString(8); break;
                                case 's': a = ((a = String(a)) && m[6] ? a.substring(0, m[6]) : a); break;
                                case 'u': a = Math.abs(a); break;
                                case 'x': a = a.toString(16); break;
                                case 'X': a = a.toString(16).toUpperCase(); break;
                        }
                        a = (/[def]/.test(m[7]) && m[2] && a >= 0 ? '+'+ a : a);
                        c = m[3] ? m[3] == '0' ? '0' : m[3].charAt(1) : ' ';
                        x = m[5] - String(a).length - s.length;
                        p = m[5] ? str_repeat(c, x) : '';
                        o.push(s + (m[4] ? a + p : p + a));
                }
                else {
                        throw('Huh ?!');
                }
                f = f.substring(m[0].length);
        }
        return o.join('');
}

$(document).ready(function() {
  function checkPermitAction() {
    var nbSelected = 0;
    if ($("input[name=setSelected]").is(':checked')) {
      nbSelected = nb_thumbs_set;
    }
    else {
      $(".thumbnails input[type=checkbox]").each(function() {
         if ($(this).is(':checked')) {
           nbSelected++;
         }
      });
    }

    if (nbSelected == 0) {
      $("#permitAction").hide();
      $("#forbidAction").show();
    }
    else {
      $("#permitAction").show();
      $("#forbidAction").hide();
    }

    $("#applyOnDetails").text(
      sprintf(
        applyOnDetails_pattern,
        nbSelected
      )
    );

    // display the number of currently selected photos in the "Selection" fieldset
    if (nbSelected == 0) {
      $("#selectedMessage").text(
        sprintf(
          selectedMessage_none,
          nb_thumbs_set
        )
      );
    }
    else if (nbSelected == nb_thumbs_set) {
      $("#selectedMessage").text(
        sprintf(
          selectedMessage_all,
          nb_thumbs_set
        )
      );
    }
    else {
      $("#selectedMessage").text(
        sprintf(
          selectedMessage_pattern,
          nbSelected,
          nb_thumbs_set
        )
      );
    }
  }

  $('img.thumbnail').tipTip({
    'delay' : 0,
    'fadeIn' : 200,
    'fadeOut' : 200,
  });

  $("[id^=action_]").hide();

  $("select[name=selectAction]").click(function () {
    $("[id^=action_]").hide();
    $("#action_"+$(this).attr("value")).show();
  });

  $(".wrap1 label").click(function () {
    $("input[name=setSelected]").attr('checked', false);

    var wrap2 = $(this).children(".wrap2");
    var checkbox = $(this).children("input[type=checkbox]");

    if ($(checkbox).is(':checked')) {
      $(wrap2).addClass("thumbSelected"); 
    }
    else {
      $(wrap2).removeClass('thumbSelected'); 
    }

    checkPermitAction();
  });

  $("#selectAll").click(function () {
    $(".thumbnails label").each(function() {
      var wrap2 = $(this).children(".wrap2");
      var checkbox = $(this).children("input[type=checkbox]");

      $(checkbox).attr('checked', true);
      $(wrap2).addClass("thumbSelected"); 
    });

    if (nb_thumbs_page < nb_thumbs_set) {
      $("#selectSetMessage").show();
    }

    checkPermitAction();

    return false;
  });

  $("#selectNone").click(function () {
    $("input[name=setSelected]").attr('checked', false);

    $(".thumbnails label").each(function() {
      var wrap2 = $(this).children(".wrap2");
      var checkbox = $(this).children("input[type=checkbox]");

      $(checkbox).attr('checked', false);
      $(wrap2).removeClass("thumbSelected"); 
    });
    checkPermitAction();
    return false;
  });

  $("#selectInvert").click(function () {
    $("#selectSetMessage").hide();
    $("input[name=setSelected]").attr('checked', false);

    $(".thumbnails label").each(function() {
      var wrap2 = $(this).children(".wrap2");
      var checkbox = $(this).children("input[type=checkbox]");

      $(checkbox).attr('checked', !$(checkbox).is(':checked'));

      if ($(checkbox).is(':checked')) {
        $(wrap2).addClass("thumbSelected"); 
      }
      else {
        $(wrap2).removeClass('thumbSelected'); 
      }
    });
    checkPermitAction();
    return false;
  });

  $("#selectSet").click(function () {
    $("input[name=setSelected]").attr('checked', true);
    checkPermitAction();
    return false;
  });

  $("input[name=remove_author]").click(function () {
    if ($(this).is(':checked')) {
      $("input[name=author]").hide();
    }
    else {
      $("input[name=author]").show();
    }
  });

  $("input[name=remove_name]").click(function () {
    if ($(this).is(':checked')) {
      $("input[name=name]").hide();
    }
    else {
      $("input[name=name]").show();
    }
  });

  $("input[name=remove_date_creation]").click(function () {
    if ($(this).is(':checked')) {
      $("#set_date_creation").hide();
    }
    else {
      $("#set_date_creation").show();
    }
  });

  $("select[name=selectAction]").change(function() {
    if ($(this).val() != -1) {
      $("#applyActionBlock").show();
    }
    else {
      $("#applyActionBlock").hide();
    }
  });

  $(".removeFilter").click(function () {
    var filter = $(this).parent('li').attr("id");
    filter_disable(filter);

    return false;
  });

  function filter_enable(filter) {
    /* show the filter*/
    $("#"+filter).show();

    /* check the checkbox to declare we use this filter */
    $("input[type=checkbox][name="+filter+"_use]").attr("checked", true);

    /* forbid to select this filter in the addFilter list */
    $("#addFilter").children("option[value="+filter+"]").attr("disabled", "disabled");
  }

  $("#addFilter").change(function () {
    var filter = $(this).attr("value");
    filter_enable(filter);
    $(this).attr("value", -1);
  });

  function filter_disable(filter) {
    /* hide the filter line */
    $("#"+filter).hide();

    /* uncheck the checkbox to declare we do not use this filter */
    $("input[name="+filter+"_use]").removeAttr("checked");

    /* give the possibility to show it again */
    $("#addFilter").children("option[value="+filter+"]").removeAttr("disabled");
  }

  $("#removeFilters").click(function() {
    $("#filterList li").each(function() {
      var filter = $(this).attr("id");
      filter_disable(filter);
    });
    return false;
  });

  checkPermitAction()
});
{/literal}{/footer_script}

{literal}
<style>
#action p {text-align:left;}
.bulkAction {text-align:left;margin:15px 0;padding:0;}
#action_del_tags ul.tagSelection {margin:0 !important; width:620px;}
#selectAction {}
#checkActions {text-align:left; margin:0 0 20px 0;}
.content ul.thumbnails span.wrap1 {margin:5px}
.content ul.thumbnails span.wrap2 {border:0;background-color:#ddd;}
.content ul.thumbnails span.wrap2:hover {background-color:#7CBA0F;}
.thumbSelected {background-color:#C2F5C2 !important}

#selectedMessage {background-color:#C2F5C2; padding:5px; -moz-border-radius:5px;}
#selectSet a {border-bottom:1px dotted}
#applyOnDetails {font-style:italic;}

.actionButtons {text-align:left;}
#filterList {padding-left:5px;}
#filterList li {margin-bottom:5px; list-style-type:none;}
a.removeFilter {background: url(plugins/bulk_manager/remove_filter.png) no-repeat top left;width:7px;height:7px;display:inline-block}
a.removeFilter:hover {background: url(plugins/bulk_manager/remove_filter_hover.png); border:none;}
.removeFilter span {display:none}
#applyFilterBlock {margin-top:20px;}
.useFilterCheckbox {display:none}
</style>
{/literal}

  <p style="float:left; font-size:90%;margin:5px 0 0 0;padding:0;">
    <a href="{$U_UNIT_MODE}">Switch to unit mode</a>
  </p>

<h2>{'Batch manager'|@translate}</h2>

  <form action="{$F_ACTION}" method="post">

  <fieldset>
    <legend>{'Filter'|@translate}</legend>

    <ul id="filterList">
      <li id="filter_prefilter" {if !isset($filter.prefilter)}style="display:none"{/if}>
        <a href="#" class="removeFilter" title="remove this filter"><span>[x]</span></a>
        <input type="checkbox" name="filter_prefilter_use" class="useFilterCheckbox" {if isset($filter.prefilter)}checked="checked"{/if}>
        predefined filter
        <select name="filter_prefilter">
          <option value="caddie" {if $filter.prefilter eq 'caddie'}selected="selected"{/if}>caddie</option>
          <option value="last import" {if $filter.prefilter eq 'last import'}selected="selected"{/if}>last import</option>
<!--          <option value="with no album">with no album</option> -->
<!--          <option value="with no virtual album">with no virtual album</option> -->
<!--          <option value="with no tag">with no tag</option> -->
        </select>
      </li>
      <li id="filter_category" {if !isset($filter.category)}style="display:none"{/if}>
        <a href="#" class="removeFilter" title="remove this filter"><span>[x]</span></a>
        <input type="checkbox" name="filter_category_use" class="useFilterCheckbox" {if isset($filter.category)}checked="checked"{/if}>
        album
        <select style="width:400px" name="filter_category" size="1">
          {html_options options=$filter_category_options selected=$filter_category_options_selected}
        </select>
        <label><input type="checkbox" name="filter_category_recursive" {if isset($filter.category_recursive)}checked="checked"{/if}> {'include child albums'|@translate}</label>
      </li>
      <li id="filter_level" {if !isset($filter.level)}style="display:none"{/if}>
        <a href="#" class="removeFilter" title="remove this filter"><span>[x]</span></a>
        <input type="checkbox" name="filter_level_use" class="useFilterCheckbox" {if isset($filter.level)}checked="checked"{/if}>
        {'Who can see these photos?'|@translate}
        <select name="filter_level" size="1">
          {html_options options=$filter_level_options selected=$filter_level_options_selected}
        </select>
      </li>
    </ul>

    <p class="actionButtons" style="">
      <select id="addFilter">
        <option value="-1">Add a filter</option>
        <option disabled="disabled">------------------</option>
        <option value="filter_prefilter">predefined filter</option>
        <option value="filter_category">album</option>
        <option value="filter_level">{'Who can see these photos?'|@translate}</option>
      </select>
<!--      <input id="removeFilters" class="submit" type="submit" value="Remove all filters" name="removeFilters"> -->
      <a id="removeFilters" href="">Remove all filters</a>
    </p>

    <p class="actionButtons" id="applyFilterBlock">
      <input id="applyFilter" class="submit" type="submit" value="Refresh photo set" name="submitFilter">
    </p>

  </fieldset>

  <fieldset>

    <legend>{'Selection'|@translate}</legend>

  {if !empty($thumbnails)}
  <p id="checkActions">
    {'Select:'|@translate}
    <a href="#" id="selectAll">{'All'|@translate}</a>
    (<a href="#" id="selectSet">or the whole set</a>),
    <a href="#" id="selectNone">{'None'|@translate}</a>,
    <a href="#" id="selectInvert">{'Invert'|@translate}</a>

    <span id="selectedMessage"></span>

    <input type="checkbox" name="setSelected" style="display:none" {if count($selection) == $nb_thumbs_set}checked="checked"{/if}>
  </p>

    <ul class="thumbnails">
      {foreach from=$thumbnails item=thumbnail}
        {if in_array($thumbnail.ID, $selection)}
          {assign var='isSelected' value=true}
        {else}
          {assign var='isSelected' value=false}
        {/if}

      <li><span class="wrap1">
          <label>
            <span class="wrap2{if $isSelected} thumbSelected{/if}">
        {if $thumbnail.LEVEL > 0}
        <em class="levelIndicatorB">{$pwg->l10n($pwg->sprintf('Level %d',$thumbnail.LEVEL))}</em>
        <em class="levelIndicatorF" title="{'Who can see these photos?'|@translate} : ">{$pwg->l10n($pwg->sprintf('Level %d',$thumbnail.LEVEL))}</em>
        {/if}
            <span>
              <img src="{$thumbnail.TN_SRC}"
                 alt="{$thumbnail.FILE}"
                 title="{$thumbnail.TITLE|@escape:'html'}"
                 class="thumbnail">
            </span></span>
            <input type="checkbox" name="selection[]" value="{$thumbnail.ID}" {if $isSelected}checked="checked"{/if}>
          </label>
          </span>
      </li>
      {/foreach}
    </ul>

  {if !empty($navbar) }
  <div style="clear:both;">

    <div style="float:left">
    {include file='navigation_bar.tpl'|@get_extent:'navbar'}
    </div>

    <div style="float:right;margin-top:10px;">{'display'|@translate}
      <a href="{$U_DISPLAY}&amp;display=20">20</a>
      &middot; <a href="{$U_DISPLAY}&amp;display=50">50</a>
      &middot; <a href="{$U_DISPLAY}&amp;display=100">100</a>
      &middot; <a href="{$U_DISPLAY}&amp;display=all">{'all'|@translate}</a>
      thumbnails per page
    </div>
  </div>
  {/if}

  {else}
  <div>No photo in the current set.</div>
  {/if}
  </fieldset>

  <fieldset id="action">

    <legend>{'Action'|@translate}</legend>
      <div id="forbidAction"{if count($selection) != 0}style="display:none"{/if}>No photo selected, no action possible.</div>
      <div id="permitAction"{if count($selection) == 0}style="display:none"{/if}>

    <select name="selectAction">
      <option value="-1">Choose an action</option>
      <option disabled="disabled">------------------</option>
  {if isset($show_delete_form) }
      <option value="delete">{'Delete selected photos'|@translate}</option>
  {/if}
      <option value="associate">{'associate to category'|@translate}</option>
  {if !empty($dissociate_options)}
      <option value="dissociate">{'dissociate from category'|@translate}</option>
  {/if}
      <option value="add_tags">{'add tags'|@translate}</option>
  {if !empty($DEL_TAG_SELECTION)}
      <option value="del_tags">{'remove tags'|@translate}</option>
  {/if}
      <option value="author">{'Set author'|@translate}</option>
      <option value="name">{'Set title'|@translate}</option>
      <option value="date_creation">{'Set creation date'|@translate}</option>
      <option value="level">{'Who can see these photos?'|@translate}</option>
  {if ($IN_CADDIE)}
      <option value="remove_from_caddie">{'Remove from caddie'|@translate}</option>
  {else}
      <option value="add_to_caddie">{'Add to caddie'|@translate}</option>
  {/if}
    </select>

    <!-- delete -->
    <div id="action_delete" class="bulkAction">
{if $ENABLE_SYNCHRONIZATION}
    <p style="font-style:italic;width:500px;">{'Note: photo deletion does not apply to photos added by synchronization. For photos added by synchronization, remove them from the filesystem and then perform another synchronization.'|@translate}</p>
{/if}
    <p><label><input type="checkbox" name="confirm_deletion" value="1"> {'Are you sure?'|@translate}</label></p>
    </div>

    <!-- associate -->
    <div id="action_associate" class="bulkAction">
          <select style="width:400px" name="associate" size="1">
            {html_options options=$associate_options }
         </select>
    </div>

    <!-- dissociate -->
    <div id="action_dissociate" class="bulkAction">
          <select style="width:400px" name="dissociate" size="1">
            {if !empty($dissociate_options)}{html_options options=$dissociate_options }{/if}
          </select>
    </div>


    <!-- add_tags -->
    <div id="action_add_tags" class="bulkAction">
<select id="tags" name="add_tags">
</select>
    </div>

    <!-- del_tags -->
    <div id="action_del_tags" class="bulkAction">
{$DEL_TAG_SELECTION}
    </div>

    <!-- author -->
    <div id="action_author" class="bulkAction">
    <label><input type="checkbox" name="remove_author"> remove author</label><br>
    {assign var='authorDefaultValue' value='Type here the author name'}
<input type="text" class="large" name="author" value="{$authorDefaultValue}" onfocus="this.value=(this.value=='{$authorDefaultValue}') ? '' : this.value;" onblur="this.value=(this.value=='') ? '{$authorDefaultValue}' : this.value;">
    </div>    

    <!-- name -->
    <div id="action_name" class="bulkAction">
    <label><input type="checkbox" name="remove_name"> remove name</label><br>
    {assign var='nameDefaultValue' value='Type here the name name'}
<input type="text" class="large" name="name" value="{$nameDefaultValue}" onfocus="this.value=(this.value=='{$nameDefaultValue}') ? '' : this.value;" onblur="this.value=(this.value=='') ? '{$nameDefaultValue}' : this.value;">
    </div>

    <!-- date_creation -->
    <div id="action_date_creation" class="bulkAction">
      <label><input type="checkbox" name="remove_date_creation"> remove creation date</label><br>
      <div id="set_date_creation">
          <select id="date_creation_day" name="date_creation_day">
             <option value="0">--</option>
            {section name=day start=1 loop=32}
              <option value="{$smarty.section.day.index}" {if $smarty.section.day.index==$DATE_CREATION_DAY}selected="selected"{/if}>{$smarty.section.day.index}</option>
            {/section}
          </select>
          <select id="date_creation_month" name="date_creation_month">
            {html_options options=$month_list selected=$DATE_CREATION_MONTH}
          </select>
          <input id="date_creation_year"
                 name="date_creation_year"
                 type="text"
                 size="4"
                 maxlength="4"
                 value="{$DATE_CREATION_YEAR}">
          <input id="date_creation_linked_date" name="date_creation_linked_date" type="hidden" size="10" disabled="disabled">
      </div>
    </div>

    <!-- level -->
    <div id="action_level" class="bulkAction">
        <select name="level" size="1">
          {html_options options=$level_options selected=$level_options_selected}
        </select>
    </div>

    <p id="applyActionBlock" style="display:none" class="actionButtons">
      <input id="applyAction" class="submit" type="submit" value="{'Apply action'|@translate}" name="submit" {$TAG_INPUT_ENABLED}> <span id="applyOnDetails"></span></p>

    </div> <!-- #permitAction -->
  </fieldset>

  </form>
