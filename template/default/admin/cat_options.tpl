<table width="100%" align="center">
  <tr class="admin">
    <th colspan="2">{L_TITLE}</th>
  </tr>
</table>

<form action="{F_ACTION}" method="post">

  <table>
    <tr>
      <td style="text-align:center;">{L_CAT_OPTIONS_TRUE}</td>
      <td></td>
      <td style="text-align:center;">{L_CAT_OPTIONS_FALSE}</td>
    </tr>
    <tr>
      <td>
  <select style="width:300px" multiple="multiple" name="cat_true[]" size="20">
    <!-- BEGIN category_option_true -->
    <option class="{category_option_true.CLASS}" {category_option_true.SELECTED} value="{category_option_true.VALUE}">{category_option_true.OPTION}</option>
    <!-- END category_option_true -->
  </select>
      </td>
      <td valign="middle">
  <input type="submit" value="&rarr;" name="falsify" style="font-size:15px;" class="bouton" />
  <br />
  <input type="submit" value="&larr;" name="trueify" style="font-size:15px;" class="bouton" />
      </td>
      <td>
  <select style="width:300px" multiple="multiple" name="cat_false[]" size="20">
    <!-- BEGIN category_option_false -->
    <option class="{category_option_false.CLASS}" {category_option_false.SELECTED} value="{category_option_false.VALUE}">{category_option_false.OPTION}</option>
    <!-- END category_option_false -->
  </select>
      </td>
    </tr>
  </table>

  <p>{L_CAT_OPTIONS_INFO}</p>

  <p style="text-align:center;">
    <input type="reset" name="reset" value="{L_RESET}" class="bouton" />
  </p>

</form>
