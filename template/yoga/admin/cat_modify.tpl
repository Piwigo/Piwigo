<!-- $Id$ -->
<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{lang:title_edit_cat}</h2>
</div>

<h3>{CATEGORIES_NAV}</h3>

<ul class="categoryActions">
  <li><a href="{U_JUMPTO}" title="{lang:jump to category}"><img src="{themeconf:icon_dir}/category_jump-to.png" class="button" alt="{lang:jump to}" /></a></li>
  <!-- BEGIN elements -->
  <li><a href="{elements.URL}" title="{lang:manage category elements}"><img src="{themeconf:icon_dir}/category_elements.png" class="button" alt="{lang:elements}" /></a></li>
  <!-- END elements -->
  <li><a href="{U_CHILDREN}" title="{lang:manage sub-categories}"><img src="{themeconf:icon_dir}/category_children.png" class="button" alt="{lang:sub-categories}" /></a></li>
  <!-- BEGIN permissions -->
  <li><a href="{permissions.URL}" title="{lang:edit category permissions}" ><img src="{themeconf:icon_dir}/category_permissions.png" class="button" alt="{lang:permissions}" /></a></li>
  <!-- END permissions -->
  <!-- BEGIN delete -->
  <li><a href="{delete.URL}" title="{lang:delete category}" onclick="return confirm('{lang:Are you sure?}');"><img src="{themeconf:icon_dir}/category_delete.png" class="button" alt="{lang:delete}" /></a></li>
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
        <img src="{themeconf:icon_dir}/category_representant_random.png" class="button" alt="{lang:Random picture}" class="miniature" />
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

<form action="{F_ACTION}" method="POST" id="links">
  
<fieldset>
  <legend>{lang:Create a destination category}</legend>

  <table>
    <tr>
      <td>{lang:virtual category name}</td>
      <td><input type="text" name="virtual_name"></td>
    </tr>

    <tr>
      <td>{lang:parent category}</td>
      <td>
        <select class="categoryList" name="parent">
          <!-- BEGIN category_option_parent -->
          <option {category_option_parent.SELECTED} value="{category_option_parent.VALUE}">{category_option_parent.OPTION}</option>
          <!-- END category_option_parent -->
        </select>
      </td>
    </tr>
  </table>

  <p style="text-align:center;">
    <input type="submit" value="{lang:Submit}" name="submitAdd" />
    <input type="reset" value="{lang:Reset}" name="reset" />
  </p>  

</fieldset>

<fieldset>
  <legend>{lang:Source/destination links}</legend>

  <table class="doubleSelect">
    <tr>
      <td>
        <h3>{lang:Destination categories}</h3>
        <select class="categoryList" name="destination_true[]" multiple="multiple" size="30">
          <!-- BEGIN destination_option_true -->
          <option {destination_option_true.SELECTED} value="{destination_option_true.VALUE}">{destination_option_true.OPTION}</option>
          <!-- END destination_option_true -->
        </select>
        <p><input type="submit" value="&raquo;" name="destination_falsify" style="font-size:15px;"/></p>
      </td>

      <td>
        <h3>{lang:Non destination categories}</h3>
        <select class="categoryList" name="destination_false[]" multiple="multiple" size="30">
          <!-- BEGIN destination_option_false -->
          <option {destination_option_false.SELECTED} value="{destination_option_false.VALUE}">{destination_option_false.OPTION}</option>
          <!-- END destination_option_false -->
        </select>
        <p><input type="submit" value="&laquo;" name="destination_trueify" style="font-size:15px;" /></p>
      </td>
    </tr>
  </table>

  <table class="doubleSelect">
    <tr>
      <td>
        <h3>{lang:Source categories}</h3>
        <select class="categoryList" name="source_true[]" multiple="multiple" size="30">
          <!-- BEGIN source_option_true -->
          <option {source_option_true.SELECTED} value="{source_option_true.VALUE}">{source_option_true.OPTION}</option>
          <!-- END source_option_true -->
        </select>
        <p><input type="submit" value="&raquo;" name="source_falsify" style="font-size:15px;"/></p>
      </td>

      <td>
        <h3>{lang:Non source categories}</h3>
        <select class="categoryList" name="source_false[]" multiple="multiple" size="30">
          <!-- BEGIN source_option_false -->
          <option {source_option_false.SELECTED} value="{source_option_false.VALUE}">{source_option_false.OPTION}</option>
          <!-- END source_option_false -->
        </select>
        <p><input type="submit" value="&laquo;" name="source_trueify" style="font-size:15px;" /></p>
      </td>
    </tr>
  </table>
  
</fieldset>
</form>
