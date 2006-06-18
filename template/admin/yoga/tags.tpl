<!-- $Id: comments.tpl 960 2005-12-03 17:33:38Z chrisaga $ -->
<div id="content">

  <div class="titrePage">
    <ul class="categoryActions">
      <li><a href="{U_HOME}" title="{lang:return to homepage}"><img src="{themeconf:icon_dir}/home.png" class="button" alt="{lang:home}"/></a></li>
    </ul>
    <h2>{lang:Tags}</h2>
  </div>

  <ul id="fullTagCloud">
    <!-- BEGIN tag -->
    <li><a href="{tag.URL}" class="{tag.CLASS}" title="{tag.TITLE}">{tag.NAME}</a></li>
    <!-- END tag -->
  </ul>

</div> <!-- content -->
