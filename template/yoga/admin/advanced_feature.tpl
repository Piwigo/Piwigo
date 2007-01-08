<!-- $Id: advanced_feature.tpl 1111 2006-03-28 21:05:12Z rub $ -->
<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{lang:Advanced_features}</h2>
</div>

<ul>
  <!-- BEGIN advanced_features -->
    <!-- BEGIN advanced_feature -->
      <li><a href="{advanced_features.advanced_feature.URL}" {TAG_INPUT_ENABLED}>{advanced_features.advanced_feature.CAPTION}</a></li>
    <!-- END advanced_feature -->
  <!-- END advanced_features -->
</ul>
