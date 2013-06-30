{include file='infos_errors.tpl'}
<div data-role="content">
{**{if isset($MENUBAR)}{$MENUBAR}{/if}
<div id="content" class="content{if isset($MENUBAR)} contentWithMenu{/if}">

<div class="titrePage">
	<ul class="categoryActions">
	</ul>
	<h2><a href="{$U_HOME}">{'Home'|@translate}</a>{$LEVEL_SEPARATOR}{'User comments'|@translate}</h2>
</div>

{include file='infos_errors.tpl'}

<form class="filter" action="{$F_ACTION}" method="get">

  <fieldset>
    <legend>{'Filter'|@translate}</legend>

    <label>{'Keyword'|@translate}<input type="text" name="keyword" value="{$F_KEYWORD}"></label>

    <label>{'Author'|@translate}<input type="text" name="author" value="{$F_AUTHOR}"></label>

    <label>
      {'Album'|@translate}
      <select name="cat">
        <option value="0">------------</option>
        {html_options options=$categories selected=$categories_selected}
      </select>
    </label>

    <label>
      {'Since'|@translate}
      <select name="since">
        {html_options options=$since_options selected=$since_options_selected}
      </select>
    </label>

  </fieldset>

  <fieldset>

    <legend>{'Display'|@translate}</legend>

    <label>
      {'Sort by'|@translate}
      <select name="sort_by">
        {html_options options=$sort_by_options selected=$sort_by_options_selected}
      </select>
    </label>

    <label>
      {'Sort order'|@translate}
      <select name="sort_order">
        {html_options options=$sort_order_options selected=$sort_order_options_selected}
      </select>
    </label>

    <label>
      {'Number of items'|@translate}
      <select name="items_number">
        {html_options options=$item_number_options selected=$item_number_options_selected}
      </select>
    </label>

  </fieldset>

  <p><input type="submit" value="{'Filter and display'|@translate}"></p>

</form>

{if !empty($navbar) }{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}**}

{if isset($comments)}
	{include file='comment_list.tpl' comment_derivative_params=$derivative_params}
{/if}

</div> <!-- content -->

