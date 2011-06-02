{include file='include/autosize.inc.tpl'}
{include file='include/datepicker.inc.tpl'}
{include file='include/colorbox.inc.tpl'}

{combine_script id='jquery.tokeninput' load='async' require='jquery' path='themes/default/js/plugins/jquery.tokeninput.js'}
{footer_script require='jquery.tokeninput'}
var tag_boxes_selector = "";
{foreach from=$elements item=element name=element}
{if $smarty.foreach.element.first}
var prefix = "";
{else}
prefix = ", ";
{/if}
	tag_boxes_selector = tag_boxes_selector + prefix + "#tags-" + {$element.ID};
{/foreach}

jQuery(document).ready(function() {ldelim}
  jQuery(tag_boxes_selector).tokenInput(
    [{foreach from=$tags item=tag name=tags}{ldelim}"name":"{$tag.name|@escape:'javascript'}","id":"{$tag.id}"{rdelim}{if !$smarty.foreach.tags.last},{/if}{/foreach}],
    {ldelim}
      hintText: '{'Type in a search term'|@translate}',
      noResultsText: '{'No results'|@translate}',
      searchingText: '{'Searching...'|@translate}',
      newText: ' ({'new'|@translate})',
      animateDropdown: false,
      preventDuplicates: true,
      allowCreation: true
    }
  );

  jQuery("a.preview-box").colorbox();
});
{/footer_script}

<h2>{'Batch Manager'|@translate}</h2>

<form action="{$F_ACTION}" method="POST">
<fieldset>
  <legend>{'Display options'|@translate}</legend>
  <p>{'photos per page'|@translate} :
      <a href="{$U_ELEMENTS_PAGE}&amp;display=5">5</a>
    | <a href="{$U_ELEMENTS_PAGE}&amp;display=10">10</a>
    | <a href="{$U_ELEMENTS_PAGE}&amp;display=50">50</a>
    | <a href="{$U_ELEMENTS_PAGE}&amp;display=all">{'all'|@translate}</a>
  </p>

</fieldset>

{if !empty($navbar) }{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}

{if !empty($elements) }
<div><input type="hidden" name="element_ids" value="{$ELEMENT_IDS}"></div>
{foreach from=$elements item=element}
<fieldset class="elementEdit">
  <legend>{$element.LEGEND}</legend>

  <span class="thumb">
    <a href="{$element.FILE_SRC}" class="preview-box" title="{$element.LEGEND}"><img src="{$element.TN_SRC}" alt=""></a>
    <br/>
    <a href="{$element.U_EDIT}">{'Informations'|@translate}</a>
  </span>

  <table>

    <tr>
      <td><strong>{'Name'|@translate}</strong></td>
      <td><input type="text" class="large" name="name-{$element.ID}" value="{$element.NAME}"></td>
    </tr>

    <tr>
      <td><strong>{'Author'|@translate}</strong></td>
      <td><input type="text" class="large" name="author-{$element.ID}" value="{$element.AUTHOR}"></td>
    </tr>

    <tr>
      <td><strong>{'Creation date'|@translate}</strong></td>
      <td>
        <label><input type="radio" name="date_creation_action-{$element.ID}" value="unset"> {'unset'|@translate}</label>
        <label><input type="radio" name="date_creation_action-{$element.ID}" value="set" id="date_creation_action_set-{$element.ID}"> {'set to'|@translate}</label>

        <select id="date_creation_day-{$element.ID}" name="date_creation_day-{$element.ID}">
         	<option value="0">--</option>
           {section name=day start=1 loop=32}
             <option value="{$smarty.section.day.index}" {if $smarty.section.day.index==$element.DATE_CREATION_DAY}selected="selected"{/if}>{$smarty.section.day.index}</option>
           {/section}
        </select>
        <select id="date_creation_month-{$element.ID}" name="date_creation_month-{$element.ID}">
          {html_options options=$month_list selected=$element.DATE_CREATION_MONTH}
        </select>
        <input id="date_creation_year-{$element.ID}"
               name="date_creation_year-{$element.ID}"
               type="text"
               size="4"
               maxlength="4"
               value="{$element.DATE_CREATION_YEAR}">
        <input id="date_creation_linked_date-{$element.ID}" name="date_creation_linked_date-{$element.ID}" type="hidden" size="10" disabled="disabled">
        {footer_script}
          pwg_initialization_datepicker("#date_creation_day-{$element.ID}", "#date_creation_month-{$element.ID}", "#date_creation_year-{$element.ID}", "#date_creation_linked_date-{$element.ID}", "#date_creation_action_set-{$element.ID}");
        {/footer_script}
      </td>
    </tr>
    <tr>
      <td><strong>{'Who can see this photo?'|@translate}</strong></td>
      <td>
        <select name="level-{$element.ID}">
          {html_options options=$level_options selected=$element.LEVEL}
        </select>
      </td>
    </tr>

    <tr>
      <td><strong>{'Tags'|@translate}</strong></td>
      <td>

<select id="tags-{$element.ID}" name="tags-{$element.ID}">
{foreach from=$element.TAGS item=tag}
  <option value="{$tag.id}" class="selected">{$tag.name}</option>
{/foreach}
</select>

      </td>
    </tr>

    <tr>
      <td><strong>{'Description'|@translate}</strong></td>
      <td><textarea cols="50" rows="5" name="description-{$element.ID}" id="description-{$element.ID}" class="description">{$element.DESCRIPTION}</textarea></td>
    </tr>

  </table>

</fieldset>
{/foreach}

<p>
  <input class="submit" type="submit" value="{'Submit'|@translate}" name="submit">
  <input class="submit" type="reset" value="{'Reset'|@translate}">
</p>
{/if}

</form>

<script type="text/javascript">// <![CDATA[
{literal}$(document).ready(function() {
	$(".elementEdit img").fadeTo("slow", 0.6); // Opacity on page load
	$(".elementEdit img").hover(function(){
		$(this).fadeTo("slow", 1.0); // Opacity on hover
	},function(){
   		$(this).fadeTo("slow", 0.6); // Opacity on mouseout
	});
});{/literal}
// ]]>
</script>
