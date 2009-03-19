{* $Id$ *}

{include file='include/autosize.inc.tpl'}
{include file='include/datepicker.inc.tpl'}

<h2>{'Batch management'|@translate}</h2>

<h3>{$CATEGORIES_NAV}</h3>

<p style="text-align:center;">
  <a href="{$U_GLOBAL_MODE}">{'global mode'|@translate}</a>
  | {'unit mode'|@translate}
</p>

<form action="{$F_ACTION}" method="POST">
<fieldset>
  <legend>{'Display options'|@translate}</legend>
  <p>{'elements per page'|@translate} :
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

  <a href="{$element.U_EDIT}"><img src="{$element.TN_SRC}" alt="" title="{'Edit all picture informations'|@translate}"></a>

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
        <script type="text/javascript">
          pwg_initialization_datepicker("#date_creation_day-{$element.ID}", "#date_creation_month-{$element.ID}", "#date_creation_year-{$element.ID}", "#date_creation_linked_date-{$element.ID}", "#date_creation_action_set-{$element.ID}");
        </script>
      </td>
    </tr>

    <tr>
      <td><strong>{'Tags'|@translate}</strong></td>
      <td>{$element.TAG_SELECTION}</td>
    </tr>

    <tr>
      <td><strong>{'Description'|@translate}</strong></td>
      <td><textarea cols="50" rows="5" name="description-{$element.ID}" id="description-{$element.ID}" class="description">{$element.DESCRIPTION}</textarea></td>
    </tr>

  </table>

</fieldset>
{/foreach}

<p>
  <input class="submit" type="submit" value="{'Submit'|@translate}" name="submit" {$TAG_INPUT_ENABLED}>
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
