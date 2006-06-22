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
        <textarea cols="50" rows="5" name="comment" class="description">{CAT_COMMENT}</textarea>
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
  <input type="submit" value="{L_SUBMIT}" name="submit" {TAG_INPUT_ENABLED}/>
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
        <p><input type="submit" name="set_random_representant" value="{L_SET_RANDOM_REPRESENTANT}" {TAG_INPUT_ENABLED}/></p>
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
  <legend>{lang:Link all category elements to a new category}</legend>

  <table>
    <tr>
      <td>{lang:Virtual category name}</td>
      <td><input type="text" name="virtual_name"></td>
    </tr>

    <tr>
      <td>{lang:Parent category}</td>
      <td>
        <select class="categoryList" name="parent">
          <!-- BEGIN category_option_parent -->
          <option {category_option_parent.SELECTED} value="{category_option_parent.VALUE}">{category_option_parent.OPTION}</option>
          <!-- END category_option_parent -->
        </select>
      </td>
    </tr>
  </table>

  <p>
    <input type="submit" value="{lang:Submit}" name="submitAdd" {TAG_INPUT_ENABLED}/>
    <input type="reset" value="{lang:Reset}" name="reset" />
  </p>  

</fieldset>

<fieldset>
  <legend>{lang:Link all category elements to some existing categories}</legend>

  <table>
    <tr>
      <td>{lang:Categories}</td>
      <td>
        <select class="categoryList" name="destinations[]" multiple="multiple" size="5">
          <!-- BEGIN category_option_destination -->
          <option {category_option_destination.SELECTED} value="{category_option_destination.VALUE}">{category_option_destination.OPTION}</option>
          <!-- END category_option_destination -->
        </select>
      </td>
    </tr>
  </table>

  <p>
    <input type="submit" value="{lang:Submit}" name="submitDestinations" {TAG_INPUT_ENABLED}/>
    <input type="reset" value="{lang:Reset}" name="reset" />
  </p>  

</fieldset>

</form>
