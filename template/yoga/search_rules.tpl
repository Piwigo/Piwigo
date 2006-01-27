<div id="content">
<h2>{lang:Search rules}</h2>

<p>{INTRODUCTION}</p>

<ul>

  <!-- BEGIN words -->
  <li>{words.CONTENT}</li>
  <!-- END words -->

  <!-- BEGIN author -->
  <li>{author.CONTENT}</li>
  <!-- END author -->

  <!-- BEGIN date_creation -->
  <li>{date_creation.CONTENT}</li>
  <!-- END date_creation -->

  <!-- BEGIN date_available -->
  <li>{date_available.CONTENT}</li>
  <!-- END date_available -->

  <!-- BEGIN categories -->
  <li>
    <p>{categories.LIST_INTRO}</p>

    <ul>
      <!-- BEGIN category -->
      <li>{categories.category.NAME}</li>
      <!-- END category -->
    </ul>
  </li>
  <!-- END categories -->
  
</ul>

</div> <!-- content -->

<p id="pageBottomActions">
  <a href="#" onclick="window.close();" title="{lang:Close this window}">
    <img src="{themeconf:icon_dir}/exit.png" class="button" alt="close">
  </a>
</p>
