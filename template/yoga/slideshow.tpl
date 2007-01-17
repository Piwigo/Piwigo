<!-- $Id: slideshow.tpl 1672 2006-12-17 11:02:09Z vdigital $ -->
<div id="imageHeaderBar">
  <div class="browsePath">
    <!-- BEGIN stop_slideshow -->
    [ <a href="{stop_slideshow.U_SLIDESHOW}">{lang:slideshow_stop}</a> ]
    <!-- END stop_slideshow -->
  </div>
  <div class="imageNumber">{PHOTO}</div>
</div>
<div id="theImage">
  {ELEMENT_CONTENT}
</div>
