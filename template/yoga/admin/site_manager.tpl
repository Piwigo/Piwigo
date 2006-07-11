<!-- $Id$ -->
<div class="titrePage">
  <ul class="categoryActions">
    <li><a href="{U_HELP}" onclick="popuphelp(this.href); return false;" title="{lang:Help}"><img src="{themeconf:icon_dir}/help.png" class="button" alt="(?)"></a></li>
  </ul>
  <h2>{lang:Site manager}</h2>
</div>

<!-- BEGIN remote_output -->
<div class="remoteOutput">
  <ul>
    <!-- BEGIN remote_line -->
    <li class="{remote_output.remote_line.CLASS}">{remote_output.remote_line.CONTENT}</li>
    <!-- END remote_line -->
  </ul>
</div>
<!-- END remote_output -->

<!-- BEGIN local_listing -->
{lang:remote_site_local_found} {local_listing.URL}
<!-- BEGIN create -->
<form action="" method="post">
  <p>
    {lang:remote_site_local_create}:
    <input type="hidden" name="no_check" value="1"/>
    <input type="hidden" name="galleries_url" value="{local_listing.URL}" />
    <input type="submit" name="submit" value="{lang:submit}" {TAG_INPUT_ENABLED} />
  </p>
</form>
<!-- END create -->
<!-- BEGIN update -->
<a href="{local_listing.update.U_SYNCHRONIZE}" title="{lang:remote_site_local_update}">{lang:site_synchronize}</a>
<!-- END update -->
<!-- END local_listing -->

<!-- BEGIN sites -->
<table border="1" cellpadding="0" cellspacing="0">
  <!-- BEGIN site -->
  <tr align="left"><td>
    <a href="{sites.site.NAME}">{sites.site.NAME}</a><br>({sites.site.TYPE}, {sites.site.CATEGORIES} {lang:Categories}, {sites.site.IMAGES} {lang:picture}s)
  </td><td>
    [<a href="{sites.site.U_SYNCHRONIZE}" title="{lang:site_synchronize_hint}">{lang:site_synchronize}</a>]
    <!-- BEGIN delete -->
      [<a href="{sites.site.delete.U_DELETE}" onclick="return confirm('{lang:Are you sure?}');"
                title="{lang:site_delete_hint}" {TAG_INPUT_ENABLED}>{lang:site_delete}</a>]
    <!-- END delete -->
    <!-- BEGIN remote -->
      <br>
      [<a href="{sites.site.remote.U_TEST}" title="{lang:remote_site_test_hint}" {TAG_INPUT_ENABLED}>{lang:remote_site_test}</a>]
      [<a href="{sites.site.remote.U_GENERATE}" title="{lang:remote_site_generate_hint}" {TAG_INPUT_ENABLED}>{lang:remote_site_generate}</a>]
      [<a href="{sites.site.remote.U_CLEAN}" title="{lang:remote_site_clean_hint}" {TAG_INPUT_ENABLED}>{lang:remote_site_clean}</a>]
    <!-- END remote -->
  </td></tr>
  <!-- END site -->
</table>
<!-- END sites -->

<form action="{F_ACTION}" method="post">
  <p>
    <label for="galleries_url" >{lang:site_create}</label>
    <input type="text" name="galleries_url" id="galleries_url" />
  </p>
  <p>
    <input type="submit" name="submit" value="{lang:submit}" {TAG_INPUT_ENABLED} />
  </p>
</form>
