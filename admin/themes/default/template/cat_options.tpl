<div class="titrePage">
  <h2>{'Properties'|@translate} {$TABSHEET_TITLE}</h2>
</div>

<form method="post" action="{$F_ACTION}" id="cat_options">
  <fieldset>
    <legend>{$L_SECTION}</legend>
    {$DOUBLE_SELECT}
  </fieldset>
<input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
</form>

