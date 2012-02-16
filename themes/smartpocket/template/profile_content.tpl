
<form method="post" name="profile" action="{$F_ACTION}" id="profile" class="properties">

  <div data-role="fieldcontain">
  <label for="username">{'Username'|@translate}</label>
  <input type="text" name="username" id="username" value="{$USERNAME}" disabled="disabled">
  </div>

  {if not $SPECIAL_USER} {* can modify password + email*}
  <div data-role="fieldcontain">
  <label for="mail_address">{'Email address'|@translate}</label>
  <input type="text" name="mail_address" id="mail_address" value="{$EMAIL}">
  </div>

  <div data-role="fieldcontain">
  <label for="password">{'Password'|@translate}</label>
  <input type="password" name="password" id="password" value="">
  </div>

  <div data-role="fieldcontain">
  <label for="use_new_pwd">{'New password'|@translate}</label>
  <input type="password" name="use_new_pwd" id="use_new_pwd" value="">
  </div>

  <div data-role="fieldcontain">
  <label for="passwordConf">{'Confirm Password'|@translate}</label>
  <input type="password" name="passwordConf" id="passwordConf" value="">
  </div>
  {/if}

{if $ALLOW_USER_CUSTOMIZATION}
  <div data-role="fieldcontain">
  <label for="language">{'Language'|@translate}</label>
  {html_options name=language options=$language_options selected=$language_selection}
  </div>
{/if}


  <div data-role="fieldcontain">
    {if $ALLOW_USER_CUSTOMIZATION}
    <input type="hidden" name="nb_image_page" value="{$NB_IMAGE_PAGE}">
    <input type="hidden" name="theme" value="{$template_selection}">
    <input type="hidden" name="recent_period" value="{$RECENT_PERIOD}">
    <input type="hidden" name="expand" value="{$EXPAND}">
    <input type="hidden" name="show_nb_comments" value="{$NB_COMMENTS}">
    <input type="hidden" name="show_nb_hits" value="{$NB_HITS}">
    {/if}
    <input type="hidden" name="redirect" value="{$REDIRECT}">
    <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
    <input class="submit" type="submit" name="validate" value="{'Submit'|@translate}">
    <input class="submit" type="reset" name="reset" value="{'Reset'|@translate}">
    {if $ALLOW_USER_CUSTOMIZATION}
    <input class="submit" type="submit" name="reset_to_default" value="{'Reset to default values'|@translate}">
    {/if}
  </div>

</form>

