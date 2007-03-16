<!-- $Id$ -->
<div class="titrePage">
  <h2>{lang:waiting} {TABSHEET_TITLE}</h2>
  {TABSHEET}
</div>

<h3>{lang:title_upload}</h3>

<form action="{F_ACTION}" method="post" id="waiting">
  <table style="width:100%;" >
    <tr class="throw">
      <th style="width:20%;">{lang:category}</th>
      <th style="width:20%;">{lang:date}</th>
      <th style="width:20%;">{lang:file}</th>
      <th style="width:20%;">{lang:thumbnail}</th>
      <th style="width:20%;">{lang:Author}</th>
      <th style="width:1px;">&nbsp;</th>
    </tr>
    <!-- BEGIN picture -->
    <tr>
      <td class="{picture.WAITING_CLASS}" style="white-space:nowrap;">{picture.CATEGORY_IMG}</td>
      <td class="{picture.WAITING_CLASS}" style="white-space:nowrap;">{picture.DATE_IMG}</td>
      <td class="{picture.WAITING_CLASS}" style="white-space:nowrap;">
        <a href="{picture.PREVIEW_URL_IMG}" title="{picture.FILE_TITLE}">{picture.FILE_IMG}</a>
      </td>
      <td class="{picture.WAITING_CLASS}" style="white-space:nowrap;">
        <!-- BEGIN thumbnail -->
        <a href="{picture.thumbnail.PREVIEW_URL_TN_IMG}" title="{picture.thumbnail.FILE_TN_TITLE}">{picture.thumbnail.FILE_TN_IMG}</a>
        <!-- END thumbnail -->
      </td>
      <td class="{picture.WAITING_CLASS}" style="white-space:nowrap;">
        <a href="mailto:{picture.UPLOAD_EMAIL}">{picture.UPLOAD_USERNAME}</a>
      </td>
      <td class="{picture.WAITING_CLASS}" style="white-space:nowrap;">
        <label><input type="radio" name="action-{picture.ID_IMG}" value="validate" /> {lang:Validate}</label>
        <label><input type="radio" name="action-{picture.ID_IMG}" value="reject" /> {lang:Reject}</label>
      </td>
    </tr>
    <!-- END picture -->
  </table>

  <p class="bottomButtons">
    <input type="hidden" name="list" value="{LIST}" />
    <input class="submit" type="submit" name="submit" value="{lang:Submit}" {TAG_INPUT_ENABLED}/>
    <input class="submit" type="submit" name="validate-all" value="{lang:Validate All}" {TAG_INPUT_ENABLED}/>
    <input class="submit" type="submit" name="reject-all" value="{lang:Reject All}" {TAG_INPUT_ENABLED}/>
    <input class="submit" type="reset" value="{lang:Reset}" />
  </p>

</form>
