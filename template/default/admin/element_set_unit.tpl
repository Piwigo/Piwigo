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

<fieldset>

  <legend>Elements</legend>

  <div class="navigationBar">{NAV_BAR}</div>

  <table width="100%">

    <tr>
      <th class="row2" style="text-align:center;">&nbsp;</td>
      <th class="row2" style="text-align:center;">name</td>
      <th class="row2" style="text-align:center;">author</td>
      <th class="row2" style="text-align:center;">description</td>
      <th class="row2" style="text-align:center;">creation date</td>
      <th class="row2" style="text-align:center;">keywords</td>
    </tr>

    <!-- BEGIN element -->
    <tr>

      <td style="text-align:center;"><img src="{element.TN_SRC}" alt="" class="miniature" title="{element.FILENAME}" /></td>

      <td style="text-align:center;"><input type="text" name="name-{element.ID}" value="{element.NAME}" maxlength="255"/></td>

      <td style="text-align:center;"><input type="text" name="author-{element.ID}" value="{element.AUTHOR}" maxlength="255" size="12" /></td>

      <td style="text-align:center;"><textarea name="comment-{element.ID}" rows="5" cols="30" style="overflow:auto">{element.COMMENT}</textarea></td>

      <td style="text-align:left;">
        <input type="radio" name="date_creation_action-{element.ID}" value="leave" checked="checked" /> leave unchanged
        <br /><input type="radio" name="date_creation_action-{element.ID}" value="unset" /> unset
        <br /><input type="radio" name="date_creation_action-{element.ID}" value="set" id="date_creation_action_set-{element.ID}" />

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

      <td style="text-align:center;"><input type="text" name="keywords-{element.ID}" value="{element.KEYWORDS}" length="255" /></td>

    </tr>
    <!-- END element -->

  </table>

  <p style="text-align:center;">
    <input type="submit" value="{L_SUBMIT}" name="submit" class="bouton" />
  </p>

</fieldset>

</form>
