{include file='include/colorbox.inc.tpl'}

{footer_script}{literal}
jQuery().ready(function(){
  jQuery(".illustration a").colorbox({rel:'group1'});
});
{/literal}{/footer_script}

{html_head}{literal}
<style type="text/css">
.illustration {float:left; margin-right:10px;}
fieldset p {text-align:left;margin-top:0}
</style>
{/literal}{/html_head}

<div class="titrePage">
  <h2>{'Upload Photos'|@translate} {$TABSHEET_TITLE}</h2>
</div>

<fieldset>
  <legend>pLoader</legend>

<div class="illustration"><a href="http://piwigo.org/ext/upload/extension-270/screenshot.jpg" title="pLoader"><img src="http://piwigo.org/ext/upload/extension-270/thumbnail.jpg"></a></div>
<p>{'pLoader stands for <em>Piwigo Uploader</em>. From your computer, pLoader prepares your photos and transfer them to your Piwigo photo gallery.'|@translate}</p>

<p>Available versions for
<a href="{$URL_DOWNLOAD_WINDOWS}">Windows</a>,
<a href="{$URL_DOWNLOAD_MAC}">Mac</a>,
<a href="{$URL_DOWNLOAD_LINUX}">Linux</a>
</p>

</fieldset>

<fieldset>
  <legend>Piwigo Mobile</legend>

<div class="illustration"><a href="http://piwigo.com/blog/wp-content/uploads/2011/10/piwigo-ios-iphone-ipad.jpg" title="Piwigo Mobile for iOS and Android"><img src="http://piwigo.com/blog/wp-content/uploads/2011/10/piwigo-ios-iphone-ipad.jpg" style="width:150px"></a></div>
<p>{'<em>Piwigo Mobile</em> application empowers you to connect to your Piwigo gallery from your iPhone, iPad or Android, create some albums and upload several photos at once.'|@translate}</p>

<p>
Available on <a target="_blank" href="http://itunes.apple.com/us/app/piwigo/id472225196">Apple AppStore</a>
and <a target="_blank" href="https://market.android.com/details?id=org.piwigo">Android market</a>
</p>
</fieldset>

<fieldset>
  <legend>Lightroom</legend>

<div class="illustration"><a href="http://alloyphoto.com/images/piwigo/dialog.png" title="Piwigo Publish plugin for Lightroom"><img src="http://alloyphoto.com/images/piwigo/dialog.png" style="width:150px"></a></div>
<p>Adobe Photoshop Lightroom is a photography software designed to manage large quantities of digital images and doing post production work. The Piwigo publish Plug-in allows you to export photos from Lightroom directly to your Piwigo photo gallery.</p>

<p>
Details and download on <a target="_blank" href="http://alloyphoto.com/plugins/piwigo/">alloyphoto.com</a>
</p>
</fieldset>

<fieldset>
  <legend>Shotwell</legend>

<div class="illustration"><a href="http://piwigo.files.wordpress.com/2010/11/shotwell_002.png" title="Piwigo publish plugin for Shotwell"><img src="http://piwigo.files.wordpress.com/2010/11/shotwell_002.png?w=150"></a></div>
<p><a href="http://yorba.org/shotwell/" target="_blank">Shotwell</a> is a digital photo organizer that runs on Linux. It is the default photo manager in Ubuntu and Fedora.</p>

<p>On your Linux, simply install Shotwell with your package manager and the activate Piwigo publishing option.</p>
</fieldset>

<fieldset>
  <legend>digiKam</legend>
<div class="illustration"><a href="http://fr.piwigo.org/forum/showimage.php?pid=133064&filename=digikam2piwigo-01.png" title="Piwigo publish plugin for digiKam"><img src="http://fr.piwigo.org/forum/showimage.php?pid=133064&filename=digikam2piwigo-01.png" style="width:150px"></a></div>
<p><a href="http://digikam.org/" target="_blank">digiKam</a> is an advanced digital photo management application for Linux, Windows, and Mac-OSX.</p>

<p>To export your photos from digiKam to Piwigo, simply install digiKam and the Kipi-plugins.</p>
</fieldset>

<fieldset>
  <legend>iPhoto</legend>

<div class="illustration"><a href="http://piwigo.org/forum/showimage.php?pid=126856&filename=iphoto-to-piwigo.jpg" title="Piwigo export plugin for iPhoto"><img src="http://piwigo.org/forum/showimage.php?pid=126856&filename=iphoto-to-piwigo.jpg" style="width:150px"></a></div>
<p>iPhoto is the default photo manager on MacOSX. The Piwigo export plugin let you create new albums and export your photos directly from iPhoto to your Piwigo photo gallery.</p>

<p>
<a target="_blank" href="http://piwigo.org/ext/extension_view.php?eid=592">Details and download</a>
</p>
</fieldset>

<fieldset>
  <legend>Aperture</legend>

<div class="illustration"><a href="http://piwigo.org/ext/upload/extension-598/screenshot.jpg" title="Piwigo export plugin for Aperture"><img src="http://piwigo.org/ext/upload/extension-598/thumbnail.jpg" style="width:150px"></a></div>
<p>Aperture is a powerful tool to refine images and manage massive libraries on your Mac. It's pro performance with iPhoto simplicity. The Piwigo export plugin allows you to create albums and export photos.</p>

<p>
<a target="_blank" href="http://piwigo.org/ext/extension_view.php?eid=598">Details and download</a>
</p>
</fieldset>

<fieldset>
  <legend>ReGalAndroid</legend>

<div class="illustration"><a href="http://piwigo.org/screenshots/regalandroid.png" title="ReGalAndroid"><img src="http://piwigo.org/screenshots/regalandroid.png" style="width:150px"></a></div>
<p>ReGalAndroid (RemoteGallery client for Android) is an open source (GPL v3) Piwigo client for the Android platform. Features include gallery browsing, album creation and photo upload.</p>

<p>
Details and download on <a target="_blank" href="http://market.android.com/details?id=net.dahanne.android.regalandroid">Android Market</a>
</p>
</fieldset>
