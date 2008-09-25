{* $Id$ *}
<table class="doubleSelect">
  <tr>
    <td>
      <h3>{$L_CAT_OPTIONS_TRUE}</h3>
      <div class="hscroll">
        <select class="categoryList" name="cat_true[]" multiple="multiple" size="30">
          {html_options options=$category_option_true selected=$category_option_true_selected}
        </select>
      </div>
      <p><input class="submit" type="submit" value="&raquo;" name="falsify" style="font-size:15px;" {$TAG_INPUT_ENABLED}/></p>
    </td>

    <td>
      <div class="right">
        <h3>{$L_CAT_OPTIONS_FALSE}</h3>
        <div class="hscroll">
          <select class="categoryList" name="cat_false[]" multiple="multiple" size="30">
            {html_options options=$category_option_false selected=$category_option_false_selected}
          </select>
        </div>
        <p><input class="submit" type="submit" value="&laquo;" name="trueify" style="font-size:15px;" {$TAG_INPUT_ENABLED}/></p>
      </div>
      </td>
  </tr>
</table>