<!-- BEGIN confirmation -->
<div style="color:red;text-align:center;">
  {L_EDIT_CONFIRM} <a href="{U_PERMISSIONS}">{L_HERE}</a></div>
<!-- END confirmation -->
<h3>{CATEGORIES_NAV}</h3>
<form action="{F_ACTION}" method="POST" id="cat_representant">
<table style="width:100%;">
    <!-- BEGIN representant -->
    <tr>
      <td style="width:50%;" align="center">
        <a href="{representant.URL}"><img src="{representant.SRC}" alt="" class="miniature" /></a>
      </td>
      <td class="row1"><input type="submit" name="set_random_representant" value="{L_SET_RANDOM_REPRESENTANT}" class="bouton" /></td>
    </tr>
    <!-- END representant -->
  </table>
</form>
<form action="{F_ACTION}" method="POST" id="cat_modify">
    <!-- BEGIN server -->
    <ul>
      <dt><strong>{L_REMOTE_SITE}</strong></dt>
      <dd>{server.SITE_URL}</dd>
    </ul>
    <!-- END server -->
    <ul>
      <dt><strong>{L_EDIT_NAME}</strong></dt>
      <dd> <input type="text" name="name" value="{CAT_NAME}" maxlength="60"/> </dd>
    </ul>
	<!-- BEGIN storage -->
    <ul>
      <dt><strong>{L_STORAGE}</strong></dt>
      <dd>{storage.CATEGORY_DIR}</dd>
    </ul>
	<!-- END storage -->
    <ul>
      <dt><strong>{L_EDIT_COMMENT}</strong></dt>
      <dd> <textarea name="comment" rows="3" cols="50" >{CAT_COMMENT}</textarea> </dd>
    </ul>
	<h3>{L_EDIT_CAT_OPTIONS}</h3>
    <ul>
      <dt class="biglabel"><strong>{L_EDIT_STATUS}&nbsp;:</strong><br /><span class="small">{L_EDIT_STATUS_INFO}</span></dt>
      <dd>
	  <input type="radio" class="radio" name="status" value="public" {STATUS_PUBLIC} />{L_STATUS_PUBLIC}&nbsp;&nbsp;
	  <input type="radio" class="radio" name="status" value="private" {STATUS_PRIVATE} />{L_STATUS_PRIVATE}
      </dd>
    </ul>
    <ul>
      <dt class="biglabel"><strong>{L_EDIT_LOCK}&nbsp;:</strong><br /><span class="small">{L_EDIT_LOCK_INFO}</span></dt>
      <dd>
	  <input type="radio" class="radio" name="visible" value="false" {LOCKED} />{L_YES}&nbsp;&nbsp;
	  <input type="radio" class="radio" name="visible" value="true" {UNLOCKED} />{L_NO}
      </dd>
    </ul>
    <ul>
      <dt class="biglabel"><strong>{L_EDIT_COMMENTABLE}&nbsp;:</strong><br /><span class="small">{L_EDIT_COMMENTABLE_INFO}</span></dt>
      <dd>
          <input type="radio" class="radio" name="commentable" value="true" {COMMENTABLE_TRUE} />{L_YES}&nbsp;&nbsp;
          <input type="radio" class="radio" name="commentable" value="false" {COMMENTABLE_FALSE} />{L_NO}
      </dd>
    </ul>
    <!-- BEGIN upload -->
    <ul>
      <dt class="biglabel"><strong>{L_EDIT_UPLOADABLE}&nbsp;:</strong><br /><span class="small">{L_EDIT_UPLOADABLE_INFO}</span></dt>
      <dd>
          <input type="radio" class="radio" name="uploadable" value="true" {UPLOADABLE_TRUE} />{L_YES}&nbsp;&nbsp;
          <input type="radio" class="radio" name="uploadable" value="false" {UPLOADABLE_FALSE} />{L_NO}
      </dd>
    </ul>
    <!-- END upload -->
    <p>
        <input type="submit" name="submit" value="{L_SUBMIT}" class="bouton" />
    <p>
</form>
