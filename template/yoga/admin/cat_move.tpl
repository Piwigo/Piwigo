<!-- $Id$ -->
<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{lang:Move categories}</h2>
</div>

<form method="post" action="{F_ACTION}" class="filter" id="catMove">
  <fieldset>
    <legend>{lang:Virtual categories movement}</legend>

    <label>
      {lang:Virtual categories to move}

      <select class="categoryList" name="selection[]" multiple="multiple">
        <!-- BEGIN category_option_selection -->
        <option {category_option_selection.SELECTED} value="{category_option_selection.VALUE}">{category_option_selection.OPTION}</option>
        <!-- END category_option_selection -->
      </select>
    </label>

    <label>
      {lang:New parent category}

      <select class="categoryDropDown" name="parent">
        <!-- BEGIN category_option_parent -->
        <option {category_option_parent.SELECTED} value="{category_option_parent.VALUE}">{category_option_parent.OPTION}</option>
        <!-- END category_option_parent -->
      </select>
    </label>

  </fieldset>

  <p>
    <input class="submit" type="submit" name="submit" value="{lang:Submit}" {TAG_INPUT_ENABLED}>
    <input class="submit" type="reset" name="reset" value="{lang:Reset}">
  </p>

</form>
