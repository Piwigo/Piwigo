<!-- DEV TAG: not smarty migrated -->
<div id="content">

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{U_HOME}" title="{lang:return to homepage}"><img src="{themeconf:icon_dir}/home.png" class="button" alt="{lang:home}"/></a></li>
    </ul>
    <h2>{lang:upload_title}</h2>
  </div>

  <!-- BEGIN upload_not_successful -->
  <form enctype="multipart/form-data" method="post" action="{F_ACTION}">
    <table style="width:80%;margin-left:auto;margin-right:auto;">
    <!-- BEGIN errors -->
        <tr>
        <td colspan="2">
          <div class="errors">
          <ul>
            <!-- BEGIN error -->
            <li>{upload_not_successful.errors.error.ERROR}</li>
            <!-- END error -->
          </ul>
          </div>
        </td>
        </tr>
    <!-- END errors -->
    <tr>
      <td colspan="2" class="menu">
      <div style="text-align:center;">{ADVISE_TITLE}</div>
      <ul>
        <!-- BEGIN advise -->
        <li>{upload_not_successful.advise.ADVISE}</li>
        <!-- END advise -->
      </ul>
      </td>
    </tr>
    <tr>
      <td colspan="2" align="center" style="padding:10px;">
      <input name="picture" type="file" value="" />
      </td>
    </tr>
    <!-- BEGIN fields -->
    <!-- username  -->
    <tr>
      <td class="menu">{lang:upload_username} <span style="color:red;">*</span></td>
      <td align="left" style="padding:10px;">
      <input name="username" type="text" value="{NAME}" />
      </td>
    </tr>
    <!-- mail address  -->
    <tr>
      <td class="menu">{lang:mail_address} <span style="color:red;">*</span></td>
      <td align="left" style="padding:10px;">
      <input name="mail_address" type="text" value="{EMAIL}" />
      </td>
    </tr>
    <!-- name of the picture  -->
    <tr>
      <td class="menu">{lang:upload_name}</td>
      <td align="left" style="padding:10px;">
      <input name="name" type="text" value="{NAME_IMG}" />
      </td>
    </tr>
    <!-- author  -->
    <tr>
      <td class="menu">{lang:upload_author}</td>
      <td align="left" style="padding:10px;">
      <input name="author" type="text" value="{AUTHOR_IMG}" />
      </td>
    </tr>
    <!-- date of creation  -->
    <tr>
      <td class="menu">{lang:upload_creation_date}</td>
      <td align="left" style="padding:10px;">
      <input name="date_creation" type="text" value="{DATE_IMG}" />
      </td>
    </tr>
    <!-- comment  -->
    <tr>
      <td class="menu">{lang:comment}</td>
      <td align="left" style="padding:10px;">
       <textarea name="comment" rows="3" cols="40" style="overflow:auto">{COMMENT_IMG}</textarea>
      </td>
    </tr>
    <!-- END fields -->
    <tr>
      <td colspan="2" align="center">
      <input class="submit" name="submit" type="submit" value="{lang:submit}" />
      </td>
    </tr>
    </table>
  </form>
  <!-- END upload_not_successful -->
  <!-- BEGIN upload_successful -->
  {lang:upload_successful}<br />
  <div style="text-align:center;">
    <a href="{U_RETURN}">[ {lang:home} ]</a>
  </div>
  <!-- END upload_successful -->
  <!-- BEGIN note -->
  <div style="text-align:left;"><span style="color:red;">*</span> : {lang:mandatory}</div>
  <!-- END note -->
</div> <!-- content -->
