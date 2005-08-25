<!-- BEGIN errors -->
<div class="errors">
  <ul>
    <!-- BEGIN error -->
    <li>{errors.error.ERROR}</li>
    <!-- END error -->
  </ul>
</div>
<!-- END errors -->

<h2>{L_TITLE}</h2>

<div class="formbox">
<form method="post" name="profile" action="{F_ACTION}" id="profile">

  <p><input type="hidden" name="userid" value="{USERID}" /></p>

  <h3>{L_REGISTRATION_INFO}</h3>
  <dl>
    <dt width="50%">{L_USERNAME}</dt>
    <dd>{USERNAME}</dd>
    <dt>{L_EMAIL}</dt>
    <dd><input type="text" name="mail_address" value="{EMAIL}" /></dd>
  </dl>
  <dl class="biglabel">
    <dt>{L_CURRENT_PASSWORD} : <br /><span class="small">{L_CURRENT_PASSWORD_HINT}</span></dt>
    <dd><input type="password" name="password" value="" /></dd>
  </dl>
  <dl class="biglabel">
    <dt>{L_NEW_PASSWORD} : <br /><span class="small">{L_NEW_PASSWORD_HINT}</span></dt>
    <dd><input type="password" name="use_new_pwd" value="" /></dd>
  </dl>
  <dl class="biglabel">
    <dt>{L_CONFIRM_PASSWORD} : <br /><span class="small">{L_CONFIRM_PASSWORD_HINT}</span></dt>
    <dd><input type="password" name="passwordConf" value="" /></dd>
  </dl>
  <h3>{L_PREFERENCES}</h3>
  <dl>
    <dt>{L_NB_IMAGE_LINE}</dt>
    <dd>
      <input type="text" size="3" maxlength="2" name="nb_image_line" value="{NB_IMAGE_LINE}" />
    </dd>
    <dt>{L_NB_ROW_PAGE}</dt>
    <dd>
      <input type="text" size="3" maxlength="2" name="nb_line_page" value="{NB_ROW_PAGE}" />
    </dd>
    <dt>{L_STYLE_SELECT}</dt>
    <dd>
      {STYLE_SELECT}
    </dd>
    <dt>{L_LANG_SELECT}</dt>
    <dd>
      {LANG_SELECT}
    </dd>
    <dt>{L_RECENT_PERIOD}</dt>
    <dd>
      <input type="text" size="3" maxlength="2" name="recent_period" value="{RECENT_PERIOD}" />
    </dd>
    <dt>{L_EXPAND_TREE}</dt>
    <dd>
      <input type="radio" class="radio" name="expand" value="true" {EXPAND_TREE_YES} />{L_YES}&nbsp;&nbsp;
      <input type="radio" class="radio" name="expand" value="false" {EXPAND_TREE_NO} />{L_NO}
    </dd>
    <dt>{L_NB_COMMENTS}</dt>
    <dd>
      <input type="radio" class="radio" name="show_nb_comments" value="true" {NB_COMMENTS_YES} />{L_YES}&nbsp;&nbsp;
      <input type="radio" class="radio" name="show_nb_comments" value="false" {NB_COMMENTS_NO} />{L_NO}
    </dd>
    <dt>{L_MAXWIDTH}</dt>
    <dd>
      <input type="text" size="4" maxlength="4" name="maxwidth" value="{MAXWIDTH}" />
    </dd>
    <dt>{L_MAXHEIGHT}</dt>
    <dd>
      <input type="text" size="4" maxlength="4" name="maxheight" value="{MAXHEIGHT}" />
    </dd>
  </dl>

  <p class="bottomButtons">
    <input type="submit" name="validate" value="{L_SUBMIT}">
    <input type="reset" name="reset" value="{L_RESET}">
  </p>

</form>

  <p><a href="{U_RETURN}" title="{L_RETURN_HINT}">[{L_RETURN}]</a></p>
</div>
