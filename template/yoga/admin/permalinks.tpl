<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{lang:Permalinks}</h2>
</div>

<form method="post">
<fieldset><legend>{lang:Add/delete a permalink}</legend>
  <label>Category:
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
    <input type="submit" class="submit" name="set_permalink" value="{lang:submit}"/>
  </p>
  </fieldset>
</form>

<h3>{lang:Permalinks}</h3>
<table class="table2">
  <tr class="throw">
    <td>Id</td>
    <td>{lang:Category}</td>
    <td>{lang:Permalink}</td>
  </tr>
<!-- BEGIN permalink -->
  <tr>
    <td>{permalink.CAT_ID}</td>
    <td>{permalink.CAT}</td>
    <td>{permalink.PERMALINK}</td>
  </tr>
<!-- END permalink -->
</table>

<h3>{lang:Permalink history}</h3>
<table class="table2">
  <tr class="throw">
    <td>Id</td>
    <td>{lang:Category}</td>
    <td>{lang:Permalink}</td>
    <td>Deleted on</td>
    <td>Last hit</td>
    <td>Hit</td>
    <td></td>
  </tr>
<!-- BEGIN deleted_permalink -->
  <tr>
    <td>{deleted_permalink.cat_id}</td>
    <td>{deleted_permalink.display_name}</td>
    <td>{deleted_permalink.permalink}</td>
    <td>{deleted_permalink.date_deleted}</td>
    <td>{deleted_permalink.last_hit}</td>
    <td>{deleted_permalink.hit}</td>
    <td><a href="{deleted_permalink.U_DELETE}"><img src="{pwg_root}{themeconf:icon_dir}/delete.png" alt="Delete"></a></td>
  </tr>
<!-- END deleted_permalink -->
</table>
