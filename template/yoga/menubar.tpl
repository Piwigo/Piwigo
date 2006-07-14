<!-- $Id$ -->
<div id="menubar">
<!-- BEGIN links -->
<dl id="mbLinks">
  <dt>{lang:Links}</dt>
  <dd>
    <ul>
      <!-- BEGIN link -->
      <li><a href="{links.link.URL}">{links.link.LABEL}</a></li>
      <!-- END link -->
    </ul>
  </dd>
</dl>
<!-- END links -->
<dl id="mbCategories">
  <dt><a href="{U_HOME}">{lang:Categories}</a></dt>
  <dd>
    {MENU_CATEGORIES_CONTENT}
    <p class="totalImages">{NB_PICTURE} {lang:total}</p>
  </dd>
</dl>

<!-- BEGIN tags -->
<dl id="mbTags">
  <dt>{lang:Related tags}</dt>
  <dd>
    <ul id="menuTagCloud">
      <!-- BEGIN tag -->
      <li>
        <a href="{tags.tag.URL_ADD}" title="{tags.tag.TITLE_ADD}"><img src="{pwg_root}{themeconf:icon_dir}/add_tag.png" alt="+"></a>
        <a href="{tags.tag.URL}" class="{tags.tag.CLASS}" title="{tags.tag.TITLE}">{tags.tag.NAME}</a>
      </li>
      <!-- END tag -->
    </ul>
  </dd>
</dl>
<!-- END tags -->

<dl id="mbSpecial">
  <dt>{lang:special_categories}</dt>
  <dd>
    <ul>
      <!-- BEGIN special_cat -->
      <li><a href="{special_cat.URL}" title="{special_cat.TITLE}">{special_cat.NAME}</a></li>
      <!-- END special_cat -->
    </ul>
  </dd>
</dl>
<dl id="mbMenu">
  <dt>{lang:title_menu}</dt>
  <dd>
    <ul>
      <!-- BEGIN summary -->
      <li><a href="{summary.U_SUMMARY}" title="{summary.TITLE}" {summary.REL}>{summary.NAME}</a></li>
      <!-- END summary -->
      <!-- BEGIN upload -->
      <li><a href="{upload.U_UPLOAD}">{lang:upload_picture}</a></li>
      <!-- END upload -->
    </ul>
  </dd>
</dl>
<dl id="mbIdentification">
  <dt>{lang:identification}</dt>
  <dd>
    <!-- BEGIN hello -->
    <p>{lang:hello}&nbsp;{USERNAME}&nbsp;!</p>
    <!-- END hello -->
    <ul>
      <!-- BEGIN register -->
      <li><a href="{U_REGISTER}" rel="nofollow">{lang:ident_register}</a></li>
      <!-- END register -->
      <!-- BEGIN login -->
      <li><a href="{F_IDENTIFY}" rel="nofollow">{lang:Connection}</a></li>
      <!-- END login -->
      <!-- BEGIN logout -->
      <li><a href="{U_LOGOUT}">{lang:logout}</a></li>
      <!-- END logout -->
      <!-- BEGIN profile -->
      <li><a href="{U_PROFILE}" title="{lang:hint_customize}">{lang:customize}</a></li>
      <!-- END profile -->
      <!-- BEGIN admin -->
      <li><a href="{U_ADMIN}" title="{lang:hint_admin}">{lang:admin}</a></li>
      <!-- END admin -->
    </ul>
    <!-- BEGIN quickconnect -->
    <form method="post" action="{F_IDENTIFY}" class="filter" id="quickconnect">
      <fieldset>
        <legend>{lang:Quick connect}</legend>

        <label>
          {lang:Username}
          <input type="text" name="username" size="15" value="" onfocus="this.className='focus';" onblur="this.className='nofocus';">
        </label>

        <label>
          {lang:password}
          <input type="password" name="password" size="15" onfocus="this.className='focus';" onblur="this.className='nofocus';">
        </label>

        <!-- BEGIN remember_me -->
        <label>
          {lang:remember_me}
          <input type="checkbox" name="remember_me" value="1">
        </label>
        <!-- END remember_me -->

        <p>
         <input type="submit" name="login" value="{lang:submit}">
        </p>
    <ul class="actions">
      <li><a href="{U_LOST_PASSWORD}" title="{lang:Forgot your password?}" rel="nofollow"><img src="{pwg_root}{themeconf:icon_dir}/lost_password.png" class="button" alt="{lang:Forgot your password?}"></a></li>
      <li><a href="{U_REGISTER}" title="{lang:Create a new account}" rel="nofollow"><img src="{pwg_root}{themeconf:icon_dir}/register.png" class="button" alt="{lang:register}"/></a></li>
    </ul>

      </fieldset>
    </form>
    <!-- END quickconnect -->

  </dd>
</dl>
</div> <!-- menubar -->
