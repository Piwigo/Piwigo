<!-- BEGIN information -->
<div class="information">{information.INFORMATION}</div>
<!-- END information -->
<div class="titrePage">
  <div id="gauche"><a href="{U_HOME}">{L_HOME}</a>{LEVEL_SEPARATOR}{CATEGORY}</div>
  <div id="centre" class="nameImage">{TITLE}</div>
  <div id="droite">{PHOTO}</div>
</div>

<div id="imgBarMenu">
<div class="imgMenu" id="left">
  <a href="{U_UP}" title="{L_UP_HINT}">
	<img src="template/default/theme/categories.gif" alt="{L_UP_ALT}" />
  </a>
</div>
<div class="imgMenu" id="left">
  <a href="{U_SLIDESHOW}" title="{L_SLIDESHOW}">
	<img src="template/default/theme/slideshow.gif" alt="{L_SLIDESHOW}" />
  </a>
</div>
<div class="imgMenu" id="left">
  <a href="{U_METADATA}" title="{L_PICTURE_METADATA}">
	<img src="template/default/theme/metadata.gif" alt="{L_PICTURE_METADATA}" />
  </a>
</div>
<!-- BEGIN favorite -->
<div class="imgMenu" id="right">
  <a href="{favorite.U_FAVORITE}" title="{favorite.FAVORITE_HINT}">
	<img src="{favorite.FAVORITE_IMG}" alt="{favorite.FAVORITE_ALT}" />
  </a>
</div>
<!-- END favorite -->
<!-- BEGIN download -->
<div class="imgMenu" id="right">
  <a href="{download.U_DOWNLOAD}" title="{L_DOWNLOAD}">
	<img src="template/default/theme/download.gif" alt="{L_DOWNLOAD}" />
  </a>
</div>
<!-- END download -->
<!-- BEGIN admin -->
<div class="imgMenu" id="right">
  <a href="{U_ADMIN}" title="{L_ADMIN}">
	<img src="template/default/theme/properties.gif" alt="{L_ADMIN}" />
  </a>
</div>
<!-- END admin -->
</div>
<!-- BEGIN high -->
<a href="javascript:phpWGOpenWindow('{high.U_HIGH}','{high.UUID}','scrollbars=yes,toolbar=yes,status=yes,resizable=yes,width={high.WIDTH_IMG},height={high.HEIGHT_IMG}')">
<!-- END high -->
  <img class="image" src="{SRC_IMG}" style="width:{WIDTH_IMG}px;height:{HEIGHT_IMG}px;" alt="{ALT_IMG}"/>
<!-- BEGIN high -->
</a>
<div style="text-align:center;font-weight:bold;">{L_PICTURE_HIGH}</div>
<!-- END high -->
<!-- BEGIN legend -->
<div class="commentImage">{legend.COMMENT_IMG}</div>
<!-- END legend -->

<!-- BEGIN stop_slideshow -->
<div style="text-align:center;margin-bottom:5px;">
  [ <a href="{stop_slideshow.U_SLIDESHOW}" class="back" style="font-weight:bold;">{L_STOP_SLIDESHOW}</a> ]
</div>
<!-- END stop_slideshow -->

<div id="gauche">
&nbsp;
  <!-- BEGIN previous -->
	<a class="none" href="{previous.U_IMG}" title="{L_PREV_IMG}{previous.TITLE_IMG}">
	<img style="border:none;" width="30" height="100" src="template/default/theme/left-arrow.jpg" alt="" />
	  <img src="{previous.IMG}" class="thumbLink" style="margin-right:10px;margin-left:5px;" alt="{previous.TITLE_IMG}"/>
	  </a>
  <!-- END previous -->
</div>
<div id="centre">
  <table style="margin:auto;margin-top:5px;margin-bottom:5px;">
	  <!-- BEGIN info_line -->
	  <tr>
		 <td class="menu" style="font-weight:bold;">{info_line.INFO} : </td>
		 <td class="menu" style="text-align:right;">{info_line.VALUE}</td>
	  </tr>
	  <!-- END info_line -->
  </table>
</div>
<div id="droite">
  <!-- BEGIN next -->
  <a  class="none" href="{next.U_IMG}" title="{L_NEXT_IMG}{next.TITLE_IMG}">
	  <img class="thumbLink" src="{next.IMG}" style="margin-right:10px;margin-left:5px;" alt="{next.TITLE_IMG}"/>
	  <img style="border:none;" class="thumbLink" width="30" height="100" src="template/default/theme/right-arrow.jpg" alt="" />
	  </a>
  <!-- END next -->
  &nbsp;
</div>
<div style="clear:both"></div>
         <!-- BEGIN metadata -->
          <table class="metadata">
            <!-- BEGIN headline -->
            <tr>
              <th colspan="2">{metadata.headline.TITLE}</th>
            </tr>
            <!-- END headline -->
            <!-- BEGIN line -->
            <tr>
              <td>{metadata.line.KEY}</td>
              <td>{metadata.line.VALUE}</td>
            </tr>
            <!-- END line -->
          </table>
          <!-- END metadata -->

          <!-- BEGIN rate -->
          <div>
            {rate.SENTENCE} :
            <!-- BEGIN rate_option -->
            {rate.rate_option.SEPARATOR} <a href="{rate.rate_option.URL}">{rate.rate_option.OPTION}</a>
            <!-- END rate_option -->
          </div>
          <!-- END rate -->
         
<table class="tablecompact">
      <!-- BEGIN comments -->
      <tr align="center" valign="middle">
        <td colspan="3" class="table2">
                  <div class="commentTitle">
                    [{comments.NB_COMMENT}] {L_COMMENT_TITLE}
                  </div>
                  <div class="commentsNavigationBar">{comments.NAV_BAR}</div>
				  <table class="tablecompact">
                  <!-- BEGIN comment -->
				    <tr class="throw">
					  <td class="throw">
					  {comments.comment.COMMENT_AUTHOR}
					  </td>
					  <td colspan="2" class="commentDate">
					  {comments.comment.COMMENT_DATE}
					<!-- BEGIN delete -->
					  <a href="{comments.comment.delete.U_COMMENT_DELETE}" title="{L_DELETE_COMMENT}"><img src="template/default/theme/delete.gif" style="border:none;vertical-align:middle; margin-left:5px;" alt="[{L_DELETE}]"/></a>
					<!-- END delete -->
					  </td>
					</tr>
					<tr class="row1">
					  <td class="comment" colspan="3">{comments.comment.COMMENT}</td>
					</tr>
                  <!-- END comment -->
            <!-- BEGIN add_comment -->
			<tr class="throw">
			  <td colspan="3">{L_ADD_COMMENT}</td>
			</tr>
			<form  method="post" action="{U_ADD_COMMENT}">
  		    <tr class="row1">
			  <td class="comment" >
                    <!-- BEGIN author_field -->
                    {L_AUTHOR}</td><td colspan="2"><input type="text" name="author" />
					</td></tr>
					<tr class="row1">
					<td class="comment" >
                    <!-- END author_field -->
                    <!-- BEGIN author_known -->
                    <input type="hidden" name="author"  value="{comments.add_comment.author_known.KNOWN_AUTHOR}" />
                    <!-- END author_known -->
                    {L_COMMENT}</td>
					<td style="width:100%;">
					<input name="content" type="text" maxlength="200" style="width:100%;" value="" /></td><td>
					<input type="submit" value="{L_SUBMIT}" class="bouton" />
			  </td>
			 </tr>
			 </form>
           	</table>
            <!-- END add_comment -->
        </td>
      </tr>
      <!-- END comments -->
    </table>
