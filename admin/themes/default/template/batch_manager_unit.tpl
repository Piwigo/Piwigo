{include file='include/autosize.inc.tpl'}
{include file='include/datepicker.inc.tpl'}
{include file='include/colorbox.inc.tpl'}

{combine_script id='LocalStorageCache' load='footer' path='admin/themes/default/js/LocalStorageCache.js'}

{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.default.css"}

{footer_script}
(function(){
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

{* <!-- THUMBNAILS --> *}
$(".elementEdit img")
  .css("opacity", 0.6) // Opacity on page load
  .hover(function(){
    $(this).fadeTo("slow", 1.0); // Opacity on hover
  },function(){
    $(this).fadeTo("slow", 0.6); // Opacity on mouseout
  });
}());
{/footer_script}

<h2>{'Batch Manager'|@translate}</h2>

<form action="{$F_ACTION}" method="POST">
<fieldset>
  <legend>{'Display options'|@translate}</legend>
  <p>{'photos per page'|@translate} :
      <a href="{$U_ELEMENTS_PAGE}&amp;display=5">5</a>
    | <a href="{$U_ELEMENTS_PAGE}&amp;display=10">10</a>
    | <a href="{$U_ELEMENTS_PAGE}&amp;display=50">50</a>
    | <a href="{$U_ELEMENTS_PAGE}&amp;display=all">{'all'|@translate}</a>
  </p>

</fieldset>

{if !empty($navbar) }{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}

{if !empty($elements) }
<div><input type="hidden" name="element_ids" value="{$ELEMENT_IDS}"></div>
{foreach from=$elements item=element}
<fieldset class="elementEdit">
  <legend>{$element.LEGEND}</legend>

  <span class="thumb">
    <a href="{$element.FILE_SRC}" class="preview-box" title="{$element.LEGEND|@htmlspecialchars}"><img src="{$element.TN_SRC}" alt=""></a>
    <br/>
    <a href="{$element.U_EDIT}">{'Edit'|@translate}</a>
  </span>

  <table>

    <tr>
      <td><strong>{'Title'|@translate}</strong></td>
      <td><input type="text" class="large" name="name-{$element.id}" value="{$element.NAME}"></td>
    </tr>

    <tr>
      <td><strong>{'Author'|@translate}</strong></td>
      <td><input type="text" class="large" name="author-{$element.id}" value="{$element.AUTHOR}"></td>
    </tr>

    <tr>
      <td><strong>{'Creation date'|@translate}</strong></td>
      <td>
        <input type="hidden" name="date_creation-{$element.id}" value="{$element.DATE_CREATION}">
        <label>
          <i class="icon-calendar"></i>
          <input type="text" data-datepicker="date_creation-{$element.id}" data-datepicker-unset="date_creation_unset-{$element.id}" readonly>
        </label>
        <a href="#" class="icon-cancel-circled" id="date_creation_unset-{$element.id}">{'unset'|translate}</a>
      </td>
    </tr>
    <tr>
      <td><strong>{'Who can see this photo?'|@translate}</strong></td>
      <td>
        <select name="level-{$element.id}">
          {html_options options=$level_options selected=$element.LEVEL}
        </select>
      </td>
    </tr>

    <tr>
      <td><strong>{'Tags'|@translate}</strong></td>
      <td>
        <select data-selectize="tags" data-value="{$element.TAGS|@json_encode|escape:html}"
          name="tags-{$element.id}[]" multiple style="width:500px;" ></select>
      </td>
    </tr>

    <tr>
      <td><strong>{'Description'|@translate}</strong></td>
      <td><textarea cols="50" rows="5" name="description-{$element.id}" id="description-{$element.id}" class="description">{$element.DESCRIPTION}</textarea></td>
    </tr>

  </table>

</fieldset>
{/foreach}

{if !empty($navbar)}{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}

<p>
  <input type="submit" value="{'Submit'|@translate}" name="submit">
  <input type="reset" value="{'Reset'|@translate}">
</p>
{/if}

</form>