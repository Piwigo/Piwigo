<div class="titrePage">
  <h2>{'Move albums'|@translate}</h2>
</div>

<form method="post" action="{$F_ACTION}" class="filter" id="catMove">
  <fieldset>
    <legend>{'Move albums'|@translate}</legend>

    <label>
      {'Virtual albums to move'|@translate}

      <select class="categoryList" name="selection[]" multiple="multiple">
        {html_options options=$category_to_move_options}
      </select>
    </label>

    <label>
      {'New parent album'|@translate}

      <select class="categoryDropDown" name="parent">
        <option value="0">------------</option>
        {html_options options=$category_parent_options}
      </select>
    </label>

  </fieldset>

  <p style="text-align:left;">
    <input class="submit" type="submit" name="submit" value="{'Submit'|@translate}">
  </p>

</form>
