<form method="post" name="profile" action="{$F_ACTION}" id="profile" class="properties">

  <fieldset>
    <legend>{'Registration'|@translate}</legend>
    <input type="hidden" name="redirect" value="{$REDIRECT}">
    <ul>
      <li>
        <span class="property">{'Username'|@translate}</span>
        {$USERNAME}
      </li>
{if not $SPECIAL_USER} {* can modify password + email*}
      <li>
        <span class="property">
          <label for="mail_address">{'Email address'|@translate}</label>
        </span>
        <input type="text" name="mail_address" id="mail_address" value="{$EMAIL}">
      </li>
{if not $IN_ADMIN} {* admins do not need old password*}
      <li>
        <span class="property">
          <label for="password">{'Password'|@translate}</label>
        </span>
        <input type="password" name="password" id="password" value="">
      </li>
{/if}
      <li>
        <span class="property">
          <label for="use_new_pwd">{'New password'|@translate}</label>
        </span>
        <input type="password" name="use_new_pwd" id="use_new_pwd" value="">
      </li>
      <li>
        <span class="property">
          <label for="passwordConf">{'Confirm Password'|@translate}</label>
        </span>
        <input type="password" name="passwordConf" id="passwordConf" value="">
      </li>
{/if}
    </ul>
  </fieldset>

  <fieldset>
    <legend>{'Preferences'|@translate}</legend>

    <ul>
      <li>
        <span class="property">
          <label for="nb_image_line">{'Number of images per row'|@translate}</label>
        </span>
        <input type="text" size="3" maxlength="2" name="nb_image_line" id="nb_image_line" value="{$NB_IMAGE_LINE}">
      </li>
      <li>
        <span class="property">
          <label for="nb_line_page">{'Number of rows per page'|@translate}</label>
        </span>
        <input type="text" size="3" maxlength="2" name="nb_line_page" id="nb_line_page" value="{$NB_ROW_PAGE}" >
      </li>
      <li>
        <span class="property">
          <label for="template">{'Interface theme'|@translate}</label>
        </span>
        {html_options id=template name=theme options=$template_options selected=$template_selection}
      </li>
      <li>
        <span class="property">
          <label for="language">{'Language'|@translate}</label>
        </span>
        {html_options id=language name=language options=$language_options selected=$language_selection}
      </li>
      <li>
        <span class="property">
          <label for="recent_period">{'Recent period'|@translate}</label>
        </span>
        <input type="text" size="3" maxlength="2" name="recent_period" id="recent_period" value="{$RECENT_PERIOD}">
      </li>
      <li>
        <span class="property">{'Expand all categories'|@translate}</span>
        {html_radios name='expand' options=$radio_options selected=$EXPAND}
      </li>
      <li>
        <span class="property">{'Show number of comments'|@translate}</span>
        {html_radios name='show_nb_comments' options=$radio_options selected=$NB_COMMENTS}
      </li>
      <li>
        <span class="property">{'Show number of hits'|@translate}</span>
        {html_radios name='show_nb_hits' options=$radio_options selected=$NB_HITS}
      </li>
      <li>
        <span class="property">
          <label for="maxwidth">{'Maximum width of the pictures'|@translate}</label>
        </span>
        <input type="text" size="4" maxlength="4" name="maxwidth" id="maxwidth" value="{$MAXWIDTH}">
      </li>
      <li>
        <span class="property">
          <label for="maxheight">{'Maximum height of the pictures'|@translate}</label>
        </span>
        <input type="text" size="4" maxlength="4" name="maxheight" id="maxheight" value="{$MAXHEIGHT}">
      </li>
    </ul>
  </fieldset>

  <p class="bottomButtons">
    <input class="submit" type="submit" name="validate" value="{'Submit'|@translate}">
    <input class="submit" type="reset" name="reset" value="{'Reset'|@translate}">
  </p>

</form>
