{footer_script require='jquery.ui.sortable'}{literal}
jQuery(document).ready(function(){
  jQuery(".catPos").hide();
  jQuery(".drag_button").show();
  jQuery(".categoryLi").css("cursor","move");
  jQuery(".categoryUl").sortable({
    axis: "y",
    opacity: 0.8,
    update : function() {
      jQuery("#manualOrder").show();
      jQuery("#notManualOrder").hide();
      jQuery("#autoOrder").hide();
      jQuery("#createAlbum").hide();
    },
  });

  jQuery("#categoryOrdering").submit(function(){
    ar = jQuery('.categoryUl').sortable('toArray');
    for(i=0;i<ar.length;i++) {
      cat = ar[i].split('cat_');
      document.getElementsByName('catOrd[' + cat[1] + ']')[0].value = i;
    }
  });

  jQuery("input[name=order_type]").click(function () {
    jQuery("#automatic_order_params").hide();
    if (jQuery("input[name=order_type]:checked").val() == "automatic") {
      jQuery("#automatic_order_params").show();
    }
  });

  jQuery("#addAlbumOpen").click(function(){
    jQuery("#createAlbum").toggle();
    jQuery("input[name=virtual_name]").focus();
    jQuery("#autoOrder").hide();
  });

  jQuery("#addAlbumClose").click(function(){
    jQuery("#createAlbum").hide();
  });


  jQuery("#autoOrderOpen").click(function(){
    jQuery("#autoOrder").toggle();
    jQuery("#createAlbum").hide();
  });

  jQuery("#autoOrderClose").click(function(){
    jQuery("#autoOrder").hide();
  });

  jQuery("#cancelManualOrder").click(function(){
    jQuery(".categoryUl").sortable("cancel");
    jQuery("#manualOrder").hide();
    jQuery("#notManualOrder").show();
  });
});
{/literal}{/footer_script}

<h2><span style="letter-spacing:0">{$CATEGORIES_NAV}</span> &#8250; {'Album list management'|@translate}</h2>

<form id="categoryOrdering" action="{$F_ACTION}" method="post">
<input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">

<p class="showCreateAlbum">
  <span id="notManualOrder">
    <a href="#" id="addAlbumOpen">{'create a new album'|@translate}</a>
    | <a href="#" id="autoOrderOpen">{'apply automatic sort order'|@translate}</a>
  </span>
  <span id="manualOrder" style="display:none;">
    <input class="submit" name="submitManualOrder" type="submit" value="{'Save manual order'|@translate}">
    {'... or '|@translate} <a href="#" id="cancelManualOrder">{'cancel manual order'|@translate}</a>
  </span>
</p>

<fieldset id="createAlbum" style="display:none;">
  <legend>{'create a new album'|@translate}</legend>
  <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">

  <p><strong>{'Album name'|@translate}</strong>
    <br><input type="text" name="virtual_name">
  </p>
  <p class="actionButtons">
    <input class="submit" type="submit" value="{'Create'|@translate}" name="submitAdd">
    <a href="#" id="addAlbumClose">{'Cancel'|@translate}</a>
  </p>
</fieldset>

{if count($categories) }

<fieldset id="autoOrder" style="display:none;">
  <legend>{'Automatic sort order'|@translate}</legend>
  <p><strong>{'Sort order'|@translate}</strong>
    <br><label><input type="radio" value="asc" name="ascdesc" checked="checked">{'ascending'|@translate}</label>
    <br><label><input type="radio" value="desc" name="ascdesc">{'descending'|@translate}</label>
  </p>

  <p>
    <label><input type="checkbox" name="recursive"> <strong>{'Apply to sub-albums'|@translate}</strong></label>
  </p>

  <p class="actionButtons">
    <input class="submit" name="submitAutoOrder" type="submit" value="{'Save order'|@translate}">
    <a href="#" id="autoOrderClose">{'Cancel'|@translate}</a>
  </p>
</fieldset>

  <ul class="categoryUl">

    {foreach from=$categories item=category}
    <li class="categoryLi{if $category.IS_VIRTUAL} virtual_cat{/if}" id="cat_{$category.ID}">
      <!-- category {$category.ID} -->
      <p class="albumTitle">
<img src="{$themeconf.admin_icon_dir}/cat_move.png" class="button drag_button" style="display:none;" alt="{'Drag to re-order'|@translate}" title="{'Drag to re-order'|@translate}">
      <strong><a href="{$category.U_CHILDREN}" title="{'manage sub-albums'|@translate}">{$category.NAME}</a></strong>
      </p>

      <p class="catPos">
        <label>
          {'Position'|@translate} :
          <input type="text" size="4" name="catOrd[{$category.ID}]" maxlength="4" value="{$category.RANK}">
        </label>
      </p>

<p class="albumActions">
        <a href="{$category.U_EDIT}">{'Edit'|@translate}</a>
        {if isset($category.U_MANAGE_ELEMENTS) }
        | <a href="{$category.U_MANAGE_ELEMENTS}">{'manage album photos'|@translate}</a>
        {/if}
        | <a href="{$category.U_CHILDREN}">{'manage sub-albums'|@translate}</a>
        {if isset($category.U_MANAGE_PERMISSIONS) }
        | <a href="{$category.U_MANAGE_PERMISSIONS}">{'Permissions'|@translate}</a>
        {/if}
        {if isset($category.U_SYNC) }
        | <a href="{$category.U_SYNC}">{'Synchronize'|@translate}</a>
        {/if}
        {if isset($category.U_DELETE) }
        | <a href="{$category.U_DELETE}" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');">{'delete album'|@translate}</a>
{/if}
{if cat_admin_access($category.ID)}
| 
<a href="{$category.U_JUMPTO}">{'jump to album'|@translate} →</a>
{/if}
</p>

    </li>
    {/foreach}
  </ul>
{/if}
</form>
