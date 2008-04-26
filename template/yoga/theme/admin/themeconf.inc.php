<?php
$themeconf = array(
  'template' => 'yoga',
  'theme' => 'admin',
  'template_dir' => 'template/yoga',
  'icon_dir' => 'template/yoga/icon',
  'admin_icon_dir' => 'template/yoga/icon/admin',
  'mime_icon_dir' => 'template/yoga/icon/mimetypes/',
  'local_head' => '
<!-- Admin Accordion Menus -->
  <script type="text/javascript" src="template-common/lib/jquery.js"></script>
  <script type="text/javascript" src="template-common/lib/chili-1.7.pack.js"></script>
  <script type="text/javascript" src="template-common/lib/jquery.easing.js"></script>
  <script type="text/javascript" src="template-common/lib/jquery.dimensions.js"></script>
  <script type="text/javascript" src="template-common/jquery.accordion.js"></script>
  <script type="text/javascript">
  jQuery().ready(function(){
    jQuery(\'#menubar\').accordion({
      header: "dt.rdion",
      event: "mouseover"
    });
  });
  </script>'
);
?>
