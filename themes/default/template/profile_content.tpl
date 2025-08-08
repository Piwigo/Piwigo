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
      <li>
        <span class="property">
          <label for="password">{'Password'|@translate}</label>
        </span>
        <input type="password" name="password" id="password" value="">
      </li>
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

{if $ALLOW_USER_CUSTOMIZATION}
  <fieldset>
    <legend>{'Preferences'|@translate}</legend>

    <ul>
      <li>
        <span class="property">
          <label for="nb_image_page">{'Number of photos per page'|@translate}</label>
        </span>
        <input type="text" size="4" maxlength="3" name="nb_image_page" id="nb_image_page" value="{$NB_IMAGE_PAGE}">
      </li>
      <li>
        <span class="property">
          <label for="template">{'Theme'|@translate}</label>
        </span>
        {html_options name=theme options=$template_options selected=$template_selection}
      </li>
      <li>
        <span class="property">
          <label for="Language">{'Language'|@translate}</label>
        </span>
        {html_options name=language options=$language_options selected=$language_selection}
      </li>
      <li>
        <span class="property">
          <label for="Recent period">{'Recent period'|@translate}</label>
        </span>
        <input type="text" size="3" maxlength="2" name="recent_period" id="recent_period" value="{$RECENT_PERIOD}">
      </li>
      <li>
        <span class="property">{'Expand all albums'|@translate}</span>
        {html_radios name='expand' options=$radio_options selected=$EXPAND}
      </li>
    {if $ACTIVATE_COMMENTS}
      <li>
        <span class="property">{'Show number of comments'|@translate}</span>
        {html_radios name='show_nb_comments' options=$radio_options selected=$NB_COMMENTS}
      </li>
    {/if}
      <li>
        <span class="property">{'Show number of hits'|@translate}</span>
        {html_radios name='show_nb_hits' options=$radio_options selected=$NB_HITS}
      </li>
    </ul>
  </fieldset>
{/if}

{if isset($PLUGINS_PROFILE)}
  {foreach from=$PLUGINS_PROFILE item=plugin_block}
    <fieldset>
      <legend>{$plugin_block.name}</legend>
      <div class="plugins fields">
        {include file=$plugin_block.template}
      </div>
    </fieldset>
  {/foreach}
{/if}

  <p class="bottomButtons">
    <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
    <input class="submit" type="submit" name="validate" value="{'Submit'|@translate}">
    <input class="submit" type="reset" name="reset" value="{'Reset'|@translate}">
    {if $ALLOW_USER_CUSTOMIZATION}
    <input class="submit" type="submit" name="reset_to_default" value="{'Reset to default values'|@translate}">
    {/if}
  </p>

</form>
