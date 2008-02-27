<!-- DEV TAG: not smarty migrated -->
<div class="titrePage">
  <h2>{lang:Manage tags}</h2>
</div>

<form action="{F_ACTION}" method="post">

  <!-- BEGIN edit_tags -->
  <fieldset>
    <legend>{lang:Edit tags}</legend>
    <input type="hidden" name="edit_list" value="{edit_tags.LIST}" />
    <table class="table2">
      <tr class="throw">
        <th>{lang:Current name}</th>
        <th>{lang:New name}</th>
      </tr>
      <!-- BEGIN tag -->
      <tr>
        <td>{edit_tags.tag.NAME}</td>
        <td><input type="text" name="tag_name-{edit_tags.tag.ID}" value="{edit_tags.tag.NAME}" /></td>
      </tr>
      <!-- END tag -->
    </table>

    <p>
      <input class="submit" type="submit" name="submit" value="{lang:Submit}" {TAG_INPUT_ENABLED} />
      <input class="submit" type="reset" value="{lang:Reset}" />
    </p>
  </fieldset>
  <!-- END edit_tags -->

  <fieldset>
    <legend>{lang:Add a tag}</legend>

    <label>
      {lang:New tag}
      <input type="text" name="add_tag" />
    </label>
    
    <p><input class="submit" type="submit" name="add" value="{lang:Submit}" {TAG_INPUT_ENABLED}/></p>
  </fieldset>

  <fieldset>
    <legend>{lang:Tag selection}</legend>
    
    {TAG_SELECTION}

    <p>
      <input class="submit" type="submit" name="edit" value="{lang:Edit selected tags}"/>
      <input class="submit" type="submit" name="delete" value="{lang:Delete selected tags}" onclick="return confirm('{lang:Are you sure?}');" {TAG_INPUT_ENABLED}/>
    </p>
  </fieldset>

</form>
