{* $Id$ *}
<div class="titrePage">
  <h2>{'title_configuration'|@translate} {$TABSHEET_TITLE}</h2>
</div>

<form method="post" action="{$F_ACTION}" class="properties">

{if isset($main)}
<fieldset id="mainConf">
  <ul>
    <li>
      <span class="property">
        <label for="gallery_title">{'Gallery title'|@translate}</label>
      </span>
      <input type="text" maxlength="255" size="50" name="gallery_title" id="gallery_title" value="{$main.CONF_GALLERY_TITLE}" />
    </li>

    <li>
      <span class="property">
        <label for="page_banner">{'Page banner'|@translate}</label>
      </span>
      <textarea class="description" name="page_banner" id="page_banner">{$main.CONF_PAGE_BANNER}</textarea>
    </li>

    <li>
      <span class="property">
        <label for="gallery_url">{'Gallery URL'|@translate}</label>
      </span>
      <input type="text" maxlength="255" size="50" name="gallery_url" id="gallery_url" value="{$main.CONF_GALLERY_URL}" />
    </li>
  </ul>
</fieldset>

<fieldset id="mainConfCheck">
  <ul>

    <li>
      <label>
        <span class="property">{'Lock gallery'|@translate}</span>
        <input type="checkbox" name="gallery_locked" {if ($main.gallery_locked)}checked="checked"{/if} />
      </label>
    </li>

    <li>
      <label>
        <span class="property">{'Rating'|@translate}</span>
        <input type="checkbox" name="rate" {if ($main.rate)}checked="checked"{/if} />
      </label>
    </li>

    <li>
      <label>
        <span class="property">{'Rating by guests'|@translate}</span>
        <input type="checkbox" name="rate_anonymous" {if ($main.rate_anonymous)}checked="checked"{/if} />
      </label>
    </li>

    <li>
      <label>
        <span class="property">{'Allow user registration'|@translate}</span>
        <input type="checkbox" name="allow_user_registration" {if ($main.allow_user_registration)}checked="checked"{/if} />
      </label>
    </li>

    <li>
      <label>
        <span class="property">{'obligatory_user_mail_address'|@translate}</span>
        <input type="checkbox" name="obligatory_user_mail_address" {if ($main.obligatory_user_mail_address)}checked="checked"{/if} />
      </label>
    </li>

    <li>
      <label>
        <span class="property">{'Email administrators when a new user registers'|@translate}</span>
        <input type="checkbox" name="email_admin_on_new_user" {if ($main.email_admin_on_new_user)}checked="checked"{/if} />
      </label>
    </li>
  </ul>
</fieldset>
{/if}

{if isset($history)}
<fieldset id="historyConf">
  <ul>
      <li>
        <label><span class="property">{'conf_history_guest'|@translate}</span><input type="checkbox" name="history_guest" {if ($history.history_guest)}checked="checked"{/if} /></label>
      </li>

      <li>
        <label><span class="property">{'conf_history_user'|@translate}</span><input type="checkbox" name="log" {if ($history.log)}checked="checked"{/if} /></label>
      </li>

      <li>
        <label><span class="property">{'conf_history_admin'|@translate}</span><input type="checkbox" name="history_admin" {if ($history.history_admin)}checked="checked"{/if} /></label>
      </li>
  </ul>
</fieldset>
{/if}


{if isset($comments)}
<fieldset id="commentsConf">
  <ul>
    <li>
      <label>
        <span class="property">{'Comments for all'|@translate}</span>
        <input type="checkbox" name="comments_forall" {if ($comments.comments_forall)}checked="checked"{/if} />
      </label>
    </li>

    <li>
      <span class="property">
        <label for="nb_comment_page">{'Number of comments per page'|@translate}</label>
      </span>
      <input type="text" size="3" maxlength="4" name="nb_comment_page" id="nb_comment_page" value="{$comments.NB_COMMENTS_PAGE}" />
    </li>

    <li>
      <label>
        <span class="property">{'Validation'|@translate}</span>
        <input type="checkbox" name="comments_validation" {if ($comments.comments_validation)}checked="checked"{/if} />
      </label>
    </li>


    <li>
      <label>
        <span class="property">{'Email administrators when a valid comment is entered'|@translate}</span>
        <input type="checkbox" name="email_admin_on_comment" {if ($comments.email_admin_on_comment)}checked="checked"{/if} />
      </label>
    </li>

    <li>
      <label>
        <span class="property">{'Email administrators when a comment requires validation'|@translate}</span>
        <input type="checkbox" name="email_admin_on_comment_validation" {if ($comments.email_admin_on_comment_validation)}checked="checked"{/if} />
      </label>
    </li>

  </ul>
</fieldset>
{/if}

{if isset($upload)}
<fieldset id="uploadConf">
  <ul>
    <li>
      <label><span class="property">{'Show upload link every time'|@translate}</span>
      <input type="checkbox" name="upload_link_everytime" {if ($upload.upload_link_everytime)}checked="checked"{/if} /></label>
    </li>
    <li>
      <label><span class="property">{'User access level to upload'|@translate}</span>
      {html_options name="upload_user_access" options=$upload.upload_user_access_options selected=$upload.upload_user_access_options_selected}
    </li>
    <li>
      <label>
        <span class="property">{'Email administrators when a picture is uploaded'|@translate}</span>
        <input type="checkbox" name="email_admin_on_picture_uploaded" {if ($upload.email_admin_on_picture_uploaded)}checked="checked"{/if} />
      </label>
    </li>
  </ul>
</fieldset>
{/if}

{if isset($default)}
{$PROFILE_CONTENT}
{/if}

{if !isset($default)}
  <p>
    <input class="submit" type="submit" name="submit" value="{'Submit'|@translate}" {$TAG_INPUT_ENABLED}>
    <input class="submit" type="reset" name="reset" value="{'Reset'|@translate}">
  </p>
{/if}
</form>