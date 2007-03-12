<div class="content">
  <h2>{lang:An_advice_about} {ADVICE_ABOUT}</h2>
    <h3>{ADVICE_TEXT}</h3>
    <table summary="Admin advices summary">
    <tr><td style="text-align: left; width: 50%;">
    <!-- BEGIN More -->
      {More.ADVICE}  <br />
    <!-- END More -->
    <br />
    </td><td style="text-align: right; width: 20%;">
    <!-- BEGIN thumbnail -->
    <a href="{thumbnail.U_MODIFY}" title="{lang:link_info_image}">
    <img class="thumbnail" src="{thumbnail.IMAGE}"
	       alt="{thumbnail.IMAGE_ALT}" title="{thumbnail.IMAGE_TITLE}"></a>
    </td><td style="text-align: left;">
    <img src="{thumbnail.NAME}.png"
	       alt="{thumbnail.IMAGE_ALT}" title="{thumbnail.IMAGE_TITLE}"> {lang:Name}<br />
    <img src="{thumbnail.COMMENT}.png"
	       alt="{thumbnail.IMAGE_ALT}" title="{thumbnail.IMAGE_TITLE}"> {lang:Description}<br />
    <img src="{thumbnail.AUTHOR}.png"
	       alt="{thumbnail.IMAGE_ALT}" title="{thumbnail.IMAGE_TITLE}"> {lang:Author}<br />
    <img src="{thumbnail.CREATE_DATE}.png"
	       alt="{thumbnail.IMAGE_ALT}" title="{thumbnail.IMAGE_TITLE}"> {lang:Creation date}<br />
    <img src="{thumbnail.METADATA}.png"
	       alt="{thumbnail.IMAGE_ALT}" title="{thumbnail.IMAGE_TITLE}"> {lang:Metadata}<br />
    <img src="{thumbnail.TAGS}.png"
	       alt="{thumbnail.IMAGE_ALT}" title="{thumbnail.IMAGE_TITLE}"> {lang:Tags} ({thumbnail.NUM_TAGS})
    <!-- END thumbnail -->
  </td></tr>
  </table>

</div>
