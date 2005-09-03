<table class="doubleSelect">
  <tr>
    <td>
      <h3>{L_CAT_OPTIONS_TRUE}</h3>
      <select class="categoryList" name="cat_true[]" multiple="multiple" size="30">
        <!-- BEGIN category_option_true -->
        <option {category_option_true.SELECTED} value="{category_option_true.VALUE}">{category_option_true.OPTION}</option>
        <!-- END category_option_true -->
      </select>
      <p><input type="submit" value="&raquo;" name="falsify" style="font-size:15px;"/></p>
    </td>

    <td>
      <h3>{L_CAT_OPTIONS_FALSE}</h3>
      <select class="categoryList" name="cat_false[]" multiple="multiple" size="30">
        <!-- BEGIN category_option_false -->
        <option {category_option_false.SELECTED} value="{category_option_false.VALUE}">{category_option_false.OPTION}</option>
        <!-- END category_option_false -->
      </select>
      <p><input type="submit" value="&laquo;" name="trueify" style="font-size:15px;" /></p>
    </td>
  </tr>
</table>
