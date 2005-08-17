<h1>{lang:title_picmod}</h1>

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

<form name="form1" method="post" action="{F_ACTION}">
  <fieldset>
    <legend>{lang:Association to categories}</legend>

    <table class="doubleSelect">
      <tr>
        <td>
          <h3>{L_CAT_ASSOCIATED}</h3>
          <select class="categoryList" name="cat_associated[]" multiple="multiple" size="30">
            <!-- BEGIN associated_option -->
            <option {associated_option.SELECTED} value="{associated_option.VALUE}">{associated_option.OPTION}</option>
            <!-- END associated_option -->
          </select>
          <p><input type="submit" value="&raquo;" name="dissociate" style="font-size:15px;"/></p>
        </td>

        <td>
          <h3>{L_CAT_DISSOCIATED}</h3>
          <select class="categoryList" name="cat_dissociated[]" multiple="multiple" size="30">
            <!-- BEGIN dissociated_option -->
            <option {dissociated_option.SELECTED} value="{dissociated_option.VALUE}">{dissociated_option.OPTION}</option>
            <!-- END dissociated_option -->
          </select>
          <p><input type="submit" value="&laquo;" name="associate" style="font-size:15px;" /></p>
        </td>
      </tr>
    </table>

  </fieldset>
</form>

<form name="form2" method="post" action="{F_ACTION}">
  <fieldset>
    <legend>{lang:Representation of categories}</legend>

    <table class="doubleSelect">
      <tr>
        <td>
          <h3>{L_REPRESENTS}</h3>
          <select class="categoryList" name="cat_elected[]" multiple="multiple" size="30">
            <!-- BEGIN elected_option -->
            <option {elected_option.SELECTED} value="{elected_option.VALUE}">{elected_option.OPTION}</option>
            <!-- END elected_option -->
          </select>
          <p><input type="submit" value="&raquo;" name="dismiss" style="font-size:15px;"/></p>
        </td>

        <td>
          <h3>{L_DOESNT_REPRESENT}</h3>
          <select class="categoryList" name="cat_dismissed[]" multiple="multiple" size="30">
            <!-- BEGIN dismissed_option -->
            <option {dismissed_option.SELECTED} value="{dismissed_option.VALUE}">{dismissed_option.OPTION}</option>
            <!-- END dismissed_option -->
          </select>
          <p><input type="submit" value="&laquo;" name="elect" style="font-size:15px;" /></p>
        </td>
      </tr>
    </table>

  </fieldset>
</form>
