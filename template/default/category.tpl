<div style="padding:3px;"><img src="template/default/images/logo.jpg"></div>
<table style="width:100%;">
  <tr>
    <td valign="top" style="padding:0px 10px 0px 10px;width:1%;">
      <div class="table1">
        <div class="titreMenu">
          <a href="{U_HOME}">{L_CATEGORIES}</a>
        </div>
        <div class="menu">
            {MENU_CATEGORIES_CONTENT}
        <div class="totalImages">[&nbsp;{NB_PICTURE}&nbsp;{L_TOTAL}&nbsp;]</div>
		</div>
		<div class="titreMenu">{L_SPECIAL_CATEGORIES}</div>
		<div class="menu">
		    <ul class="menu">
                      <!-- BEGIN special_cat -->
                      <li><a href="{special_cat.URL}" title="{special_cat.TITLE}">{special_cat.NAME}</a></li>
                      <!-- END special_cat -->
		    </ul>
		</div>
		<div class="titreMenu">{L_SUMMARY}</div>
		<div class="menu">
		<ul class="menu">
		  <!-- BEGIN summary -->
		  <li><a href="{summary.U_SUMMARY}" title="{summary.TITLE}">{summary.NAME}</a></li>
		  <!-- END summary -->
		  <!-- BEGIN upload -->
		  <li><a href="{upload.U_UPLOAD}">{L_UPLOAD}</a></li>
		  <!-- END upload -->
		</ul>
		</div>
		<div class="titreMenu">{L_IDENTIFY}</div>
		 <div class="menu">
		 <!-- BEGIN login -->
		<ul class="menu">
		  <li><a href="{U_REGISTER}">{L_REGISTER}</a></li>
			<li><a href="{F_IDENTIFY}">{L_LOGIN}</a></li>
		</ul>
		<hr />
		<form method="post" action="{F_IDENTIFY}">
		<input type="hidden" name="redirect" value="{U_REDIRECT}">
		{L_USERNAME}<br />
		<input type="text" name="username" size="15" value="" /><br />
		{L_PASSWORD}<br />
		<input type="password" name="password" size="15"><br />
                <!-- BEGIN remember_me -->
                <input type="checkbox" name="remember_me" value="1" /> {L_REMEMBER_ME}<br />
                <!-- END remember_me -->
		<input type="submit" name="login" value="{L_SUBMIT}" class="bouton" />
		</form>
		<!-- END login -->
		<!-- BEGIN logout -->
		<p>{L_HELLO}&nbsp;{USERNAME}&nbsp;!</p>
		<ul class="menu">
		  <li><a href="{U_LOGOUT}">{L_LOGOUT}</a></li>
		  <li><a href="{U_PROFILE}" title="{L_PROFILE_HINT}">{L_PROFILE}</a></li>
		<!-- BEGIN admin -->
	      <li><a href="{U_ADMIN}" title="{L_ADMIN_HINT}">{L_ADMIN}</a></li>
		<!-- END admin -->
		</ul>
		<!-- END logout -->
		</div>
      </div>
	</td>
	<td style="padding:0px 10px 0px 10px; width:99%;" valign="top">
	  <div class="home">
			<div class="titrePage">{TITLE}</div>
			<!-- BEGIN calendar -->
               <div class="navigationBar">{calendar.YEARS_NAV_BAR}</div>
			  <div class="navigationBar">{calendar.MONTHS_NAV_BAR}</div>
			<!-- END calendar -->
			<!-- BEGIN thumbnails -->
			<table valign="top" align="center" class="thumbnail">
			  <!-- BEGIN line -->
			  <tr>
				<!-- BEGIN thumbnail -->
				<td class="thumbnail">
				  <a href="{thumbnails.line.thumbnail.U_IMG_LINK}">
				  <img src="{thumbnails.line.thumbnail.IMAGE}"
					   alt="{thumbnails.line.thumbnail.IMAGE_ALT}"
					   title="{thumbnails.line.thumbnail.IMAGE_TITLE}"
					   class="thumbLink" />
				  <br />
				  <span class="{thumbnails.line.thumbnail.IMAGE_STYLE}">{thumbnails.line.thumbnail.IMAGE_NAME}</span></a>
				  {thumbnails.line.thumbnail.IMAGE_TS}
				  <!-- BEGIN nb_comments -->
				  <br />{thumbnails.line.thumbnail.nb_comments.NB_COMMENTS} {L_COMMENT}
				  <!-- END nb_comments -->
				</td>
				<!-- END thumbnail -->
			  </tr>
			  <!-- END line -->
			</table>
			<!-- END thumbnails -->
		<br />
			<!-- BEGIN cat_infos -->
			  <!-- BEGIN navigation -->
			  <div class="navigationBar">{cat_infos.navigation.NAV_BAR}</div>
			  <!-- END navigation -->
			  <!-- BEGIN comment -->
			  <div class="info">{cat_infos.comment.COMMENTS}</div>
			  <!-- END comment -->
			<!-- END cat_infos -->
      </div>
	</td>
  </tr>
</table>
