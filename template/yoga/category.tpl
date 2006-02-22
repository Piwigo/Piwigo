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
  <dt><a href="{U_HOME}">{L_CATEGORIES}</a></dt>
  <dd>
    {MENU_CATEGORIES_CONTENT}
    <p class="totalImages">{NB_PICTURE} {L_TOTAL}</p>
  </dd>
</dl>
<dl>
  <dt>{L_SPECIAL_CATEGORIES}</dt>
  <dd>
    <ul>
      <!-- BEGIN special_cat -->
      <li><a href="{special_cat.URL}" title="{special_cat.TITLE}">{special_cat.NAME}</a></li>
      <!-- END special_cat -->
    </ul>
  </dd>
</dl>
<dl>
  <dt>{L_SUMMARY}</dt>
  <dd>
    <ul>
      <!-- BEGIN summary -->
      <li><a href="{summary.U_SUMMARY}" title="{summary.TITLE}" {summary.REL}>{summary.NAME}</a></li>
      <!-- END summary -->
      <!-- BEGIN upload -->
      <li><a href="{upload.U_UPLOAD}">{L_UPLOAD}</a></li>
      <!-- END upload -->
    </ul>
  </dd>
</dl>
<dl>
  <dt>{L_IDENTIFY}</dt>
  <dd>
    <!-- BEGIN hello -->
    <p>{L_HELLO}&nbsp;{USERNAME}&nbsp;!</p>
    <!-- END hello -->
    <ul>
      <!-- BEGIN register -->
      <li><a href="{U_REGISTER}" rel="nofollow">{L_REGISTER}</a></li>
      <!-- END register -->
      <!-- BEGIN login -->
      <li><a href="{F_IDENTIFY}" rel="nofollow">{lang:Connection}</a></li>
      <!-- END login -->
      <!-- BEGIN logout -->
      <li><a href="{U_LOGOUT}">{L_LOGOUT}</a></li>
      <!-- END logout -->
      <!-- BEGIN profile -->
      <li><a href="{U_PROFILE}" title="{L_PROFILE_HINT}">{L_PROFILE}</a></li>
      <!-- END profile -->
      <!-- BEGIN admin -->
      <li><a href="{U_ADMIN}" title="{L_ADMIN_HINT}">{L_ADMIN}</a></li>
      <!-- END admin -->
    </ul>
    <!-- BEGIN quickconnect -->
    <form method="post" action="{F_IDENTIFY}" class="filter" id="quickconnect">
      <fieldset>
        <legend>{lang:Quick connect}</legend>
    
        <label>
          {lang:Username}
          <input type="text" name="username" size="15" value="">
        </label>
        
        <label>
          {L_PASSWORD}
          <input type="password" name="password" size="15">
        </label>
        
        <!-- BEGIN remember_me -->
        <label>
          {L_REMEMBER_ME}
          <input type="checkbox" name="remember_me" value="1">
        </label>
        <!-- END remember_me -->
    
        <p>
         <input type="submit" name="login" value="{lang:submit}">
        </p>
    <ul class="actions">
      <li><a href="{U_LOST_PASSWORD}" title="{lang:Forgot your password?}"><img src="{themeconf:icon_dir}/lost_password.png" class="button" alt="{lang:Forgot your password?}"></a></li>
      <li><a href="{U_REGISTER}" title="{lang:Create a new account}"><img src="{themeconf:icon_dir}/register.png" class="button" alt="{lang:register}"/></a></li>
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
      <li><a href="{caddie.URL}" title="{lang:add to caddie}"><img src="{themeconf:icon_dir}/caddie_add.png" class="button" alt="{lang:caddie}"/></a></li>
      <!-- END caddie -->
      
      <!-- BEGIN edit -->
      <li><a href="{edit.URL}" title="{lang:edit category informations}"><img src="{themeconf:icon_dir}/category_edit.png" class="button" alt="{lang:edit}"/></a></li>
      <!-- END edit -->

      <!-- BEGIN search_rules -->
      <li><a href="{search_rules.URL}" style="border:none;" onclick="popuphelp(this.href); return false;" title="{lang:Search rules}"><img src="{themeconf:icon_dir}/search_rules.png" class="button" alt="(?)"></a></li>
      <!-- END search_rules -->
      
      <!-- BEGIN calendar_view -->
      <li><a href="{calendar_view.URL}" title="{lang:calendar}"><img src="{themeconf:icon_dir}/calendar.png" class="button" alt="{lang:calendar}"></a></li>
      <!-- END calendar_view -->
      <!-- BEGIN normal_view -->
      <li><a href="{normal_view.URL}" title="{lang:calendar}"><img src="{themeconf:icon_dir}/calendar.png" class="button" alt="{lang:calendar}"></a></li>
      <!-- END normal_view -->
    </ul>
    
  <h2>{TITLE}</h2>
    
  </div> <!-- content -->
    
<!-- BEGIN calendar -->
<div class="navigationBar">{calendar.YEARS_NAV_BAR}</div>
<div class="navigationBar">{calendar.MONTHS_NAV_BAR}</div>
<!-- END calendar -->

<!-- BEGIN calendar -->

<!-- BEGIN styles -->
<div class="calendarStyles">Style: {calendar.styles.BAR}</div>
<!-- END styles -->

<!-- BEGIN views -->
<div class="calendarViews">{calendar.views.BAR}</div>
<!-- END views -->
<br/>
<!-- BEGIN navbar -->
<div class="navigationBar">{calendar.navbar.BAR}</div>
<!-- END navbar -->

<!-- BEGIN calbar -->
<div class="calendarBar">{calendar.calbar.BAR}</div>
<!-- END calbar -->
<!-- END calendar -->

<!-- BEGIN thumbnails -->
<ul class="thumbnails">
  <!-- BEGIN line -->
  <!-- BEGIN thumbnail -->
  <li>
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
      <br />{thumbnails.line.thumbnail.nb_comments.NB_COMMENTS} {L_COMMENT}
      <!-- END nb_comments -->
      </span>
    </span>
  </li>
  <!-- END thumbnail -->
  <!-- END line -->

</ul>
<!-- END thumbnails -->

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
