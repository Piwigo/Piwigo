{* $Id: /piwigo/trunk/admin/template/goto/cat_options.tpl 6371 2008-09-14T12:25:34.485116Z vdigital  $ *}
<div class="titrePage">
  <h2>{'cat_options_title'|@translate} {$TABSHEET_TITLE}</h2>
</div>

<form method="post" action="{$F_ACTION}" id="cat_options">
  <fieldset>
    <legend>{$L_SECTION}</legend>
    {$DOUBLE_SELECT}
  </fieldset>
</form>

