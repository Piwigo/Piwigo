<!-- BEGIN errors -->
<div class="errors">
<ul>
  <!-- BEGIN error -->
  <li>{errors.error.ERROR}</li>
  <!-- END error -->
</ul>
</div>
<!-- END errors -->

<form action="{F_ACTION}" method="POST">
<div class="admin">{L_INFOS_TITLE} &quot;{CATEGORY}&quot;</div>
  <table width="100%">
    <tr>
      <td><div style="margin-left:50px;">{L_AUTHOR}</div></td>
      <td style="row1">
        <input type="text" name="author_cat" value="" maxlength="255" />
      </td>
      <td style="text-align:left;">
        <input type="checkbox" name="use_common_author" value="1" />
        {L_INFOS_OVERALL_USE}
      </td>
    </tr>
    <tr>
      <td>
        <div style="margin-left:50px;">{L_INFOS_CREATION_DATE} [DD/MM/YYYY]</div>
      </td>
      <td style="row1">
        <input type="text" name="date_creation_cat" value="" size="12" maxlength="10"/>
      </td>
      <td style="text-align:left;">
        <input type="checkbox" name="use_common_date_creation" value="1" />
        {L_INFOS_OVERALL_USE}
      </td>
    </tr>
    <tr>
      <td>
        <div style="margin-left:50px;">{L_KEYWORD} {L_KEYWORD_SEPARATION}</div>
      </td>
      <td style="row1">
        <input type="text" name="keywords_cat" value="" maxlength="255" />
      </td>
      <td style="text-align:left;">
        <input type="radio" name="common_keywords" value="add" />
        {L_INFOS_ADDTOALL}
        <input type="radio" name="common_keywords" value="remove" />
        {L_INFOS_REMOVEFROMALL}
      </td>
    </tr>
  </table>
  <br />
  <div class="admin">{L_INFOS_DETAIL}</div>
  <div class="navigationBar">{NAV_BAR}</div>
  <table width="100%">
    <tr>
      <td style="width:0px;">&nbsp;</td>
      <td class="row2" style="text-align:center;">{L_THUMBNAIL}</td>
      <td class="row2" style="text-align:center;">{L_INFOS_IMG}</td>
      <td class="row2" style="text-align:center;">{L_AUTHOR}</td>
      <td class="row2" style="text-align:center;">{L_INFOS_COMMENT}</td>
      <td class="row2" style="text-align:center;">{L_INFOS_CREATION_DATE}</td>
      <td class="row2" style="text-align:center;">{L_KEYWORD}</td>
    </tr>
    <!-- BEGIN picture -->
    <tr>
      <td style="width:0px;">
        <div style="margin-left:2px;margin-right:2px;">
          <input type="checkbox" name="check-{picture.ID_IMG}" value="1" />
        </div>
      </td>
      <td style="text-align:center;"><a name="{picture.DEFAULTNAME_IMG}" href="{picture.URL_IMG}"><img src="{picture.TN_URL_IMG}" alt="" class="miniature" title="{picture.FILENAME_IMG}" /></a></td>
      <td style="text-align:center;">{picture.DEFAULTNAME_IMG}<br /><input type="text" name="name-{picture.ID_IMG}" value="{picture.NAME_IMG}" maxlength="255"/></td>
      <td style="text-align:center;"><input type="text" name="author-{picture.ID_IMG}" value="{picture.AUTHOR_IMG}" maxlength="255" size="12" /></td>
      <td style="text-align:center;"><textarea name="comment-{picture.ID_IMG}" rows="5" cols="30" style="overflow:auto">{picture.COMMENT_IMG}</textarea></td>
      <td style="text-align:center;"><input type="text" name="date_creation-{picture.ID_IMG}" value="{picture.DATE_IMG}" maxlength="10" size="10" /></td>
      <td style="text-align:center;"><input type="text" name="keywords-{picture.ID_IMG}" value="{picture.KEYWORDS_IMG}" length="255" /></td>
    </tr>
    <!-- END picture -->
    <tr>
      <td colspan="7">
        <img src="./template/default/admin/images/arrow_select.gif" alt="&lt;" />
        {L_INFOS_ASSOCIATE}
        <!-- BEGIN associate_LOV -->
        <select name="associate">
          <!-- BEGIN associate_cat -->
          <option value="{#value}">{#content}</option>
          <!-- END associate_cat -->
        </select>
        <!-- END associate_LOV -->
      </td>
    </tr>
    <tr>
      <td colspan="7" style="text-align:center;">
        <input type="submit" value="{L_SUBMIT}" name="submit" class="bouton" />
      </td>
    </tr>
  </table>
</form>
