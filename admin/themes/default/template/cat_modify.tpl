
{include file='include/autosize.inc.tpl'}
{include file='include/resize.inc.tpl'}

<div class="titrePage">
  <h2>{'Edit a category'|@translate}</h2>
</div>

<h3>{$CATEGORIES_NAV}</h3>

<ul class="categoryActions">
  {if cat_admin_access($CAT_ID)}
  <li><a href="{$U_JUMPTO}" title="{'jump to category'|@translate}"><img src="{$themeconf.admin_icon_dir}/category_jump-to.png" class="button" alt="{'jump to category'|@translate}"></a></li>
  {/if}
  {if isset($U_MANAGE_ELEMENTS) }
  <li><a href="{$U_MANAGE_ELEMENTS}" title="{'elements'|@translate}"><img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/category_elements.png" class="button" alt="{'elements'|@translate}"></a></li>
  <li><a href="{$U_MANAGE_RANKS}" title="{'manage image ranks'|@translate}"><img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/ranks.png" class="button" alt="{'ranks'|@translate}"></a></li>
  {/if}
  <li><a href="{$U_CHILDREN}" title="{'sub-categories'|@translate}"><img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/category_children.png" class="button" alt="{'sub-categories'|@translate}"></a></li>
  {if isset($U_MANAGE_PERMISSIONS) }
  <li><a href="{$U_MANAGE_PERMISSIONS}" title="{'Permissions'|@translate}"><img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/category_permissions.png" class="button" alt="{'Permissions'|@translate}"></a></li>
  {/if}
  {if isset($U_DELETE) }
  <li><a href="{$U_DELETE}" title="{'delete'|@translate}"><img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/category_delete.png" class="button" alt="{'delete'|@translate}" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');"></a></li>
  {/if}
</ul>

<form action="{$F_ACTION}" method="POST" id="catModify">

<fieldset>
  <legend>{'Informations'|@translate}</legend>
  <table>

    {if isset($CAT_FULL_DIR) }
    <tr>
      <td><strong>{'Directory'|@translate}</strong></td>
      <td class="row1">{$CAT_FULL_DIR}</td>
    </tr>
    {/if}
    
    <tr>
      <td><strong>{'Name'|@translate}</strong></td>
      <td>
        <input type="text" class="large" name="name" value="{$CAT_NAME}" maxlength="60">
      </td>
    </tr>
    <tr>
      <td><strong>{'Description'|@translate}</strong></td>
      <td>
        <textarea cols="50" rows="5" name="comment" id="comment" class="description">{$CAT_COMMENT}</textarea>
      </td>
    </tr>
  </table>
</fieldset>

{if isset($move_cat_options) }
<fieldset id="move">
  <legend>{'Move'|@translate}</legend>
  {'Parent category'|@translate}
  <select class="categoryDropDown" name="parent">
    <option value="0">------------</option>
    {html_options options=$move_cat_options selected=$move_cat_options_selected }
  </select>
</fieldset>
{/if}

<fieldset id="options">
  <legend>{'Options'|@translate}</legend>
  <table>
    <tr>
      <td><strong>{'Access type'|@translate}</strong>
      <td>
        {html_radios name='status' values=$status_values output=$status_values|translate selected=$CAT_STATUS}
      </td>
    </tr>
    <tr>
      <td><strong>{'Lock'|@translate}</strong>
      <td>
        {html_radios name='visible' values='true,false'|@explode output='No,Yes'|@explode|translate selected=$CAT_VISIBLE}
      </td>
    </tr>
    <tr>
      <td><strong>{'Comments'|@translate}</strong>
      <td>
        {html_radios name='commentable' values='false,true'|@explode output='No,Yes'|@explode|translate selected=$CAT_COMMENTABLE}
      </td>
    </tr>
    {if isset($SHOW_UPLOADABLE) }
    <tr>
      <td><strong>{'Authorize upload'|@translate}</strong>
      <td>
        {html_radios name='uploadable' values='false,true'|@explode output='No,Yes'|@explode|translate selected=$CAT_UPLOADABLE}
      </td>
    </tr>
    {/if}
  </table>
</fieldset>

<fieldset id="image_order">
  <legend>{'Sort order'|@translate}</legend>
  <input type="checkbox" name="image_order_default" id="image_order_default" {$IMG_ORDER_DEFAULT}>
  <label for="image_order_default">{'Use the default image sort order (defined in the configuration file)'|@translate}</label>
  <br>
  <input type="checkbox" name="image_order_subcats" id="image_order_subcats">
  <label for="image_order_subcats">{'Apply to subcategories'|@translate}</label>
  <br>
  
  {foreach from=$image_orders item=order}
    <select name="order_field_{$order.ID}">
      {html_options options=$image_order_field_options selected=$order.FIELD }
    </select>
    <select name="order_direction_{$order.ID}">
      {html_options options=$image_order_direction_options selected=$order.DIRECTION }
    </select><br>
  {/foreach}
  
</fieldset>

<p style="text-align:center;">
  <input class="submit" type="submit" value="{'Submit'|@translate}" name="submit" {$TAG_INPUT_ENABLED}>
  <input class="submit" type="reset" value="{'Reset'|@translate}" name="reset">
</p>

{if isset($representant) }
<fieldset id="representant">
  <legend>{'Representant'|@translate}</legend>
  <table>
    <tr>
      <td align="center">
        {if isset($representant.picture) }
        <a href="{$representant.picture.URL}"><img src="{$representant.picture.SRC}" alt="" class="miniature"></a>
        {else}
        <img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/category_representant_random.png" class="button" alt="{'Random picture'|@translate}" class="miniature">
        {/if}
      </td>
      <td>
        {if $representant.ALLOW_SET_RANDOM }
        <p><input class="submit" type="submit" name="set_random_representant" value="{'Find a new representant by random'|@translate}" {$TAG_INPUT_ENABLED}></p>
        {/if}

        {if isset($representant.ALLOW_DELETE) }
        <p><input class="submit" type="submit" name="delete_representant" value="{'Delete Representant'|@translate}"></p>
        {/if}
      </td>
    </tr>
  </table>
</fieldset>
{/if}

</form>

<form action="{$F_ACTION}" method="POST" id="links">

<fieldset id="linkAllNew">
  <legend>{'Link all category elements to a new category'|@translate}</legend>

  <table>
    <tr>
      <td>{'Virtual category name'|@translate}</td>
      <td><input type="text" class="large" name="virtual_name"></td>
    </tr>

    <tr>
      <td>{'Parent category'|@translate}</td>
      <td>
        <select class="categoryDropDown" name="parent">
          <option value="0">------------</option>
          {html_options options=$create_new_parent_options }
        </select>
      </td>
    </tr>
  </table>

  <p>
    <input class="submit" type="submit" value="{'Submit'|@translate}" name="submitAdd" {$TAG_INPUT_ENABLED}>
    <input class="submit" type="reset" value="{'Reset'|@translate}" name="reset">
  </p>

</fieldset>

<fieldset id="linkAllExist">
  <legend>{'Link all category elements to some existing categories'|@translate}</legend>

  <table>
    <tr>
      <td>{'Categories'|@translate}</td>
      <td>
        <select class="categoryList" name="destinations[]" multiple="multiple" size="5">
          {html_options options=$category_destination_options }
        </select>
      </td>
    </tr>
  </table>

  <p>
    <input class="submit" type="submit" value="{'Submit'|@translate}" name="submitDestinations" {$TAG_INPUT_ENABLED}>
    <input class="submit" type="reset" value="{'Reset'|@translate}" name="reset">
  </p>

</fieldset>

{if isset($group_mail_options)}
<fieldset id="emailCatInfo">
  <legend>{'Send an information email to group members'|@translate}</legend>

  <table>
    <tr>
      <td><strong>{'Group'|@translate}</strong></td>
      <td>
        <select name="group">
          {html_options options=$group_mail_options}
        </select>
      </td>
    </tr>
    <tr>
      <td><strong>{'Mail content'|@translate}</strong></td>
      <td>
        <textarea cols="50" rows="5" name="mail_content" id="mail_content" class="description">{$MAIL_CONTENT}</textarea>
      </td>
    </tr>

  </table>

  <p>
    <input class="submit" type="submit" value="{'Submit'|@translate}" name="submitEmail" {$TAG_INPUT_ENABLED}>
    <input class="submit" type="reset" value="{'Reset'|@translate}" name="reset">
  </p>

</fieldset>
{/if}

</form>
