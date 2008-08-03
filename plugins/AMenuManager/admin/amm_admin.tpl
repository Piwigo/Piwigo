<div class="titrePage">
  <h2 style="position:absolute;right:0px;top:32px;height:auto;font-size:12px;font-weight:normal;">:: {$plugin.AMM_VERSION} ::</h2>
  <h2>{'g002_title_page'|@translate} <span style="font-size:-1;font-weight:normal;">{$TABSHEET_TITLE}</span></h2>

  {$tabsheet}
</div>

{if isset($page_nfo)}
<p>{$page_nfo}</p>
{/if}

{$AMM_BODY_PAGE}

