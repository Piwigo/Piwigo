{include file='include/autosize.inc.tpl'}
{include file='include/dbselect.inc.tpl'}
{include file='include/datepicker.inc.tpl'}

{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.default.css"}

{footer_script}
(function(){
{* <!-- CATEGORIES --> *}
var categoriesCache = new LocalStorageCache('categoriesAdminList', 5*60, function(callback) {
  jQuery.getJSON('{$ROOT_URL}ws.php?format=json&method=pwg.categories.getAdminList', function(data) {
    callback(data.result.categories);
  });
});

jQuery('[data-selectize=categories]').selectize({
  valueField: 'id',
  labelField: 'fullname',
  searchField: ['fullname'],
  plugins: ['remove_button']
});

categoriesCache.get(function(categories) {
  jQuery('[data-selectize=categories]').each(function() {
    this.selectize.load(function(callback) {
      callback(categories);
    });

    jQuery.each(jQuery(this).data('value'), jQuery.proxy(function(i, id) {
      this.selectize.addItem(id);
    }, this));
  });
});

{* <!-- TAGS --> *}
var tagsCache = new LocalStorageCache('tagsAdminList', 5*60, function(callback) {
  jQuery.getJSON('{$ROOT_URL}ws.php?format=json&method=pwg.tags.getAdminList', function(data) {
    var tags = data.result.tags;
    
    for (var i=0, l=tags.length; i<l; i++) {
      tags[i].id = '~~' + tags[i].id + '~~';
    }
    
    callback(tags);
  });
});

jQuery('[data-selectize=tags]').selectize({
  valueField: 'id',
  labelField: 'name',
  searchField: ['name'],
  plugins: ['remove_button'],
  create: function(input, callback) {
    tagsCache.clear();
    
    callback({
      id: input,
      name: input
    });
  }
});

tagsCache.get(function(tags) {
  jQuery('[data-selectize=tags]').each(function() {
    this.selectize.load(function(callback) {
      callback(tags);
    });

    jQuery.each(jQuery(this).data('value'), jQuery.proxy(function(i, tag) {
      this.selectize.addItem(tag.id);
    }, this));
  });
});

{* <!-- DATEPICKER --> *}
jQuery(function(){ {* <!-- onLoad needed to wait localization loads --> *}
  jQuery('[data-datepicker]').pwgDatepicker({ showTimepicker: true });
});
}());
{/footer_script}

<h2>{$TITLE} &#8250; {'Edit photo'|@translate} {$TABSHEET_TITLE}</h2>

<form action="{$F_ACTION}" method="post" id="catModify">

  <fieldset>
    <legend>{'Informations'|@translate}</legend>

    <table>

      <tr>
        <td id="albumThumbnail">
          <img src="{$TN_SRC}" alt="{'Thumbnail'|@translate}" class="Thumbnail">
        </td>
        <td id="albumLinks" style="width:400px;vertical-align:top;">
          <ul style="padding-left:15px;margin:0;">
            <li>{$INTRO.file}</li>
            <li>{$INTRO.add_date}</li>
            <li>{$INTRO.added_by}</li>
            <li>{$INTRO.size}</li>
            <li>{$INTRO.stats}</li>
            <li>{$INTRO.id}</li>
          </ul>
        </td>
        <td class="photoLinks">
          <ul>
          {if isset($U_JUMPTO) }
            <li><a class="icon-eye" href="{$U_JUMPTO}">{'jump to photo'|@translate} →</a></li>
          {/if}
          {if !url_is_remote($PATH)}
            <li><a class="icon-arrows-cw" href="{$U_SYNC}">{'Synchronize metadata'|@translate}</a></li>

            <li><a class="icon-trash" href="{$U_DELETE}" onclick="return confirm('{'Are you sure?'|@translate|@escape:javascript}');">{'delete photo'|@translate}</a></li>
          {/if}
          </ul>
        </td>
      </tr>
    </table>

  </fieldset>

  <fieldset>
    <legend>{'Properties'|@translate}</legend>

    <p>
      <strong>{'Title'|@translate}</strong>
      <br>
      <input type="text" class="large" name="name" value="{$NAME|@escape}">
    </p>

    <p>
      <strong>{'Author'|@translate}</strong>
      <br>
      <input type="text" class="large" name="author" value="{$AUTHOR}">
    </p>

    <p>
      <strong>{'Creation date'|@translate}</strong>
      <br>
      <input type="hidden" name="date_creation" value="{$DATE_CREATION}">
      <label>
        <i class="icon-calendar"></i>
        <input type="text" data-datepicker="date_creation" data-datepicker-unset="date_creation_unset" readonly>
      </label>
      <a href="#" class="icon-cancel-circled" id="date_creation_unset">{'unset'|translate}</a>
    </p>

    <p>
      <strong>{'Linked albums'|@translate}</strong>
      <br>
      <select data-selectize="categories" data-value="{$associate_options_selected|@json_encode|escape:html}"
        name="associate[]" multiple style="width:600px;" ></select>
    </p>

    <p>
      <strong>{'Representation of albums'|@translate}</strong>
      <br>
      <select data-selectize="categories" data-value="{$represent_options_selected|@json_encode|escape:html}"
        name="represent[]" multiple style="width:600px;" ></select>
    </p>

    <p>
      <strong>{'Tags'|@translate}</strong>
      <br>
      <select data-selectize="tags" data-value="{$tag_selection|@json_encode|escape:html}"
        name="tags[]" multiple style="width:600px;" ></select>
    </p>

    <p>
      <strong>{'Description'|@translate}</strong>
      <br>
      <textarea name="description" id="description" class="description">{$DESCRIPTION}</textarea>
    </p>

    <p>
      <strong>{'Who can see this photo?'|@translate}</strong>
      <br>
      <select name="level" size="1">
        {html_options options=$level_options selected=$level_options_selected}
      </select>
   </p>

  <p style="margin:40px 0 0 0">
    <input class="submit" type="submit" value="{'Save Settings'|@translate}" name="submit">
  </p>
</fieldset>

</form>
