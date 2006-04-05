<div id="content">

<!-- BEGIN errors -->
<div class="errors">
  <ul>
    <!-- BEGIN error -->
    <li>{errors.error.ERROR}</li>
    <!-- END error -->
  </ul>
</div>
<!-- END errors -->

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{U_RETURN}" title="{lang:return to homepage}"><img src="{themeconf:icon_dir}/home.png" class="button" alt="{lang:home}"/></a></li>
    </ul>
    <h2>{lang:Profile}</h2>
  </div>

<form method="post" name="profile" action="{F_ACTION}" id="profile" class="properties">

  <input type="hidden" name="userid" value="{USERID}" />

  <fieldset>
    <legend>{L_REGISTRATION_INFO}</legend>

    <ul>
      <li>
        <span class="property">{lang:Username}</span>
        {USERNAME}
      </li>

      <li>
        <span class="property">
          <label for="mail_address">{lang:Email address}</label>
        </span>
        <input type="text" name="mail_address" id="mail_address" value="{EMAIL}" onfocus="this.className='focus';" onblur="this.className='nofocus';">
      </li>

      <li>
        <span class="property">
          <label for="password">{L_CURRENT_PASSWORD}</label>
        </span>
        <input type="password" name="password" id="password" value="" onfocus="this.className='focus';" onblur="this.className='nofocus';">
      </li>

      <li>
        <span class="property">
          <label for="use_new_pwd">{L_NEW_PASSWORD}</label>
        </span>
        <input type="password" name="use_new_pwd" id="use_new_pwd" value="" onfocus="this.className='focus';" onblur="this.className='nofocus';">
      </li>

      <li>
        <span class="property">
          <label for="passwordConf">{L_CONFIRM_PASSWORD}</label>
        </span>
        <input type="password" name="passwordConf" id="passwordConf" value="" onfocus="this.className='focus';" onblur="this.className='nofocus';">
      </li>
    </ul>
  </fieldset>

  <fieldset>
    <legend>{L_PREFERENCES}</legend>

    <ul>
      <li>
        <span class="property">
          <label for="nb_image_line">{L_NB_IMAGE_LINE}</label>
        </span>
        <input type="text" size="3" maxlength="2" name="nb_image_line" id="nb_image_line" value="{NB_IMAGE_LINE}"
               onfocus="this.className='focus';" onblur="this.className='nofocus';">
      </li>

      <li>
        <span class="property">
          <label for="nb_line_page">{L_NB_ROW_PAGE}</label>
        </span>
        <input type="text" size="3" maxlength="2" name="nb_line_page" id="nb_line_page" value="{NB_ROW_PAGE}"
               onfocus="this.className='focus';" onblur="this.className='nofocus';">
      </li>

      <li>
        <span class="property">
          <label for="template">{L_STYLE_SELECT}</label>
        </span>
        <select name="template" id="template" onfocus="this.className='focus';" onblur="this.className='nofocus';">
          <!-- BEGIN template_option -->
          <option value="{template_option.VALUE}" {template_option.SELECTED}>{template_option.CONTENT}</option>
          <!-- END template_option -->
        </select>
      </li>

      <li>
        <span class="property">
          <label for="language">{L_LANG_SELECT}</label>
        </span>
        <select name="language" id="language" onfocus="this.className='focus';" onblur="this.className='nofocus';">
          <!-- BEGIN language_option -->
          <option value="{language_option.VALUE}" {language_option.SELECTED}>{language_option.CONTENT}</option>
          <!-- END language_option -->
        </select>
      </li>

      <li>
        <span class="property">
          <label for="recent_period">{L_RECENT_PERIOD}</label>
        </span>
        <input type="text" size="3" maxlength="2" name="recent_period" id="recent_period" value="{RECENT_PERIOD}"
               onfocus="this.className='focus';" onblur="this.className='nofocus';">
      </li>

      <li>
        <span class="property">{L_EXPAND_TREE}</span>
        <label><input type="radio" class="radio" name="expand" value="true" {EXPAND_TREE_YES}> {L_YES}</label>
        <label><input type="radio" class="radio" name="expand" value="false" {EXPAND_TREE_NO}> {L_NO}</label>
      </li>

      <li>
        <span class="property">{L_NB_COMMENTS}</span>
        <label><input type="radio" class="radio" name="show_nb_comments" value="true" {NB_COMMENTS_YES}> {L_YES}</label>
        <label><input type="radio" class="radio" name="show_nb_comments" value="false" {NB_COMMENTS_NO}> {L_NO}</label>
      </li>
 
      <li>
        <span class="property">
          <label for="maxwidth">{L_MAXWIDTH}</label>
        </span>
        <input type="text" size="4" maxlength="4" name="maxwidth" id="maxwidth" value="{MAXWIDTH}"
               onfocus="this.className='focus';" onblur="this.className='nofocus';">
      </li>

      <li>
        <span class="property">
          <label for="maxheight">{L_MAXHEIGHT}</label>
        </span>
        <input type="text" size="4" maxlength="4" name="maxheight" id="maxheight" value="{MAXHEIGHT}"
               onfocus="this.className='focus';" onblur="this.className='nofocus';">
      </li>
    </ul>
  </fieldset>

  <p class="bottomButtons">
    <input type="submit" name="validate" value="{L_SUBMIT}">
    <input type="reset" name="reset" value="{L_RESET}">
  </p>

</form>

</div> <!-- content -->
