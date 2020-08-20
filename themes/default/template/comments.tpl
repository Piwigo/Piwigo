{if isset($MENUBAR)}{$MENUBAR}{/if}
<div id="content" class="content{if isset($MENUBAR)} contentWithMenu{/if}">

<div class="titrePage">
	<ul class="categoryActions">
	</ul>
	<h2><a href="{$U_HOME}">{'Home'|@translate}</a>{$LEVEL_SEPARATOR}{'User comments'|@translate}</h2>
</div>

{include file='infos_errors.tpl'}

<form class="filter" action="{$F_ACTION}" method="get">

  <fieldset>
    <legend><span class="icon-filter icon-green"></span>{'Filter'|@translate}</legend>

		<ul>
			<li>
				<label>{'Keyword'|@translate}</label>
			</li>
			<li>
				<input type="text" name="keyword" value="{$F_KEYWORD}">
			</li>
		</ul>

		<ul>
			<li>
				<label>{'Author'|@translate}</label>
			</li>
			<li>
				<input type="text" name="author" value="{$F_AUTHOR}">
			</li>
		</ul>

		<ul>
			<li>
				<label>{'Album'|@translate}</label>
			</li>
			<li>
				<select name="cat">
					<option value="0">------------</option>
					{html_options options=$categories selected=$categories_selected}
				</select>
			</li>
		</ul>

		<ul>
			<li>
				<label>{'Since'|@translate}</label>
			</li>
			<li>
				<select name="since">
					{html_options options=$since_options selected=$since_options_selected}
				</select>
			</li>
		</ul>

  </fieldset>

  <fieldset>

    <legend>{'Display'|@translate}</legend>

		<ul>
			<li>
				<label>{'Sort by'|@translate}</label>
			</li>
			<li>
				<select name="sort_by">
					{html_options options=$sort_by_options selected=$sort_by_options_selected}
				</select>
			</li>
		</ul>

		<ul>
			<li>
				<label>{'Sort order'|@translate}</label>
			</li>
			<li>
				<select name="sort_order">
					{html_options options=$sort_order_options selected=$sort_order_options_selected}
				</select>
			</li>
		</ul>

		<ul>
			<li>
				<label>{'Number of items'|@translate}</label>
			</li>
			<li>
				<select name="items_number">
					{html_options options=$item_number_options selected=$item_number_options_selected}
				</select>
			</li>
		</ul>

  </fieldset>

  <p><input type="submit" value="{'Filter and display'|@translate}"></p>

</form>

{if !empty($navbar) }{include file='navigation_bar.tpl'|@get_extent:'navbar'}{/if}

{if isset($COMMENT_LIST)}
<div id="comments">
	{$COMMENT_LIST}
</div>
{/if}

</div> <!-- content -->

