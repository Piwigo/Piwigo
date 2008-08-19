{* $Id$ *}
  <div class="navButtons">
  
{if isset($last)}
  <a class="navButton" href="{$last.U_IMG}" title="{'last_page'|@translate} : {$last.TITLE}" rel="last"><img src="{$ROOT_URL}{$themeconf.icon_dir}/last.png" class="button" alt="{'last_page'|@translate}"></a>
{else}
  <a class="navButton"><img src="{$ROOT_URL}{$themeconf.icon_dir}/last_unactive.png" class="button" alt=""></a>
{/if}
  
{if isset($next)}
  <a class="navButton" href="{$next.U_IMG}" title="{'next_page'|@translate} : {$next.TITLE}" rel="next"><img src="{$ROOT_URL}{$themeconf.icon_dir}/right.png" class="button" alt="{'next_page'|@translate}"></a>
{else}
  <a class="navButton"><img src="{$ROOT_URL}{$themeconf.icon_dir}/right_unactive.png" class="button" alt=""></a>
{/if}

{if isset($slideshow.U_START_PLAY)}
  <a class="navButton" href="{$slideshow.U_START_PLAY}" title="{'start_play'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/play.png" class="button" alt="{'start_play'|@translate}"></a>
{/if}

{if isset($slideshow.U_STOP_PLAY)}
  <a class="navButton" href="{$slideshow.U_STOP_PLAY}" title="{'stop_play'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/pause.png" class="button" alt="{'stop_play'|@translate}"></a>
{/if}

{if isset($U_UP) and !isset($slideshow)}
  <a class="navButton" href="{$U_UP}" title="{'thumbnails'|@translate}" rel="up"><img src="{$ROOT_URL}{$themeconf.icon_dir}/up.png" class="button" alt="{'thumbnails'|@translate}"></a>
{/if}
  
{if isset($previous)}
  <a class="navButton" href="{$previous.U_IMG}" title="{'previous_page'|@translate} : {$previous.TITLE}" rel="prev"><img src="{$ROOT_URL}{$themeconf.icon_dir}/left.png" class="button" alt="{'previous_page'|@translate}"></a>
{else}
  <a class="navButton"><img src="{$ROOT_URL}{$themeconf.icon_dir}/left_unactive.png" class="button" alt=""></a>
{/if}
  
{if isset($first)}
  <a class="navButton" href="{$first.U_IMG}" title="{'first_page'|@translate} : {$first.TITLE}" rel="first"><img src="{$ROOT_URL}{$themeconf.icon_dir}/first.png" class="button" alt="{'first_page'|@translate}"></a>
{else}
  <a class="navButton"><img src="{$ROOT_URL}{$themeconf.icon_dir}/first_unactive.png" class="button" alt=""></a>
{/if}


{if isset($slideshow.U_START_REPEAT)}
  <a class="navButton" href="{$slideshow.U_START_REPEAT}" title="{'start_repeat'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/start_repeat.png" class="button" alt="{'start_repeat'|@translate}"></a>
{/if}

{if isset($slideshow.U_STOP_REPEAT)}
  <a class="navButton" href="{$slideshow.U_STOP_REPEAT}" title="{'stop_repeat'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/stop_repeat.png" class="button" alt="{'stop_repeat'|@translate}"></a>
{/if}

{if isset($slideshow)}
  {if isset($slideshow.U_DEC_PERIOD)}
    <a class="navButton" href="{$slideshow.U_DEC_PERIOD}" title="{'dec_period'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/dec_period.png" class="button" alt="{'dec_period'|@translate}"></a>
  {else}
    <a class="navButton"> <img src="{$ROOT_URL}{$themeconf.icon_dir}/dec_period_unactive.png" class="button" alt=""></a>
  {/if}

  {if isset($slideshow.U_INC_PERIOD)}
    <a class="navButton" href="{$slideshow.U_INC_PERIOD}" title="{'inc_period'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/inc_period.png" class="button" alt="{'inc_period'|@translate}"></a>
  {else}
    <a class="navButton"> <img src="{$ROOT_URL}{$themeconf.icon_dir}/inc_period_unactive.png" class="button" alt=""></a>
  {/if}
{/if}

  </div>

<script type="text/javascript">
{literal}
function keyboardNavigation(e)
{
  if(!e) var e=window.event;
  if (e.altKey) return true;
  var target = e.target || e.srcElement;
  if (target && target.type) return true; //an input editable element
  var keyCode=e.keyCode || e.which;
  var docElem = document.documentElement;
  switch(keyCode) {
{/literal}
{if isset($next)}
  case 63235: case 39: if (e.ctrlKey || docElem.scrollLeft==docElem.scrollWidth-docElem.clientWidth ){ldelim}window.location="{$next.U_IMG}".replace( "&amp;", "&" ); return false; } break;
{/if}
{if isset($previous)}
  case 63234: case 37: if (e.ctrlKey || docElem.scrollLeft==0){ldelim}window.location="{$previous.U_IMG|@escape:jasvascript}".replace("&amp;","&"); return false; } break;
{/if}
{if isset($first)}
  /*Home*/case 36: if (e.ctrlKey){ldelim}window.location="{$first.U_IMG|@escape:jasvascript}".replace("&amp;","&"); return false; } break;
{/if}
{if isset($last)}
  /*End*/case 35: if (e.ctrlKey){ldelim}window.location="{$last.U_IMG|@escape:jasvascript}".replace("&amp;","&"); return false; } break;
{/if}
{if isset($U_UP) and !isset($slideshow)}
  /*Up*/case 38: if (e.ctrlKey){ldelim}window.location="{$U_UP|@escape:jasvascript}".replace("&amp;","&"); return false; } break;
{/if}

{if isset($slideshow.U_START_PLAY)}
  /*Pause*/case 32: {ldelim}window.location="{$slideshow.U_START_PLAY|@escape:jasvascript}".replace("&amp;","&"); return false; } break;
{/if}
{if isset($slideshow.U_STOP_PLAY)}
  /*Play*/case 32: {ldelim}window.location="{$slideshow.U_STOP_PLAY|@escape:jasvascript}".replace("&amp;","&"); return false; } break;
{/if}
  }
  return true;
}
document.onkeydown=keyboardNavigation;
</script>
