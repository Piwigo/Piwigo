{* $Id$ *}

<div class="titrePage">
  <h2>{'Move categories'|@translate}</h2>
</div>

<form method="post" action="{$F_ACTION}" class="filter" id="catMove">
  <fieldset>
    <legend>{'Virtual categories movement'|@translate}</legend>

    <label>
      {'Virtual categories to move'|@translate}

      <select class="categoryList" name="selection[]" multiple="multiple">
        {html_options options=$category_to_move_options}
      </select>
    </label>

    <label>
      {'New parent category'|@translate}

      <select class="categoryDropDown" name="parent">
        <option value="0">------------</option>
        {html_options options=$category_parent_options}
      </select>
    </label>

  </fieldset>

  <p>
    <input class="submit" type="submit" name="submit" value="{'Submit'|@translate}" {$TAG_INPUT_ENABLED}>
    <input class="submit" type="reset" name="reset" value="{'Reset'|@translate}">
  </p>

</form>
