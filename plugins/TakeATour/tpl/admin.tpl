<div class="titrePage">
  <h2>{'takeatour_configpage'|@translate}</h2>
</div>
<div id="helpContent">
  <p>{'TAT_descrp'|@translate}</p>
  {if !isset($TAT_tour_ignored) or (isset($TAT_tour_ignored) and in_array(first_contact, $TAT_tour_ignored))}
  <fieldset>
    <legend>{'First Contact'|@translate}</legend>
    <div class="TAT_description">{'first_contact_descrp'|@translate}</div>
    <form action="{$F_ACTION}" method="post">
      <input type="hidden" name="submited_tour" value="first_contact">
      <input type="hidden" name="pwg_token" value="{$pwg_token}">
      <input type="submit" name="button2" id="button2" value="{'Start the Tour'|@translate}">
    </form>
  </fieldset>
  {/if}
  {if !isset($TAT_tour_ignored) or (isset($TAT_tour_ignored) and in_array(2_7_0, $TAT_tour_ignored))}
  <fieldset>
    <legend>{'2.7 Tour'|@translate}</legend>
    <div class="TAT_description">{'2_7_0_descrp'|@translate}</div>
    <form action="{$F_ACTION}" method="post">
      <input type="hidden" name="submited_tour" value="2_7_0">
      <input type="hidden" name="pwg_token" value="{$pwg_token}">
      <input type="submit" name="button2" id="button2" value="{'Start the Tour'|@translate}">
    </form>
  </fieldset>
  {/if}
</div>