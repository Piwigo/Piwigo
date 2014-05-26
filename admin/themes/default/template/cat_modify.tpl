{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.default.css"}

{footer_script}
{* <!-- CATEGORIES --> *}
var categoriesCache = new LocalStorageCache({
  key: 'categoriesAdminList',
  serverKey: '{$CACHE_KEYS.categories}',
  serverId: '{$CACHE_KEYS._hash}',

  loader: function(callback) {
    jQuery.getJSON('{$ROOT_URL}ws.php?format=json&method=pwg.categories.getAdminList', function(data) {
      callback(data.result.categories);
    });
  }
});

jQuery('[data-selectize=categories]').selectize({
  valueField: 'id',
  labelField: 'fullname',
  sortField: 'global_rank',
  searchField: ['fullname'],
  plugins: ['remove_button']
});

categoriesCache.get(function(categories) {
  categories.push({
    id: 0,
    fullname: '------------',
    global_rank: 0
  });
  
  // remove itself and children
  categories = jQuery.grep(categories, function(cat) {
    return !(/\b{$CAT_ID}\b/.test(cat.uppercats));
  });
  
  jQuery('[data-selectize=categories]').each(function() {
    this.selectize.load(function(callback) {
      callback(categories);
    });

    if (jQuery(this).data('value')) {
      this.selectize.setValue(jQuery(this).data('value')[0]);
    }
    
    // prevent empty value
    if (this.selectize.getValue() == '') {
      this.selectize.setValue(0);
    }
    this.selectize.on('dropdown_close', function() {
      if (this.getValue() == '') {
        this.setValue(0);
      }
    });
  });
});
{/footer_script}


<div class="titrePage">
  <h2><span style="letter-spacing:0">{$CATEGORIES_NAV}</span> &#8250; {'Edit album'|@translate} {$TABSHEET_TITLE}</h2>
</div>

<form action="{$F_ACTION}" method="POST" id="catModify">

<fieldset>
  <legend>{'Informations'|@translate}</legend>

  <table style="width:100%">
    <tr>
      <td id="albumThumbnail">
{if isset($representant) }
  {if isset($representant.picture) }
        <a href="{$representant.picture.URL}"><img src="{$representant.picture.SRC}" alt=""></a>
  {else}
        <img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/category_representant_random.png" alt="{'Random photo'|@translate}">
  {/if}

  {if $representant.ALLOW_SET_RANDOM }
        <p style="text-align:center;"><input class="submit" type="submit" name="set_random_representant" value="{'Refresh'|@translate}" title="{'Find a new representant by random'|@translate}"></p>
  {/if}

  {if isset($representant.ALLOW_DELETE) }
        <p><input class="submit" type="submit" name="delete_representant" value="{'Delete Representant'|@translate}"></p>
  {/if}
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
        name="parent" style="width:400px"></select>
  </p>
{/if}

  <p>
    <strong>{'Lock'|@translate}</strong>
    <br>
		{html_radios name='visible' values=['true','false'] output=['No'|translate,'Yes'|translate] selected=$CAT_VISIBLE}
  </p>

  {if isset($CAT_COMMENTABLE)}
  <p>
    <strong>{'Comments'|@translate}</strong>
    <br>
		{html_radios name='commentable' values=['false','true'] output=['No'|translate,'Yes'|translate] selected=$CAT_COMMENTABLE}
  </p>
  {/if}

  <p style="margin:0">
    <input class="submit" type="submit" value="{'Save Settings'|@translate}" name="submit">
  </p>
</fieldset>

</form>
