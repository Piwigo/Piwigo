<!-- $Id: admin_advices.tpl 939 2005-11-17 20:13:36Z VDigital $ -->

<div class="content" style="list-style-type:none; margin: 0 1em 0 14.5em; border: 1px solid;">
  <h2 style="font-weight: bold; padding-left: 2em;">{lang:About}: {ADVICE_ABOUT}</h2>
    <h3 style="text-align: left; padding-left: 3em;">{ADVICE_TEXT}</h3>
    <table>
    <tr><td style="text-align: left; padding-left: 2em; width:50%;">
    <!-- BEGIN More -->
      {More.ADVICE}  <br />
    <!-- END More -->
    </td><td style="text-align: left; width:15%;">
    <!-- BEGIN thumbnail -->
    <a href="{thumbnail.U_MODIFY}" alt="{lang:link_info_image}">
    <img class="thumbnail" src="{thumbnail.IMAGE}"
	       alt="{thumbnail.IMAGE_ALT}" title="{thumbnail.IMAGE_TITLE}"></a>
    </td><td style="text-align: left; width:15%;">
    <img src="{thumbnail.NAME}.png"
	       alt="{thumbnail.IMAGE_ALT}" title="{thumbnail.IMAGE_TITLE}">{lang:Name}<br />
    <img src="{thumbnail.COMMENT}.png"
	       alt="{thumbnail.IMAGE_ALT}" title="{thumbnail.IMAGE_TITLE}">{lang:Description}<br />
    <img src="{thumbnail.AUTHOR}.png"
	       alt="{thumbnail.IMAGE_ALT}" title="{thumbnail.IMAGE_TITLE}">{lang:Author}<br />
    <img src="{thumbnail.CREATE_DATE}.png"
	       alt="{thumbnail.IMAGE_ALT}" title="{thumbnail.IMAGE_TITLE}">{lang:Creation date}<br />
    <img src="{thumbnail.METADATA}.png"
	       alt="{thumbnail.IMAGE_ALT}" title="{thumbnail.IMAGE_TITLE}">{lang:Metadata}<br />
<!-- DEGING miss_Tags 
    <img src="{thumbnail.TAGS}.png"
	       alt="{thumbnail.IMAGE_ALT}" title="{thumbnail.IMAGE_TITLE}">{lang:Tags}
 END miss_Tags -->	       
    <!-- END thumbnail -->
  </td></tr>
  </table>
</div>
