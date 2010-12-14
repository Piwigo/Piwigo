<div class="titrePage">
  <h2>{'Thumbnail creation'|@translate}</h2>
</div>

{if isset($results) }
<div class="admin">{'Results of miniaturization'|@translate}</div>
<table style="width:100%;">
  <tr class="throw">
    <td>{'Path'|@translate}</td>
    <td>{'Thumbnail'|@translate}</td>
    <td>{'generated in'|@translate}</td>
    <td>{'Filesize'|@translate}</td>
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
    <td colspan="2">{'General statistics'|@translate}</td>
  </tr>
  <tr>
    <td>{'number of miniaturized pictures'|@translate}</td>
    <td style="text-align:center;">{$results.TN_NB}</td>
  </tr>
  <tr>
    <td>{'total time'|@translate}</td>
    <td style="text-align:right;">{$results.TN_TOTAL}</td>
  </tr>
  <tr>
    <td>{'max time'|@translate}</td>
    <td style="text-align:right;">{$results.TN_MAX}</td>
  </tr>
  <tr>
    <td>{'min time'|@translate}</td>
    <td style="text-align:right;">{$results.TN_MIN}</td>
  </tr>
  <tr>
    <td>{'average time'|@translate}</td>
    <td style="text-align:right;">{$results.TN_AVERAGE}</td>
  </tr>
</table>
<br>
{/if}

{if isset($params) }
<form method="post" action="{$params.F_ACTION}" class="properties">

  <fieldset>
    <legend>{'Miniaturization parameters'|@translate}</legend>

    <ul>
      <li>
        <span class="property">{'GD version'|@translate}</span>
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
	<label><input type="radio" name="n" value="all" {if $params.N_SELECTED=='all'}checked="checked"{/if}> {'all'|@translate}</label>
      </li>
    </ul>
  </fieldset>

  <p><input class="submit" type="submit" name="submit" value="{'Submit'|@translate}"></p>
</form>
{/if} {*isset params*}

{if !empty($remainings) }
<div class="admin">{$TOTAL_NB_REMAINING} {'pictures without thumbnail (jpeg and png only)'|@translate}</div>
<table style="width:100%;">
  <tr class="throw">
    <td>&nbsp;</td>
    <td style="width:60%;">{'Path'|@translate}</td>
    <td>{'Filesize'|@translate}</td>
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
<div style="text-align:center;font-weight:bold;margin:10px;"> [ {'No missing thumbnail'|@translate} ]</div>
{/if}
