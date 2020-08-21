{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}
{include file='include/colorbox.inc.tpl'}
{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}

{footer_script}
{* <!-- CATEGORIES --> *}
var categoriesCache = new CategoriesCache({
  serverKey: '{$CACHE_KEYS.categories}',
  serverId: '{$CACHE_KEYS._hash}',
  rootUrl: '{$ROOT_URL}'
});

categoriesCache.selectize(jQuery('[data-selectize=categories]'), {
  default: 0,
  filter: function(categories, options) {
    // remove itself and children
    var filtered = jQuery.grep(categories, function(cat) {
      return !(/\b{$CAT_ID}\b/.test(cat.uppercats));
    });
    
    filtered.push({
      id: 0,
      fullname: '------------',
      global_rank: 0
    });
    
    return filtered;
  }
});

jQuery(document).ready(function() {
  jQuery(document).on('click', '.refreshRepresentative',  function(e) {
    var $this = jQuery(this);
    var method = 'pwg.categories.refreshRepresentative';

    jQuery.ajax({
      url: "ws.php?format=json&method="+method,
      type:"POST",
      data: {
        category_id: $this.data("category_id")
      },
      success:function(data) {
        var data = jQuery.parseJSON(data);
        if (data.stat == 'ok') {
          jQuery(".deleteRepresentative").show();
          
          jQuery(".albumThumbailImage, .albumThumbnailRandom").on('load', function () {
            cropImage();
          })

          jQuery(".albumThumbailImage, .albumThumbnailRandom")
            .attr('src', data.result.src)
            .end().show();
          
          jQuery(".albumThumbnailRandom").hide();
        }
        else {
          alert("error on "+method);
        }
      },
      error:function(XMLHttpRequest, textStatus, errorThrows) {
        alert("serious error on "+method);
      }
    });

    e.preventDefault();
  });

  jQuery(document).on('click', '.deleteRepresentative',  function(e) {
    var $this = jQuery(this);
    var method = 'pwg.categories.deleteRepresentative';

    jQuery.ajax({
      url: "ws.php?format=json&method="+method,
      type:"POST",
      data: {
        category_id: $this.data("category_id")
      },
      success:function(data) {
        var data = jQuery.parseJSON(data);
        if (data.stat == 'ok') {
          jQuery(".deleteRepresentative").hide();
          jQuery(".albumThumbnailImage").hide();
          jQuery(".albumThumbnailRandom").show();
        }
        else {
          alert("error on "+method);
        }
      },
      error:function(XMLHttpRequest, textStatus, errorThrows) {
        alert("serious error on "+method);
      }
    });

    e.preventDefault();
  });

  jQuery(".deleteAlbum").click(function() {
    jQuery.colorbox({
      inline:true,
      title:"{'delete album'|translate|escape:javascript}",
      href:".delete_popin"
    });

    return false;
  });

  function set_photo_deletion_mode() {
    if (jQuery("input[name=photo_deletion_mode]").length > 0) {
      var $photo_deletion_mode = jQuery("input[name=photo_deletion_mode]:checked").val();
      jQuery("#deleteConfirm").data("photo_deletion_mode", $photo_deletion_mode);
    }
  }

  set_photo_deletion_mode();

  jQuery("input[name=photo_deletion_mode]").change(function() {
    set_photo_deletion_mode();
  });

  jQuery("#deleteConfirm").click(function() {
    if (jQuery("input[name=photo_deletion_mode]").length > 0) {
      var $href = jQuery(this).attr("href");
      jQuery(this).attr("href", $href+"&photo_deletion_mode="+jQuery(this).data("photo_deletion_mode"));
    }
  });

  jQuery(document).on('click', '.close-delete_popin',  function(e) {
    jQuery('.delete_popin').colorbox.close();
    e.preventDefault();
  });
});

$(window).bind("load", function() {
  cropImage();
});

$(window).resize(function() {
  cropImage();
});

function cropImage() {
  let image = $(".albumThumbailImage");
  let imageW = image[0].naturalWidth;
  let imageH = image[0].naturalHeight;
  let size = $('.catThumbnail').innerWidth();

  if (imageW > imageH) {
    image.css('height', size+'px');
    image.css('width', (imageW * size / imageH)+'px');
  } else {
    image.css('width', size+'px');
    image.css('heigth', (imageH * size / imageW)+'px');
  }
}

{/footer_script}

{html_style}
.delete_popin {
  padding:20px 30px;
}

.delete_popin p {
  margin:0;
}

.delete_popin ul {
  padding:0;
  margin:30px 0;
}

.delete_popin ul li {
  list-style-type:none;
  margin:10px 0;
}

.delete_popin .buttonLike {
  padding:5px;
  margin-right:10px;
}

.delete_popin p.popin-actions {
  margin-top:30px;
}
{/html_style}


<div class="titrePage">
  <h2>{'Edit album'|@translate} <span title="{'Numeric identifier'|@translate}">#{$CAT_ID}</span> <span style="letter-spacing:0">{$CATEGORIES_NAV}</span></h2>
</div>

<div id="catModify">

<form action="{$F_ACTION}" method="POST">

<fieldset>
  <legend><span class="icon-info-circled-1 icon-blue"></span>{'Informations'|@translate}</legend>

  <div id="catHeader">

  
    <div class="catThumbnail">
      <div class="thumbnailContainer">
        {if isset($representant) }
        <img class="albumThumbailImage" src="{$representant.picture.src}">
        <div class="albumThumbnailRandom" style="{if isset($representant.picture)}display:none{/if}"><span class="icon-dice-solid"></span></div>

        <div class="albumThumbnailActions" data-random_allowed="{$representant.ALLOW_SET_RANDOM}">
          <div class="albumThumbnailActionContainer"> 
            {if $representant.ALLOW_SET_RANDOM }
            <a href="#refresh" data-category_id="{$CAT_ID}" class="refreshRepresentative icon-ccw" title="{'Find a new representant by random'|@translate}">{'Refresh thumbnail'|@translate}</a>
            {/if}
            {if isset($representant.ALLOW_DELETE)}
            <a href="#delete" data-category_id="{$CAT_ID}" class="deleteRepresentative icon-cancel" title="{'Delete Representant'|@translate}" style="{if !isset($representant.picture)}display:none{/if}">{'Remove thumbnail'|translate}</a>
            {/if}
          </div>
        </div>
        {else}
          <div class="albumThumbnailNoPhoto" title="{'No photos in the current album, no thumbnail available'|@translate}"><span class="icon-file-image"></span></div>
        {/if}
      </div>
    </div>

    <div class="catInfo">
      <div class="container">
        {if isset($INFO_CREATION)}
          <span class="icon-yellow">{$INFO_CREATION}</span>
        {/if}
        <span class="icon-red">{$INFO_LAST_MODIFIED}</span>
        {if isset($INFO_PHOTO)}
          <span class="icon-purple" title="{$INFO_TITLE}">{$INFO_PHOTO}</span>
        {/if}
        {if isset($INFO_DIRECT_SUB)}
          <span class="icon-blue">{$INFO_DIRECT_SUB}</span>
        {/if}
        {if isset($U_SYNC) }
        <span class="icon-green" >{'Directory'|@translate} : {$CAT_FULL_DIR}</span>
        {/if}
      </div>
    </div>

    <div class="catAction">
      <div class="container">
      <strong>{"Actions"|@translate}</strong>
      {if cat_admin_access($CAT_ID)}
        <a class="icon-eye" href="{$U_JUMPTO}">{'Open in gallery'|@translate}</a>
      {/if}

      {if isset($U_MANAGE_ELEMENTS) }
        <a class="icon-picture" href="{$U_MANAGE_ELEMENTS}">{'manage album photos'|@translate}</a>
      {/if}

        <a class="icon-plus-circled" href="{$U_ADD_PHOTOS_ALBUM}">{'Add Photos'|translate}</a>

        <a class="icon-sitemap" href="{$U_CHILDREN}">{'manage sub-albums'|@translate}</a>

      {if isset($U_SYNC) }
        <a class="icon-exchange" href="{$U_SYNC}">{'Synchronize'|@translate}</a>
      {/if}

      {if isset($U_DELETE) }
        <a class="icon-trash deleteAlbum" href="#">{'delete album'|@translate}</a>
      {/if} 
      </div>
    </div>

    <div class="catLock">
    <div class="container">
      <div>
        <strong>
          {'Publication'|@translate}
          <span class="icon-help-circled" title="{'Locked albums are disabled for maintenance. Only administrators can view them in the gallery. Lock this album will also lock his Sub-albums'|@translate}" style="cursor:help"></span>
        </strong>
        <div class="switch-input">
          <span class="label">{'Unlocked'|@translate}</span>
          <label class="switch">
            <input type="checkbox" name="visible" id="toggleSelectionMode" value="true" {if $CAT_VISIBLE == "false"}checked{/if}>
            <span class="slider round"></span>
          </label>
          <span class="label">{'Locked'|@translate}</span>
        </div>    
      </div>
    {if isset($CAT_COMMENTABLE)}
      <div>
        <strong>{'Comments'|@translate} <span class="icon-help-circled" title="{'A photo can receive comments from your visitors if it belongs to an album with comments activated.'|@translate}" style="cursor:help"></span></strong>
        <div class="switch-input">
          <span class="label">{'Forbidden'|@translate}</span>
          <label class="switch">
            <input type="checkbox" name="commentable" id="commentable" value="true" {if $CAT_COMMENTABLE == "true"}checked{/if}>
            <span class="slider round"></span>
          </label>
          <span class="label">{'Authorized'|@translate}</span>
        <div>
        <label id="applytoSubAction">
        {if isset($INFO_DIRECT_SUB)}
        <label class="font-checkbox"><span class="icon-check"></span><input type="checkbox" name="apply_commentable_on_sub"></label>
          {'Apply to sub-albums'|@translate}
        </label>
        {/if}
      </div>
    {/if}
      </div>
    </div>
  </div>
</fieldset>

<fieldset>
  <legend><span class="icon-tools icon-red"></span>{'Properties'|@translate}</legend>
  <p>
    <strong>{'Name'|@translate}</strong>
    <br>
    <input type="text" class="large" name="name" value="{$CAT_NAME}" maxlength="255">
  </p>

  <p>
    <strong>{'Description'|@translate}</strong>
    <br>
    <textarea cols="50" rows="5" name="comment" id="comment" class="description">{$CAT_COMMENT}</textarea>
  </p>

{if isset($parent_category) }
  <p>
    <strong>{'Parent album'|@translate}</strong>
    <br>
    <select data-selectize="categories" data-value="{$parent_category|@json_encode|escape:html}"
        name="parent" style="width:100%"></select>
  </p>
{/if}

  <p style="margin:0">
    <button name="submit" type="submit" class="buttonGradient">
      <i class="icon-floppy"></i> {'Save Settings'|@translate}
    </button>
  </p>
</fieldset>

</form>

<div style="display:none">
  <div class="delete_popin">

    <p>
{if $NB_SUBCATS == 0}
      {'Delete album "%s".'|translate:$CATEGORY_FULLNAME}
{else}
      {'Delete album "%s" and its %d sub-albums.'|translate:$CATEGORIES_NAV:$NB_SUBCATS}
{/if}
    </p>

{if $NB_IMAGES_RECURSIVE > 0}
  <ul>
  {if $NB_IMAGES_ASSOCIATED_OUTSIDE > 0}
    <li><label class="font-checkbox"><span class="icon-dot-circled"></span><input type="radio" name="photo_deletion_mode" value="force_delete"> {'delete album and all %d photos, even the %d associated to other albums'|translate:$NB_IMAGES_RECURSIVE:$NB_IMAGES_ASSOCIATED_OUTSIDE}</label></li>
  {/if}
  {if $NB_IMAGES_BECOMING_ORPHAN > 0}
    <li><label class="font-checkbox"><span class="icon-dot-circled"></span><input type="radio" name="photo_deletion_mode" value="delete_orphans"> {'delete album and the %d orphan photos'|translate:$NB_IMAGES_BECOMING_ORPHAN}</label></li>
  {/if}
    <li><label class="font-checkbox"><span class="icon-dot-circled"></span><input type="radio" name="photo_deletion_mode" value="no_delete" checked="checked"> {'delete only album, not photos'|translate}</label></li>
  </ul>
{/if}

    <p class="popin-actions">
      <a id="deleteConfirm" class="buttonLike" type="submit" href="{$U_DELETE}"><i class="icon-trash"></i> {'Confirm deletion'|translate}</button>
      <a class="icon-cancel-circled close-delete_popin" href="#">{'Cancel'|translate}</a>
    </p>

{* $U_DELETE *}
  </div>
</div>

</div> {* #catModify *}