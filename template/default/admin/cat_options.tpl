<p class="confMenu">
  <a class="{UPLOAD_CLASS}" href="{U_UPLOAD}">{L_CAT_OPTIONS_MENU_UPLOAD}</a>
  <a class="{COMMENTS_CLASS}" href="{U_COMMENTS}">{L_CAT_OPTIONS_MENU_COMMENTS}</a>
  <a class="{VISIBLE_CLASS}" href="{U_VISIBLE}">{L_CAT_OPTIONS_MENU_VISIBLE}</a>
  <a class="{STATUS_CLASS}" href="{U_STATUS}">{L_CAT_OPTIONS_MENU_STATUS}</a>
</p>

<form action="{F_ACTION}" method="post">

  <select style="width:500px" multiple="multiple" name="cat[]" size="20">
    <!-- BEGIN category_option -->
    <option class="{category_option.CLASS}" {category_option.SELECTED} value="{category_option.VALUE}">{category_option.OPTION}</option>
    <!-- END category_option -->
  </select>

  <!-- BEGIN upload -->
  <p>{L_CAT_OPTIONS_UPLOAD_INFO}</p>
  <p>
    <input type="radio" name="option" value="true"/> <span class="optionTrue">{L_CAT_OPTIONS_UPLOAD_TRUE}</span>
  </p>
  <p> 
    <input type="radio" name="option" value="false"/> <span class="optionFalse">{L_CAT_OPTIONS_UPLOAD_FALSE}</span>
  </p>
  <!-- END upload -->

  <!-- BEGIN comments -->
  <p>{L_CAT_OPTIONS_COMMENTS_INFO}</p>
  <p>
    <input type="radio" name="option" value="true"/> <span class="optionTrue">{L_CAT_OPTIONS_COMMENTS_TRUE}</span>
  </p>
  <p>
    <input type="radio" name="option" value="false"/> <span class="optionFalse">{L_CAT_OPTIONS_COMMENTS_FALSE}</span>
  </p>
  <!-- END comments -->

  <!-- BEGIN visible -->
  <p>{L_CAT_OPTIONS_VISIBLE_INFO}</p>
  <p>
    <input type="radio" name="option" value="true"/> <span class="optionTrue">{L_CAT_OPTIONS_VISIBLE_TRUE}</span>
  </p>
  <p>
    <input type="radio" name="option" value="false"/> <span class="optionFalse">{L_CAT_OPTIONS_VISIBLE_FALSE}</span>
  </p>
  <!-- END visible -->

  <!-- BEGIN status -->
  <p>{L_CAT_OPTIONS_STATUS_INFO}</p>
  <p>
    <input type="radio" name="option" value="true"/> <span class="optionTrue">{L_CAT_OPTIONS_STATUS_TRUE}</span>
  </p>
  <p>
    <input type="radio" name="option" value="false"/> <span class="optionFalse">{L_CAT_OPTIONS_STATUS_FALSE}</span>
  </p>
  <!-- END status -->

  <p style="text-align:center;">
    <input type="submit" value="{L_SUBMIT}" name="submit" class="bouton" />
    <input type="reset" name="reset" value="{L_RESET}" class="bouton" />
  </p>

</form>
