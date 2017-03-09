{combine_script id='jquery.selectize' load='footer' path='themes/default/js/plugins/selectize.min.js'}
{combine_css id='jquery.selectize' path="themes/default/js/plugins/selectize.{$themeconf.colorscheme}.css"}

{footer_script}
jQuery(document).ready(function() {
  jQuery("#authors, #tags, #categories").each(function() {
    jQuery(this).selectize({
      plugins: ['remove_button'],
      maxOptions:jQuery(this).find("option").length
    });
  })
});
{/footer_script}

{html_style}
.ui-checkbox, .ui-radio, .ui-btn-text {
  z-index:0;
}

.form-actions {
  margin-top:3em;
  margin-bottom:3em;
}
{/html_style}

{include file='infos_errors.tpl'}
<div data-role="content">
  <ul data-role="listview" data-inset="true">
      <li data-role="list-divider">{'Search'|@translate}</li>
  </ul>


<form class="filter" method="post" name="search" action="{$F_SEARCH_ACTION}">
  <fieldset data-role="controlgroup">
    <legend>{'Search for words'|@translate}</legend>

    <input type="text" name="search_allwords">

    <input type="radio" name="mode" id="mode_and" value="AND" checked="checked">
    <label for="mode_and">{'Search for all terms'|@translate}</label>

    <input type="radio" name="mode" id="mode_or" value="OR">
    <label for="mode_or">{'Search for any term'|@translate}</label>
  </fieldset>

  <fieldset data-role="controlgroup">
  <legend>{'Apply on properties'|translate}</legend>

    <input type="checkbox" name="fields[]" value="name" checked="checked" id="field-name">
    <label for="field-name">{'Photo title'|translate}</label>

    <input type="checkbox" name="fields[]" value="comment" checked="checked" id="field-comment">
    <label for="field-comment">{'Photo description'|translate}</label>

    <input type="checkbox" name="fields[]" value="file" checked="checked" id="field-file">
    <label for="field-file">{'File name'|translate}</label>

{if isset($TAGS)}
    <input type="checkbox" name="search_in_tags" value="tags" id="field-tags">
    <label for="field-tags">{'Tags'|translate}</label>
{/if}
  </fieldset>

{if count($AUTHORS)>=1}
<fieldset  data-role="controlgroup">
  <legend>{'Search for Author'|@translate}</legend>
  <select id="authors" placeholder="{'Type in a search term'|translate}" name="authors[]" multiple>
{foreach from=$AUTHORS item=author}
    <option value="{$author.author|strip_tags:false|escape:html}">{$author.author|strip_tags:false} ({$author.counter|translate_dec:'%d photo':'%d photos'})</option>
{/foreach}
  </select>
</fieldset>
{/if}

{if isset($TAGS)}
<fieldset data-role="controlgroup">
  <legend>{'Search tags'|@translate}</legend>
  <select id="tags" placeholder="{'Type in a search term'|translate}" name="tags[]" multiple>
{foreach from=$TAGS item=tag}
    <option value="{$tag.id}">{$tag.name} ({$tag.counter|translate_dec:'%d photo':'%d photos'})</option>
{/foreach}
  </select>
  <input type="radio" name="tag_mode" id="tag_mode_and" value="AND" checked="checked">
  <label for="tag_mode_and">{'All tags'|@translate}</label>

  <input type="radio" name="tag_mode" id="tag_mode_or" value="OR">
  <label for="tag_mode_or">{'Any tag'|@translate}</label>
</fieldset>
{/if}

<fieldset data-role="controlgroup">
  <legend>{'Search in albums'|@translate}</legend>
  <select id="categories" placeholder="{'Type in a search term'|translate}" name="cat[]" multiple>
    {html_options options=$category_options selected=$category_options_selected}
  </select>

  <input type="checkbox" name="subcats-included" value="1" checked="checked" id="subcats-included">
  <label for="subcats-included">{'Search in sub-albums'|@translate}</label>
</fieldset>


  <div class="form-actions">
    <input class="submit" type="submit" name="submit" value="{'Submit'|@translate}">
  </div>

</form>

<script type="text/javascript"><!--
document.search.search_allwords.focus();
//--></script>

</div> <!-- content -->
