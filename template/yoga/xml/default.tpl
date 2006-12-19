<?xml version="1.0" encoding="UTF-8" ?> 

<!-- Just a sample right now -->

<group>
  <title>PhpWebGallery - Web Service</title>
  <text>default.tpl: To be customized.</text>

  <!-- BEGIN row -->
  <picture id="{row.ID}">
    <caption>{row.CAPTION}</caption>
    <date>{row.DATE}</date>
    <text>{row.COMMENT}</text>
    <!-- BEGIN High -->
    <full-image width="{row.High.WIDTH}" height="{row.High.HEIGHT}">{row.High.URL}</full-image>
    <!-- END High -->
    <!-- BEGIN Normal -->
    <image width="{row.Normal.WIDTH}" height="{row.Normal.HEIGHT}">{row.Normal.URL}</image>
    <!-- END Normal -->
    <!-- BEGIN Thumbnail -->
    <small-image width="{row.Thumbnail.WIDTH}" height="{row.Thumbnail.HEIGHT}">{row.Thumbnail.URL}</small-image>
    <!-- END Thumbnail -->
  </picture>
  <!-- END row -->
<group>
