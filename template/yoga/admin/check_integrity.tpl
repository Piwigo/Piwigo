{* $Id$ *}
<dl>
  <dt>{'c13y_title'|@translate}</dt>
  <dd>
    <ul>
      <form method="post" name="c13y" id="c13y" action="{$F_C13Y_ACTION}">
      <fieldset>
        <table class="table2">
          <tr class="throw">
            <th></th>
            <th>{'c13y_Anomaly'|@translate}</th>
            <th>{'c13y_Correction'|@translate}</th>
          </tr>
          {if isset($c13y_list)}
            {foreach from=$c13y_list item=c13y name=c13y_loop}
              <tr class="{if $smarty.foreach.c13y_loop.index is odd}row1{else}row2{/if}">
                <td>
                  {if $c13y.can_select}
                    <input type="checkbox" name="c13y_selection[]" value="{$c13y.id}" id="c13y_selection-{$c13y.id}" /><label for="c13y_selection-{$c13y.id}"></label>
                  {/if}
                </td>
                <td><label for="c13y_selection-{$c13y.id}">{$c13y.anomaly}</label></td>
                <td>
                  <label for="c13y_selection-{$c13y.id}">
                    {if $c13y.show_ignore_msg}
                      {'c13y_ignore_msg1'|@translate}
                      <br />
                      {'c13y_ignore_msg2'|@translate}
                    {/if}
                    {if $c13y.show_correction_fct}
                      {'c13y_Automatic_correction'|@translate}
                    {/if}
                    {if $c13y.show_correction_bad_fct}
                      {'c13y_Impossible_automatic_correction'|@translate}
                    {/if}
                    {if $c13y.show_correction_success_fct}
                      {'c13y_Correction_applied_success'|@translate}
                    {/if}
                    {if !empty($c13y.correction_error_fct)}
                      {'c13y_Correction_applied_error'|@translate}
                      <br />
                      {$c13y.c13y.correction_error_fct}
                    {/if}
                    {if !empty($c13y.correction_msg)}
                      {if $c13y.show_correction_success_fct or !empty($c13y.correction_error_fct) or $c13y.show_correction_fct or $c13y.show_correction_bad_fct }
                        <br />
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
            return false;">{'c13y_check_auto'|@translate}</a>
          {/if}
        </p>

        <p>
          {if $c13y_show_submit_automatic_correction}
            <input class="submit" type="submit" value="{'c13y_submit_correction'|@translate}" name="c13y_submit_correction" {$TAG_INPUT_ENABLED} />
          {/if}
          {if $c13y_show_submit_ignore}
            <input class="submit" type="submit" value="{'c13y_submit_ignore'|@translate}" name="c13y_submit_ignore" {$TAG_INPUT_ENABLED} />
          {/if}
          <input class="submit" type="submit" value="{'c13y_submit_refresh'|@translate}" name="c13y_submit_refresh" />
          </p>

      </fieldset>
      </form>
    </ul>
  </dd>
