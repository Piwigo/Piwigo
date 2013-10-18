{footer_script require='jquery.ui.sortable'}{literal}
jQuery(document).ready(function(){
  jQuery(".drag_button").show();
  jQuery(".categoryLi").css("cursor","move");
  jQuery(".categoryUl").sortable({
    axis: "y",
    opacity: 0.8,
    update : function() {
      jQuery("#manualOrder").show();
      jQuery("#notManualOrder").hide();
      jQuery("#formAutoOrder").hide();
      jQuery("#formCreateAlbum").hide();
    }
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
    jQuery("#formCreateAlbum").toggle();
    jQuery("input[name=virtual_name]").focus();
    jQuery("#formAutoOrder").hide();
  });

  jQuery("#addAlbumClose").click(function(){
    jQuery("#formCreateAlbum").hide();
  });


  jQuery("#autoOrderOpen").click(function(){
    jQuery("#formAutoOrder").toggle();
    jQuery("#formCreateAlbum").hide();
  });

  jQuery("#autoOrderClose").click(function(){
    jQuery("#formAutoOrder").hide();
  });

  jQuery("#cancelManualOrder").click(function(){
    jQuery(".categoryUl").sortable("cancel");
    jQuery("#manualOrder").hide();
    jQuery("#notManualOrder").show();
  });
});
{/literal}{/footer_script}

<h2><span style="letter-spacing:0">{$CATEGORIES_NAV}</span> &#8250; {'Album list management'|@translate}</h2>
<p class="showCreateAlbum" id="notManualOrder">
  <a href="#" id="addAlbumOpen">{'create a new album'|@translate}</a>
  {if count($categories)}| <a href="#" id="autoOrderOpen">{'apply automatic sort order'|@translate}</a>{/if}
</p>
<form id="formCreateAlbum" action="{$F_ACTION}" method="post" style="display:none;">
  <fieldset>
      <legend>{'create a new album'|@translate}</legend>
      <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
      
      <p>
        <strong>{'Album name'|@translate}</strong><br>
        <input type="text" name="virtual_name" maxlength="255">
      </p>
      
      <p class="actionButtons">
        <input class="submit" name="submitAdd" type="submit" value="{'Create'|@translate}">
        <a href="#" id="addAlbumClose">{'Cancel'|@translate}</a>
      </p>
  </fieldset>
</form>
{if count($categories)}
<form id="formAutoOrder" action="{$F_ACTION}" method="post" style="display:none;">
  <fieldset>
    <legend>{'Automatic sort order'|@translate}</legend>
    <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
    
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
</form>
{/if}

<form id="categoryOrdering" action="{$F_ACTION}" method="post">
  <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
  <p id="manualOrder" style="display:none">
    <input class="submit" name="submitManualOrder" type="submit" value="{'Save manual order'|@translate}">
    {'... or '|@translate} <a href="#" id="cancelManualOrder">{'cancel manual order'|@translate}</a>
  </p>
  
{if count($categories)}
  <ul class="categoryUl">
    {foreach from=$categories item=category}
    <li class="categoryLi{if $category.IS_VIRTUAL} virtual_cat{/if}" id="cat_{$category.ID}">
      <!-- category {$category.ID} -->
      <p class="albumTitle">
        <img src="{$themeconf.admin_icon_dir}/cat_move.png" class="drag_button" style="display:none;" alt="{'Drag to re-order'|@translate}" title="{'Drag to re-order'|@translate}">
        <strong><a href="{$category.U_CHILDREN}" title="{'manage sub-albums'|@translate}">{$category.NAME}</a></strong>
      </p>

      <input type="hidden" name="catOrd[{$category.ID}]" value="{$category.RANK}">

      <p class="albumActions">
        <a href="{$category.U_EDIT}"><span class="icon-pencil"></span>{'Edit'|@translate}</a>
        {if isset($category.U_MANAGE_ELEMENTS) }
        | <a href="{$category.U_MANAGE_ELEMENTS}"><span class="icon-picture"></span>{'manage album photos'|@translate}</a>
        {/if}
        | <a href="{$category.U_CHILDREN}"><span class="icon-sitemap"></span>{'manage sub-albums'|@translate}</a>
        {if isset($category.U_SYNC) }
        | <a href="{$category.U_SYNC}"><span class="icon-exchange"></span>{'Synchronize'|@translate}</a>
        {/if}
        {if isset($category.U_DELETE) }
        | <a href="{$category.U_DELETE}" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');"><span class="icon-trash"></span>{'delete album'|@translate}</a>
      {/if}
      {if cat_admin_access($category.ID)}
        | <a href="{$category.U_JUMPTO}">{'jump to album'|@translate} →</a>
      {/if}
      </p>

    </li>
    {/foreach}
  </ul>
{/if}
</form>
