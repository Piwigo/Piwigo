<div align="center" style="padding:10px;"><img src="template/default/images/logo.jpg" width="360" height="100"></div>
<table style="width:100%;">
  <tr>
    <td valign="top" style="padding:10px;width:1%;">
      <div class="table1">
        <div class="titreMenu">
          <a href="{U_HOME}">{L_CATEGORIES}</a>
        </div>
        <div class="menu">
          <!-- BEGIN category -->
            {category.INDENT}
            <img src="{category.BULLET_IMAGE}" style="border:none;" alt="" />
            <a href="{category.U_LINK}"><span title='{L_HINT_CATEGORY}' style="{category.T_NAME}">{category.LINK_NAME}</span></a>
            <!-- BEGIN infocat -->
            <span class="menuInfoCat">[<span title="{category.infocat.TOTAL_CAT} {L_IMG_AVAILABLE}">{category.infocat.TOTAL_CAT}</span>]</span>
	    {category.infocat.CAT_ICON}
            <!-- END infocat -->
            <br />
          <!-- END category -->
        <div class="totalImages">[&nbsp;{NB_PICTURE}&nbsp;{L_TOTAL}&nbsp;]</div>
		  <br />
		  <ul class="menu">
		    <!-- BEGIN favorites -->
		    <li><a href="{U_FAVORITE}"><span title="{L_FAVORITE_HINT}">{L_FAVORITE}</span></a>&nbsp;<span class="menuInfoCat">[&nbsp;{favorites.NB_FAV}&nbsp;]</span></li>
		    <!-- END favorites -->
		    <li><span style="font-weight:bold;">{L_SPECIAL_CATEGORIES}</span></li>
		    <ul class="menu">
		      <li><a href="{U_MOST_VISITED}"><span title="{L_MOST_VISITED_HINT}">{TOP_VISITED}&nbsp;{L_MOST_VISITED}</span></a></li>
		      <li><a href="{U_RECENT_PICS}"><span title="{L_RECENT_PICS_HINT}">{L_RECENT_PICS}</span></a> {T_SHORT}</li>
		      <li><a href="{U_RECENT_CATS}"><span title="{L_RECENT_CATS_HINT}">{L_RECENT_CATS}</span></a></li>
		      <li><a href="{U_CALENDAR}"><span title="{L_CALENDAR_HINT}">{L_CALENDAR}</span></a></li>
		    </ul>
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
		<form method="post" action="{F_IDENTIFY}">
		<input type="hidden" name="redirect" value="{U_REDIRECT}">
		{L_USERNAME}<br />
		<input type="text" name="username" size="15" value="" /><br />
		{L_PASSWORD}<br />
		<input type="password" name="password" size="15"><br />
		<input type="submit" name="login" value="{L_SUBMIT}" class="bouton" />
		</form>
		<!-- END login -->
		<!-- BEGIN logout -->
		<p>{L_HELLO}&nbsp;{USERNAME}&nbsp;!</p>
		&nbsp;<img src="{T_COLLAPSED}" alt="&gt;"/>&nbsp;<a href="{U_LOGOUT}">{L_LOGOUT}</a><br />
		&nbsp;<img src="{T_COLLAPSED}" alt="&gt;"/>&nbsp;<a href="{U_PROFILE}" title="{L_PROFILE_HINT}">{L_PROFILE}</a><br />
		<!-- BEGIN admin -->
	    &nbsp;<img src="{T_COLLAPSED}" alt="&gt;"/>&nbsp;<a href="{U_ADMIN}" title="{L_ADMIN_HINT}">{L_ADMIN}</a><br />
		<!-- END admin -->
		<!-- END logout -->
		</div>
      </div>
	</td>
	<td style="padding:10px;width:99%;" valign="top">
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
				  <!-- BEGIN bullet -->
				  <img src="./template/default/theme/collapsed.gif" style="border:none;" alt="&gt;" />
				  <!-- END bullet -->
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
