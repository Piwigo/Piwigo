<!-- BEGIN directory_list -->
<div class="admin">{L_THUMBNAIL_TITLE}</div>
<ul class="menu">
  <!-- BEGIN directory -->
  <li>[<a href="{directory_list.directory.URL}">{directory_list.directory.DIRECTORY}</a>] {directory_list.directory.NB_MISSING} {L_MISSING_THUMBNAILS}</li>
  <!-- END directory -->
</ul>
<!-- END directory_list -->
<!-- BEGIN warning -->
<div style="text-align:center;font-weight:bold;margin:10px;"> [ 0 {L_UNLINK} ]</div>
<!-- END warning -->

<!-- BEGIN errors -->
<div class="errors">
<ul>
  <!-- BEGIN error -->
  <li>{errors.error.ERROR}</li>
  <!-- END error -->
</ul>
</div>
<!-- END errors -->

<!-- BEGIN results -->
<div class="admin">{L_RESULTS}</div>
<table style="width:100%;">
  <tr class="throw">
    <th>&nbsp;</td>
    <th>{L_TN_PICTURE}</td>
    <th>{L_FILESIZE}</td>
    <th>{L_WIDTH}</td>
    <th>{L_HEIGHT}</td>
    <th>{L_GENERATED}</th>
    <th>{L_THUMBNAIL}</td>
    <th>{L_FILESIZE}</td>
    <th>{L_WIDTH}</td>
    <th>{L_HEIGHT}</td>
  </tr>
  <!-- BEGIN picture -->
  <tr class="{results.picture.T_CLASS}">
    <td>{results.picture.NB_IMG}</td>
    <td>{results.picture.FILE_IMG}</td>
    <td style="text-align:right;">{results.picture.FILESIZE_IMG}</td>
    <td style="text-align:right;">{results.picture.WIDTH_IMG}</td>
    <td style="text-align:right;">{results.picture.HEIGHT_IMG}</td>
    <th>
      <div style="text-align:right;margin-right:5px;color:{results.picture.T_COLOR};">{results.picture.GEN_TIME}</div>
    </th>
    <td><img src="{results.picture.TN_FILE_IMG}" /></td>
    <td style="text-align:right;">{results.picture.TN_FILESIZE_IMG}</td>
    <td style="text-align:right;">{results.picture.TN_WIDTH_IMG}</td>
    <td style="text-align:right;">{results.picture.TN_HEIGHT_IMG}</td>
  </tr>
  <!-- END picture -->
  <tr>
    <td colspan="10">&nbsp;</td>
  </tr>
</table>
<table class="table2">
  <tr class="throw">
    <th colspan="2">{L_TN_STATS}</td>
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
    <td style="text-align:right;color:red;">{results.TN_MAX}</td>
  </tr>
  <tr>
    <td>{L_TN_MIN}</td>
    <td style="text-align:right;color:#33FF00;">{results.TN_MIN}</td>
  </tr>
  <tr>
    <td>{L_TN_AVERAGE}</td>
    <td style="text-align:right;">{results.TN_AVERAGE}</td>
  </tr>
</table>
<br />
<!-- END results -->

<!-- BEGIN params -->
<form method="post" action="{params.F_ACTION}">
  <table style="width:100%;">
    <tr>
      <th class="admin" colspan="3">{L_PARAMS}</th>
    </tr>
    <tr><td colspan="3">&nbsp;</td></tr>
    <tr>
      <td><div class="key">{L_GD}</div></td>
      <td class="choice">
        <input type="radio" name="gd" value="2" {params.GD2_CHECKED} />2.x
        <input type="radio" name="gd" value="1" {params.GD1_CHECKED} />1.x
      </td>
      <td style="width:50%;" class="row2">{L_GD_INFO}</td>
    </tr>
    <tr>
      <td><div class="key">{L_WIDTH}</div></td>
      <td class="choice">
        <input type="text" name="width" value="{params.WIDTH_TN}"/>
      </td>
      <td>{L_WIDTH_INFO}</td>
    </tr>
    <tr>
      <td><div class="key">{L_HEIGHT}</div></td>
      <td class="choice">
        <input type="text" name="height" value="{params.HEIGHT_TN}"/>
      </td>
      <td>{L_HEIGHT_INFO}</td>
    </tr>
    <tr>
      <td><div class="key">{L_CREATE}</div></td>
      <td class="choice">
        <select name="n">
          <option selected>5</option>
          <option>10</option>
          <option>20</option>
          <option>40</option>
        </select>
      </td>
      <td>{L_CREATE_INFO}</td>
    </tr>
    <tr>
      <td><div class="key">{L_FORMAT}</div></td>
      <td class="choice"><span style="font-weight:bold;">jpeg</span></td>
      <td>{L_FORMAT_INFO}</td>
    </tr>
    <tr>
      <td colspan="3" style="text-align:center;">
        <input type="submit" name="submit" class="bouton" value="{L_SUBMIT}"/>
      </td>
    </tr>
  </table>
</form>
<!-- END params -->

<!-- BEGIN remainings -->
<div class="admin">{remainings.TOTAL_IMG} {L_REMAINING}</div>
<table style="width:100%;">
  <tr class="throw">
    <th>&nbsp;</td>
    <th style="width:60%;">{L_TN_PICTURE}</td>
    <th>{L_FILESIZE}</td>
    <th>{L_WIDTH}</td>
    <th>{L_HEIGHT}</td>
  </tr>
  <!-- BEGIN remaining -->
  <tr class="{remainings.remaining.T_CLASS}">
    <td>{remainings.remaining.NB_IMG}</td>
    <td><div style="margin-left:10px;">{remainings.remaining.FILE_TN}</div></td>
    <td><div style="margin-left:10px;">{remainings.remaining.FILESIZE_IMG}</div></td>
    <td><div style="margin-left:10px;">{remainings.remaining.WIDTH_IMG}</div></td>
    <td><div style="margin-left:10px;">{remainings.remaining.HEIGHT_IMG}</div></td>
  </tr>
  <!-- END remaining -->
</table>
<!-- END remainings -->
