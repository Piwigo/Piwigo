<!-- $Id$ -->
<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{lang:title_thumbnails}</h2>
</div>

<!-- BEGIN results -->
<div class="admin">{L_RESULTS}</div>
<table style="width:100%;">
  <tr class="throw">
    <td>{L_PATH}</td>
    <td>{L_THUMBNAIL}</td>
    <td>{L_GENERATED}</td>
    <td>{L_FILESIZE}</td>
    <td>{L_WIDTH}</td>
    <td>{L_HEIGHT}</td>
  </tr>
  <!-- BEGIN picture -->
  <tr class="{results.picture.T_CLASS}">
    <td>{results.picture.PATH}</td>
    <td><img src="{results.picture.TN_FILE_IMG}" /></td>
    <td style="text-align:right;" class="{results.picture.T_CLASS}">{results.picture.GEN_TIME}</td>
    <td style="text-align:right;">{results.picture.TN_FILESIZE_IMG}</td>
    <td style="text-align:right;">{results.picture.TN_WIDTH_IMG}</td>
    <td style="text-align:right;">{results.picture.TN_HEIGHT_IMG}</td>
  </tr>
  <!-- END picture -->
</table>

<table class="table2">
  <tr class="throw">
    <td colspan="2">{L_TN_STATS}</td>
  </tr>
  <tr>
    <td>{L_TN_NB_STATS}</td>
    <td style="text-align:center;">{results.TN_NB}</td>
  </tr>
  <tr>
    <td>{L_TN_TOTAL}</td>
    <td style="text-align:right;">{results.TN_TOTAL}</td>
  </tr>
  <tr>
    <td>{L_TN_MAX}</td>
    <td style="text-align:right;" class="worst_gen_time">{results.TN_MAX}</td>
  </tr>
  <tr>
    <td>{L_TN_MIN}</td>
    <td style="text-align:right;" class="best_gen_time">{results.TN_MIN}</td>
  </tr>
  <tr>
    <td>{L_TN_AVERAGE}</td>
    <td style="text-align:right;">{results.TN_AVERAGE}</td>
  </tr>
</table>
<br />
<!-- END results -->

<!-- BEGIN params -->
<form method="post" action="{params.F_ACTION}" class="properties">

  <fieldset>
    <legend>{L_PARAMS}</legend>

    <ul>
      <li>
        <span class="property">{L_GD}</span>
	<label>
          <input type="radio" name="gd" value="2" {params.GD2_CHECKED} />2.x
        </label>
        <label>
          <input type="radio" name="gd" value="1" {params.GD1_CHECKED} />1.x
        </label>
      </li>

      <li>
        <span class="property">
          <label for="width">{lang:maximum width}</label>
        </span>
	<input type="text" id="width" name="width" value="{params.WIDTH_TN}" />
      </li>

      <li>
        <span class="property">
          <label for="height">{lang:maximum height}</label>
        </span>
	<input type="text" id="height" name="height" value="{params.HEIGHT_TN}" />
      </li>

      <li>
        <span class="property">{lang:Number of thumbnails to create}</span>
	<label><input type="radio" name="n" value="5"   {params.n_5_CHECKED} /> 5</label>
	<label><input type="radio" name="n" value="10"  {params.n_10_CHECKED} /> 10</label>
	<label><input type="radio" name="n" value="20"  {params.n_20_CHECKED} /> 20</label>
	<label><input type="radio" name="n" value="all" {params.n_all_CHECKED} /> {L_ALL}</label>
      </li>
    </ul>
  </fieldset>

  <p><input class="submit" type="submit" name="submit" value="{L_SUBMIT}" {TAG_INPUT_ENABLED}/></p>
</form>
<!-- END params -->

<!-- BEGIN warning -->
<div style="text-align:center;font-weight:bold;margin:10px;"> [ {L_UNLINK} ]</div>
<!-- END warning -->

<!-- BEGIN remainings -->
<div class="admin">{remainings.TOTAL_IMG} {L_REMAINING}</div>
<table style="width:100%;">
  <tr class="throw">
    <td>&nbsp;</td>
    <td style="width:60%;">{L_PATH}</td>
    <td>{L_FILESIZE}</td>
    <td>{L_WIDTH}</td>
    <td>{L_HEIGHT}</td>
  </tr>
  <!-- BEGIN remaining -->
  <tr class="{remainings.remaining.T_CLASS}">
    <td>{remainings.remaining.NB_IMG}</td>
    <td><div style="margin-left:10px;">{remainings.remaining.PATH}</div></td>
    <td><div style="margin-left:10px;">{remainings.remaining.FILESIZE_IMG}</div></td>
    <td><div style="margin-left:10px;">{remainings.remaining.WIDTH_IMG}</div></td>
    <td><div style="margin-left:10px;">{remainings.remaining.HEIGHT_IMG}</div></td>
  </tr>
  <!-- END remaining -->
</table>
<!-- END remainings -->
