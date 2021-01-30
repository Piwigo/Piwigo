{* $Id *}
<h2>{$TITLE}</h2>

<form method="post" action="{$F_ACTION}">
  {$DOUBLE_SELECT}
  <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
</form>

<p>{'Only private albums are listed'|@translate}</p>
