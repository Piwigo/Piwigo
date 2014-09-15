{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

<h2>{'Piwigo configuration'|translate} {$TABSHEET_TITLE}</h2>

<form method="post" name="profile" action="{$GUEST_F_ACTION}" id="profile" class="properties">

<div id="configContent">

{if $GUEST_USERNAME!='guest'}
  <fieldset>
      {'The settings for the guest are from the %s user'|translate:$GUEST_USERNAME}
  </fieldset>
{/if}

  <fieldset>
    <legend>{'Preferences'|translate}</legend>
    <input type="hidden" name="redirect" value="{$GUEST_REDIRECT}">

    <ul>
      <li>
        <span class="property">
          <label for="nb_image_page">{'Number of photos per page'|translate}</label>
        </span>
        <input type="text" size="4" maxlength="3" name="nb_image_page" id="nb_image_page" value="{$GUEST_NB_IMAGE_PAGE}">
      </li>

      <li>
        <span class="property">
          <label for="recent_period">{'Recent period'|translate}</label>
        </span>
        <input type="text" size="3" maxlength="2" name="recent_period" id="recent_period" value="{$GUEST_RECENT_PERIOD}">
      </li>

      <li>
        <span class="property">{'Expand all albums'|translate}</span>
        {html_radios name='expand' options=$radio_options selected=$GUEST_EXPAND}
      </li>

    {if $GUEST_ACTIVATE_COMMENTS}
      <li>
        <span class="property">{'Show number of comments'|translate}</span>
        {html_radios name='show_nb_comments' options=$radio_options selected=$GUEST_NB_COMMENTS}
      </li>
    {/if}

      <li>
        <span class="property">{'Show number of hits'|translate}</span>
        {html_radios name='show_nb_hits' options=$radio_options selected=$GUEST_NB_HITS}
      </li>
    </ul>
  </fieldset>

  <p class="bottomButtons">
    <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
    <input class="submit" type="submit" name="validate" value="{'Submit'|translate}">
    <input class="submit" type="reset" name="reset" value="{'Reset'|translate}">
  </p>

</div>

</form>