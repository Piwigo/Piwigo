<!-- DEV TAG: not smarty migrated -->
<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{lang:Permalinks}</h2>
</div>

<form method="post" action="{F_ACTION}">
<fieldset><legend>{lang:Add/delete a permalink}</legend>
  <label>{lang:Category}:
    <select name="cat_id">
      <option value="0">------</option>
<!-- BEGIN categories -->
      <option value="{categories.VALUE}" {categories.SELECTED}>{categories.OPTION}</option>
<!-- END categories -->
    </select>
  </label>

  <label>{lang:Permalink}:
    <input name="permalink" />
  </label>

  <label>{lang:Save to permalink history}:
    <input type="checkbox" name="save" checked="checked" />
  </label>

  <p>
    <input type="submit" class="submit" name="set_permalink" value="{lang:submit}" {TAG_INPUT_ENABLED}/>
  </p>
  </fieldset>
</form>

<h3>{lang:Permalinks}</h3>
<table class="table2">
  <tr class="throw">
    <td>Id {SORT_ID}</td>
    <td>{lang:Category} {SORT_NAME}</td>
    <td>{lang:Permalink} {SORT_PERMALINK}</td>
  </tr>
<!-- BEGIN permalink -->
  <tr>
    <td>{permalink.id}</td>
    <td>{permalink.name}</td>
    <td>{permalink.permalink}</td>
  </tr>
<!-- END permalink -->
</table>

<h3>{lang:Permalink history} <a name="old_permalinks"></a></h3>
<table class="table2">
  <tr class="throw">
    <td>Id {SORT_OLD_CAT_ID}</td>
    <td>{lang:Category}</td>
    <td>{lang:Permalink} {SORT_OLD_PERMALINK}</td>
    <td>Deleted on {SORT_OLD_DATE_DELETED}</td>
    <td>Last hit {SORT_OLD_LAST_HIT}</td>
    <td>Hit {SORT_OLD_HIT}</td>
    <td></td>
  </tr>
<!-- BEGIN deleted_permalink -->
  <tr>
    <td>{deleted_permalink.cat_id}</td>
    <td>{deleted_permalink.name}</td>
    <td>{deleted_permalink.permalink}</td>
    <td>{deleted_permalink.date_deleted}</td>
    <td>{deleted_permalink.last_hit}</td>
    <td>{deleted_permalink.hit}</td>
    <td><a href="{deleted_permalink.U_DELETE}" {TAG_INPUT_ENABLED}><img src="{pwg_root}{themeconf:icon_dir}/delete.png" alt="Delete"></a></td>
  </tr>
<!-- END deleted_permalink -->
</table>
