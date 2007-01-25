<form method="post" name="profile" action="{F_ACTION}" id="profile" class="properties">

  <fieldset>
    <legend>{lang:register_title}</legend>
    <input type="hidden" name="userid" value="{USERID}" />
    <input type="hidden" name="redirect" value="{REDIRECT}" />
    <ul>
      <li>
        <span class="property">{lang:Username}</span>
        {USERNAME}
      </li>
      <li>
        <span class="property">
          <label for="mail_address">{lang:Email address}</label>
        </span>
        <input type="text" name="mail_address" id="mail_address" value="{EMAIL}">
      </li>
<!-- BEGIN not_admin -->
      <li>
        <span class="property">
          <label for="password">{lang:Password}</label>
        </span>
        <input type="password" name="password" id="password" value="">
      </li>
<!-- END not_admin -->
      <li>
        <span class="property">
          <label for="use_new_pwd">{lang:new_password}</label>
        </span>
        <input type="password" name="use_new_pwd" id="use_new_pwd" value="">
      </li>
      <li>
        <span class="property">
          <label for="passwordConf">{lang:Confirm Password}</label>
        </span>
        <input type="password" name="passwordConf" id="passwordConf" value="">
      </li>
    </ul>
  </fieldset>

  <fieldset>
    <legend>{lang:preferences}</legend>

    <ul>
      <li>
        <span class="property">
          <label for="nb_image_line">{lang:nb_image_per_row}</label>
        </span>
        <input type="text" size="3" maxlength="2" name="nb_image_line" id="nb_image_line" value="{NB_IMAGE_LINE}">
      </li>
      <li>
        <span class="property">
          <label for="nb_line_page">{lang:nb_row_per_page}</label>
        </span>
        <input type="text" size="3" maxlength="2" name="nb_line_page" id="nb_line_page" value="{NB_ROW_PAGE}" >
      </li>
      <li>
        <span class="property">
          <label for="template">{lang:theme}</label>
        </span>
        <select name="template" id="template">
          <!-- BEGIN template_option -->
          <option value="{template_option.VALUE}" {template_option.SELECTED}>{template_option.CONTENT}</option>
          <!-- END template_option -->
        </select>
      </li>
      <li>
        <span class="property">
          <label for="language">{lang:language}</label>
        </span>
        <select name="language" id="language">
          <!-- BEGIN language_option -->
          <option value="{language_option.VALUE}" {language_option.SELECTED}>{language_option.CONTENT}</option>
          <!-- END language_option -->
        </select>
      </li>
      <li>
        <span class="property">
          <label for="recent_period">{lang:recent_period}</label>
        </span>
        <input type="text" size="3" maxlength="2" name="recent_period" id="recent_period" value="{RECENT_PERIOD}">
      </li>
      <li>
        <span class="property">{lang:auto_expand}</span>
        <label><input type="radio" name="expand" value="true" {EXPAND_TREE_YES}> {lang:yes}</label>
        <label><input type="radio" name="expand" value="false" {EXPAND_TREE_NO}> {lang:no}</label>
      </li>
      <li>
        <span class="property">{lang:show_nb_comments}</span>
        <label><input type="radio" name="show_nb_comments" value="true" {NB_COMMENTS_YES}> {lang:yes}</label>
        <label><input type="radio" name="show_nb_comments" value="false" {NB_COMMENTS_NO}> {lang:no}</label>
      </li>
      <li>
        <span class="property">
          <label for="maxwidth">{lang:maxwidth}</label>
        </span>
        <input type="text" size="4" maxlength="4" name="maxwidth" id="maxwidth" value="{MAXWIDTH}">
      </li>
      <li>
        <span class="property">
          <label for="maxheight">{lang:maxheight}</label>
        </span>
        <input type="text" size="4" maxlength="4" name="maxheight" id="maxheight" value="{MAXHEIGHT}">
      </li>
    </ul>
  </fieldset>

  <p class="bottomButtons">
    <input type="submit" name="validate" value="{lang:submit}">
    <input type="reset" name="reset" value="{lang:reset}" class="reset">
  </p>

</form>
