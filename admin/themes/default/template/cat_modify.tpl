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
          jQuery(".albumThumbnailImage")
            .attr('href', data.result.url)
            .find("img").attr('src', data.result.src)
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
          jQuery(".albumThumbnailImage").hide();
          if (jQuery(".albumThumbnailActions").data('random_allowed') == '') {
            jQuery(".deleteRepresentative").hide();
          } else {
            jQuery(".albumThumbnailRandom").show();
          }
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
  <h2><span style="letter-spacing:0">{$CATEGORIES_NAV}</span> &#8250; {'Edit album'|@translate} {$TABSHEET_TITLE}</h2>
</div>

<div id="catModify">

<fieldset>
  <legend>{'Informations'|@translate}</legend>

  <table style="width:100%">
    <tr>
      <td id="albumThumbnail">
{if isset($representant) }
        <a class="albumThumbnailImage" style="{if !isset($representant.picture)}display:none{/if}" href="{$representant.picture.url}"><img src="{$representant.picture.src}"></a>
        <img class="albumThumbnailRandom" style="{if isset($representant.picture)}display:none{/if}" src="{$ROOT_URL}{$themeconf.admin_icon_dir}/category_representant_random.png" alt="{'Random photo'|@translate}">

<p class="albumThumbnailActions" data-random_allowed="{$representant.ALLOW_SET_RANDOM}">
  {assign var="action_separator" value=""}
  {if $representant.ALLOW_SET_RANDOM }
  <a href="#refresh" data-category_id="{$CAT_ID}" class="refreshRepresentative" title="{'Find a new representant by random'|@translate}">{'Refresh'|@translate}</a>
  {assign var="action_separator" value="|"}
  {/if}

  {if isset($representant.ALLOW_DELETE) }
  {$action_separator}
   <a href="#delete" data-category_id="{$CAT_ID}" class="deleteRepresentative" title="{'Delete Representant'|@translate}">{'Delete'|translate}</a>
  {/if}
</p>
{/if}
      </td>

      <td id="albumLinks">
<p>{$INTRO}</p>
<ul>
{if cat_admin_access($CAT_ID)}
  <li><a class="icon-eye" href="{$U_JUMPTO}">{'jump to album'|@translate} →</a></li>
{/if}

{if isset($U_MANAGE_ELEMENTS) }
  <li><a class="icon-picture" href="{$U_MANAGE_ELEMENTS}">{'manage album photos'|@translate}</a></li>
{/if}

  <li style="text-transform:lowercase;"><a class="icon-plus-circled" href="{$U_ADD_PHOTOS_ALBUM}">{'Add Photos'|translate}</a></li>

  <li><a class="icon-sitemap" href="{$U_CHILDREN}">{'manage sub-albums'|@translate}</a></li>

{if isset($U_SYNC) }
  <li><a class="icon-exchange" href="{$U_SYNC}">{'Synchronize'|@translate}</a> ({'Directory'|@translate} = {$CAT_FULL_DIR})</li>
{/if}

{if isset($U_DELETE) }
  <li><a class="icon-trash deleteAlbum" href="#">{'delete album'|@translate}</a></li>
{/if}

</ul>
      </td>
    </tr>
  </table>

</fieldset>

<form action="{$F_ACTION}" method="POST">
<fieldset>
  <legend>{'Properties'|@translate}</legend>
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
        name="parent" style="width:600px"></select>
  </p>
{/if}

  <p>
    <strong>{'Lock'|@translate}</strong>
    <br>
      <label class="font-checkbox"><span class="icon-dot-circled"></span><input type="radio" name="visible" value="true"{if $CAT_VISIBLE == "true"} checked="checked"{/if}>{'No'|translate}</label>
      <label class="font-checkbox"><span class="icon-dot-circled"></span><input type="radio" name="visible" value="true_sub">{'No and unlock sub-albums'|translate}</label>
      <label class="font-checkbox"><span class="icon-dot-circled"></span><input type="radio" name="visible" value="false"{if $CAT_VISIBLE == "false"} checked="checked"{/if}>{'Yes'|translate}</label>
  </p>

  {if isset($CAT_COMMENTABLE)}
  <p>
    <strong>{'Comments'|@translate}</strong>
    <br>
      <label class="font-checkbox"><span class="icon-dot-circled"></span><input type="radio" name="commentable" value="false"{if $CAT_COMMENTABLE == "false"} checked="checked"{/if}>{'No'|translate}</label>
      <label class="font-checkbox"><span class="icon-dot-circled"></span><input type="radio" name="commentable" value="true"{if $CAT_COMMENTABLE == "true"} checked="checked"{/if}>{'Yes'|translate}</label>
    <label id="applytoSubAction">
      <label class="font-checkbox"><span class="icon-check"></span><input type="checkbox" name="apply_commentable_on_sub"></label>
      {'Apply to sub-albums'|@translate}
    </label>
  </p>
  {/if}

  <p style="margin:0">
    <button name="submit" type="submit" class="buttonLike">
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