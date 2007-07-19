<!-- $Id$ -->
<dl>
  <dt>{lang:c13y_title}</dt>
  <dd>
    <ul>
      <form method="post" name="preferences" action="{F_c13y_ACTION}">
      <fieldset>
        <table class="table2">
          <tr class="throw">
            <th>{lang:c13y_Anomaly}</th>
            <th>{lang:c13y_Correction}</th>
          </tr>
          <!-- BEGIN c13y -->
          <tr class="{c13y.CLASS}">
            <td><label for="c13y_selection-{c13y.ID}">{c13y.ANOMALY}</label></td>
            <td>
              <!-- BEGIN correction_fct -->
              <input type="checkbox" name="c13y_selection[]" value="{c13y.ID}" {c13y.CHECKED} id="c13y_selection-{c13y.ID}" /><label for="c13y_selection-{c13y.ID}"> {lang:c13y_Automatic_correction}</label>
              <!-- END correction_fct -->
              <!-- BEGIN correction_bad_fct -->
              {lang:c13y_Impossible_automatic_correction}
              <!-- END correction_bad_fct -->
              <!-- BEGIN correction_success_fct -->
              {lang:c13y_Correction_applied_success}
              <!-- END correction_success_fct -->
              <!-- BEGIN correction_error_fct -->
              {lang:c13y_Correction_applied_error}
              <BR />
              {c13y.correction_error_fct.WIKI_FOROM_LINKS}
              <!-- END correction_error_fct -->
              <!-- BEGIN br -->
              <br />
              <!-- END br -->
              <!-- BEGIN correction_msg -->
              {c13y.correction_msg.DATA}
              <!-- END correction_msg -->
            </td>
          </tr>
          <!-- END c13y -->
        </table>
        <!-- BEGIN c13y_submit -->
        <p>
          <input class="submit" type="submit" value="{lang:c13y_Apply_selected_corrections}" name="c13y_submit" {TAG_INPUT_ENABLED} />
        </p>
        <!-- END c13y_submit -->
      </fieldset>
      </form>
    </ul>
  </dd>
