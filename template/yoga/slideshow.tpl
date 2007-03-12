<!-- $Id: slideshow.tpl 1672 2006-12-17 11:02:09Z vdigital $ -->
<div id="imageHeaderBar">
  <div class="browsePath">
    <!-- BEGIN stop_slideshow -->
    [ <a href="{stop_slideshow.U_SLIDESHOW}">{lang:slideshow_stop}</a> ]
    <!-- END stop_slideshow -->
  </div>
  <div class="imageNumber">{PHOTO}</div>
  <!-- BEGIN title -->
  <h2 class="showtitle">{TITLE}</h2>
  <!-- END title -->
</div>
<div id="theImage">
  {ELEMENT_CONTENT}
<!-- BEGIN legend -->
<p class="showlegend">{legend.COMMENT_IMG}</p>
<!-- END legend -->
</div>
