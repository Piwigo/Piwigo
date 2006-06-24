<!-- $Id$ -->
{MENUBAR}
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
  <h2>{calendar.TITLE}</h2>
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
