<div id="slideshow">
  <div id="imageHeaderBar">
	<div class="imageNumber">{$PHOTO}</div>
	<div class="browsePath">
	  {if isset($U_SLIDESHOW_STOP)}
		<a href="{$U_SLIDESHOW_STOP}" title="{'stop the slideshow'|translate}" style="font-size:22px">◼</a>&nbsp;
	  {/if}
		<a href="#" onclick="return fotoramaTogglePause();" title="{'slideshow'|@translate}" class="pwg-state-default pwg-button" rel="nofollow">
		<span class="pwg-icon pwg-icon-{if $Fotorama.autoplay}pause{else}play{/if}" id="togglePause"></span>
	</a>
{footer_script}
function fotoramaTogglePause() {
	if ($("#togglePause").hasClass("pwg-icon-pause")) {
		$(".fotorama").data("fotorama").stopAutoplay();
	}
	else {
		$(".fotorama").data("fotorama").startAutoplay();
	}
	$("#togglePause").toggleClass("pwg-icon-slideshow pwg-icon-pause");
	return false;
}
{/footer_script}
	  <h2 class="showtitle">{$current.TITLE}</h2>
	</div>
  </div>

  <div id="content">
	<div id="theImage">
      {include file=$FOTORAMA_CONTENT_PATH}
	</div>
  </div>
</div>
