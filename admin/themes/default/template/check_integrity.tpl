<dl>
  <dt>{'Check integrity'|@translate}</dt>
  <dd>
    <ul>
      <form method="post" name="c13y" id="c13y" action="">
      <fieldset>
        <table class="table2">
          <tr class="throw">
            <th></th>
            <th>{'Anomaly'|@translate}</th>
            <th>{'Correction'|@translate}</th>
          </tr>
          {if isset($c13y_list)}
            {foreach from=$c13y_list item=c13y name=c13y_loop}
              <tr class="{if $smarty.foreach.c13y_loop.index is odd}row1{else}row2{/if}">
                <td>
                  {if $c13y.can_select}
                    <input type="checkbox" name="c13y_selection[]" value="{$c13y.id}" id="c13y_selection-{$c13y.id}"><label for="c13y_selection-{$c13y.id}"></label>
                  {/if}
                </td>
                <td><label for="c13y_selection-{$c13y.id}">{$c13y.anomaly}</label></td>
                <td>
                  <label for="c13y_selection-{$c13y.id}">
                    {if $c13y.show_ignore_msg}
                      {'The anomaly will be ignored until next application version'|@translate}
                      <br>
                      {'Correction the anomaly will cancel the fact that it\'s ignored'|@translate}
                    {/if}
                    {if $c13y.show_correction_fct}
                      {'Automatic correction'|@translate}
                    {/if}
                    {if $c13y.show_correction_bad_fct}
                      {'Impossible automatic correction'|@translate}
                    {/if}
                    {if $c13y.show_correction_success_fct}
                      {'Correction applied with success'|@translate}
                    {/if}
                    {if !empty($c13y.correction_error_fct)}
                      {'Correction applied with error'|@translate}
                      <br>
                      {$c13y.c13y.correction_error_fct}
                    {/if}
                    {if !empty($c13y.correction_msg)}
                      {if $c13y.show_correction_success_fct or !empty($c13y.correction_error_fct) or $c13y.show_correction_fct or $c13y.show_correction_bad_fct }
                        <br>
                      {/if}
                      {$c13y.correction_msg|@nl2br}
                    {/if}
                  </label>
                </td>
              </tr>
            {/foreach}
          {/if}
        </table>

        <p>
          {if $c13y_show_submit_ignore}
              <a href="#" onclick="SelectAll(document.getElementById('c13y')); return false;">{'Check all'|@translate}</a>
            / <a href="#" onclick="DeselectAll(document.getElementById('c13y')); return false;">{'Uncheck all'|@translate}</a>
          {/if}
          {if isset($c13y_do_check)}
            / <a href="#" onclick="DeselectAll(document.getElementById('c13y'));
            {foreach from=$c13y_do_check item=ID}
              document.getElementById('c13y_selection-{$ID}').checked = true;
            {/foreach}
            return false;">{'Check automatic corrections'|@translate}</a>
          {/if}
        </p>

        <p>
          {if $c13y_show_submit_automatic_correction}
            <input class="submit" type="submit" value="{'Apply selected corrections'|@translate}" name="Apply selected corrections" {$TAG_INPUT_ENABLED}>
          {/if}
          {if $c13y_show_submit_ignore}
            <input class="submit" type="submit" value="{'Ignore selected anomalies'|@translate}" name="Ignore selected anomalies" {$TAG_INPUT_ENABLED}>
          {/if}
          <input class="submit" type="submit" value="{'Refresh'|@translate}" name="Refresh">
          </p>

      </fieldset>
      </form>
    </ul>
  </dd>
