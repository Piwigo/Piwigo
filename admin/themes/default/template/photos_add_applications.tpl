{include file='include/colorbox.inc.tpl'}

{footer_script}{literal}
jQuery().ready(function(){
  jQuery(".illustration a").colorbox({rel:'group1'});
});
{/literal}{/footer_script}

{html_head}{literal}
<style type="text/css">
  .applicationContainer {
    width: 100%;
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
  }

  .applicationCard {
    max-width: 500px;
    margin: 20px;

    display: flex;
    flex-direction: row;
  }

  .applicationCard img {
    max-width: 210px;
    height: 175px;
    margin: 20px;
  }

  .applicationCard .textSide {
    display: flex;
    flex-direction: column;
  }

  .applicationCard .applicationName {
    font-size: 15px;
    font-weight: bold;
    margin: 20px 0 10px 0;
    padding-right: 15px;
    text-align: left;
  }

  .applicationCard .applicationDesc {
    text-align: left;
    padding-right: 15px;

    display: -webkit-box;
    -webkit-line-clamp: 6;
    -webkit-box-orient: vertical;  
    overflow: hidden;
  }
  
  .applicationCard .applicationLink {
    width: fit-content;
    margin-top: auto;
    margin-bottom: 20px;
    display: flex;
    justify-content: flex-start;
  }

  .applicationCard .applicationLink a {
    text-decoration: none;
    margin-left: 0;
  }

</style>
{/literal}{/html_head}

<div class="applicationContainer">

  <div class="applicationCard"> 
    <div class="illustration"><a href="https://piwigo.org/screenshots/applications/piwigo-remote-sync.png" title="Piwigo Remote Sync"><img src="https://piwigo.org/screenshots/applications/piwigo-remote-sync.png"></a></div>

    <div class="textSide">
      <div class="applicationName"> Piwigo Remote Sync </div> 
      <div class="applicationDesc"> {'Piwigo Remote Sync is able to upload a whole folder hierarchy. If you run it again, only new photos will be uploaded.'|@translate} </div> 
      <div class="applicationLink">
        <a class="buttonGradient" href="http://piwigo.org/ext/extension_view.php?eid=851" target="_blank">{'Learn more'|@translate}</a>
      </div> 
    </div>
  </div>

  <div class="applicationCard"> 
    <div class="illustration"><a href="https://piwigo.org/screenshots/applications/piwigo-ios.png" title="{'Piwigo for iOS (iPhone, iPad, iPod Touch)'|@translate}"><img src="https://piwigo.org/screenshots/applications/thumbnail/piwigo-ios.png"></a></div>

    <div class="textSide">
      <div class="applicationName"> {'Piwigo for iOS (iPhone, iPad, iPod Touch)'|@translate}</div> 
      <div class="applicationDesc"> {'<em>Piwigo for iOS</em> application empowers you to connect to your Piwigo gallery from your iPhone, iPad or iPod Touch, create some albums and upload several photos at once.'|@translate}</div> 
      <div class="applicationLink">
        <a class="buttonGradient" target="_blank" href="http://itunes.apple.com/us/app/piwigo/id472225196"> {'Available on'|@translate} Apple AppStore</a>
      </div> 
    </div>
  </div>

  <div class="applicationCard"> 
    <div class="illustration"><a href="https://piwigo.org/screenshots/applications/piwigo-android.png" title="{'Piwigo for Android'|@translate}"><img src="https://piwigo.org/screenshots/applications/thumbnail/piwigo-android.png"></a></div>

    <div class="textSide">
      <div class="applicationName"> {'Piwigo for Android'|@translate} </div> 
      <div class="applicationDesc"> {'<em>Piwigo for Android</em> application empowers you to connect your Android phone or table to your Piwigo gallery, create some albums and upload several photos at once.'|@translate} </div> 
      <div class="applicationLink">
        <a class="buttonGradient" target="_blank" href="https://play.google.com/store/apps/details?id=com.piwigo.piwigo_ng">{'Available on'|@translate} Google Play</a> 
      </div>
    </div>
  </div>

  <div class="applicationCard"> 
    <div class="illustration"><a href="https://piwigo.org/screenshots/applications/lightroom.png" title="{'Piwigo Publish plugin for Lightroom'|@translate}"><img src="https://piwigo.org/screenshots/applications/lightroom.png"></a></div>

    <div class="textSide">
      <div class="applicationName"> Lightroom </div> 
      <div class="applicationDesc"> 
        {'Adobe Photoshop Lightroom is a photography software designed to manage large quantities of digital images and doing post production work.'|@translate}
        {'The Piwigo publish Plug-in allows you to export and synchronize photos from Lightroom directly to your Piwigo photo gallery.'|@translate} 
      </div> 
      <div class="applicationLink">
        <a class="buttonGradient" target="_blank" href="http://alloyphoto.com/plugins/piwigo/">{'Available on'|@translate} alloyphoto.com</a>
      </div>
    </div>
  </div>

  <div class="applicationCard"> 
    <div class="illustration"><a href="https://piwigo.org/screenshots/applications/shotwell.png" title="{'Piwigo publish plugin for Shotwell'|@translate}"><img src="https://piwigo.org/screenshots/applications/shotwell.png"></a></div>

    <div class="textSide">
      <div class="applicationName"> Shotwell </div> 
      <div class="applicationDesc"> {'Shotwell is an open source digital photo organizer that runs on Linux. It is the default photo manager in Ubuntu and Fedora.'|@translate}</div> 
      <div class="applicationLink">
        <a title="{'On your Linux, simply install Shotwell with your package manager and the activate Piwigo publishing option.'|@translate}" class="buttonGradient" href="http://yorba.org/shotwell/" target="_blank">{'Learn more'|@translate}</a>
      </div>
    </div>
  </div>

  <div class="applicationCard"> 
    <div class="illustration"><a href="https://piwigo.org/screenshots/applications/digikam.png" title="{'Piwigo publish plugin for digiKam'|@translate}"><img src="https://piwigo.org/screenshots/applications/digikam.png"></a></div>

    <div class="textSide">
      <div class="applicationName"> digiKam </div> 
      <div class="applicationDesc"> 
        {'digiKam is an advanced digital photo management free software for Linux, Windows, and MacOSX.'|@translate}
        {'digiKam is designed for photographers who want to view, manage, edit, enhance, organize, tag, and share photographs.'|@translate}
      </div> 
      <div class="applicationLink">
        <a title="{'To export your photos from digiKam to Piwigo, simply install digiKam and the Kipi-plugins.'|@translate}" class="buttonGradient" href="http://digikam.org/" target="_blank">{'Learn more'|@translate}</a>
      </div>
    </div>
  </div>

  <div class="applicationCard"> 
    <div class="illustration"><a href="https://piwigo.org/screenshots/applications/macsharetopiwigo.jpg" title="MacShareToPiwigo"><img src="https://piwigo.org/screenshots/applications/macsharetopiwigo.jpg"></a></div>

    <div class="textSide">
      <div class="applicationName"> MacShareToPiwigo </div> 
      <div class="applicationDesc"> {'Share / Send your photos directly from your Mac Os X (10.10 and following) to Piwigo'|@translate} </div> 
      <div class="applicationLink">
        <a class="buttonGradient" target="_blank" href="http://piwigo.org/ext/extension_view.php?eid=804">{'Learn more'|@translate}</a>
      </div>
    </div>
  </div>

  <div class="applicationCard"> 
    <div class="illustration"><a href="https://piwigo.org/screenshots/applications/iphoto.jpg" title="{'Piwigo export plugin for iPhoto'|@translate}"><img src="https://piwigo.org/screenshots/applications/iphoto.jpg"></a></div>

    <div class="textSide">
      <div class="applicationName"> iPhoto </div> 
      <div class="applicationDesc"> {'iPhoto is the default photo manager on MacOSX. The Piwigo export plugin let you create new albums and export your photos directly from iPhoto to your Piwigo photo gallery.'|@translate} </div> 
      <div class="applicationLink">
        <a class="buttonGradient" target="_blank" href="http://piwigo.org/ext/extension_view.php?eid=592">{'Learn more'|@translate}</a>
      </div>
    </div>
  </div>

  <div class="applicationCard"> 
    <div class="illustration"><a href="https://piwigo.org/screenshots/applications/aperture.png" title="{'Piwigo export plugin for Aperture'|@translate}"><img src="https://piwigo.org/screenshots/applications/aperture.png"></a></div>

    <div class="textSide">
      <div class="applicationName"> Aperture </div> 
      <div class="applicationDesc"> 
        {'Aperture is a powerful tool to refine images and manage massive libraries on Mac.'|@translate}
        {'Aperture is designed for professional photographers with iPhoto simplicity.'|@translate}
        {'The Piwigo export plugin allows you to create albums and export photos.'|@translate}
      </div> 
      <div class="applicationLink">
        <a class="buttonGradient" target="_blank" href="http://piwigo.org/ext/extension_view.php?eid=598">{'Learn more'|@translate}</a>
      </div>
    </div>
  </div>

</div>