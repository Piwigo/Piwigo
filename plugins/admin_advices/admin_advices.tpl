<!-- $Id: admin_advices.tpl 939 2005-11-17 20:13:36Z VDigital $ -->

<div style="list-style-type:none; background-color: #fff; margin: 0 1em 0 14.5em; border: 1px solid #69c;">
  <h2 style="font-weight: bold; padding-left: 2em;">{lang:About}: {ADVICE_ABOUT}</h2>
    <h3 style="text-align: left; padding-left: 3em;">{ADVICE_TEXT}</h3>
    <table>
    <tr><td style="text-align: left; padding-left: 2em; width:50%;">
    <!-- BEGIN More -->
      {More.ADVICE}  <br />
    <!-- END More -->
    </td><td style="text-align: left; width:30%;">
    <!-- BEGIN thumbnail -->
    <img class="thumbnail" src="{thumbnail.IMAGE}"
	       alt="{thumbnail.IMAGE_ALT}" title="{thumbnail.IMAGE_TITLE}">
    <!-- END thumbnail -->
  </td></tr>
  </table>
</div>
