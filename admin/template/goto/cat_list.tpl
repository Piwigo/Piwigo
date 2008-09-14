{* $Id$ *}
<h2>{'title_categories'|@translate}</h2>

<h3>{$CATEGORIES_NAV}</h3>

<form id="addVirtual" action="{$F_ACTION}" method="post">
  <p>
    {'cat_add'|@translate} : <input type="text" name="virtual_name" />
    <input class="submit" type="submit" value="{'Submit'|@translate}" name="submitAdd" {$TAG_INPUT_ENABLED} />
    {if count($categories)>9 }
    <a href="#EoP" class="button"><img src="{$themeconf.admin_icon_dir}/page_end.png" class="button" alt="page_end" /></a>
    {/if}
  </p>
</form>

{if count($categories) }
<form id="categoryOrdering" action="{$F_ACTION}" method="post">
  <p>
    <input class="submit" name="submitOrder" type="submit" value="{'Save order'|@translate}" {$TAG_INPUT_ENABLED} />
    <input class="submit" name="submitOrderAlphaNum" type="submit" value="{'Order alphanumerically'|@translate}" {$TAG_INPUT_ENABLED} />
  </p>
  <ul class="categoryUl">

    {foreach from=$categories item=category}
    <li class="categoryLi{if $category.IS_VIRTUAL} virtual_cat{/if}">
      <!-- category {$category.ID} -->
      <ul class="categoryActions">
        <li><a href="{$category.U_JUMPTO}" title="{'jump to category'|@translate}"><img src="{$themeconf.admin_icon_dir}/category_jump-to.png" class="button" alt="{'jump to category'|@translate}" /></a></li>
        <li><a href="{$category.U_EDIT}" title="{'edit category informations'|@translate}"><img src="{$themeconf.admin_icon_dir}/category_edit.png" class="button" alt="{'edit'|@translate}"/></a></li>
        {if isset($category.U_MANAGE_ELEMENTS) }
        <li><a href="{$category.U_MANAGE_ELEMENTS}" title="{'manage category elements'|@translate}"><img src="{$themeconf.admin_icon_dir}/category_elements.png" class="button" alt="{'elements'|@translate}" /></a></li>
        {/if}
        <li><a href="{$category.U_CHILDREN}" title="{'manage sub-categories'|@translate}"><img src="{$themeconf.admin_icon_dir}/category_children.png" class="button" alt="{'sub-categories'|@translate}" /></a></li>
        {if isset($category.U_MANAGE_PERMISSIONS) }
        <li><a href="{$category.U_MANAGE_PERMISSIONS}" title="{'edit category permissions'|@translate}" ><img src="{$themeconf.admin_icon_dir}/category_permissions.png" class="button" alt="{'permissions'|@translate}" /></a></li>
        {/if}
        {if isset($category.U_DELETE) }
        <li><a href="{$category.U_DELETE}" title="{'delete category'|@translate}" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');"><img src="{$themeconf.admin_icon_dir}/category_delete.png" class="button" alt="{'delete'|@translate}" /></a></li>
        {/if}
      </ul>

      <p>
      <strong><a href="{$category.U_CHILDREN}" title="{'manage sub-categories'|@translate}">{$category.NAME}</a></strong>
      {if $category.IS_VIRTUAL}
      <img src="{$themeconf.admin_icon_dir}/virt_category.png" class="button" alt="{'virtual_category'|@translate}" />
      {/if}
      </p>

      <p>
        <label>
          {'Position'|@translate} :
          <input type="text" size="4" name="catOrd[{$category.ID}]" maxlength="4" value="{$category.RANK}" />
        </label>
      </p>

    </li>
    {/foreach}
  </ul>
  <p>
    <input class="submit" name="submitOrder" type="submit" value="{'Save order'|@translate}" {$TAG_INPUT_ENABLED} />
    <input class="submit" name="submitOrderAlphaNum" type="submit" value="{'Order alphanumerically'|@translate}" {$TAG_INPUT_ENABLED} />
  </p>

</form>
{/if}
