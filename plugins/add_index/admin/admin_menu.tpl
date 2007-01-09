<!-- $Id: admin_menu.tpl Ruben ARNAUD -->
<div class="titrePage">
  <h2>{lang:Add_Index}</h2>
</div>

<p>{lang:add_index_Description_1}</p>
<p>{lang:add_index_Description_2}</p>

<form method="post" name="admin_menu" id="admin_menu" action="{F_ACTION}">
  <fieldset>
    <legend>{lang:add_index_Parameters}</legend>
    <table>
      <tr>
        <td>
          <label for="filename">{lang:add_index_filename}</label>
        </td>
        <td><input type="text" maxlength="35" size="35" name="add_index_filename" id="filename" value="{filename}"/></td>
      </tr>
      <tr>
        <td>
          <label for="source_directory_path">{lang:add_index_source_directory_path}</label>
        </td>
        <td><input type="text" maxlength="35" size="35" name="add_index_source_directory_path" id="source_directory_path" value="{source_directory_path}"/></td>
      </tr>
    </table>
  </fieldset>

  <p>
    <!--<input type="submit" value="{lang:Submit}" name="param_submit" {TAG_INPUT_ENABLED}/>-->
    <input type="reset" value="{lang:Reset}" name="param_reset"/>
  </p>

</form>
