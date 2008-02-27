<!-- DEV TAG: not smarty migrated -->
<!-- $Id$ -->
<dl>
  <dt>{lang:c13y_title}</dt>
  <dd>
    <ul>
      <form method="post" name="c13y" id="c13y" action="{F_c13y_ACTION}">
      <fieldset>
        <table class="table2">
          <tr class="throw">
            <th></th>
            <th>{lang:c13y_Anomaly}</th>
            <th>{lang:c13y_Correction}</th>
          </tr>
          <!-- BEGIN c13y -->
          <tr class="{c13y.CLASS}">
            <td>
              <!-- BEGIN can_select -->
              <input type="checkbox" name="c13y_selection[]" value="{c13y.ID}" {c13y.CHECKED} id="c13y_selection-{c13y.ID}" /><label for="c13y_selection-{c13y.ID}"></label>
              <!-- END can_select -->
            </td>
            <td><label for="c13y_selection-{c13y.ID}">{c13y.ANOMALY}</label></td>
            <td>
              <label for="c13y_selection-{c13y.ID}">
                <!-- BEGIN ignore_msg -->
                {lang:c13y_ignore_msg1}
                <br />
                {lang:c13y_ignore_msg2}
                <!-- END ignore_msg -->
                <!-- BEGIN correction_fct -->
                {lang:c13y_Automatic_correction}
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
              </label>
            </td>
          </tr>
          <!-- END c13y -->
        </table>

        <p>
          <!-- BEGIN c13y_link_check_uncheck -->
          <a href="#" onclick="SelectAll(document.getElementById('c13y')); return false;">{lang:c13y_check_all}</a>
        / <a href="#" onclick="DeselectAll(document.getElementById('c13y')); return false;">{lang:c13y_uncheck_all}</a>
          <!-- END c13y_link_check_uncheck -->
          <!-- BEGIN c13y_link_check_automatic_correction -->
          / <a href="#" onclick="DeselectAll(document.getElementById('c13y'));
            <!-- BEGIN c13y_do_check -->
              document.getElementById('c13y_selection-{c13y_link_check_automatic_correction.c13y_do_check.ID}').checked = true;
            <!-- END c13y_do_check -->
              return false;">{lang:c13y_check_auto}</a>
          <!-- END c13y_link_check_automatic_correction -->
        </p>

        <p>
          <!-- BEGIN c13y_submit_automatic_correction -->
          <input class="submit" type="submit" value="{lang:c13y_submit_correction}" name="c13y_submit_correction" {TAG_INPUT_ENABLED} />
          <!-- END c13y_submit_automatic_correction -->
          <!-- BEGIN c13y_submit_ignore -->
          <input class="submit" type="submit" value="{lang:c13y_submit_ignore}" name="c13y_submit_ignore" {TAG_INPUT_ENABLED} />
          <!-- END c13y_submit_ignore -->
          <input class="submit" type="submit" value="{lang:c13y_submit_refresh}" name="c13y_submit_refresh" {TAG_INPUT_ENABLED} />
        </p>

      </fieldset>
      </form>
    </ul>
  </dd>
