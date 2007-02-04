<!-- BEGIN information -->
<div class="pleaseNote">{information.INFORMATION}</div>
<!-- END information -->

<div id="imageHeaderBar">
  <div class="browsePath">
    <a href="{U_HOME}" rel="home">{lang:home}</a>
    {LEVEL_SEPARATOR}{SECTION_TITLE}
    {LEVEL_SEPARATOR}{PICTURE_TITLE}
  </div>
  <div class="imageNumber">{PHOTO}</div>
  <!-- BEGIN title -->
  <h2>{TITLE}</h2>
  <!-- END title -->
</div>

<div id="imageToolBar">

<div class="randomButtons">
  <a href="{U_SLIDESHOW}" title="{lang:slideshow}" rel="nofollow"><img src="{pwg_root}{themeconf:icon_dir}/slideshow.png" class="button" alt="{lang:slideshow}"></a>
  <a href="{U_METADATA}" title="{lang:picture_show_metadata}"><img src="{pwg_root}{themeconf:icon_dir}/metadata.png" class="button" alt="{lang:picture_show_metadata}"></a>
<!-- BEGIN representative -->
  <a href="{representative.URL}" title="{lang:set as category representative}"><img src="{pwg_root}{themeconf:icon_dir}/representative.png" class="button" alt="{lang:representative}"/></a>
<!-- END representative -->
<!-- BEGIN favorite -->
  <a href="{favorite.U_FAVORITE}" title="{favorite.FAVORITE_HINT}"><img src="{favorite.FAVORITE_IMG}" class="button" alt="{favorite.FAVORITE_ALT}"></a>
<!-- END favorite -->
<!-- BEGIN download -->
  <a href="{download.U_DOWNLOAD}" title="{lang:download_hint}"><img src="{pwg_root}{themeconf:icon_dir}/save.png" class="button" alt="{lang:download}"></a>
<!-- END download -->
<!-- BEGIN admin -->
  <a href="{U_ADMIN}" title="{lang:link_info_image}"><img src="{pwg_root}{themeconf:icon_dir}/preferences.png" class="button" alt="{lang:link_info_image}"></a>
<!-- END admin -->
<!-- BEGIN caddie -->
  <a href="{caddie.URL}" title="{lang:add to caddie}"><img src="{pwg_root}{themeconf:icon_dir}/caddie_add.png" class="button" alt="{lang:caddie}"/></a>
<!-- END caddie -->
</div>

<div class="navButtons">
<!-- BEGIN last -->
  <a class="navButton prev" href="{last.U_IMG}" title="{lang:last_page} : {last.TITLE_IMG}" rel="last"><img src="{pwg_root}{themeconf:icon_dir}/last.png" class="button" alt="{lang:last_page}"></a>
<!-- END last -->
<!-- BEGIN next -->
  <a class="navButton next" href="{next.U_IMG}" title="{lang:next_page} : {next.TITLE_IMG}" rel="next"><img src="{pwg_root}{themeconf:icon_dir}/right.png" class="button" alt="{lang:next_page}"></a>
<!-- END next -->
  <a class="navButton up" href="{U_UP}" title="{lang:thumbnails}" rel="up"><img src="{pwg_root}{themeconf:icon_dir}/up.png" class="button" alt="{lang:home}"></a>
<!-- BEGIN previous -->
  <a class="navButton prev" href="{previous.U_IMG}" title="{lang:previous_page} : {previous.TITLE_IMG}" rel="prev"><img src="{pwg_root}{themeconf:icon_dir}/left.png" class="button" alt="{lang:previous_page}"></a>
<!-- END previous -->
<!-- BEGIN first -->
  <a class="navButton prev" href="{first.U_IMG}" title="{lang:first_page} : {first.TITLE_IMG}" rel="first"><img src="{pwg_root}{themeconf:icon_dir}/first.png" class="button" alt="{lang:first_page}"></a>
<!-- END first -->
</div>

</div> <!-- imageToolBar -->

<div id="theImage">
{ELEMENT_CONTENT}
<!-- BEGIN legend -->
<p>{legend.COMMENT_IMG}</p>
<!-- END legend -->
<!-- BEGIN stop_slideshow -->
<p>
  [ <a href="{stop_slideshow.U_SLIDESHOW}">{lang:slideshow_stop}</a> ]
</p>
<!-- END stop_slideshow -->
</div>

<!-- BEGIN previous -->
<a class="navThumb" id="thumbPrev" href="{previous.U_IMG}" title="{lang:previous_page} : {previous.TITLE_IMG}" rel="prev">
  <img src="{previous.IMG}" class="thumbLink" id="linkPrev" alt="{previous.TITLE_IMG}">
</a>
<!-- END previous -->
<!-- BEGIN next -->
<a class="navThumb" id="thumbNext" href="{next.U_IMG}" title="{lang:next_page} : {next.TITLE_IMG}" rel="next">
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
    <td class="label">{lang:Tags}</td>
    <td class="value">{INFO_TAGS}</td>
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
<form action="{rate.F_ACTION}" method="post" id="rateForm">
<div>{rate.SENTENCE} :
<!-- BEGIN rate_option -->
{rate.rate_option.SEPARATOR}
<!-- BEGIN my_rate -->
<input type="button" name="rate" value="{rate.rate_option.OPTION}" class="rateButtonSelected" />
<!-- END my_rate -->
<!-- BEGIN not_my_rate -->
<input type="submit" name="rate" value="{rate.rate_option.OPTION}" class="rateButton" />
<!-- END not_my_rate -->
<!-- END rate_option -->
<script type="text/javascript" src="{pwg_root}{themeconf:template_dir}/rating.js"></script>
</div>
</form>
<!-- END rate -->

<hr class="separation">

<!-- BEGIN comments -->
<div id="comments">
  <h2>[{comments.NB_COMMENT}] {lang:comments_title}</h2>

  <div class="navigationBar">{comments.NAV_BAR}</div>

  <!-- BEGIN comment -->
  <div class="comment">
    <!-- BEGIN delete -->
    <p class="userCommentDelete">
    <a href="{comments.comment.delete.U_COMMENT_DELETE}" title="{lang:comments_del}">
      <img src="{pwg_root}{themeconf:icon_dir}/delete.png" class="button" style="border:none;vertical-align:middle; margin-left:5px;" alt="[{lang:delete}]"/>
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
      <legend>{lang:comments_add}</legend>
      <!-- BEGIN author_field -->
      <label>{lang:upload_author}<input type="text" name="author"></label>
      <!-- END author_field -->
      <label>{lang:comment}<textarea name="content" rows="5" cols="80">{comments.add_comment.CONTENT}</textarea></label>
      <input type="hidden" name="key" value="{comments.add_comment.KEY}" />
      <input type="submit" class="submit" value="{lang:submit}">
    </fieldset>
  </form>
  <!-- END add_comment -->

</div>
<!-- END comments -->
