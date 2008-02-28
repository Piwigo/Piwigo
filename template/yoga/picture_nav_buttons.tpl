<!-- DEV TAG: not smarty migrated -->
<!-- $Id$ -->
  <div class="navButtons">
  <!-- BEGIN last -->
    <a class="navButton prev" href="{last.U_IMG}" title="{lang:last_page} : {last.TITLE_IMG}" rel="last"><img src="{pwg_root}{themeconf:icon_dir}/last.png" class="button" alt="{lang:last_page}"></a>
  <!-- END last -->
  <!-- BEGIN last_unactive -->
    <a class="navButton prev"><img src="{pwg_root}{themeconf:icon_dir}/last_unactive.png" class="button" alt=""></a>
  <!-- END last_unactive -->
  <!-- BEGIN next -->
    <a class="navButton next" href="{next.U_IMG}" title="{lang:next_page} : {next.TITLE_IMG}" rel="next"><img src="{pwg_root}{themeconf:icon_dir}/right.png" class="button" alt="{lang:next_page}"></a>
  <!-- END next -->
  <!-- BEGIN next_unactive -->
    <a class="navButton next"><img src="{pwg_root}{themeconf:icon_dir}/right_unactive.png" class="button" alt=""></a>
  <!-- END next_unactive -->
  <!-- BEGIN start_play -->
    <a class="navButton play" href="{start_play.U_IMG}" title="{lang:start_play}" rel="play"><img src="{pwg_root}{themeconf:icon_dir}/play.png" class="button" alt="{lang:start_play}"></a>
  <!-- END start_play -->
  <!-- BEGIN stop_play -->
    <a class="navButton play" href="{stop_play.U_IMG}" title="{lang:stop_play}" rel="play"><img src="{pwg_root}{themeconf:icon_dir}/pause.png" class="button" alt="{lang:stop_play}"></a>
  <!-- END stop_play -->
  <!-- BEGIN up -->
    <a class="navButton up" href="{up.U_URL}" title="{lang:thumbnails}" rel="up"><img src="{pwg_root}{themeconf:icon_dir}/up.png" class="button" alt="{lang:thumbnails}"></a>
  <!-- END up -->
  <!-- BEGIN previous -->
    <a class="navButton prev" href="{previous.U_IMG}" title="{lang:previous_page} : {previous.TITLE_IMG}" rel="prev"><img src="{pwg_root}{themeconf:icon_dir}/left.png" class="button" alt="{lang:previous_page}"></a>
  <!-- END previous -->
  <!-- BEGIN previous_unactive -->
    <a class="navButton prev"><img src="{pwg_root}{themeconf:icon_dir}/left_unactive.png" class="button" alt=""></a>
  <!-- END previous_unactive -->
  <!-- BEGIN first -->
    <a class="navButton prev" href="{first.U_IMG}" title="{lang:first_page} : {first.TITLE_IMG}" rel="first"><img src="{pwg_root}{themeconf:icon_dir}/first.png" class="button" alt="{lang:first_page}"></a>
  <!-- END first -->
  <!-- BEGIN first_unactive -->
    <a class="navButton prev"><img src="{pwg_root}{themeconf:icon_dir}/first_unactive.png" class="button" alt=""></a>
  <!-- END first_unactive -->
  <!-- BEGIN start_repeat -->
    <a class="navButton repeat" href="{start_repeat.U_IMG}" title="{lang:start_repeat}" rel="repeat"><img src="{pwg_root}{themeconf:icon_dir}/start_repeat.png" class="button" alt="{lang:start_repeat}"></a>
  <!-- END start_repeat -->
  <!-- BEGIN stop_repeat -->
    <a class="navButton repeat" href="{stop_repeat.U_IMG}" title="{lang:stop_repeat}" rel="repeat"><img src="{pwg_root}{themeconf:icon_dir}/stop_repeat.png" class="button" alt="{lang:stop_repeat}"></a>
  <!-- END stop_repeat -->
  <!-- BEGIN inc_period -->
    <a class="navButton inc_period" href="{inc_period.U_IMG}" title="{lang:inc_period}" rel="repeat"><img src="{pwg_root}{themeconf:icon_dir}/inc_period.png" class="button" alt="{lang:inc_period}"></a>
  <!-- END inc_period -->
  <!-- BEGIN inc_period_unactive -->
    <a class="navButton inc_period" <img src="{pwg_root}{themeconf:icon_dir}/inc_period_unactive.png" class="button" alt=""></a>
  <!-- END inc_period_unactive -->
  <!-- BEGIN dec_period -->
    <a class="navButton dec_period" href="{dec_period.U_IMG}" title="{lang:dec_period}" rel="repeat"><img src="{pwg_root}{themeconf:icon_dir}/dec_period.png" class="button" alt="{lang:dec_period}"></a>
  <!-- END dec_period -->
  <!-- BEGIN dec_period_unactive -->
    <a class="navButton dec_period" <img src="{pwg_root}{themeconf:icon_dir}/dec_period_unactive.png" class="button" alt=""></a>
  <!-- END dec_period_unactive -->
  </div>

<script type="text/javascript">
function keyboardNavigation(e)
{
  if(!e) var e=window.event;
  if (e.altKey) return true;
  var target = e.target || e.srcElement;
  if (target && target.type) return true; //an input editable element
  var keyCode=e.keyCode || e.which;
  var docElem = document.documentElement;
  switch(keyCode) {
<!-- BEGIN next -->
    case 63235: case 39: if (e.ctrlKey || docElem.scrollLeft==docElem.scrollWidth-docElem.clientWidth ){window.location="{next.U_IMG}".replace( "&amp;", "&" ); return false; } break;
<!-- END next -->
<!-- BEGIN previous -->
    case 63234: case 37: if (e.ctrlKey || docElem.scrollLeft==0){ window.location="{previous.U_IMG}".replace("&amp;","&"); return false; } break;
<!-- END previous -->
<!-- BEGIN first -->
    /*Home*/case 36: if (e.ctrlKey){window.location="{first.U_IMG}".replace("&amp;","&"); return false; } break;
<!-- END first -->
<!-- BEGIN last -->
    /*End*/case 35: if (e.ctrlKey){window.location="{last.U_IMG}".replace("&amp;","&"); return false; } break;
<!-- END last -->
<!-- BEGIN up -->
    /*Up*/case 38: if (e.ctrlKey){window.location="{up.U_UP}".replace("&amp;","&"); return false; } break;
<!-- END up -->
<!-- BEGIN start_play -->
    /*Pause*/case 32: {window.location="{start_play.U_IMG}".replace("&amp;","&"); return false; } break;
<!-- END start_play -->
<!-- BEGIN stop_play -->
    /*Play*/case 32: {window.location="{stop_play.U_IMG}".replace("&amp;","&"); return false; } break;
<!-- END stop_play -->
  }
  return true;
}
document.onkeydown=keyboardNavigation;
</script>
