  <div class="navButtons">

{if $DISPLAY_NAV_BUTTONS and !isset($slideshow)}
{if isset($last)}
  <a class="navButton" href="{$last.U_IMG}" title="{'Last'|@translate} : {$last.TITLE}" rel="last"><img src="{$ROOT_URL}{$themeconf.icon_dir}/last.png" class="button" alt="{'Last'|@translate}"></a>
{else}
  <a class="navButton"><img src="{$ROOT_URL}{$themeconf.icon_dir}/last_unactive.png" class="button" alt=""></a>
{/if}
  
{if isset($next)}
  <a class="navButton" href="{$next.U_IMG}" title="{'Next'|@translate} : {$next.TITLE}" rel="next"><img src="{$ROOT_URL}{$themeconf.icon_dir}/right.png" class="button" alt="{'Next'|@translate}"></a>
{else}
  <a class="navButton"><img src="{$ROOT_URL}{$themeconf.icon_dir}/right_unactive.png" class="button" alt=""></a>
{/if}

{if isset($slideshow.U_START_PLAY)}
  <a class="navButton" href="{$slideshow.U_START_PLAY}" title="{'Play of slideshow'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/play.png" class="button" alt="{'Play of slideshow'|@translate}"></a>
{/if}

{if isset($slideshow.U_STOP_PLAY)}
  <a class="navButton" href="{$slideshow.U_STOP_PLAY}" title="{'Pause of slideshow'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/pause.png" class="button" alt="{'Pause of slideshow'|@translate}"></a>
{/if}

{if isset($U_UP) and !isset($slideshow)}
  <a class="navButton" href="{$U_UP}" title="{'Thumbnails'|@translate}" rel="up"><img src="{$ROOT_URL}{$themeconf.icon_dir}/up.png" class="button" alt="{'Thumbnails'|@translate}"></a>
{/if}
  
{if isset($previous)}
  <a class="navButton" href="{$previous.U_IMG}" title="{'Previous'|@translate} : {$previous.TITLE}" rel="prev"><img src="{$ROOT_URL}{$themeconf.icon_dir}/left.png" class="button" alt="{'Previous'|@translate}"></a>
{else}
  <a class="navButton"><img src="{$ROOT_URL}{$themeconf.icon_dir}/left_unactive.png" class="button" alt=""></a>
{/if}
  
{if isset($first)}
  <a class="navButton" href="{$first.U_IMG}" title="{'First'|@translate} : {$first.TITLE}" rel="first"><img src="{$ROOT_URL}{$themeconf.icon_dir}/first.png" class="button" alt="{'First'|@translate}"></a>
{else}
  <a class="navButton"><img src="{$ROOT_URL}{$themeconf.icon_dir}/first_unactive.png" class="button" alt=""></a>
{/if}


{if isset($slideshow.U_START_REPEAT)}
  <a class="navButton" href="{$slideshow.U_START_REPEAT}" title="{'Repeat the slideshow'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/start_repeat.png" class="button" alt="{'Repeat the slideshow'|@translate}"></a>
{/if}

{if isset($slideshow.U_STOP_REPEAT)}
  <a class="navButton" href="{$slideshow.U_STOP_REPEAT}" title="{'Not repeat the slideshow'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/stop_repeat.png" class="button" alt="{'Not repeat the slideshow'|@translate}"></a>
{/if}

{if isset($slideshow)}
  {if isset($slideshow.U_DEC_PERIOD)}
    <a class="navButton" href="{$slideshow.U_DEC_PERIOD}" title="{'Accelerate diaporama speed'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/dec_period.png" class="button" alt="{'Accelerate diaporama speed'|@translate}"></a>
  {else}
    <a class="navButton"> <img src="{$ROOT_URL}{$themeconf.icon_dir}/dec_period_unactive.png" class="button" alt=""></a>
  {/if}

  {if isset($slideshow.U_INC_PERIOD)}
    <a class="navButton" href="{$slideshow.U_INC_PERIOD}" title="{'Reduce diaporama speed'|@translate}"><img src="{$ROOT_URL}{$themeconf.icon_dir}/inc_period.png" class="button" alt="{'Reduce diaporama speed'|@translate}"></a>
  {else}
    <a class="navButton"> <img src="{$ROOT_URL}{$themeconf.icon_dir}/inc_period_unactive.png" class="button" alt=""></a>
  {/if}
{/if}
{/if}

  </div>

<script type="text/javascript">// <![CDATA[
{literal}
function keyboardNavigation(e)
{
	if(!e) e=window.event;
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
// ]]></script>
