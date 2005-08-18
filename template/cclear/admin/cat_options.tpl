<div class="admin">{L_CAT_TITLE}</div>
<form name="form1" method="post" action="{F_ACTION}" style="text-align:center;width:800px;">
<div style="clear:both;"></div>
<div style="height:auto;">
  <div style="float:left;padding:10px;width:300px;">
  <span class="titreMenu">{L_CAT_OPTIONS_TRUE}</span><br />
  <select style="height:auto;width:280px" name="cat_true[]" multiple="multiple" size="10">
    <!-- BEGIN category_option_true -->
    <option class="{category_option_true.CLASS}" {category_option_true.SELECTED} value="{category_option_true.VALUE}">{category_option_true.OPTION}</option>
    <!-- END category_option_true -->
  </select>
  </div>
  <div style="float:left;padding-top:80px;padding-bottom:80px;text-align:center;width:160px;" >
    <input type="submit" value="&laquo;" name="trueify" style="font-size:15px;" class="bouton" /><br/>
    <input type="submit" value="&raquo;" name="falsify" style="font-size:15px;" class="bouton" />
  </div>
  <div style="float:right;padding:10px;width:300px;">
   <span class="titreMenu">{L_CAT_OPTIONS_FALSE}</span><br />
  <select style="width:280px" name="cat_false[]" multiple="multiple" size="10">
    <!-- BEGIN category_option_false -->
    <option class="{category_option_false.CLASS}" {category_option_false.SELECTED} value="{category_option_false.VALUE}">{category_option_false.OPTION}</option>
    <!-- END category_option_false -->
  </select>
  </div>
</div>
<div style="clear:both;"></div>
<input type="hidden" name="{HIDDEN_NAME}" value="{HIDDEN_VALUE}" />
<input type="reset" name="reset" value="{L_RESET}" class="bouton" />
</form>
<div class="information">{L_CAT_OPTIONS_INFO}</div>

