{include file='infos_errors.tpl'}
<div data-role="content">
  <ul data-role="listview" data-inset="true">
      <li data-role="list-divider">{'Search'|@translate}</li>
  </ul>


<form class="filter" method="post" name="search" action="{$F_SEARCH_ACTION}">
<fieldset data-role="controlgroup">
  <legend>{'Filter'|@translate}</legend>
  <div data-role="fieldcontain">
    <label for="search_allwords">{'Search for words'|@translate}</label>
    <input type="text" id="search_allwords" style="width: 300px" name="search_allwords" size="30">
  </div>
  
  <input type="radio" name="mode" id="mode_and" value="AND" checked="checked">
  <label for="mode_and">{'Search for all terms'|@translate}</label>
  <input type="radio" name="mode" id="mode_or" value="OR">
  <label for="mode_or">{'Search for any term'|@translate}</label>
  <div data-role="fieldcontain">
    <label for="search_author">{'Search for Author'|@translate}</label>
    <input type="text" style="width: 300px" name="search_author" id="search_author" size="30">
  </div>
</fieldset>

{if isset($TAG_SELECTION)}
<fieldset data-role="controlgroup">
  <legend>{'Search tags'|@translate}</legend>
  {$TAG_SELECTION}
</fieldset>

<fieldset data-role="controlgroup">
  <input type="radio" name="tag_mode" id="tag_mode_and" value="AND" checked="checked">
  <label for="tag_mode_and">{'All tags'|@translate}</label>
  <input type="radio" name="tag_mode" id="tag_mode_or" value="OR">
  <label for="tag_mode_or">{'Any tag'|@translate}</label>
</fieldset>
{/if}

<div data-role="fieldcontain">
  <legend>{'Search in albums'|@translate}</legend>
  <label for="categoryList">{'Albums'|@translate}
    <select class="categoryList" id="categoryList" name="cat[]" multiple="multiple" data-native-menu="false">
      {html_options options=$category_options selected=$category_options_selected}
    </select>
  </label>
  <fieldset data-role="controlgroup">
  <legend>{'Search in sub-albums'|@translate}</legend>
    <input type="radio" name="subcats-included" value="1" id="subcats-included-yes" checked="checked">
    <label for="subcats-included-yes">{'Yes'|@translate}</label>
    <input type="radio" name="subcats-included" id="subcats-included-no" value="0">
    <label for="subcats-included-no">{'No'|@translate}</label>
  </fieldset>
</div>
<p>
  <input class="submit" type="submit" name="submit" value="{'Submit'|@translate}">
  <input class="submit" type="reset" value="{'Reset'|@translate}">
</p>
</form>

<script type="text/javascript"><!--
document.search.search_allwords.focus();
//--></script>

</div> <!-- content -->
