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
        <label><input type="radio" name="date_creation_action-{$element.id}" value="unset"> {'unset'|@translate}</label>
        <label><input type="radio" name="date_creation_action-{$element.id}" value="set" id="date_creation_action_set-{$element.id}"> {'set to'|@translate}</label>

        <select id="date_creation_day-{$element.id}" name="date_creation_day-{$element.id}">
         	<option value="0">--</option>
           {section name=day start=1 loop=32}
             <option value="{$smarty.section.day.index}" {if $smarty.section.day.index==$element.DATE_CREATION_DAY}selected="selected"{/if}>{$smarty.section.day.index}</option>
           {/section}
        </select>
        <select id="date_creation_month-{$element.id}" name="date_creation_month-{$element.id}">
          {html_options options=$month_list selected=$element.DATE_CREATION_MONTH}
        </select>
        <input id="date_creation_year-{$element.id}"
               name="date_creation_year-{$element.id}"
               type="text"
               size="4"
               maxlength="4"
               value="{$element.DATE_CREATION_YEAR}">
        <input id="date_creation_linked_date-{$element.id}" name="date_creation_linked_date-{$element.id}" type="hidden" size="10" disabled="disabled">
        {footer_script}
          pwg_initialization_datepicker("#date_creation_day-{$element.id}", "#date_creation_month-{$element.id}", "#date_creation_year-{$element.id}", "#date_creation_linked_date-{$element.id}", "#date_creation_action_set-{$element.id}");
        {/footer_script}
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

{footer_script}
{literal}$(document).ready(function() {
	$(".elementEdit img")
		.fadeTo("slow", 0.6) // Opacity on page load
		.hover(function(){
			$(this).fadeTo("slow", 1.0); // Opacity on hover
		},function(){
   		$(this).fadeTo("slow", 0.6); // Opacity on mouseout
		});
});{/literal}
{/footer_script}
