<!-- BEGIN errors -->
<div class="errors">
<ul>
  <!-- BEGIN error -->
  <li>{errors.error.ERROR}</li>
  <!-- END error -->
</ul>
</div>
<!-- END errors -->
<div class="admin">{CATEGORIES_NAV}</div>
<table style="width:100%;">
<!-- BEGIN category -->
<tr>
  <td style="width:1px;padding:5px;">{category.CATEGORY_IMG}</td>
  <td style="width:50%;text-align:left;"><a class="titreImg" href="{category.U_CATEGORY}">{category.CATEGORY_NAME}</a>
    <br />
	<!-- BEGIN storage -->
    {L_STORAGE} : {category.CATEGORY_DIR} - 
    <!-- END storage -->
	{L_NB_IMG} : {category.CATEGORY_NB_IMG}
  </td>
  <td class="row1" style="width:10%;white-space:nowrap;text-align:center;">
    <a href="{category.U_MOVE_UP}">{L_MOVE_UP}</a><br />
	<a href="{category.U_MOVE_DOWN}">{L_MOVE_DOWN}</a>
  </td>
  <td class="row1" style="width:10%;white-space:nowrap;text-align:center;">
    <a href="{category.U_CAT_EDIT}">{L_EDIT}</a>
  </td>
  <td class="row1" style="width:10%;white-space:nowrap;text-align:center;">
    <!-- BEGIN image_info -->
    <a href="{category.U_INFO_IMG}">{L_INFO_IMG}</a>
    <!-- END image_info -->
    <!-- BEGIN no_image_info -->
    <span style="color:darkgray;">{L_INFO_IMG}</span>
    <!-- END no_image_info -->
  </td>
  <td class="row1" style="width:10%;white-space:nowrap;text-align:center;">
    <a href="{category.U_CAT_UPDATE}">{L_UPDATE}</a>
  </td>
  <td class="row1" style="width:10%;white-space:nowrap;text-align:center;">
    <!-- BEGIN virtual -->
    <a href="{category.U_CAT_DELETE}">{L_DELETE}</a>
    <!-- END virtual -->
    <!-- BEGIN storage -->
    <span style="color:darkgray;">{L_DELETE}</span>
    <!-- END storage -->
  </td>
<tr>
<!-- END category -->
</table>
<form action="" method="post">
  {L_ADD_VIRTUAL} : <input type="text" name="virtual_name" />
  <input type="hidden" name="rank" value="{NEXT_RANK}"/>
  <input type="submit" value="{L_SUBMIT}" class="bouton" name="submit" />
</form>