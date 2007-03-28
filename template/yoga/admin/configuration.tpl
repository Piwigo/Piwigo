<!-- $Id$ -->
<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{lang:title_configuration} {TABSHEET_TITLE}</h2>
  {TABSHEET}
</div>

<form method="post" action="{F_ACTION}" class="properties">

<!-- BEGIN main -->
<fieldset id="mainConf">
  <ul>
    <li>
      <span class="property">
        <label for="gallery_title">{lang:Gallery title}</label>
      </span>
      <input type="text" maxlength="255" size="50" name="gallery_title" id="gallery_title" value="{main.CONF_GALLERY_TITLE}" />
    </li>

    <li>
      <span class="property">
        <label for="page_banner">{lang:Page banner}</label>
      </span>
      <textarea class="description" name="page_banner" id="page_banner">{main.CONF_PAGE_BANNER}</textarea>
    </li>

    <li>
      <span class="property">
        <label for="gallery_url">{lang:Gallery URL}</label>
      </span>
      <input type="text" maxlength="255" size="50" name="gallery_url" id="gallery_url" value="{main.CONF_GALLERY_URL}" />
    </li>

    <li>
      <span class="property">{lang:Lock gallery}</span>
      <label><input type="radio" class="radio" name="gallery_locked" value="true" {main.GALLERY_LOCKED_YES} />{lang:Yes}</label>
      <label><input type="radio" class="radio" name="gallery_locked" value="false" {main.GALLERY_LOCKED_NO} />{lang:No}</label>
    </li>
  </ul>
</fieldset>

<fieldset id="mainConfCheck">
  <ul>

    <li>
      <span class="property">{lang:Rating}</span>
      <input type="checkbox" name="rate" {main.RATE} />
    </li>

    <li>
      <span class="property">{lang:Rating by guests}</span>
      <input type="checkbox" name="rate_anonymous" {main.RATE_ANONYMOUS} />
    </li>

    <li>
      <label for="allow_user_registration">
        <span class="property">{lang:Allow user registration}</span>
        <input type="checkbox" name="allow_user_registration" id="allow_user_registration" {main.ALLOW_USER_REGISTRATION} />
      </label>
    </li>

    <li>
      <label>
        <span class="property">{lang:Email administrators when a new user registers}</span>
        <input type="checkbox" name="email_admin_on_new_user" {main.EMAIL_ADMIN_ON_NEW_USER} />
      </label>
    </li>

    <li>
      <label>
        <span class="property">{lang:Email administrators when a picture is uploaded}</span>
        <input type="checkbox" name="email_admin_on_picture_uploaded" {main.EMAIL_ADMIN_ON_PICTURE_UPLOADED} />
      </label>
    </li>
  </ul>
</fieldset>
<!-- END main -->

<!-- BEGIN history -->
<fieldset id="historyConf">
  <ul>
      <li>
        <label><span class="property">{lang:conf_history_guest}</span><input type="checkbox" name="history_guest" {history.HISTORY_GUEST} /></label>
      </li>

      <li>
        <label><span class="property">{lang:conf_history_user}</span><input type="checkbox" name="log" {history.LOG} /></label>
      </li>

      <li>
        <label><span class="property">{lang:conf_history_admin}</span><input type="checkbox" name="history_admin" {history.HISTORY_ADMIN} /></label>
      </li>
  </ul>
</fieldset>
<!-- END history -->

<!-- BEGIN comments -->
<fieldset id="commentsConf">
  <ul>
    <li>
      <label>
        <span class="property">{lang:Comments for all}</span>
        <input type="checkbox" name="comments_forall" {comments.COMMENTS_FORALL} />
      </label>
    </li>

    <li>
      <span class="property">
        <label for="nb_comment_page">{lang:Number of comments per page}</label>
      </span>
      <input type="text" size="3" maxlength="4" name="nb_comment_page" id="nb_comment_page" value="{comments.NB_COMMENTS_PAGE}" />
    </li>

    <li>
      <label>
        <span class="property">{lang:Validation}</span>
        <input type="checkbox" name="comments_validation" {comments.COMMENTS_VALIDATION} />
      </label>
    </li>


    <li>
      <label>
        <span class="property">{lang:Email administrators when a valid comment is entered}</span>
        <input type="checkbox" name="email_admin_on_comment" {comments.EMAIL_ADMIN_ON_COMMENT} />
      </label>
    </li>

    <li>
      <label>
        <span class="property">{lang:Email administrators when a comment requires validation}</span>
        <input type="checkbox" name="email_admin_on_comment_validation" {comments.EMAIL_ADMIN_ON_COMMENT_VALIDATION} />
      </label>
    </li>

  </ul>
</fieldset>
<!-- END comments -->

<!-- BEGIN default -->
{PROFILE_CONTENT}
<!-- END default -->

<!-- BEGIN include_submit_buttons -->
  <p>
    <input class="submit" type="submit" name="submit" value="{lang:Submit}" {TAG_INPUT_ENABLED}>
    <input class="submit" type="reset" name="reset" value="{lang:Reset}">
  </p>
<!-- END include_submit_buttons -->
</form>
