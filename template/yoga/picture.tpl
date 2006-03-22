<!-- BEGIN information -->
<div class="pleaseNote">{information.INFORMATION}</div>
<!-- END information -->

<div id="imageHeaderBar">
  <div class="browsePath"><a href="{U_HOME}" rel="home">{L_HOME}</a>{LEVEL_SEPARATOR}{CATEGORY}</div>
  <div class="imageNumber">{PHOTO}</div>
  <!-- BEGIN title -->
  <h2>{TITLE}</h2>
  <!-- END title -->
  <hr class="separation">
</div>

<div id="imageToolBar">

<div class="randomButtons">
  <a href="{U_SLIDESHOW}" title="{L_SLIDESHOW}" rel="nofollow"><img src="{pwg_root}{themeconf:icon_dir}/slideshow.png" class="button" alt="{L_SLIDESHOW}"></a>
  <a href="{U_METADATA}" title="{L_PICTURE_METADATA}"><img src="{pwg_root}{themeconf:icon_dir}/metadata.png" class="button" alt="{L_PICTURE_METADATA}"></a>
<!-- BEGIN representative -->
  <a href="{representative.URL}" title="{lang:set as category representative}"><img src="{pwg_root}{themeconf:icon_dir}/representative.png" class="button" alt="{lang:representative}"/></a>
<!-- END representative -->
<!-- BEGIN favorite -->
  <a href="{favorite.U_FAVORITE}" title="{favorite.FAVORITE_HINT}"><img src="{favorite.FAVORITE_IMG}" class="button" alt="{favorite.FAVORITE_ALT}"></a>
<!-- END favorite -->
<!-- BEGIN download -->
  <a href="{download.U_DOWNLOAD}" title="{L_DOWNLOAD}"><img src="{pwg_root}{themeconf:icon_dir}/save.png" class="button" alt="{L_DOWNLOAD}"></a>
<!-- END download -->
<!-- BEGIN admin -->
  <a href="{U_ADMIN}" title="{L_ADMIN}"><img src="{pwg_root}{themeconf:icon_dir}/preferences.png" class="button" alt="{L_ADMIN}"></a>
<!-- END admin -->
<!-- BEGIN caddie -->
  <a href="{caddie.URL}" title="{lang:add to caddie}"><img src="{pwg_root}{themeconf:icon_dir}/caddie_add.png" class="button" alt="{lang:caddie}"/></a>
<!-- END caddie -->
</div>

<div class="navButtons">
<!-- BEGIN last -->
  <a class="navButton prev" href="{last.U_IMG}" rel="last"><img src="{pwg_root}{themeconf:icon_dir}/last.png" class="button" alt="{lang:last_page}"></a>
<!-- END last -->
<!-- BEGIN next -->
  <a class="navButton next" href="{next.U_IMG}" title="{L_NEXT_IMG}{next.TITLE_IMG}" rel="next"><img src="{pwg_root}{themeconf:icon_dir}/right.png" class="button" alt="next"></a>
<!-- END next -->
  <a class="navButton up" href="{U_UP}" title="{L_UP_HINT}" rel="up"><img src="{pwg_root}{themeconf:icon_dir}/up.png" class="button" alt="{L_UP_ALT}"></a>
<!-- BEGIN previous -->
  <a class="navButton prev" href="{previous.U_IMG}" title="{L_PREV_IMG}{previous.TITLE_IMG}" rel="prev"><img src="{pwg_root}{themeconf:icon_dir}/left.png" class="button" alt="previous"></a>
<!-- END previous -->
<!-- BEGIN first -->
  <a class="navButton prev" href="{first.U_IMG}" rel="first"><img src="{pwg_root}{themeconf:icon_dir}/first.png" class="button" alt="{lang:first_page}"></a>
<!-- END first -->
</div>

</div> <!-- imageToolBar -->

<div id="theImage">
<!-- BEGIN high -->
<a href="javascript:phpWGOpenWindow('{high.U_HIGH}','{high.UUID}','scrollbars=yes,toolbar=no,status=no,resizable=yes')">
<!-- END high -->
  <img src="{SRC_IMG}" style="width:{WIDTH_IMG}px;height:{HEIGHT_IMG}px;" alt="{ALT_IMG}">
<!-- BEGIN high -->
</a>
  <p>{L_PICTURE_HIGH}</p>
<!-- END high -->
<!-- BEGIN legend -->
<p>{legend.COMMENT_IMG}</p>
<!-- END legend -->
<!-- BEGIN stop_slideshow -->
<p>
  [ <a href="{stop_slideshow.U_SLIDESHOW}">{L_STOP_SLIDESHOW}</a> ]
</p>
<!-- END stop_slideshow -->
</div>

<!-- BEGIN previous -->
<a class="navThumb" id="thumbPrev" href="{previous.U_IMG}" title="{L_PREV_IMG}{previous.TITLE_IMG}" rel="prev">
  <img src="{previous.IMG}" class="thumbLink" id="linkPrev" alt="{previous.TITLE_IMG}">
</a>
<!-- END previous -->
<!-- BEGIN next -->
<a class="navThumb" id="thumbNext" href="{next.U_IMG}" title="{L_NEXT_IMG}{next.TITLE_IMG}" rel="next">
  <img src="{next.IMG}" class="thumbLink" id="linkNext" alt="{next.TITLE_IMG}">
</a>
<!-- END next -->

<table class="infoTable" summary="Some info about this picture">
  <tr>
    <td class="label">{lang:Author}</td>
    <td class="value">{INFO_AUTHOR}</td>
  </tr>
  <tr>
    <td class="label">{lang:Created on}</td>
    <td class="value">{INFO_CREATION_DATE}</td>
  </tr>
  <tr>
    <td class="label">{lang:Posted on}</td>
    <td class="value">{INFO_POSTED_DATE}</td>
  </tr>
  <tr>
    <td class="label">{lang:Dimensions}</td>
    <td class="value">{INFO_DIMENSIONS}</td>
  </tr>
  <tr>
    <td class="label">{lang:File}</td>
    <td class="value">{INFO_FILE}</td>
  </tr>
  <tr>
    <td class="label">{lang:Filesize}</td>
    <td class="value">{INFO_FILESIZE}</td>
  </tr>
  <tr>
    <td class="label">{lang:Keywords}</td>
    <td class="value">{INFO_KEYWORDS}</td>
  </tr>
  <tr>
    <td class="label">{lang:Categories}</td>
    <td class="value">
      <ul>
        <!-- BEGIN category -->
        <li>{category.LINE}</li>
        <!-- END category -->
      </ul>
    </td>
  </tr>
  <tr>
    <td class="label">{lang:Visits}</td>
    <td class="value">{INFO_VISITS}</td>
  </tr>
  <!-- BEGIN info_rate -->
  <tr>
    <td class="label">{lang:Average rate}</td>
    <td class="value">{info_rate.CONTENT}</td>
  </tr>
  <!-- END info_rate -->
</table>

<!-- BEGIN metadata -->
<table class="infoTable" summary="Some more (technical) info about this picture">
  <!-- BEGIN headline -->
  <tr>
    <th colspan="2">{metadata.headline.TITLE}</th>
  </tr>
  <!-- END headline -->
  <!-- BEGIN line -->
  <tr>
    <td class="label">{metadata.line.KEY}</td>
    <td class="value">{metadata.line.VALUE}</td>
  </tr>
  <!-- END line -->
</table>
<!-- END metadata -->

<!-- BEGIN rate -->
<p>
{rate.SENTENCE} :
<!-- BEGIN rate_option -->
{rate.rate_option.SEPARATOR} <a href="{rate.rate_option.URL}" rel="nofollow" {TAG_INPUT_ENABLED}>{rate.rate_option.OPTION}</a>
<!-- END rate_option -->
</p>
<!-- END rate -->

<hr class="separation">

<!-- BEGIN comments -->
<div id="comments">
<h2>[{comments.NB_COMMENT}] {L_COMMENT_TITLE}</h2>

<div class="navigationBar">{comments.NAV_BAR}</div>

<!-- BEGIN comment -->
<div class="comment">
  <!-- BEGIN delete -->
  <p class="userCommentDelete">
  <a href="{comments.comment.delete.U_COMMENT_DELETE}" title="{L_DELETE_COMMENT}">
    <img src="{pwg_root}{themeconf:icon_dir}/delete.png" class="button" style="border:none;vertical-align:middle; margin-left:5px;" alt="[{L_DELETE}]"/>
  </a>
  </p>
  <!-- END delete -->
  <p class="commentInfo"><span class="author">{comments.comment.COMMENT_AUTHOR}</span> - {comments.comment.COMMENT_DATE}</p>
  <blockquote>{comments.comment.COMMENT}</blockquote>
</div>
<!-- END comment -->

<!-- BEGIN add_comment -->
<form  method="post" action="{U_ADD_COMMENT}" class="filter" id="addComment">
  <fieldset>
    <legend>{L_ADD_COMMENT}</legend>

    <!-- BEGIN author_field -->
    <label>{L_AUTHOR}<input type="text" name="author"></label>
    <!-- END author_field -->

    <!-- BEGIN author_known -->
    <input type="hidden" name="author"  value="{comments.add_comment.author_known.KNOWN_AUTHOR}">
    <!-- END author_known -->

    <label>{L_COMMENT}<textarea name="content" rows="10" cols="80"></textarea></label>

    <input type="submit" value="{L_SUBMIT}" {TAG_INPUT_ENABLED}>
  </fieldset>
</form>
<!-- END add_comment -->
<!-- END comments -->

</div>
