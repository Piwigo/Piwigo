<!-- $Id$ -->
<div id="menubar">
<!-- BEGIN links -->
<dl>
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
<dl>
  <dt><a href="{U_HOME}">{lang:Categories}</a></dt>
  <dd>
    {MENU_CATEGORIES_CONTENT}
    <p class="totalImages">{NB_PICTURE} {lang:total}</p>
  </dd>
</dl>

<!-- BEGIN tags -->
<dl>
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

<dl>
  <dt>{lang:special_categories}</dt>
  <dd>
    <ul>
      <!-- BEGIN special_cat -->
      <li><a href="{special_cat.URL}" title="{special_cat.TITLE}">{special_cat.NAME}</a></li>
      <!-- END special_cat -->
    </ul>
  </dd>
</dl>
<dl>
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
<dl>
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

<div id="content">
  <div class="titrePage">
    <ul class="categoryActions">
      <li>&nbsp;</li>
      <!-- BEGIN preferred_image_order -->
      <li>
      {lang:Sort order}:
      <select onchange="document.location = this.options[this.selectedIndex].value;">
        <!-- BEGIN order -->
        <option value="{preferred_image_order.order.URL}" {preferred_image_order.order.SELECTED_OPTION}>{preferred_image_order.order.DISPLAY}</option>
        <!-- END order -->
      </select>
      </li>
      <!-- END preferred_image_order -->

      <!-- BEGIN caddie -->
      <li><a href="{caddie.URL}" title="{lang:add to caddie}"><img src="{pwg_root}{themeconf:icon_dir}/caddie_add.png" class="button" alt="{lang:caddie}"/></a></li>
      <!-- END caddie -->

      <!-- BEGIN edit -->
      <li><a href="{edit.URL}" title="{lang:edit category informations}"><img src="{pwg_root}{themeconf:icon_dir}/category_edit.png" class="button" alt="{lang:edit}"/></a></li>
      <!-- END edit -->

      <!-- BEGIN search_rules -->
      <li><a href="{search_rules.URL}" style="border:none;" onclick="popuphelp(this.href); return false;" title="{lang:Search rules}"><img src="{pwg_root}{themeconf:icon_dir}/search_rules.png" class="button" alt="(?)"></a></li>
      <!-- END search_rules -->

      <!-- BEGIN mode_normal -->
      <li><a href="{mode_normal.URL}" title="{lang:mode_normal_hint}"><img src="{pwg_root}{themeconf:icon_dir}/normal_mode.png" class="button" alt="{lang:mode_normal_hint}"></a></li>
      <!-- END mode_normal -->
      <!-- BEGIN mode_posted -->
      <li><a href="{mode_posted.URL}" title="{lang:mode_posted_hint}" rel="nofollow"><img src="{pwg_root}{themeconf:icon_dir}/calendar.png" class="button" alt="{lang:mode_posted_hint}"></a></li>
      <!-- END mode_posted -->
      <!-- BEGIN mode_created -->
      <li><a href="{mode_created.URL}" title="{lang:mode_created_hint}" rel="nofollow"><img src="{pwg_root}{themeconf:icon_dir}/calendar_created.png" class="button" alt="{lang:mode_created_hint}"></a></li>
      <!-- END mode_created -->
    </ul>

  <h2>{TITLE}</h2>
  <!-- BEGIN calendar -->
  <!-- BEGIN views -->
  <div class="calendarViews">{lang:calendar_view}:
    <select onchange="document.location = this.options[this.selectedIndex].value;">
    <!-- BEGIN view -->
      <option value="{calendar.views.view.VALUE}" {calendar.views.view.SELECTED}>{calendar.views.view.CONTENT}</option>
    <!-- END view -->
    </select>
  </div>
  <!-- END views -->
  <!-- END calendar -->

  <!-- BEGIN calendar -->
  <h2>{calendar.TITLE}
</h2>
  <!-- END calendar -->

  </div> <!-- titrePage -->

<!-- BEGIN calendar -->
<!-- BEGIN navbar -->
<div class="calendarBar">
<!-- BEGIN prev -->
	<div style="float:left">&laquo; <a href="{calendar.navbar.prev.URL}">{calendar.navbar.prev.LABEL}</a></div>
<!-- END prev -->
<!-- BEGIN next -->
	<div style="float:right"><a href="{calendar.navbar.next.URL}">{calendar.navbar.next.LABEL}</a> &raquo;</div>
<!-- END next -->
	{calendar.navbar.BAR}&nbsp;
</div>
<!-- END navbar -->

<!-- BEGIN calbar -->
<div class="calendarCalBar">{calendar.calbar.BAR}</div>
<!-- END calbar -->
<!-- END calendar -->

{MONTH_CALENDAR}

<!-- BEGIN thumbnails -->
<ul class="thumbnails">
  <!-- BEGIN line -->
  <!-- BEGIN thumbnail -->
  <li class="{thumbnails.line.thumbnail.CLASS}">
    <span class="wrap1">
      <span class="wrap2">
        <a href="{thumbnails.line.thumbnail.U_IMG_LINK}">
            <img class="thumbnail" src="{thumbnails.line.thumbnail.IMAGE}"
	    alt="{thumbnails.line.thumbnail.IMAGE_ALT}"
	    title="{thumbnails.line.thumbnail.IMAGE_TITLE}">
        </a>
      </span>
      <span class="thumbLegend">
      <!-- BEGIN element_name -->
      {thumbnails.line.thumbnail.element_name.NAME}
      <!-- END element_name -->
      <!-- BEGIN category_name -->
      [{thumbnails.line.thumbnail.category_name.NAME}]
      <!-- END category_name -->
      {thumbnails.line.thumbnail.IMAGE_TS}
      <!-- BEGIN nb_comments -->
      <br />{thumbnails.line.thumbnail.nb_comments.NB_COMMENTS} {lang:comments}
      <!-- END nb_comments -->
      </span>
    </span>
  </li>
  <!-- END thumbnail -->
  <!-- END line -->

</ul>
<!-- END thumbnails -->

{CATEGORIES}

<!-- BEGIN cat_infos -->
<!-- BEGIN navigation -->
<div class="navigationBar">
{cat_infos.navigation.NAV_BAR}
</div>
<!-- END navigation -->
<!-- BEGIN comment -->
<div class="additional_info">{cat_infos.comment.COMMENTS}</div>
<!-- END comment -->
<!-- END cat_infos -->

</div> <!-- content -->
