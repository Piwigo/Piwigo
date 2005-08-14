<div class="admin">{L_CAT_TITLE}</div>

<form method="post" action="{F_ACTION}">
  <input type="hidden" name="{HIDDEN_NAME}" value="{HIDDEN_VALUE}" />

  <table class="doubleSelect">
    <tr>
      <td>
        <h3>{L_CAT_OPTIONS_TRUE}</h3>
        <select class="categoryList" name="cat_true[]" multiple="multiple" size="30">
          <!-- BEGIN category_option_true -->
          <option class="{category_option_true.CLASS}" {category_option_true.SELECTED} value="{category_option_true.VALUE}">{category_option_true.OPTION}</option>
          <!-- END category_option_true -->
        </select>
        <p><input type="submit" value="&raquo;" name="falsify" style="font-size:15px;"/></p>
      </td>

      <td>
        <h3>{L_CAT_OPTIONS_FALSE}</h3>
        <select class="categoryList" name="cat_false[]" multiple="multiple" size="30">
          <!-- BEGIN category_option_false -->
          <option class="{category_option_false.CLASS}" {category_option_false.SELECTED} value="{category_option_false.VALUE}">{category_option_false.OPTION}</option>
          <!-- END category_option_false -->
        </select>
        <p><input type="submit" value="&laquo;" name="trueify" style="font-size:15px;" /></p>
      </td>
    </tr>
  </table>
</form>

<div class="information">{L_CAT_OPTIONS_INFO}</div>

