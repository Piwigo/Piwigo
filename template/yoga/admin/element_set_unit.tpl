<h2>{lang:Batch management}</h2>

<h3>{CATEGORIES_NAV}</h3>

<p style="text-align:center;">
  <a href="{U_GLOBAL_MODE}">global mode</a>
  | unit mode
</p>

<form action="{F_ACTION}" method="POST">

<input type="hidden" name="list" value="{IDS_LIST}" />

<fieldset>

  <legend>Display options</legend>

  <p>elements per page :
      <a href="{U_ELEMENTS_PAGE}&amp;display=5">5</a>
    | <a href="{U_ELEMENTS_PAGE}&amp;display=10">10</a>
    | <a href="{U_ELEMENTS_PAGE}&amp;display=50">50</a>
    | <a href="{U_ELEMENTS_PAGE}&amp;display=all">all</a>
  </p>

</fieldset>

<div class="navigationBar">{NAV_BAR}</div>

<!-- BEGIN element -->
<fieldset class="elementEdit">
  <legend>{element.LEGEND}</legend>

  <a href="{element.U_EDIT}"><img src="{element.TN_SRC}" alt="" class="miniature" title="{lang:Edit all picture informations}" /></a>

  <table>

    <tr>
      <td><strong>{lang:Name}</strong></td>
      <td><input type="text" name="name-{element.ID}" value="{element.NAME}" /></td>
    </tr>

    <tr>
      <td><strong>{lang:Author}</strong></td>
      <td><input type="text" name="author-{element.ID}" value="{element.AUTHOR}" /></td>
    </tr>

    <tr>
      <td><strong>{lang:Creation date}</strong></td>
      <td>
        <label><input type="radio" name="date_creation_action-{element.ID}" value="unset" /> unset</label>
        <input type="radio" name="date_creation_action-{element.ID}" value="set" id="date_creation_action_set-{element.ID}" /> set to
        <select onmousedown="document.getElementById('date_creation_action_set-{element.ID}').checked = true;" name="date_creation_day-{element.ID}">
          <!-- BEGIN date_creation_day -->
          <option {element.date_creation_day.SELECTED} value="{element.date_creation_day.VALUE}">{element.date_creation_day.OPTION}</option>
          <!-- END date_creation_day -->
        </select>
        <select onmousedown="document.getElementById('date_creation_action_set-{element.ID}').checked = true;" name="date_creation_month-{element.ID}">
          <!-- BEGIN date_creation_month -->
          <option {element.date_creation_month.SELECTED} value="{element.date_creation_month.VALUE}">{element.date_creation_month.OPTION}</option>
          <!-- END date_creation_month -->
        </select>
        <input onmousedown="document.getElementById('date_creation_action_set-{element.ID}').checked = true;"
               name="date_creation_year-{element.ID}"
               type="text"
               size="4"
               maxlength="4"
               value="{element.DATE_CREATION_YEAR}" />
      </td>
    </tr>

    <tr>
      <td><strong>{lang:Keywords}</strong></td>
      <td><input type="text" name="keywords-{element.ID}" value="{element.KEYWORDS}" size="50" /></td>
    </tr>

    <tr>
      <td><strong>{lang:Description}</strong></td>
      <td><textarea name="description-{element.ID}" class="description">{element.DESCRIPTION}</textarea></td>
    </tr>

  </table>

</fieldset>
<!-- END element -->

<p>
  <input type="submit" value="{L_SUBMIT}" name="submit" />
  <input type="reset" value="{lang:Reset}" />
</p>


</form>
