<!-- BEGIN errors -->
<div class="errors">
<ul>
  <!-- BEGIN error -->
  <li>{errors.error.ERROR}</li>
  <!-- END error -->
</ul>
</div>
<!-- END errors -->
<div class="admin">{TITLE_IMG}</div>
<form action="{F_ACTION}" method="POST">
  <table style="width:100%;">
    <tr>
      <td colspan="2" align="center"><a href="{URL_IMG}" class="thumbnail"><img src="{TN_URL_IMG}" alt="" class="miniature" /></a></td>
    </tr>
    <tr>
      <td style="width:50%;"><strong>{L_UPLOAD_NAME}</strong></td>
      <td class="row1"><input type="text" name="name" value="{NAME_IMG}" /> [ {L_DEFAULT} : {DEFAULT_NAME_IMG} ]</td>
    </tr>
    <tr>
      <td style="width:50%;"><strong>{L_FILE}</strong></td>
      <td class="row1">{FILE_IMG}</td>
    </tr>
    <tr>
      <td style="width:50%;"><strong>{L_SIZE}</strong></td>
      <td class="row1">{SIZE_IMG}</td>
    </tr>
    <tr>
      <td style="width:50%;"><strong>{L_FILESIZE}</strong></td>
      <td class="row1">{FILESIZE_IMG}</td>
    </tr>
    <tr>
      <td style="width:50%;"><strong>{L_REGISTRATION_DATE}</strong></td>
      <td class="row1">{REGISTRATION_DATE_IMG}</td>
    </tr>
    <tr>
      <td style="width:50%;"><strong>{L_PATH}</strong></td>
      <td class="row1">{PATH_IMG}</td>
    </tr>
    <tr>
      <td style="width:50%;"><strong>{L_STORAGE_CATEGORY}</strong></td>
      <td class="row1">{STORAGE_CATEGORY_IMG}</td>
    </tr>
    <tr>
      <td style="width:50%;"><strong>{L_AUTHOR}</strong></td>
      <td class="row1"><input type="text" name="author" value="{AUTHOR_IMG}" /></td>
    </tr>
    <tr>
      <td style="width:50%;"><strong>{L_CREATION_DATE}</strong></td>
      <td class="row1"><input type="text" name="date_creation" value="{CREATION_DATE_IMG}" /></td>
    </tr>
    <tr>
      <td style="width:50%;"><strong>{L_KEYWORDS}</strong></td>
      <td class="row1"><input type="text" name="keywords" value="{KEYWORDS_IMG}" size="50" /></td>
    </tr>
    <tr>
      <td style="width:50%;"><strong>{L_COMMENT}</strong></td>
      <td class="row1"><textarea name="comment" rows="5" cols="50" style="overflow:auto">{COMMENT_IMG}</textarea></td>
    </tr>
    <tr>
      <td colspan="2"><div style="margin-bottom:0px">&nbsp;</div></td>
    </tr>
    <tr>
      <td colspan="2" align="center">
        <input type="submit" name="submit" value="{L_SUBMIT}" class="bouton" />
        <input type="reset" name="reset" value="{L_RESET}" class="bouton" />
      </td>
    </tr>
  </table>
</form>

<form name="form1" method="post" action="{F_ACTION}" style="text-align:center;width:800px;">

  <div style="clear:both;"></div>

  <div style="height:auto;">

    <div style="float:left;padding:10px;width:300px;">
      <span class="titreMenu">{L_CAT_ASSOCIATED}</span><br />
      <select style="height:auto;width:280px" name="cat_associated[]" multiple="multiple" size="10">
        <!-- BEGIN associated_option -->
        <option class="{associated_option.CLASS}" {associated_option.SELECTED} value="{associated_option.VALUE}">{associated_option.OPTION}</option>
        <!-- END associated_option -->
      </select>
    </div>

    <div style="float:left;padding-top:80px;padding-bottom:80px;text-align:center;width:160px;" >
      <input type="submit" value="&laquo;" name="associate" style="font-size:15px;" class="bouton" /><br/>
      <input type="submit" value="&raquo;" name="dissociate" style="font-size:15px;" class="bouton" />
    </div>

    <div style="float:right;padding:10px;width:300px;">
      <span class="titreMenu">{L_CAT_DISSOCIATED}</span><br />
      <select style="width:280px" name="cat_dissociated[]" multiple="multiple" size="10">
        <!-- BEGIN dissociated_option -->
        <option class="{dissociated_option.CLASS}" {dissociated_option.SELECTED} value="{dissociated_option.VALUE}">{dissociated_option.OPTION}</option>
        <!-- END dissociated_option -->
      </select>
    </div>

  </div>

  <div style="clear:both;"></div>

  <input type="reset" name="reset" value="{L_RESET}" class="bouton" />

</form>

<form name="form2" method="post" action="{F_ACTION}" style="text-align:center;width:800px;">

  <div style="clear:both;"></div>

  <div style="height:auto;">

    <div style="float:left;padding:10px;width:300px;">
      <span class="titreMenu">{L_REPRESENTS}</span><br />
      <select style="height:auto;width:280px" name="cat_elected[]" multiple="multiple" size="10">
        <!-- BEGIN elected_option -->
        <option class="{elected_option.CLASS}" {elected_option.SELECTED} value="{elected_option.VALUE}">{elected_option.OPTION}</option>
        <!-- END elected_option -->
      </select>
    </div>

    <div style="float:left;padding-top:80px;padding-bottom:80px;text-align:center;width:160px;" >
      <input type="submit" value="&laquo;" name="elect" style="font-size:15px;" class="bouton" /><br/>
      <input type="submit" value="&raquo;" name="dismiss" style="font-size:15px;" class="bouton" />
    </div>

    <div style="float:right;padding:10px;width:300px;">
      <span class="titreMenu">{L_DOESNT_REPRESENT}</span><br />
      <select style="width:280px" name="cat_dismissed[]" multiple="multiple" size="10">
        <!-- BEGIN dismissed_option -->
        <option class="{dismissed_option.CLASS}" {dismissed_option.SELECTED} value="{dismissed_option.VALUE}">{dismissed_option.OPTION}</option>
        <!-- END dismissed_option -->
      </select>
    </div>

  </div>

  <div style="clear:both;"></div>

  <input type="reset" name="reset" value="{L_RESET}" class="bouton" />

</form>
