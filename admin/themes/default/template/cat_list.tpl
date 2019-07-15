{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

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

<div class="selectedAlbum cat-list-album-path"><span class="icon-sitemap">{$CATEGORIES_NAV}</span></div>
<p class="showCreateAlbum" id="notManualOrder">
  <a href="#" id="addAlbumOpen" class="icon-plus">{'create a new album'|@translate}</a>
  {if count($categories)}<span class="userSeparator">&middot;</span><a href="#" id="autoOrderOpen" class="icon-sort-alt-down">{'apply automatic sort order'|@translate}</a>{/if}
  {if ($PARENT_EDIT)}<span class="userSeparator">&middot;</span><a href="{$PARENT_EDIT}" class="icon-pencil"></span>{'edit'|@translate}</a>{/if}
</p>
<form id="formCreateAlbum" action="{$F_ACTION}" method="post" style="display:none;">
  <fieldset class="with-border">
      <legend>{'create a new album'|@translate}</legend>
      <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
      
      <p>
        <strong>{'Album name'|@translate}</strong><br>
        <input type="text" name="virtual_name" maxlength="255">
      </p>
      
      <p class="actionButtons">
        <button name="submitAdd" type="submit" class="buttonLike">
          <i class="icon-plus-circled"></i> {'Create'|translate}
        </button>

        <a href="#" id="addAlbumClose" class="icon-cancel-circled">{'Cancel'|@translate}</a>
      </p>
  </fieldset>
</form>
{if count($categories)}
<form id="formAutoOrder" action="{$F_ACTION}" method="post" style="display:none;">
  <fieldset class="with-border">
    <legend>{'Automatic sort order'|@translate}</legend>
    <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
    
    <p><strong>{'Sort order'|@translate}</strong>
  {foreach from=$sort_orders key=sort_code item=sort_label}
      <br>
      <label class="font-checkbox">
        <span class="icon-dot-circled"></span>
        <input type="radio" value="{$sort_code}" name="order_by" {if $sort_code eq $sort_order_checked}checked="checked"{/if}> {$sort_label}
      </label>
  {/foreach}
    </p>
  
    <p>
      <label class="font-checkbox">
        <span class="icon-check"></span>
        <input type="checkbox" name="recursive"> <strong>{'Apply to sub-albums'|@translate}</strong>
      </label>
    </p>
  
    <p class="actionButtons">
      <button name="submitAutoOrder" type="submit" class="buttonLike">
        <i class="icon-floppy"></i> {'Save order'|translate}
      </button>
      <a href="#" id="autoOrderClose" class="icon-cancel-circled">{'Cancel'|@translate}</a>
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
      <div class="albumBlock">
        <div class="albumLineBlock">
          <p class="albumTitle">
            <i class="icon-arrow-combo" title="{'Drag to re-order'|translate}"></i>
            <strong>{$category.NAME}</strong>
          </p>
          <span class="albumInfos"><span class="userSeparator">&middot;</span> {$category.NB_PHOTOS|translate_dec:'%d photo':'%d photos'} <span class="userSeparator">&middot;</span> {$category.NB_SUB_PHOTOS|translate_dec:'%d photo':'%d photos'} {$category.NB_SUB_ALBUMS|translate_dec:'in %d sub-album':'in %d sub-albums'}</span>
        </div>
  
        <input type="hidden" name="catOrd[{$category.ID}]" value="{$category.RANK}">

        <p class="albumActions">
          <a href="{$category.U_EDIT}"><span class="icon-pencil"></span>{'Edit'|@translate}</a>
          <a href="{$category.U_CHILDREN}" class="actionTitle"><span class="icon-flow-tree"></span><span>{'sub-albums'|@translate}</span></a>
          {if isset($category.U_SYNC) }
          <a href="{$category.U_SYNC}"><span class="icon-exchange"></span>{'Synchronize'|@translate}</a>
          {/if}
          {if cat_admin_access($category.ID)}
          <a href="{$category.U_JUMPTO}"><span class="icon-eye">{'Visit Gallery'|@translate} </a>
          {/if}
          <a href="{$category.U_ADD_PHOTOS_ALBUM}"><span class="icon-plus">{'Add Photos'|@translate} </a>
        </p>
      </div>

    </li>
    {/foreach}
  </ul>
{/if}
</form>
