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

<div class="illustration"><a href="http://piwigo.org/screenshots/applications/ploader.png" title="pLoader"><img src="http://piwigo.org/screenshots/applications/thumbnail/ploader.jpg"></a></div>
<p>{'pLoader stands for <em>Piwigo Uploader</em>. From your computer, pLoader prepares your photos and transfer them to your Piwigo photo gallery.'|@translate}</p>

<p>{'Available versions for'|@translate}
<a href="{$URL_DOWNLOAD_WINDOWS}">Windows</a>,
<a href="{$URL_DOWNLOAD_MAC}">Mac</a>,
<a href="{$URL_DOWNLOAD_LINUX}">Linux</a>
</p>

</fieldset>

<fieldset>
  <legend>{'Piwigo for iOS (iPhone, iPad, iPod Touch)'|@translate}</legend>
<div class="illustration"><a href="http://piwigo.org/screenshots/applications/piwigo-ios.jpg" title="{'Piwigo for iOS (iPhone, iPad, iPod Touch)'|@translate}"><img src="http://piwigo.org/screenshots/applications/thumbnail/piwigo-ios.jpg"></a></div>
<p>{'<em>Piwigo for iOS</em> application empowers you to connect to your Piwigo gallery from your iPhone, iPad or iPod Touch, create some albums and upload several photos at once.'|@translate}</p>

<p>
{'Available on'|@translate} <a target="_blank" href="http://itunes.apple.com/us/app/piwigo/id472225196">Apple AppStore</a>
</p>
</fieldset>

<fieldset>
  <legend>{'Piwigo for Android'|@translate}</legend>
<div class="illustration"><a href="http://piwigo.org/screenshots/applications/piwigo-android.jpg" title="{'Piwigo for Android'|@translate}"><img src="http://piwigo.org/screenshots/applications/thumbnail/piwigo-android.jpg"></a></div>
<p>{'<em>Piwigo for Android</em> application empowers you to connect your Android phone or table to your Piwigo gallery, create some albums and upload several photos at once.'|@translate}</p>

<p>
{'Available on'|@translate} <a target="_blank" href="https://play.google.com/store/apps/details?id=org.piwigo">Google Play</a>
</p>
</fieldset>

<fieldset>
  <legend>Lightroom</legend>

<div class="illustration"><a href="http://piwigo.org/screenshots/applications/lightroom.png" title="{'Piwigo Publish plugin for Lightroom'|@translate}"><img src="http://piwigo.org/screenshots/applications/thumbnail/lightroom.jpg"></a></div>
<p>
{'Adobe Photoshop Lightroom is a photography software designed to manage large quantities of digital images and doing post production work.'|@translate}
{'The Piwigo publish Plug-in allows you to export and synchronize photos from Lightroom directly to your Piwigo photo gallery.'|@translate}
</p>

<p>
{'Available on'|@translate} <a target="_blank" href="http://alloyphoto.com/plugins/piwigo/">alloyphoto.com</a>
</p>
</fieldset>

<fieldset>
  <legend>Shotwell</legend>

<div class="illustration"><a href="http://piwigo.org/screenshots/applications/shotwell.png" title="{'Piwigo publish plugin for Shotwell'|@translate}"><img src="http://piwigo.org/screenshots/applications/thumbnail/shotwell.jpg"></a></div>
<p>{'Shotwell is an open source digital photo organizer that runs on Linux. It is the default photo manager in Ubuntu and Fedora.'|@translate}</p>

<p>
{'On your Linux, simply install Shotwell with your package manager and the activate Piwigo publishing option.'|@translate}
<a href="http://yorba.org/shotwell/" target="_blank">{'Learn more'|@translate}</a>
</p>
</fieldset>

<fieldset>
  <legend>digiKam</legend>
<div class="illustration"><a href="http://piwigo.org/screenshots/applications/digikam.png" title="{'Piwigo publish plugin for digiKam'|@translate}"><img src="http://piwigo.org/screenshots/applications/thumbnail/digikam.jpg"></a></div>
<p>
{'digiKam is an advanced digital photo management free software for Linux, Windows, and MacOSX.'|@translate}
{'digiKam is designed for photographers who want to view, manage, edit, enhance, organize, tag, and share photographs.'|@translate}
</p>

<p>
{'To export your photos from digiKam to Piwigo, simply install digiKam and the Kipi-plugins.'|@translate}
<a href="http://digikam.org/" target="_blank">{'Learn more'|@translate}</a>
</p>
</fieldset>

<fieldset>
  <legend>iPhoto</legend>

<div class="illustration"><a href="http://piwigo.org/screenshots/applications/iphoto.jpg" title="{'Piwigo export plugin for iPhoto'|@translate}"><img src="http://piwigo.org/screenshots/applications/thumbnail/iphoto.jpg"></a></div>
<p>{'iPhoto is the default photo manager on MacOSX. The Piwigo export plugin let you create new albums and export your photos directly from iPhoto to your Piwigo photo gallery.'|@translate}</p>

<p>
<a target="_blank" href="http://piwigo.org/ext/extension_view.php?eid=592">{'Learn more'|@translate}</a>
</p>
</fieldset>

<fieldset>
  <legend>Aperture</legend>

<div class="illustration"><a href="http://piwigo.org/screenshots/applications/aperture.png" title="{'Piwigo export plugin for Aperture'|@translate}"><img src="http://piwigo.org/screenshots/applications/thumbnail/aperture.jpg"></a></div>
<p>
{'Aperture is a powerful tool to refine images and manage massive libraries on Mac.'|@translate}
{'Aperture is designed for professional photographers with iPhoto simplicity.'|@translate}
{'The Piwigo export plugin allows you to create albums and export photos.'|@translate}
</p>

<p>
<a target="_blank" href="http://piwigo.org/ext/extension_view.php?eid=598">{'Learn more'|@translate}</a>
</p>
</fieldset>

<fieldset>
  <legend>ReGalAndroid</legend>

<div class="illustration"><a href="http://piwigo.org/screenshots/applications/regalandroid.png" title="ReGalAndroid"><img src="http://piwigo.org/screenshots/applications/thumbnail/regalandroid.jpg"></a></div>
<p>
{'ReGalAndroid (RemoteGallery client for Android) is an open source (GPL v3) Piwigo client for the Android platform.'|@translate}
{'Features include gallery browsing, album creation and photo upload.'|@translate}
</p>

<p>
{'Available on'|@translate} <a target="_blank" href="https://play.google.com/store/apps/details?id=net.dahanne.android.regalandroid">Google Play</a>
</p>
</fieldset>
