<!-- BEGIN errors -->
<div class="errors">
<ul>
  <!-- BEGIN error -->
  <li>{errors.error.ERROR}</li>
  <!-- END error -->
</ul>
</div>
<!-- END errors -->
<div class="admin">{TITLE_IMG} [ {DIR_IMG} &gt; {FILE_IMG} ]</div>
<form method="post" action="{F_ACTION}">
  <table style="width:100%;">
    <tr valign="top">
      <td style="width:1px;">
	  <a href="{URL_IMG}" class="thumbnail"><img src="{TN_URL_IMG}" alt="" class="miniature" /></a>
	  </td>
      <td>
        <table>
          <tr>
            <td>{L_UPLOAD_NAME} :</td>
            <td><input type="text" name="name" value="{NAME_IMG}" /> [ {L_DEFAULT} : {DEFAULT_NAME_IMG} ]</td>
          </tr>
          <tr>
            <td>{L_FILE} :</td>
            <td>{FILE_IMG}</td>
          </tr>
          <tr>
            <td>{L_SIZE} :</td>
            <td>{SIZE_IMG}</td>
          </tr>
          <tr>
            <td>{L_FILESIZE} :</td>
            <td>{FILESIZE_IMG}</td>
          </tr>
          <tr>
            <td>{L_REGISTRATION_DATE} :</td>
            <td>{REGISTRATION_DATE_IMG}</td>
          </tr>
          <tr>
            <td>{L_AUTHOR} :</td>
            <td><input type="text" name="author" value="{AUTHOR_IMG}" /></td>
          </tr>
          <tr>
            <td>{L_CREATION_DATE} :</td>
            <td><input type="text" name="date_creation" value="{CREATION_DATE_IMG}" /></td>
          </tr>
          <tr>
            <td>{L_KEYWORDS} :</td>
            <td><input type="text" name="keywords" value="{KEYWORDS_IMG}" size="50" /></td>
          </tr>
          <tr>
            <td>{L_COMMENT} :</td>
            <td><textarea name="comment" rows="5" cols="50" style="overflow:auto">{COMMENT_IMG}</textarea></td>
          </tr>
          <tr>
            <td valign="top">{L_CATEGORIES} :</td>
            <td>
              <select style="width:280px" name="cat_data[]" multiple="multiple" size="5">
				  {ASSOCIATED_CATEGORIES}
			  </select>
			</td>
		  </tr>
          <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                  <td>{L_INFOIMAGE_ASSOCIATE}</td>
				  <td>
                    <!-- BEGIN associate_LOV -->
                    <select name="associate">
                      <!-- BEGIN associate_cat -->
                      <option value="{associate_LOV.associate_cat.VALUE_CAT}">{associate_LOV.associate_cat.VALUE_CONTENT}</option>
                      <!-- END associate_cat -->
                    </select>
                    <!-- END associate_LOV -->
                    <!-- BEGIN associate_text -->
                    <input type="text" name="associate" />
                    <!-- END associate_text -->
                    </select>
                  </td>
                </tr>
              </table>
      </td>
    </tr>
    <tr>
      <td colspan="2"><div style="margin-bottom:0px">&nbsp;</div></td>
    </tr>
    <tr>
      <td colspan="2" align="center">
        <input type="submit" name="submit" value="{L_SUBMIT}" class="bouton" />
      </td>
    </tr>
  </table>
</form>
