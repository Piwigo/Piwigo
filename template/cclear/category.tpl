<h1>PhpWebGallery demonstration site</h1>

<div id="menubar">
<dl>
  <dt><a href="{U_HOME}">{L_CATEGORIES}</a></dt>
  <dd>
    {MENU_CATEGORIES_CONTENT}
  </dd>
</dl>
<dl>
  <dt>{L_SPECIAL_CATEGORIES}</dt>
  <dd>
    <ul>
      <!-- BEGIN special_cat -->
      <li><a href="{special_cat.URL}" title="{special_cat.TITLE}">{special_cat.NAME}</a></li>
      <!-- END special_cat -->
    </ul>
  </dd>
</dl>
<dl>
  <dt>{L_SUMMARY}</dt>
  <dd>
    <ul>
      <!-- BEGIN summary -->
      <li><a href="{summary.U_SUMMARY}" title="{summary.TITLE}">{summary.NAME}</a></li>
      <!-- END summary -->
      <!-- BEGIN upload -->
      <li><a href="{upload.U_UPLOAD}">{L_UPLOAD}</a></li>
      <!-- END upload -->
    </ul>
  </dd>
</dl>
<dl>
  <dt>{L_IDENTIFY}</dt>
  <dd>
    <!-- BEGIN login -->
    <ul>
      <li><a href="{U_REGISTER}">{L_REGISTER}</a></li>
      <li><a href="{F_IDENTIFY}">{L_LOGIN}</a></li>
    </ul>
    <hr>
    <form method="post" action="{F_IDENTIFY}">
    <p>
    <input type="hidden" name="redirect" value="{U_REDIRECT}">
    <label for="username">{L_USERNAME}:</label>
    <input type="text" name="username" id="username" size="15" value="">
    <p>
    <label for="password">{L_PASSWORD}:</label>
    <input type="password" name="password" id="password" size="15">
    <p>
    <!-- BEGIN remember_me -->
    <input type="checkbox" name="remember_me" id="remember_me" value="1">
    <label for="remember_me">{L_REMEMBER_ME}</label>
    <!-- END remember_me -->
    <p>
    <input type="submit" name="login" value="{L_SUBMIT}" class="bouton">
    </form>
    <!-- END login -->
    <!-- BEGIN logout -->
    <p>{L_HELLO}&nbsp;{USERNAME}&nbsp;!</p>
    <ul>
      <li><a href="{U_LOGOUT}">{L_LOGOUT}</a></li>
      <li><a href="{U_PROFILE}" title="{L_PROFILE_HINT}">{L_PROFILE}</a></li>
      <!-- BEGIN admin -->
      <li><a href="{U_ADMIN}" title="{L_ADMIN_HINT}">{L_ADMIN}</a></li>
      <!-- END admin -->
    </ul>
    <!-- END logout -->
  </dd>
</dl>
</div> <!-- menubar -->

<div id="categoryContent">

<h2>{TITLE}</h2>

<!-- BEGIN calendar -->
<div class="navigationBar">{calendar.YEARS_NAV_BAR}</div>
<div class="navigationBar">{calendar.MONTHS_NAV_BAR}</div>
<!-- END calendar -->

<!-- BEGIN thumbnails -->
<ul class="thumbnails">
  <!-- BEGIN line -->
  <!-- BEGIN thumbnail -->
  <li><span><a href="{thumbnails.line.thumbnail.U_IMG_LINK}"><img class="thumbnail" src="{thumbnails.line.thumbnail.IMAGE}"
    alt="{thumbnails.line.thumbnail.IMAGE_ALT}"
    title="{thumbnails.line.thumbnail.IMAGE_TITLE}"></a><br>
    <a href="{thumbnails.line.thumbnail.U_IMG_LINK}">{thumbnails.line.thumbnail.IMAGE_NAME}</a>
  {thumbnails.line.thumbnail.IMAGE_TS}
  <br>
  <!-- BEGIN nb_comments -->
  {thumbnails.line.thumbnail.nb_comments.NB_COMMENTS} {L_COMMENT}
  <!-- END nb_comments -->
  </span></li>
  <!-- END thumbnail -->
  <!-- END line -->

</ul>
<!-- END thumbnails -->

<!-- BEGIN cat_infos -->
<!-- BEGIN navigation -->
<div class="navigationBar">
{cat_infos.navigation.NAV_BAR}
</div>
<!-- END navigation -->
<!-- BEGIN comment -->
<div class="additional_info">{cat_infos.comment.COMMENTS}</div>
<!-- END comment -->
<!-- END cat_infos -->

</div> <!-- categoryContent -->
