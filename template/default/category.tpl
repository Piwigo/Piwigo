<table style="width:100%;">
  <tr>
	<td valign="top" style="width:1%;padding:10px;">
	  {T_START}100%{T_BEGIN}
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
			&nbsp;<a href="{category.U_LINK}"><span title='{L_HINT_CATEGORY}' style="{category.T_NAME}">{category.LINK_NAME}</span>
			&nbsp;<span class="menuInfoCat">[
			<!-- BEGIN subcat -->
			<span title="{category.NB_SUBCATS} {L_SUBCAT}">{category.NB_SUBCATS}</span>&nbsp;-
			<!-- END subcat -->
			<span title="{category.TOTAL_CAT} {L_IMG_AVAILABLE}">{category.TOTAL_CAT}</span>&nbsp;]</span></a>{category.CAT_ICON}<br />
		  <!-- END category -->

		  <div class="totalImages">[&nbsp;{NB_PICTURE}&nbsp;{L_TOTAL}&nbsp;]</div>
		  <!-- BEGIN favorites -->
		  <br />&nbsp;<img src="{T_COLLAPSED}" alt='' />&nbsp;<a href="{U_FAVORITE}"><span title="{L_FAVORITE_HINT}" style="font-weight:bold;">{L_FAVORITE}</span></a>&nbsp;<span class="menuInfoCat">[&nbsp;{favorites.NB_FAV}&nbsp;]</span>
		  <!-- END favorites -->
		  <br />&nbsp;<img src="{T_COLLAPSED}" alt='' />&nbsp;<span style="font-weight:bold;">{L_STATS}</span>
		  <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="{T_COLLAPSED}" alt='' />&nbsp;<a href="{U_MOST_VISITED}"><span title="{L_MOST_VISITED_HINT}" style="font-weight:bold;">{S_TOP}&nbsp;{L_MOST_VISITED}</span></a>
		  <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="{T_COLLAPSED}" alt='' />&nbsp;<a href="{U_RECENT}"><span title="{L_RECENT_HINT}" style="font-weight:bold;">{L_RECENT}</span></a> {T_SHORT}
		</div>
	  {T_END}
	  <div style="margin-bottom:5px;">&nbsp;</div>
	  {T_START}100%{T_BEGIN}
		<div class="titreMenu">{L_SUMMARY}</div>
		<div class="menu">
		  <!-- BEGIN summary -->
		  &nbsp;<img src="{T_COLLAPSED}" alt=''/>&nbsp;<a href="{summary.U_SUMMARY}" title="{summary.TITLE}">{summary.NAME}</a><br />
		  <!-- END summary -->
		  <!-- BEGIN upload -->
		  <br />&nbsp;<img src="{T_COLLAPSED}" alt=''/>&nbsp;<a href="{upload.U_UPLOAD}">{L_UPLOAD}</a>
		  <!-- END upload -->
		</div>
	  {T_END}
	</td>
	<td style="padding:5px;width:99%;" valign="top">
	  <table style="width:100%;">
		<tr>
		  <td align="center">
			{T_START}1%{T_BEGIN}
			  <div class="titrePage">{TITLE}</div>
			{T_END}
			<div style="margin-bottom:5px;">&nbsp;</div>
			<!-- BEGIN thumbnails -->
			<table class="thumbnail">
			  <!-- BEGIN line -->
			  <tr>
				<!-- BEGIN thumbnail -->
				<td valign="bottom" class="thumbnail">
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
		  </td>
		</tr>
		<tr>
		  <td align="left">
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
		  </td>
		</tr>
		<tr>
		  <td align="right">
			{T_START}1%{T_BEGIN}
			  <div class="info">
				<!-- BEGIN username -->
				{L_USER}&nbsp;{USERNAME}<br />
				<!-- END username -->
				{L_RECENT_IMAGE}&nbsp;{S_SHORT_PERIOD}&nbsp;{L_DAYS}
				  {T_SHORT}<br />
				{L_RECENT_IMAGE}&nbsp;{S_LONG_PERIOD}&nbsp;{L_DAYS}
				  {T_LONG}<br />
				{L_SEND_MAIL}&nbsp;<a href="mailto:{S_MAIL}?subject={L_TITLE_MAIL}"><span style="font-weight:bold;">{S_WEBMASTER}</span></a>
			  </div>
			{T_END}
		  </td>
		</tr>
	  </table>
	</td>
  </tr>
</table>