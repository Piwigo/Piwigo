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
});

{/footer_script}


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

<p class="albumThumbnailActions">
  {if $representant.ALLOW_SET_RANDOM }
  <a href="#refresh" data-category_id="{$CAT_ID}" class="refreshRepresentative" title="{'Find a new representant by random'|@translate}">{'Refresh'|@translate}</a>
  {/if}

  {if isset($representant.ALLOW_DELETE) }
  | <a href="#delete" data-category_id="{$CAT_ID}" class="deleteRepresentative" title="{'Delete Representant'|@translate}">{'Delete'|translate}</a>
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
  <li><a class="icon-trash" href="{$U_DELETE}" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');">{'delete album'|@translate}</a></li>
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
		{html_radios name='visible' values=['true','true_sub','false'] output=['No'|translate,'No and unlock sub-albums'|translate,'Yes'|translate] selected=$CAT_VISIBLE}
  </p>

  {if isset($CAT_COMMENTABLE)}
  <p>
    <strong>{'Comments'|@translate}</strong>
    <br>
		{html_radios name='commentable' values=['false','true'] output=['No'|translate,'Yes'|translate] selected=$CAT_COMMENTABLE}
    <label id="applytoSubAction">
      <input type="checkbox" name="apply_commentable_on_sub">
      {'Apply to sub-albums'|@translate}
    </label>
  </p>
  {/if}

  <p style="margin:0">
    <input class="submit" type="submit" value="{'Save Settings'|@translate}" name="submit">
  </p>
</fieldset>

</form>
</div> {* #catModify *}