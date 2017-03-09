{footer_script require='jquery.ui.sortable'}{literal}
jQuery(document).ready(function(){
  jQuery("#addPermalinkOpen").click(function(){
    jQuery("#addPermalink").show();
    jQuery("#showAddPermalink").hide();
  });

  jQuery("#addPermalinkClose").click(function(){
    jQuery("#addPermalink").hide();
    jQuery("#showAddPermalink").show();
  });
});
{/literal}{/footer_script}

{literal}
<style>
#showAddPermalink {text-align:left;margin-left:1em;margin-top:0;}
form fieldset p {margin:0 0 1em 0;}
form fieldset p.actionButtons {margin-bottom:0}
</style>
{/literal}

{html_style}
[name="permalink"] {
  width : 100%;
  max-width : 600px;
}
{/html_style}

<div class="titrePage">
  <h2>{'Permalinks'|@translate}</h2>
</div>

<p id="showAddPermalink"><a href="#" id="addPermalinkOpen">{'Add/delete a permalink'|@translate}</a></p>

<form method="post" action="" id="addPermalink" style="display:none">
<fieldset>
  <legend>{'Add/delete a permalink'|@translate}</legend>
  <p>
    <strong>{'Album'|@translate}</strong>
    <br>
    <select name="cat_id">
      <option value="0">------</option>
      {html_options options=$categories selected=$categories_selected}
    </select>
  </p>

  <p>
    <strong>{'Permalink'|@translate}</strong>
    <br><input name="permalink">
  </p>

  <p>
    <label><input type="checkbox" name="save" checked="checked"> <strong>{'Save to permalink history'|@translate}</strong></label>
  </p>

  <p class="actionButtons">
    <input type="submit" class="submit" name="set_permalink" value="{'Submit'|@translate}">
    <a href="#" id="addPermalinkClose">{'Cancel'|@translate}</a>
  </p>
</fieldset>
</form>

<fieldset>
  <legend>{'Permalinks'|@translate}</legend>
<table class="table2" style="margin:0">
	<tr class="throw">
		<td>Id {$SORT_ID}</td>
		<td>{'Album'|@translate} {$SORT_NAME}</td>
		<td>{'Permalink'|@translate} {$SORT_PERMALINK}</td>
	</tr>
{foreach from=$permalinks item=permalink name="permalink_loop"}
	<tr class="{if $smarty.foreach.permalink_loop.index is odd}row1{else}row2{/if}" style="line-height:1.5em;">
		<td style="text-align:center;">{$permalink.id}</td>
		<td>{$permalink.name}</td>
		<td>{$permalink.permalink}</td>
	</tr>
{/foreach}
</table>
</fieldset>

<fieldset>
  <legend>{'Permalink history'|@translate} <a name="old_permalinks"></a></legend>
<table class="table2" style="margin:0">
	<tr class="throw">
		<td>Id {$SORT_OLD_CAT_ID}</td>
		<td>{'Album'|@translate}</td>
		<td>{'Permalink'|@translate} {$SORT_OLD_PERMALINK}</td>
		<td>{'Deleted on'|@translate} {$SORT_OLD_DATE_DELETED}</td>
		<td>{'Last hit'|@translate} {$SORT_OLD_LAST_HIT}</td>
		<td>{'Hit'|@translate} {$SORT_OLD_HIT}</td>
		<td style="width:5px;"></td>
	</tr>
{foreach from=$deleted_permalinks item=permalink}
	<tr style="line-height:1.5em;">
		<td style="text-align:center;">{$permalink.cat_id}</td>
		<td>{$permalink.name}</td>
		<td>{$permalink.permalink}</td>
		<td>{$permalink.date_deleted}</td>
		<td>{$permalink.last_hit}</td>
		<td>{$permalink.hit}</td>
		<td><a href="{$permalink.U_DELETE}"><img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/delete.png" alt="[{'Delete'|@translate}]"></a></td>
	</tr>
{/foreach}
</table>
</fieldset>
