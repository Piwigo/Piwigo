<!-- $Id$ -->
<h2>{lang:title_categories}</h2>

<h3>{CATEGORIES_NAV}</h3>

<form id="categoryOrdering" action="" method="post">

  <ul class="categoryUl">

    <!-- BEGIN category -->
    <li class="categoryLi"> <!-- category {category.ID} -->

      <ul class="categoryActions">
        <li><a href="{category.U_JUMPTO}" title="{lang:jump to category}"><img src="{themeconf:icon_dir}/category_jump-to.png" class="button" alt="{lang:jump to}" /></a></li> 
        <li><a href="{category.U_EDIT}" title="{lang:edit category informations}"><img src="{themeconf:icon_dir}/category_edit.png" class="button" alt="{lang:edit}"/></a></li>
        <!-- BEGIN elements -->
        <li><a href="{category.elements.URL}" title="{lang:manage category elements}"><img src="{themeconf:icon_dir}/category_elements.png" class="button" alt="{lang:elements}" /></a></li>
        <!-- END elements -->
        <li><a href="{category.U_CHILDREN}" title="{lang:manage sub-categories}"><img src="{themeconf:icon_dir}/category_children.png" class="button" alt="{lang:sub-categories}" /></a></li> 
        <!-- BEGIN permissions -->
        <li><a href="{category.permissions.URL}" title="{lang:edit category permissions}" ><img src="{themeconf:icon_dir}/category_permissions.png" class="button" alt="{lang:permissions}" /></a></li>
        <!-- END permissions -->
        <!-- BEGIN delete -->
        <li><a href="{category.delete.URL}" title="{lang:delete category}" onclick="return confirm('{lang:Are you sure?}');"><img src="{themeconf:icon_dir}/category_delete.png" class="button" alt="{lang:delete}" /></a></li>
        <!-- END delete -->
      </ul>

      <p><strong>{category.NAME}</strong></p>

      <p>
        <label>
          {lang:Position} :
          <input type="text" size="4" name="catOrd[{category.ID}]" maxlength="4" value="{category.RANK}" />
        </label>
      </p>

    </li>
    <!-- END category -->

  </ul>
  <p><input name="submitOrder" type="submit" value="{lang:Save order}" /></p>

</form>

<form id="addVirtual" action="{F_ACTION}" method="post">
  <p>
    {L_ADD_VIRTUAL} : <input type="text" name="virtual_name" />
    <input type="submit" value="{L_SUBMIT}" name="submitAdd" />
  </p>
</form>
