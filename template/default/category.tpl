<div align="center" style="padding:10px;"><img src="template/default/images/logo.jpg" width="360" height="100">
</div>
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
			 <!-- BEGIN bulletlink -->
				<a href="{category.bulletlink.U_BULLET_LINK}"><img src="{category.bulletlink.BULLET_IMAGE}" style="border:none;" alt="" /></a>
			 <!-- END bulletlink -->
			 <!-- BEGIN bulletnolink -->
				<img src="{category.bulletnolink.BULLET_IMAGE}" style="border:none;" alt="" />
			<!-- END bulletnolink -->
			&nbsp;<a href="{category.U_LINK}"><span title='{L_HINT_CATEGORY}' style="{category.T_NAME}">{category.LINK_NAME}</span></a>
			&nbsp;<span class="menuInfoCat">[
			<!-- BEGIN subcat -->
			<span title="{category.NB_SUBCATS} {L_SUBCAT}">{category.NB_SUBCATS}</span>&nbsp;-
			<!-- END subcat -->
			<span title="{category.TOTAL_CAT} {L_IMG_AVAILABLE}">{category.TOTAL_CAT}</span>&nbsp;]</span>{category.CAT_ICON}<br />
		  <!-- END category -->

		  <div class="totalImages">[&nbsp;{NB_PICTURE}&nbsp;{L_TOTAL}&nbsp;]</div>
		  <!-- BEGIN favorites -->
		  <br />&nbsp;<img src="{T_COLLAPSED}" alt='' />&nbsp;<a href="{U_FAVORITE}"><span title="{L_FAVORITE_HINT}" style="font-weight:bold;">{L_FAVORITE}</span></a>&nbsp;<span class="menuInfoCat">[&nbsp;{favorites.NB_FAV}&nbsp;]</span>
		  <!-- END favorites -->
		  <br />&nbsp;<img src="{T_COLLAPSED}" alt='' />&nbsp;<span style="font-weight:bold;">{L_STATS}</span>
		  <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="{T_COLLAPSED}" alt='' />&nbsp;<a href="{U_MOST_VISITED}"><span title="{L_MOST_VISITED_HINT}" style="font-weight:bold;">{TOP_VISITED}&nbsp;{L_MOST_VISITED}</span></a>
		  <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="{T_COLLAPSED}" alt='' />&nbsp;<a href="{U_RECENT}"><span title="{L_RECENT_HINT}" style="font-weight:bold;">{L_RECENT}</span></a> {T_SHORT}
		</div>
		<div class="titreMenu">{L_SUMMARY}</div>
		<div class="menu">
		  <!-- BEGIN summary -->
		  &nbsp;<img src="{T_COLLAPSED}" alt=''/>&nbsp;<a href="{summary.U_SUMMARY}" title="{summary.TITLE}">{summary.NAME}</a><br />
		  <!-- END summary -->
		  <!-- BEGIN upload -->
		  <br />&nbsp;<img src="{T_COLLAPSED}" alt=''/>&nbsp;<a href="{upload.U_UPLOAD}">{L_UPLOAD}</a>
		  <!-- END upload -->
		</div>
		<div class="titreMenu">{L_IDENTIFY}</div>
		 <div class="menu">
		 <!-- BEGIN login -->
		<form method="post" action="{F_IDENTIFY}">
		<input type="hidden" name="redirect" value="{U_REDIRECT}">
		{L_USERNAME}<br />
		<input type="text" name="username" size="15" value="" /><br />
		{L_PASSWORD}<br />
		<input type="password" name="password" size="15"><br /><br />
		<input type="submit" name="login" value="{L_SUBMIT}" class="bouton" />
		</form>
		<!-- END login -->
		<!-- BEGIN logout -->
		<p>{L_HELLO}&nbsp;{USERNAME}&nbsp;!</p>
		&nbsp;<img src="{T_COLLAPSED}" alt=""/>&nbsp;<a href="{U_LOGOUT}">{L_LOGOUT}</a><br />
		&nbsp;<img src="{T_COLLAPSED}" alt=''/>&nbsp;<a href="{U_PROFILE}" title="{L_PROFILE_HINT}">{L_PROFILE}</a><br />
		<!-- BEGIN admin -->
	    &nbsp;<img src="{T_COLLAPSED}" alt=''/>&nbsp;<a href="{U_ADMIN}" title="{L_ADMIN_HINT}">{L_ADMIN}</a><br />
		<!-- END admin -->
		<!-- END logout -->
		</div>
      </div>
	</td>
	<td style="padding:10px;width:99%;" valign="top">
	  <div class="home">
			<div class="titrePage">{TITLE}</div>
			<!-- BEGIN thumbnails -->
			<table valign="top" align="center" class="thumbnail">
			  <!-- BEGIN line -->
			  <tr>
				<!-- BEGIN thumbnail -->
				<td class="thumbnail">
				  <a href="{thumbnails.line.thumbnail.U_IMG_LINK}" class="back">
				  <img src="{thumbnails.line.thumbnail.IMAGE}"
					   alt="{thumbnails.line.thumbnail.IMAGE_ALT}"
					   title="{thumbnails.line.thumbnail.IMAGE_TITLE}"
					   class="imgLink" />
				  <br />
				  {thumbnails.line.thumbnail.IMAGE_NAME}</a>
				  {thumbnails.line.thumbnail.IMAGE_TS}
				  <!-- BEGIN nb_comments -->
				  <br />{thumbnails.line.thumbnail.NB_COMMENTS} {L_COMMENTS}
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
			  <div class="comments">{cat_infos.comment.COMMENTS}</div>
			  <!-- END comment -->
			  <div class="infoCat">
			  {L_NB_IMG} "{cat_infos.CAT_NAME}" : {cat_infos.NB_IMG_CAT}
			  </div>
			<!-- END cat_infos -->
      </div>
	</td>
  </tr>
</table>