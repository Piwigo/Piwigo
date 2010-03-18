{literal}
<script type="text/javascript">
$().ready(function(){
  $("#pLoaderPage  img").fadeTo("fast", 0.6);

  $("#pLoaderPage  img").hover(
    function(){
      $(this).fadeTo("fast", 1.0); // Opacity on hover
    },
    function(){
      $(this).fadeTo("fast", 0.6); // Opacity on mouseout
    }
  );
});
</script>

<style>
#pLoaderPage {
  width:600px;
  margin:0 auto;
  font-size:1.1em;
}

#pLoaderPage P {
  text-align:left;
}

#pLoaderPage .downloads {
  margin:10px auto 0 auto;
}

#pLoaderPage .downloads A {
  display:block;
  width:150px;
  text-align:center;
  font-size:16px;
  font-weight:bold;
}

#pLoaderPage .downloads A:hover {
  border:none;
}

#pLoaderPage LI {
  margin:20px;
}
</style>
{/literal}

<div class="titrePage">
  <h2>{'Piwigo Uploader'|@translate}</h2>
</div>

<div id="pLoaderPage">
<p>pLoader stands for <em>Piwigo Uploader</em>. From your computer, pLoader prepares your photos and transfer them to your Piwigo photo gallery.</p>

<ol>
  <li>
    Download,

<table class="downloads">
  <tr>
    <td>
      <a href="{$URL_DOWNLOAD_WINDOWS}">
        <img src="http://piwigo.org/screenshots/windows.png"/>
        <br>Windows
      </a>
    <td>
    <td>
      <a href="{$URL_DOWNLOAD_MAC}">
        <img src="http://piwigo.org/screenshots/mac.png" />
        <br>Mac
      </a>
    <td>
    <td>
      <a href="{$URL_DOWNLOAD_LINUX}">
        <img src="http://piwigo.org/screenshots/linux.png" />
        <br>Linux
      </a>
    <td>
  </tr>
</table>

  </li>
  <li>Install on your computer,</li>
  <li>Start pLoader and add your photos.</li>
</ol>
</div>