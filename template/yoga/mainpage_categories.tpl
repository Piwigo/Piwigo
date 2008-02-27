<!-- DEV TAG: not smarty migrated -->
<!-- BEGIN categories -->
<!-- $Id$ -->
<ul class="thumbnailCategories">
  <!-- BEGIN category -->
  <li>
    <div class="thumbnailCategory">
      <div class="illustration">
        <a href="{categories.category.URL}">
          <img src="{categories.category.SRC}" alt="{categories.category.ALT}" title="{categories.category.TITLE}">
        </a>
      </div>
      <div class="description">
        <h3>
          <a href="{categories.category.URL}">{categories.category.NAME}</a>
          {categories.category.ICON}
        </h3>
        <!-- BEGIN dates -->
        <p>{categories.category.dates.INFO}</p>
        <!-- END dates -->
        <p>{categories.category.CAPTION_NB_IMAGES}&nbsp;</p>
        <p>{categories.category.DESCRIPTION}&nbsp;</p>
      </div>
    </div>
  </li>
  <!-- END category -->
</ul>
<!-- END categories -->
