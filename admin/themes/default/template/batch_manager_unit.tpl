{include file='include/autosize.inc.tpl'}
{include file='include/datepicker.inc.tpl'}
{include file='include/colorbox.inc.tpl'}

{combine_css path='themes/default/js/plugins/jquery.tokeninput.css'}
{combine_script id='jquery.tokeninput' load='async' require='jquery' path='themes/default/js/plugins/jquery.tokeninput.js'}
{footer_script require='jquery.tokeninput'}
jQuery(document).ready(function() {ldelim}
	jQuery('select[name|="tags"]').tokenInput(
		[{foreach from=$tags item=tag name=tags}{ldelim}name:"{$tag.name|@escape:'javascript'}",id:"{$tag.id}"{rdelim}{if !$smarty.foreach.tags.last},{/if}{/foreach}],
    {ldelim}
      hintText: '{'Type in a search term'|@translate}',
      noResultsText: '{'No results'|@translate}',
      searchingText: '{'Searching...'|@translate}',
      newText: ' ({'new'|@translate})',
      animateDropdown: false,
      preventDuplicates: true,
      allowFreeTagging: true
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
    <a href="{$element.FILE_SRC}" class="preview-box" title="{$element.LEGEND|@htmlspecialchars}"><img src="{$element.TN_SRC}" alt=""></a>
    <br/>
    <a href="{$element.U_EDIT}">{'Edit'|@translate}</a>
  </span>

  <table>

    <tr>
      <td><strong>{'Title'|@translate}</strong></td>
      <td><input type="text" class="large" name="name-{$element.id}" value="{$element.NAME}"></td>
    </tr>

    <tr>
      <td><strong>{'Author'|@translate}</strong></td>
      <td><input type="text" class="large" name="author-{$element.id}" value="{$element.AUTHOR}"></td>
    </tr>

    <tr>
      <td><strong>{'Creation date'|@translate}</strong></td>
      <td>
        <label><input type="radio" name="date_creation_action-{$element.id}" value="unset"> {'unset'|@translate}</label>
        <label><input type="radio" name="date_creation_action-{$element.id}" value="set" id="date_creation_action_set-{$element.id}"> {'set to'|@translate}</label>

        <select id="date_creation_day-{$element.id}" name="date_creation_day-{$element.id}">
         	<option value="0">--</option>
           {section name=day start=1 loop=32}
             <option value="{$smarty.section.day.index}" {if $smarty.section.day.index==$element.DATE_CREATION_DAY}selected="selected"{/if}>{$smarty.section.day.index}</option>
           {/section}
        </select>
        <select id="date_creation_month-{$element.id}" name="date_creation_month-{$element.id}">
          {html_options options=$month_list selected=$element.DATE_CREATION_MONTH}
        </select>
        <input id="date_creation_year-{$element.id}"
               name="date_creation_year-{$element.id}"
               type="text"
               size="4"
               maxlength="4"
               value="{$element.DATE_CREATION_YEAR}">
        <input id="date_creation_linked_date-{$element.id}" name="date_creation_linked_date-{$element.id}" type="hidden" size="10" disabled="disabled">
        {footer_script}
          pwg_initialization_datepicker("#date_creation_day-{$element.id}", "#date_creation_month-{$element.id}", "#date_creation_year-{$element.id}", "#date_creation_linked_date-{$element.id}", "#date_creation_action_set-{$element.id}");
        {/footer_script}
      </td>
    </tr>
    <tr>
      <td><strong>{'Who can see this photo?'|@translate}</strong></td>
      <td>
        <select name="level-{$element.id}">
          {html_options options=$level_options selected=$element.LEVEL}
        </select>
      </td>
    </tr>

    <tr>
      <td><strong>{'Tags'|@translate}</strong></td>
      <td>

<select name="tags-{$element.id}">
{foreach from=$element.TAGS item=tag}
  <option value="{$tag.id}" class="selected">{$tag.name}</option>
{/foreach}
</select>

      </td>
    </tr>

    <tr>
      <td><strong>{'Description'|@translate}</strong></td>
      <td><textarea cols="50" rows="5" name="description-{$element.id}" id="description-{$element.id}" class="description">{$element.DESCRIPTION}</textarea></td>
    </tr>

  </table>

</fieldset>
{/foreach}

{if !empty($navbar)}{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}

<p>
  <input type="submit" value="{'Submit'|@translate}" name="submit">
  <input type="reset" value="{'Reset'|@translate}">
</p>
{/if}

</form>

{footer_script}
{literal}$(document).ready(function() {
	$(".elementEdit img")
		.fadeTo("slow", 0.6) // Opacity on page load
		.hover(function(){
			$(this).fadeTo("slow", 1.0); // Opacity on hover
		},function(){
   		$(this).fadeTo("slow", 0.6); // Opacity on mouseout
		});
});{/literal}
{/footer_script}
