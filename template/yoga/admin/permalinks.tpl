{* $Id$ *}
<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{$U_HELP}" onclick="popuphelp(this.href); return false;" title="{'Help'|@translate}"><img src="{$themeconf.icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{'Permalinks'|@translate}</h2>
</div>

<form method="post" action="{$F_ACTION}">
<fieldset><legend>{'Add/delete a permalink'|@translate}</legend>
  <label>{'Category'|@translate}:
    <select name="cat_id">
      <option value="0">------</option>
      {html_options options=$categories selected=$categories_selected}
    </select>
  </label>

  <label>{'Permalink'|@translate}:
    <input name="permalink" />
  </label>

  <label>{'Save to permalink history'|@translate}:
    <input type="checkbox" name="save" checked="checked" />
  </label>

  <p>
    <input type="submit" class="submit" name="set_permalink" value="{'submit'|@translate}" {$TAG_INPUT_ENABLED}/>
  </p>
  </fieldset>
</form>

<h3>{'Permalinks'|@translate}</h3>
<table class="table2">
  <tr class="throw">
    <td>Id {$SORT_ID}</td>
    <td>{'Category'|@translate} {$SORT_NAME}</td>
    <td>{'Permalink'|@translate} {$SORT_PERMALINK}</td>
  </tr>
{foreach from=$permalinks item=permalink}
  <tr>
    <td>{$permalink.id}</td>
    <td>{$permalink.name}</td>
    <td>{$permalink.permalink}</td>
  </tr>
{/foreach}
</table>

<h3>{'Permalink history'|@translate} <a name="old_permalinks"></a></h3>
<table class="table2">
  <tr class="throw">
    <td>Id {$SORT_OLD_CAT_ID}</td>
    <td>{'Category'|@translate}</td>
    <td>{'Permalink'|@translate} {$SORT_OLD_PERMALINK}</td>
    <td>Deleted on {$SORT_OLD_DATE_DELETED}</td>
    <td>Last hit {$SORT_OLD_LAST_HIT}</td>
    <td>Hit {$SORT_OLD_HIT}</td>
    <td></td>
  </tr>
{foreach from=$deleted_permalinks item=permalink}
  <tr>
    <td>{$permalink.cat_id}</td>
    <td>{$permalink.name}</td>
    <td>{$permalink.permalink}</td>
    <td>{$permalink.date_deleted}</td>
    <td>{$permalink.last_hit}</td>
    <td>{$permalink.hit}</td>
    <td><a href="{$permalink.U_DELETE}" {$TAG_INPUT_ENABLED}><img src="{$ROOT_URL}{$themeconf.icon_dir}/delete.png" alt="Delete"></a></td>
  </tr>
{/foreach}
</table>
