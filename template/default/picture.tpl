    <!-- BEGIN information -->
    <div class="information">{information.INFORMATION}</div>
    <!-- END information -->
    <!-- BEGIN start_slideshow -->
    <div style="text-align:center;margin-bottom:5px;">
      [ {L_SLIDESHOW} :
      <!-- BEGIN second -->
      <a href="{start_slideshow.second.U_SLIDESHOW}" class="back" style="font-weight:bold;">{start_slideshow.second.SLIDESHOW_SPEED}</a>
      <!-- END second -->
      {L_TIME} ]
    </div>
    <!-- END start_slideshow -->
    <!-- BEGIN stop_slideshow -->
    <div style="text-align:center;margin-bottom:5px;">
      [ <a href="{stop_slideshow.U_SLIDESHOW}" class="back" style="font-weight:bold;">{L_STOP_SLIDESHOW}</a> ]
    </div>
    <!-- END stop_slideshow -->
    <table style="width:100%;height:100%;">
      <tr align="center" valign="middle">
        <td style="width:30%;">
		<!-- BEGIN previous -->
		  <a href="{U_PREV_IMG}" title="{L_PREV_IMG}{PREV_TITLE_IMG}"><img src="{PREV_IMG}" class="imgLink" style="margin-right:10px;margin-left:5px;" alt="{PREV_TITLE_IMG}"/></a></td>
		<!-- END previous -->
        <td style="width:40%;">{T_START}1%{T_BEGIN}
        <div class="titrePage">{TITLE}</div>
        {T_END} </td>
        <td style="width:30%;">
		<!-- BEGIN next -->
		  <a href="{U_NEXT_IMG}" title="{L_NEXT_IMG}{NEXT_TITLE_IMG}"><img src="{NEXT_IMG}" class="imgLink" style="margin-right:10px;margin-left:5px;" alt="{NEXT_TITLE_IMG}"/></a></td>
		<!-- END next -->
	    </td>
      </tr>
      <tr align="center" valign="middle">
        <td colspan="3" style="width:100%;"> 
          {T_START}1%{T_BEGIN}
          <a href="{U_HOME}">
            <img class="imgLink" style="margin:10px;width:{WIDTH_IMG}px;height:{HEIGHT_IMG}px;border:1px solid" src="{SRC_IMG}" alt="{ALT_IMG}"/>
          </a>
          <div class="commentImage">{COMMENT_IMG}</div>
          <table style="width:100%;">   
            <tr>
              <td align="center">
                <table style="margin:auto;margin-top:5px;margin-bottom:5px;">
                  <!-- BEGIN info_line -->
                  <tr>
                     <td class="menu" style="font-weight:bold;">{info_line.INFO} : </td>
                     <td class="menu" style="text-align:right;">{info_line.VALUE}</td>
                  </tr>
                  <!-- END info_line -->
                </table>
              </td>
            </tr>
          </table>
          <!-- BEGIN favorite -->
          <div class="menu" style="text-align:center;margin:5px;">
          <a href="{favorite.U_FAVORITE}" title="{favorite.FAVORITE_HINT}">
  			<img src="{favorite.FAVORITE_IMG}" style="border:none;margin-left:5px;" alt="" />{favorite.FAVORITE_ALT}
		  </a>
          </div>
          <!-- END favorite -->
          <!-- BEGIN modification -->
          <div class="menu" style="text-align:center;margin:5px;">
            [ <a href="{U_ADMIN}">{L_ADMIN}</a> ]
          </div>
          <!-- END modification -->
          {T_END}
          <div style="text-align:center;">{L_BACK}</div>
        </td>
        <td>&nbsp;</td>
      </tr>
      <!-- BEGIN comments -->
      <tr align="center" valign="middle">
        <td colspan="5">
          {T_START}100%{T_BEGIN}
            <table style="width:100%;">
              <tr align="center">
                <td>
                  <div class="commentsTitle">
                    [{comments.NB_COMMENT}] {L_COMMENT_TITLE}
                  </div>
                  <div class="commentsNavigationBar">{comments.NAV_BAR}</div>
                  <!-- BEGIN comment -->
                  <table class="tableComment">
                    <tr>
                      <td rowspan="2" valign="top" class="cellAuthor">
                        <div class="commentsAuthor">{comments.comment.COMMENT_AUTHOR}</div>
                      </td>
                      <td align="right" class="cellInfo">
                        <div class="commentsInfos">{comments.comment.COMMENT_DATE}
						<!-- BEGIN delete -->
						  <a href="{comments.comment.delete.U_COMMENT_DELETE}" title="{L_DELETE_COMMENT}"><img src="{T_DEL_IMG}" style="border:none;margin-left:5px;" alt="[{L_DELETE}]"/></a>
						<!-- END delete -->
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="commentsContent">{comments.comment.COMMENT}</div>
                      </td>
                    </tr>
                  </table>
                  <!-- END comment -->
                  <div class="commentsNavigationBar">{comments.NAV_BAR}</div>
                </td>
              </tr>
            </table>
            <!-- BEGIN add_comment -->
            <form method="post" action="{U_ADD_COMMENT}">
              <table style="width:100%;">
                <tr align="center">
                  <td>
                    <div class="commentsTitle">{L_ADD_COMMENT}</div>
                    <!-- BEGIN author_field -->
                    <div class="menu">{L_AUTHOR} : <input type="text" name="author" style="margin-top:5px;"/></div>
                    <!-- END author_field -->
                    <!-- BEGIN author_known -->
                    <input type="hidden" name="author" value="{comments.add_comment.author_known.KNOWN_AUTHOR}" />
                    <!-- END author_known -->
                    <textarea name="content" rows="10" cols="50" style="overflow:auto;width:450px;margin:10px;"></textarea><br />
                    <input type="submit" value="{L_SUBMIT}" class="bouton" />
                  </td>
                </tr>
              </table>
            </form>
            <!-- END add_comment -->
          {T_END}
        </td>
      </tr>
      <!-- END comments -->
    </table>
