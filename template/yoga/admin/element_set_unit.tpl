{* $Id$ *}

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

{if !empty($NAV_BAR) }
<div class="navigationBar">{$NAV_BAR}</div>
{/if}

{if !empty($elements) }
<input type="hidden" name="element_ids" value="{$ELEMENT_IDS}" />
{foreach from=$elements item=element}
<fieldset class="elementEdit">
  <legend>{$element.LEGEND}</legend>

  <a href="{$element.U_EDIT}"><img src="{$element.TN_SRC}" alt="" title="{'Edit all picture informations'|@translate}" /></a>

  <table>

    <tr>
      <td><strong>{'Name'|@translate}</strong></td>
      <td><input type="text" name="name-{$element.ID}" value="{$element.NAME}" /></td>
    </tr>

    <tr>
      <td><strong>{'Author'|@translate}</strong></td>
      <td><input type="text" name="author-{$element.ID}" value="{$element.AUTHOR}" /></td>
    </tr>

    <tr>
      <td><strong>{'Creation date'|@translate}</strong></td>
      <td>
        <label><input type="radio" name="date_creation_action-{$element.ID}" value="unset" /> {'unset'|@translate}</label>
        <label><input type="radio" name="date_creation_action-{$element.ID}" value="set" id="date_creation_action_set-{$element.ID}" /> {'set to'|@translate}</label>

        <select onmousedown="document.getElementById('date_creation_action_set-{$element.ID}').checked = true;" name="date_creation_day-{$element.ID}">
         	<option value="0">--</option>
           {section name=day start=1 loop=31}
             <option value="{$smarty.section.day.index}" {if $smarty.section.day.index==$element.DATE_CREATION_DAY}selected="selected"{/if}>{$smarty.section.day.index}</option>
           {/section}
        </select>
        <select onmousedown="document.getElementById('date_creation_action_set-{$element.ID}').checked = true;" name="date_creation_month-{$element.ID}">
          {html_options options=$month_list selected=$element.DATE_CREATION_MONTH}
        </select>
        <input onmousedown="document.getElementById('date_creation_action_set-{$element.ID}').checked = true;"
               name="date_creation_year-{$element.ID}"
               type="text"
               size="4"
               maxlength="4"
               value="{$element.DATE_CREATION_YEAR}" />
      </td>
    </tr>

    <tr>
      <td><strong>{'Tags'|@translate}</strong></td>
      <td>{$element.TAG_SELECTION}</td>
    </tr>

    <tr>
      <td><strong>{'Description'|@translate}</strong></td>
      <td><textarea name="description-{$element.ID}" class="description">{$element.DESCRIPTION}</textarea></td>
    </tr>

  </table>

</fieldset>
{/foreach}

<p>
  <input class="submit" type="submit" value="{'Submit'|@translate}" name="submit" {$TAG_INPUT_ENABLED}/>
  <input class="submit" type="reset" value="{'Reset'|@translate}" />
</p>
{/if}

</form>
