<div class="titrePage">
  <h2>{'title_thumbnails'|@translate}</h2>
</div>

{if isset($results) }
<div class="admin">{'tn_results_title'|@translate}</div>
<table style="width:100%;">
  <tr class="throw">
    <td>{'Path'|@translate}</td>
    <td>{'thumbnail'|@translate}</td>
    <td>{'tn_results_gen_time'|@translate}</td>
    <td>{'filesize'|@translate}</td>
    <td>{'Dimensions'|@translate}</td>
  </tr>
  {foreach from=$results.elements item=elt}
  <tr>
    <td>{$elt.PATH}</td>
    <td><img src="{$elt.TN_FILE_IMG}"></td>
    <td style="text-align:right;">{$elt.GEN_TIME}</td>
    <td style="text-align:right;">{$elt.TN_FILESIZE_IMG}</td>
    <td style="text-align:right;">{$elt.TN_WIDTH_IMG} x {$elt.TN_HEIGHT_IMG}</td>
  </tr>
  {/foreach}
</table>

<table class="table2">
  <tr class="throw">
    <td colspan="2">{'tn_stats'|@translate}</td>
  </tr>
  <tr>
    <td>{'tn_stats_nb'|@translate}</td>
    <td style="text-align:center;">{$results.TN_NB}</td>
  </tr>
  <tr>
    <td>{'tn_stats_total'|@translate}</td>
    <td style="text-align:right;">{$results.TN_TOTAL}</td>
  </tr>
  <tr>
    <td>{'tn_stats_max'|@translate}</td>
    <td style="text-align:right;">{$results.TN_MAX}</td>
  </tr>
  <tr>
    <td>{'tn_stats_min'|@translate}</td>
    <td style="text-align:right;">{$results.TN_MIN}</td>
  </tr>
  <tr>
    <td>{'tn_stats_mean'|@translate}</td>
    <td style="text-align:right;">{$results.TN_AVERAGE}</td>
  </tr>
</table>
<br>
{/if}

{if isset($params) }
<form method="post" action="{$params.F_ACTION}" class="properties">

  <fieldset>
    <legend>{'tn_params_title'|@translate}</legend>

    <ul>
      <li>
        <span class="property">{'tn_params_GD'|@translate}</span>
	<label>
          <input type="radio" name="gd" value="2" {if $params.GD_SELECTED==2}checked="checked"{/if}>2.x
        </label>
        <label>
          <input type="radio" name="gd" value="1" {if $params.GD_SELECTED==1}checked="checked"{/if}>1.x
        </label>
      </li>

      <li>
        <span class="property">
          <label for="width">{'maximum width'|@translate}</label>
        </span>
	<input type="text" id="width" name="width" value="{$params.WIDTH_TN}">
      </li>

      <li>
        <span class="property">
          <label for="height">{'maximum height'|@translate}</label>
        </span>
	<input type="text" id="height" name="height" value="{$params.HEIGHT_TN}">
      </li>

      <li>
        <span class="property">{'Number of thumbnails to create'|@translate}</span>
	<label><input type="radio" name="n" value="5"   {if $params.N_SELECTED==5}checked="checked"{/if}> 5</label>
	<label><input type="radio" name="n" value="10"  {if $params.N_SELECTED==10}checked="checked"{/if}> 10</label>
	<label><input type="radio" name="n" value="20"  {if $params.N_SELECTED==20}checked="checked"{/if}> 20</label>
	<label><input type="radio" name="n" value="all" {if $params.N_SELECTED=='all'}checked="checked"{/if}> {'tn_all'|@translate}</label>
      </li>
    </ul>
  </fieldset>

  <p><input class="submit" type="submit" name="submit" value="{'Submit'|@translate}" {$TAG_INPUT_ENABLED}></p>
</form>
{/if} {*isset params*}

{if !empty($remainings) }
<div class="admin">{$TOTAL_NB_REMAINING} {'tn_alone_title'|@translate}</div>
<table style="width:100%;">
  <tr class="throw">
    <td>&nbsp;</td>
    <td style="width:60%;">{'Path'|@translate}</td>
    <td>{'filesize'|@translate}</td>
    <td>{'Dimensions'|@translate}</td>
  </tr>
  {foreach from=$remainings item=elt name=remain_loop}
  <tr class="{if $smarty.foreach.remain_loop.index is odd}row1{else}row2{/if}">
    <td>{$smarty.foreach.remain_loop.iteration}</td>
    <td><div style="margin-left:10px;">{$elt.PATH}</div></td>
    <td><div style="margin-left:10px;">{$elt.FILESIZE_IMG}</div></td>
    <td><div style="margin-left:10px;">{$elt.WIDTH_IMG} x {$elt.HEIGHT_IMG}</div></td>
  </tr>
  {/foreach}
</table>
{else}
<div style="text-align:center;font-weight:bold;margin:10px;"> [ {'tn_no_missing'|@translate} ]</div>
{/if}
