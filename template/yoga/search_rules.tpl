<!-- DEV TAG: not smarty migrated -->
<div id="content">
<h2>{lang:Search rules}</h2>

<p>{INTRODUCTION}</p>

<ul>

  <!-- BEGIN words -->
  <li>{words.CONTENT}</li>
  <!-- END words -->

  <!-- BEGIN tags -->
  <li>
    <p>{tags.LIST_INTRO}</p>

    <ul>
      <!-- BEGIN tag -->
      <li>{tags.tag.NAME}</li>
      <!-- END tag -->
    </ul>
  </li>
  <!-- END tags -->
  
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
