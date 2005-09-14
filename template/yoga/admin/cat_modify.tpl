<!-- $Id$ -->
<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="template/yoga/theme/help.png" alt="(?)"></a></li>
  </ul>
  <h2>{lang:title_edit_cat}</h2>
</div>

<h3>{CATEGORIES_NAV}</h3>

<ul class="categoryActions">
  <li><a href="{U_JUMPTO}" title="{lang:jump to category}"><img src="./template/yoga/theme/category_jump-to.png" alt="{lang:jump to}" /></a></li>
  <!-- BEGIN elements -->
  <li><a href="{elements.URL}" title="{lang:manage category elements}"><img src="./template/yoga/theme/category_elements.png" alt="{lang:elements}" /></a></li>
  <!-- END elements -->
  <li><a href="{U_CHILDREN}" title="{lang:manage sub-categories}"><img src="./template/yoga/theme/category_children.png" alt="{lang:sub-categories}" /></a></li>
  <!-- BEGIN permissions -->
  <li><a href="{permissions.URL}" title="{lang:edit category permissions}" ><img src="./template/yoga/theme/category_permissions.png" alt="{lang:permissions}" /></a></li>
  <!-- END permissions -->
  <!-- BEGIN delete -->
  <li><a href="{delete.URL}" title="{lang:delete category}"><img src="./template/yoga/theme/category_delete.png" alt="{lang:delete}" /></a></li>
  <!-- END delete -->
</ul>

<form action="{F_ACTION}" method="POST" id="cat_modify">

<fieldset>
  <legend>{lang:Informations}</legend>
  <table>
    <!-- BEGIN server -->
    <tr>
      <td><strong>{L_REMOTE_SITE}</strong></td>
      <td>{server.SITE_URL}</td>
    </tr>
    <!-- END server -->
    <!-- BEGIN storage -->
    <tr>
      <td><strong>{L_STORAGE}</strong></td>
      <td class="row1">{storage.CATEGORY_DIR}</td>
    </tr>
    <!-- END storage -->
    <tr>
      <td><strong>{L_EDIT_NAME}</strong></td>
      <td>
        <input type="text" name="name" value="{CAT_NAME}" maxlength="60"/>
      </td>
    </tr>
    <tr>
      <td><strong>{L_EDIT_COMMENT}</strong></td>
      <td>
        <textarea name="comment" class="description">{CAT_COMMENT}</textarea>
      </td>
    </tr>
  </table>
</fieldset>

<!-- BEGIN move -->
<fieldset>
  <legend>{lang:Move}</legend>
  {lang:Parent category}
  <select name="parent">
    <!-- BEGIN parent_option -->
    <option class="{move.parent_option.CLASS}" {move.parent_option.SELECTED} value="{move.parent_option.VALUE}">{move.parent_option.OPTION}</option>
    <!-- END parent_option -->
  </select>  
</fieldset>
<!-- END move -->

<fieldset>
  <legend>{lang:Options}</legend>
  <table>
    <tr>
      <td><strong>{L_EDIT_STATUS}</strong>
      <td>
	  <input type="radio" name="status" value="public" {STATUS_PUBLIC} />{L_STATUS_PUBLIC}
	  <input type="radio" name="status" value="private" {STATUS_PRIVATE} />{L_STATUS_PRIVATE}
      </td>
    </tr>
    <tr>
      <td><strong>{L_EDIT_LOCK}</strong>
      <td>
	  <input type="radio" name="visible" value="false" {LOCKED} />{L_YES}
	  <input type="radio" name="visible" value="true" {UNLOCKED} />{L_NO}
      </td>
    </tr>
    <tr>
      <td><strong>{L_EDIT_COMMENTABLE}</strong>
      <td>
          <input type="radio" name="commentable" value="true" {COMMENTABLE_TRUE} />{L_YES}
          <input type="radio"  name="commentable" value="false" {COMMENTABLE_FALSE} />{L_NO}
      </td>
    </tr>
    <!-- BEGIN upload -->
    <tr>
      <td><strong>{L_EDIT_UPLOADABLE}</strong>
      <td>
          <input type="radio" name="uploadable" value="true" {UPLOADABLE_TRUE} />{L_YES}
          <input type="radio" name="uploadable" value="false" {UPLOADABLE_FALSE} />{L_NO}
      </td>
    </tr>
    <!-- END upload -->
  </table>
</fieldset>

<p style="text-align:center;">
  <input type="submit" value="{L_SUBMIT}" name="submit" />
  <input type="reset" value="{lang:Reset}" name="reset" />
</p>

<!-- BEGIN representant -->
<fieldset>
  <legend>{lang:Representant}</legend>
  <table>
    <tr>
      <td align="center">
        <!-- BEGIN picture -->
        <a href="{representant.picture.URL}"><img src="{representant.picture.SRC}" alt="" class="miniature" /></a>
        <!-- END picture -->

        <!-- BEGIN random -->
        <img src="./template/yoga/theme/category_representant_random.png" alt="{lang:Random picture}" class="miniature" />
        <!-- END random -->
      </td>
      <td>
        <!-- BEGIN set_random --> 
        <p><input type="submit" name="set_random_representant" value="{L_SET_RANDOM_REPRESENTANT}" /></p>
        <!-- END set_random -->

        <!-- BEGIN delete_representant -->
        <p><input type="submit" name="delete_representant" value="{lang:Delete Representant}" /></p>
        <!-- END delete_representant -->
      </td>
    </tr>
  </table>
</fieldset>
<!-- END representant -->

</form>
