<?php

/* *******************************************
// Copyright 2010-2013, Anthony Hand
//
// File version 2013.10.27 (October 27, 2013)
//	Updates:
//	- Made minor update to the InitDeviceScan. Should check Tablet Tier first, then iPhone Tier, then Quick Mobile. 
//
// File version 2013.08.01 (August 1, 2013)
//	Updates:
//	- Updated DetectMobileQuick(). Moved the 'Exclude Tablets' logic to the top of the method to fix a logic bug.
//
// File version 2013.07.13 (July 13, 2013)
//	Updates:
//	- Added support for Tizen: variable and DetectTizen().
//	- Added support for Meego: variable and DetectMeego().
//	- Added support for Windows Phone 8: variable and DetectWindowsPhone8().
//	- Added a generic Windows Phone method: DetectWindowsPhone().
//	- Added support for BlackBerry 10 OS: variable and DetectBlackBerry10Phone().
//	- Added support for PlayStation Vita handheld: variable and DetectGamingHandheld().
//	- Updated DetectTierIphone(). Added Tizen; updated the Windows Phone, BB10, and PS Vita support. 
//	- Updated DetectWindowsMobile(). Uses generic DetectWindowsPhone() method rather than WP7.
//	- Updated DetectSmartphone(). Uses the IsTierIphone variable.
//	- Updated DetectSonyMylo() with more efficient code.
//	- Removed DetectGarminNuvifone() from DetectTierIphone(). How many are left in market in 2013? It is detected as a RichCSS Tier device.
//	- Removed the deviceXoom variable. It was unused.
//	- Added detection support for the Obigo mobile browser to DetectMobileQuick().
//
//
// LICENSE INFORMATION
// Licensed under the Apache License, Version 2.0 (the "License"); 
// you may not use this file except in compliance with the License. 
// You may obtain a copy of the License at 
//        http://www.apache.org/licenses/LICENSE-2.0 
// Unless required by applicable law or agreed to in writing, 
// software distributed under the License is distributed on an 
// "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, 
// either express or implied. See the License for the specific 
// language governing permissions and limitations under the License. 
//
//
// ABOUT THIS PROJECT
//   Project Owner: Anthony Hand
//   Email: anthony.hand@gmail.com
//   Web Site: http://www.mobileesp.com
//   Source Files: http://code.google.com/p/mobileesp/
//   
//   Versions of this code are available for:
//      PHP, JavaScript, Java, ASP.NET (C#), and Ruby
//
// *******************************************
*/



//**************************
// The uagent_info class encapsulates information about
//   a browser's connection to your web site. 
//   You can use it to find out whether the browser asking for
//   your site's content is probably running on a mobile device.
//   The methods were written so you can be as granular as you want.
//   For example, enquiring whether it's as specific as an iPod Touch or
//   as general as a smartphone class device.
//   The object's methods return 1 for true, or 0 for false.
class uagent_info
{
   var $useragent = "";
   var $httpaccept = "";

   //standardized values for true and false.
   var $true = 1;
   var $false = 0;

   //Let's store values for quickly accessing the same info multiple times. InitCompleted
   var $initCompleted = 0; //Stores whether we're currently initializing the most popular functions.
   var $isWebkit = 0; //Stores the result of DetectWebkit()
   var $isMobilePhone = 0; //Stores the result of DetectMobileQuick()
   var $isIphone = 0; //Stores the result of DetectIphone()
   var $isAndroid = 0; //Stores the result of DetectAndroid()
   var $isAndroidPhone = 0; //Stores the result of DetectAndroidPhone()
   var $isTierTablet = 0; //Stores the result of DetectTierTablet()
   var $isTierIphone = 0; //Stores the result of DetectTierIphone()
   var $isTierRichCss = 0; //Stores the result of DetectTierRichCss()
   var $isTierGenericMobile = 0; //Stores the result of DetectTierOtherPhones()

   //Initialize some initial smartphone string variables.
   var $engineWebKit = 'webkit';

   var $deviceIphone = 'iphone';
   var $deviceIpod = 'ipod';
   var $deviceIpad = 'ipad';
   var $deviceMacPpc = 'macintosh'; //Used for disambiguation

   var $deviceAndroid = 'android';
   var $deviceGoogleTV = 'googletv';
   var $deviceHtcFlyer = 'htc_flyer'; //HTC Flyer
   
   var $deviceWinPhone7 = 'windows phone os 7'; 
   var $deviceWinPhone8 = 'windows phone 8'; 
   var $deviceWinMob = 'windows ce';
   var $deviceWindows = 'windows'; 
   var $deviceIeMob = 'iemobile';
   var $devicePpc = 'ppc'; //Stands for PocketPC
   var $enginePie = 'wm5 pie'; //An old Windows Mobile
   
   var $deviceBB = 'blackberry';   
   var $deviceBB10 = 'bb10'; //For the new BB 10 OS
   var $vndRIM = 'vnd.rim'; //Detectable when BB devices emulate IE or Firefox
   var $deviceBBStorm = 'blackberry95';  //Storm 1 and 2
   var $deviceBBBold = 'blackberry97'; //Bold 97x0 (non-touch)
   var $deviceBBBoldTouch = 'blackberry 99'; //Bold 99x0 (touchscreen)
   var $deviceBBTour = 'blackberry96'; //Tour
   var $deviceBBCurve = 'blackberry89'; //Curve2
   var $deviceBBCurveTouch = 'blackberry 938'; //Curve Touch
   var $deviceBBTorch = 'blackberry 98'; //Torch
   var $deviceBBPlaybook = 'playbook'; //PlayBook tablet
   
   var $deviceSymbian = 'symbian';
   var $deviceS60 = 'series60';
   var $deviceS70 = 'series70';
   var $deviceS80 = 'series80';
   var $deviceS90 = 'series90';
   
   var $devicePalm = 'palm';
   var $deviceWebOS = 'webos'; //For Palm's line of WebOS devices
   var $deviceWebOShp = 'hpwos'; //For HP's line of WebOS devices
   var $engineBlazer = 'blazer'; //Old Palm browser
   var $engineXiino = 'xiino'; //Another old Palm
   
   var $deviceNuvifone = 'nuvifone'; //Garmin Nuvifone
   var $deviceBada = 'bada'; //Samsung's Bada OS
   var $deviceTizen = 'tizen'; //Tizen OS
   var $deviceMeego = 'meego'; //Meego OS

   var $deviceKindle = 'kindle'; //Amazon Kindle, eInk one
   var $engineSilk = 'silk-accelerated'; //Amazon's accelerated Silk browser for Kindle Fire
   
   //Initialize variables for mobile-specific content.
   var $vndwap = 'vnd.wap';
   var $wml = 'wml';   
   
   //Initialize variables for other random devices and mobile browsers.
   var $deviceTablet = 'tablet'; //Generic term for slate and tablet devices
   var $deviceBrew = 'brew';
   var $deviceDanger = 'danger';
   var $deviceHiptop = 'hiptop';
   var $devicePlaystation = 'playstation';
   var $devicePlaystationVita = 'vita';
   var $deviceNintendoDs = 'nitro';
   var $deviceNintendo = 'nintendo';
   var $deviceWii = 'wii';
   var $deviceXbox = 'xbox';
   var $deviceArchos = 'archos';
   
   var $engineOpera = 'opera'; //Popular browser
   var $engineNetfront = 'netfront'; //Common embedded OS browser
   var $engineUpBrowser = 'up.browser'; //common on some phones
   var $engineOpenWeb = 'openweb'; //Transcoding by OpenWave server
   var $deviceMidp = 'midp'; //a mobile Java technology
   var $uplink = 'up.link';
   var $engineTelecaQ = 'teleca q'; //a modern feature phone browser
   var $engineObigo = 'obigo'; //W 10 is a modern feature phone browser
   
   var $devicePda = 'pda'; //some devices report themselves as PDAs
   var $mini = 'mini';  //Some mobile browsers put 'mini' in their names.
   var $mobile = 'mobile'; //Some mobile browsers put 'mobile' in their user agent strings.
   var $mobi = 'mobi'; //Some mobile browsers put 'mobi' in their user agent strings.
   
   //Use Maemo, Tablet, and Linux to test for Nokia's Internet Tablets.
   var $maemo = 'maemo';
   var $linux = 'linux';
   var $qtembedded = 'qt embedded'; //for Sony Mylo and others
   var $mylocom2 = 'com2'; //for Sony Mylo also
   
   //In some UserAgents, the only clue is the manufacturer.
   var $manuSonyEricsson = "sonyericsson";
   var $manuericsson = "ericsson";
   var $manuSamsung1 = "sec-sgh";
   var $manuSony = "sony";
   var $manuHtc = "htc";

   //In some UserAgents, the only clue is the operator.
   var $svcDocomo = "docomo";
   var $svcKddi = "kddi";
   var $svcVodafone = "vodafone";

   //Disambiguation strings.
   var $disUpdate = "update"; //pda vs. update


   //**************************
   //The constructor. Allows the latest PHP (5.0+) to locate a constructor object and initialize the object.
   function __construct()
   {
	 $this->uagent_info();
   }


   //**************************
   //The object initializer. Initializes several default variables.
   function uagent_info()
   { 
	 $this->useragent = isset($_SERVER['HTTP_USER_AGENT'])?strtolower($_SERVER['HTTP_USER_AGENT']):'';
	 $this->httpaccept = isset($_SERVER['HTTP_ACCEPT'])?strtolower($_SERVER['HTTP_ACCEPT']):'';
		
	 //Let's initialize some values to save cycles later.
	 $this->InitDeviceScan();
   }
   
   //**************************
   // Initialize Key Stored Values.
   function InitDeviceScan()
   {
        //Save these properties to speed processing
        global $isWebkit, $isIphone, $isAndroid, $isAndroidPhone;
        $this->isWebkit = $this->DetectWebkit();
        $this->isIphone = $this->DetectIphone();
        $this->isAndroid = $this->DetectAndroid();
        $this->isAndroidPhone = $this->DetectAndroidPhone();
        
        //These tiers are the most useful for web development
        global $isMobilePhone, $isTierTablet, $isTierIphone;
        $this->isTierTablet = $this->DetectTierTablet(); //Do first
        $this->isTierIphone = $this->DetectTierIphone(); //Do second
        $this->isMobilePhone = $this->DetectMobileQuick(); //Do third
        
        //Optional: Comment these out if you NEVER use them.
        global $isTierRichCss, $isTierGenericMobile;
        $this->isTierRichCss = $this->DetectTierRichCss();
        $this->isTierGenericMobile = $this->DetectTierOtherPhones();
        
        $this->initCompleted = $this->true;
   }

   //**************************
   //Returns the contents of the User Agent value, in lower case.
   function Get_Uagent()
   { 
       return $this->useragent;
   }

   //**************************
   //Returns the contents of the HTTP Accept value, in lower case.
   function Get_HttpAccept()
   { 
       return $this->httpaccept;
   }
   

   //**************************
   // Detects if the current device is an iPhone.
   function DetectIphone()
   {
      if ($this->initCompleted == $this->true ||
          $this->isIphone == $this->true)
         return $this->isIphone;
      
      if (stripos($this->useragent, $this->deviceIphone) > -1)
      {
         //The iPad and iPod Touch say they're an iPhone. So let's disambiguate.
         if ($this->DetectIpad() == $this->true ||
             $this->DetectIpod() == $this->true)
            return $this->false;
         //Yay! It's an iPhone!
         else
            return $this->true; 
      }
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current device is an iPod Touch.
   function DetectIpod()
   {
      if (stripos($this->useragent, $this->deviceIpod) > -1)
         return $this->true; 
      else
         return $this->false; 
   }
   
   //**************************
   // Detects if the current device is an iPad tablet.
   function DetectIpad()
   {
      if (stripos($this->useragent, $this->deviceIpad) > -1 &&
          $this->DetectWebkit() == $this->true)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current device is an iPhone or iPod Touch.
   function DetectIphoneOrIpod()
   {
       //We repeat the searches here because some iPods may report themselves as an iPhone, which would be okay.
      if ($this->DetectIphone() == $this->true ||
             $this->DetectIpod() == $this->true)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects *any* iOS device: iPhone, iPod Touch, iPad.
   function DetectIos()
   {
      if (($this->DetectIphoneOrIpod() == $this->true) ||
        ($this->DetectIpad() == $this->true))
         return $this->true; 
      else
         return $this->false;
   }


   //**************************
   // Detects *any* Android OS-based device: phone, tablet, and multi-media player.
   // Also detects Google TV.
   function DetectAndroid()
   {
      if ($this->initCompleted == $this->true ||
          $this->isAndroid == $this->true)
         return $this->isAndroid;

      if ((stripos($this->useragent, $this->deviceAndroid) > -1)
          || ($this->DetectGoogleTV() == $this->true))
         return $this->true; 
      //Special check for the HTC Flyer 7" tablet
      if ((stripos($this->useragent, $this->deviceHtcFlyer) > -1))
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current device is a (small-ish) Android OS-based device
   // used for calling and/or multi-media (like a Samsung Galaxy Player).
   // Google says these devices will have 'Android' AND 'mobile' in user agent.
   // Ignores tablets (Honeycomb and later).
   function DetectAndroidPhone()
   {
      if ($this->initCompleted == $this->true ||
          $this->isAndroidPhone == $this->true)
         return $this->isAndroidPhone;

      if (($this->DetectAndroid() == $this->true) &&
		(stripos($this->useragent, $this->mobile) > -1))
         return $this->true;
      
      //Special check for Android phones with Opera Mobile. They should report here.
      if (($this->DetectOperaAndroidPhone() == $this->true))
         return $this->true; 
      //Special check for the HTC Flyer 7" tablet. It should report here.
      if ((stripos($this->useragent, $this->deviceHtcFlyer) > -1))
         return $this->true;
      
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current device is a (self-reported) Android tablet.
   // Google says these devices will have 'Android' and NOT 'mobile' in their user agent.
   function DetectAndroidTablet()
   {
      //First, let's make sure we're on an Android device.
      if ($this->DetectAndroid() == $this->false)
         return $this->false; 

      //Special check for Opera Android Phones. They should NOT report here.
      if ($this->DetectOperaMobile() == $this->true)
         return $this->false; 
      //Special check for the HTC Flyer 7" tablet. It should NOT report here.
      if ((stripos($this->useragent, $this->deviceHtcFlyer) > -1))
         return $this->false; 
         
      //Otherwise, if it's Android and does NOT have 'mobile' in it, Google says it's a tablet.
      if (stripos($this->useragent, $this->mobile) > -1)
         return $this->false;
      else
         return $this->true; 
   }

   //**************************
   // Detects if the current device is an Android OS-based device and
   //   the browser is based on WebKit.
   function DetectAndroidWebKit()
   {
      if (($this->DetectAndroid() == $this->true) &&
		($this->DetectWebkit() == $this->true))
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current device is a GoogleTV.
   function DetectGoogleTV()
   {
      if (stripos($this->useragent, $this->deviceGoogleTV) > -1)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current browser is based on WebKit.
   function DetectWebkit()
   {
      if ($this->initCompleted == $this->true ||
          $this->isWebkit == $this->true)
         return $this->isWebkit;

      if (stripos($this->useragent, $this->engineWebKit) > -1)
         return $this->true; 
      else
         return $this->false; 
   }


   //**************************
   // Detects if the current browser is EITHER a 
   // Windows Phone 7.x OR 8 device.
   function DetectWindowsPhone()
   {
      if (($this->DetectWindowsPhone8() == $this->true)
			|| ($this->DetectWindowsPhone7() == $this->true))
         return $this->true; 
      else
         return $this->false; 
   }
   
   //**************************
   // Detects a Windows Phone 7.x device (in mobile browsing mode).
   function DetectWindowsPhone7()
   {
      if (stripos($this->useragent, $this->deviceWinPhone7) > -1)
         return $this->true; 
      else
         return $this->false; 
   }
   
   //**************************
   // Detects a Windows Phone 8 device (in mobile browsing mode).
   function DetectWindowsPhone8()
   {
      if (stripos($this->useragent, $this->deviceWinPhone8) > -1)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current browser is a Windows Mobile device.
   // Excludes Windows Phone 7 and later devices. 
   // Focuses on Windows Mobile 6.xx and earlier.
   function DetectWindowsMobile()
   {
      if ($this->DetectWindowsPhone() == $this->true)
         return $this->false;
      
      //Most devices use 'Windows CE', but some report 'iemobile' 
      //  and some older ones report as 'PIE' for Pocket IE. 
      if (stripos($this->useragent, $this->deviceWinMob) > -1 ||
          stripos($this->useragent, $this->deviceIeMob) > -1 ||
          stripos($this->useragent, $this->enginePie) > -1)
         return $this->true; 
      //Test for Windows Mobile PPC but not old Macintosh PowerPC.
	  if (stripos($this->useragent, $this->devicePpc) > -1
		  && !(stripos($this->useragent, $this->deviceMacPpc) > 1))
         return $this->true; 
      //Test for certain Windwos Mobile-based HTC devices.
      if (stripos($this->useragent, $this->manuHtc) > -1 &&
          stripos($this->useragent, $this->deviceWindows) > -1)
         return $this->true; 
      if ($this->DetectWapWml() == $this->true &&
          stripos($this->useragent, $this->deviceWindows) > -1) 
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current browser is any BlackBerry device.
   // Includes BB10 OS, but excludes the PlayBook.
   function DetectBlackBerry()
   {
       if ((stripos($this->useragent, $this->deviceBB) > -1) ||
          (stripos($this->httpaccept, $this->vndRIM) > -1))
         return $this->true;
      if ($this->DetectBlackBerry10Phone() == $this->true) 
         return $this->true;       
       else
         return $this->false; 
   }
   
   //**************************
   // Detects if the current browser is a BlackBerry 10 OS phone.
   // Excludes tablets.
   function DetectBlackBerry10Phone()
   {
       if ((stripos($this->useragent, $this->deviceBB10) > -1) &&
          (stripos($this->useragent, $this->mobile) > -1))
         return $this->true; 
       else
         return $this->false; 
   }
   
   //**************************
   // Detects if the current browser is on a BlackBerry tablet device.
   //    Examples: PlayBook
   function DetectBlackBerryTablet()
   {
      if ((stripos($this->useragent, $this->deviceBBPlaybook) > -1))
         return $this->true; 
      else
        return $this->false; 
   }

   //**************************
   // Detects if the current browser is a BlackBerry phone device AND uses a
   //    WebKit-based browser. These are signatures for the new BlackBerry OS 6.
   //    Examples: Torch. Includes the Playbook.
   function DetectBlackBerryWebKit()
   {
      if (($this->DetectBlackBerry() == $this->true) &&
		($this->DetectWebkit() == $this->true))
         return $this->true; 
      else
        return $this->false; 
   }

   //**************************
   // Detects if the current browser is a BlackBerry Touch phone device with
   //    a large screen, such as the Storm, Torch, and Bold Touch. Excludes the Playbook.
   function DetectBlackBerryTouch()
   {  
       if ((stripos($this->useragent, $this->deviceBBStorm) > -1) ||
		(stripos($this->useragent, $this->deviceBBTorch) > -1) ||
		(stripos($this->useragent, $this->deviceBBBoldTouch) > -1) ||
		(stripos($this->useragent, $this->deviceBBCurveTouch) > -1))
         return $this->true; 
       else
         return $this->false; 
   }
   
   //**************************
   // Detects if the current browser is a BlackBerry OS 5 device AND
   //    has a more capable recent browser. Excludes the Playbook.
   //    Examples, Storm, Bold, Tour, Curve2
   //    Excludes the new BlackBerry OS 6 and 7 browser!!
   function DetectBlackBerryHigh()
   {
      //Disambiguate for BlackBerry OS 6 or 7 (WebKit) browser
      if ($this->DetectBlackBerryWebKit() == $this->true)
         return $this->false; 
      if ($this->DetectBlackBerry() == $this->true)
      {
          if (($this->DetectBlackBerryTouch() == $this->true) ||
            stripos($this->useragent, $this->deviceBBBold) > -1 ||
            stripos($this->useragent, $this->deviceBBTour) > -1 ||
            stripos($this->useragent, $this->deviceBBCurve) > -1)
          {
             return $this->true; 
          }
          else
            return $this->false; 
      }
      else
        return $this->false; 
   }

   //**************************
   // Detects if the current browser is a BlackBerry device AND
   //    has an older, less capable browser. 
   //    Examples: Pearl, 8800, Curve1.
   function DetectBlackBerryLow()
   {
      if ($this->DetectBlackBerry() == $this->true)
      {
          //Assume that if it's not in the High tier, then it's Low.
          if (($this->DetectBlackBerryHigh() == $this->true) ||
			($this->DetectBlackBerryWebKit() == $this->true))
             return $this->false; 
          else
            return $this->true; 
      }
      else
        return $this->false; 
   }


   //**************************
   // Detects if the current browser is the Nokia S60 Open Source Browser.
   function DetectS60OssBrowser()
   {
      //First, test for WebKit, then make sure it's either Symbian or S60.
      if ($this->DetectWebkit() == $this->true)
      {
        if (stripos($this->useragent, $this->deviceSymbian) > -1 ||
            stripos($this->useragent, $this->deviceS60) > -1)
        {
           return $this->true;
        }
        else
           return $this->false; 
      }
      else
         return $this->false; 
   }
   
   //**************************
   // Detects if the current device is any Symbian OS-based device,
   //   including older S60, Series 70, Series 80, Series 90, and UIQ, 
   //   or other browsers running on these devices.
   function DetectSymbianOS()
   {
       if (stripos($this->useragent, $this->deviceSymbian) > -1 || 
           stripos($this->useragent, $this->deviceS60) > -1 ||
           stripos($this->useragent, $this->deviceS70) > -1 || 
           stripos($this->useragent, $this->deviceS80) > -1 ||
           stripos($this->useragent, $this->deviceS90) > -1)
         return $this->true; 
      else
         return $this->false; 
   }


   //**************************
   // Detects if the current browser is on a PalmOS device.
   function DetectPalmOS()
   {
		//Make sure it's not WebOS first
		if ($this->DetectPalmWebOS() == $this->true)
			return $this->false;

      //Most devices nowadays report as 'Palm', but some older ones reported as Blazer or Xiino.
      if (stripos($this->useragent, $this->devicePalm) > -1 ||
          stripos($this->useragent, $this->engineBlazer) > -1 ||
          stripos($this->useragent, $this->engineXiino) > -1)
            return $this->true; 
      else
         return $this->false; 
   }


   //**************************
   // Detects if the current browser is on a Palm device
   //   running the new WebOS.
   function DetectPalmWebOS()
   {
      if (stripos($this->useragent, $this->deviceWebOS) > -1)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current browser is on an HP tablet running WebOS.
   function DetectWebOSTablet()
   {
      if ((stripos($this->useragent, $this->deviceWebOShp) > -1)
			&& (stripos($this->useragent, $this->deviceTablet) > -1))
         return $this->true; 
      else
         return $this->false; 
   }



   //**************************
   // Detects if the current browser is Opera Mobile or Mini.
   function DetectOperaMobile()
   {
      if (stripos($this->useragent, $this->engineOpera) > -1)
      {
         if ((stripos($this->useragent, $this->mini) > -1) ||
          (stripos($this->useragent, $this->mobi) > -1))
            return $this->true; 
         else
            return $this->false; 
      }
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current browser is Opera Mobile
   // running on an Android phone.
   function DetectOperaAndroidPhone()
   {
      if ((stripos($this->useragent, $this->engineOpera) > -1) &&
        (stripos($this->useragent, $this->deviceAndroid) > -1) &&
		(stripos($this->useragent, $this->mobi) > -1))
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current browser is Opera Mobile
   // running on an Android tablet.  
   function DetectOperaAndroidTablet()
   {
      if ((stripos($this->useragent, $this->engineOpera) > -1) &&
        (stripos($this->useragent, $this->deviceAndroid) > -1) &&
		(stripos($this->useragent, $this->deviceTablet) > -1))
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current device is an Amazon Kindle (eInk devices only).
   // Note: For the Kindle Fire, use the normal Android methods. 
   function DetectKindle()
   {
      if (stripos($this->useragent, $this->deviceKindle) > -1 &&
          $this->DetectAndroid() == $this->false)
         return $this->true; 
      else
         return $this->false; 
   }
   
   //**************************
   // Detects if the current Amazon device has turned on the Silk accelerated browsing feature.
   // Note: Typically used by the the Kindle Fire.
   function DetectAmazonSilk()
   {
      if (stripos($this->useragent, $this->engineSilk) > -1)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if a Garmin Nuvifone device.
   function DetectGarminNuvifone()
   {
      if (stripos($this->useragent, $this->deviceNuvifone) > -1)
         return $this->true; 
      else
         return $this->false; 
   }
   
   //**************************
   // Detects a device running the Bada smartphone OS from Samsung.
   function DetectBada()
   {
      if (stripos($this->useragent, $this->deviceBada) > -1)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects a device running the Tizen smartphone OS.
   function DetectTizen()
   {
      if (stripos($this->useragent, $this->deviceTizen) > -1)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects a device running the Meego OS.
   function DetectMeego()
   {
      if (stripos($this->useragent, $this->deviceMeego) > -1)
         return $this->true; 
      else
         return $this->false; 
   }
   
   //**************************
   // Detects the Danger Hiptop device.
   function DetectDangerHiptop()
   {
      if (stripos($this->useragent, $this->deviceDanger) > -1 ||
          stripos($this->useragent, $this->deviceHiptop) > -1)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current browser is a Sony Mylo device.
   function DetectSonyMylo()
   {
      if ((stripos($this->useragent, $this->manuSony) > -1) &&
         ((stripos($this->useragent, $this->qtembedded) > -1) ||
          (stripos($this->useragent, $this->mylocom2) > -1)))
         return $this->true; 
      else
         return $this->false; 
   }
   
   //**************************
   // Detects if the current device is on one of the Maemo-based Nokia Internet Tablets.
   function DetectMaemoTablet()
   {
      if (stripos($this->useragent, $this->maemo) > -1)
         return $this->true; 
      //For Nokia N810, must be Linux + Tablet, or else it could be something else. 
      if ((stripos($this->useragent, $this->linux) > -1)
		&& (stripos($this->useragent, $this->deviceTablet) > -1) 
		&& ($this->DetectWebOSTablet() == $this->false)
		&& ($this->DetectAndroid() == $this->false))
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current device is an Archos media player/Internet tablet.
   function DetectArchos()
   {
      if (stripos($this->useragent, $this->deviceArchos) > -1)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current device is an Internet-capable game console.
   // Includes many handheld consoles.
   function DetectGameConsole()
   {
      if (($this->DetectSonyPlaystation() == $this->true) ||
		 ($this->DetectNintendo() == $this->true) ||
		 ($this->DetectXbox() == $this->true))
         return $this->true; 
      else
         return $this->false; 
   }
   
   //**************************
   // Detects if the current device is a Sony Playstation.
   function DetectSonyPlaystation()
   {
      if (stripos($this->useragent, $this->devicePlaystation) > -1)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current device is a handheld gaming device with
   // a touchscreen and modern iPhone-class browser. Includes the Playstation Vita.
   function DetectGamingHandheld()
   {
      if ((stripos($this->useragent, $this->devicePlaystation) > -1) &&
         (stripos($this->useragent, $this->devicePlaystationVita) > -1))
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current device is a Nintendo game device.
   function DetectNintendo()
   {
      if (stripos($this->useragent, $this->deviceNintendo) > -1 || 
           stripos($this->useragent, $this->deviceWii) > -1 ||
           stripos($this->useragent, $this->deviceNintendoDs) > -1)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects if the current device is a Microsoft Xbox.
   function DetectXbox()
   {
      if (stripos($this->useragent, $this->deviceXbox) > -1)
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // Detects whether the device is a Brew-powered device.
   function DetectBrewDevice()
   {
       if (stripos($this->useragent, $this->deviceBrew) > -1)
         return $this->true; 
      else
         return $this->false; 
   }
   
   //**************************
   // Detects whether the device supports WAP or WML.
   function DetectWapWml()
   {
       if (stripos($this->httpaccept, $this->vndwap) > -1 ||
           stripos($this->httpaccept, $this->wml) > -1)
         return $this->true; 
      else
         return $this->false; 
   }
   
   //**************************
   // Detects if the current device supports MIDP, a mobile Java technology.
   function DetectMidpCapable()
   {
       if (stripos($this->useragent, $this->deviceMidp) > -1 || 
           stripos($this->httpaccept, $this->deviceMidp) > -1)
         return $this->true; 
      else
         return $this->false; 
   }



  //*****************************
  // Device Classes
  //*****************************
   
   //**************************
   // Check to see whether the device is *any* 'smartphone'.
   //   Note: It's better to use DetectTierIphone() for modern touchscreen devices. 
   function DetectSmartphone()
   {
      //Exclude duplicates from TierIphone
      if (($this->DetectTierIphone() == $this->true)
		|| ($this->DetectS60OssBrowser() == $this->true)
		|| ($this->DetectSymbianOS() == $this->true) 
		|| ($this->DetectWindowsMobile() == $this->true)
		|| ($this->DetectBlackBerry() == $this->true)
		|| ($this->DetectPalmWebOS() == $this->true))
         return $this->true; 
      else
         return $this->false; 
   }

   //**************************
   // The quick way to detect for a mobile device.
   //   Will probably detect most recent/current mid-tier Feature Phones
   //   as well as smartphone-class devices. Excludes Apple iPads and other modern tablets.
   function DetectMobileQuick()
   {
      //Let's exclude tablets
      if ($this->isTierTablet == $this->true) 
         return $this->false;
      
      if ($this->initCompleted == $this->true ||
          $this->isMobilePhone == $this->true)
         return $this->isMobilePhone;

      //Most mobile browsing is done on smartphones
      if ($this->DetectSmartphone() == $this->true) 
         return $this->true;

       if (stripos($this->useragent, $this->mobile) > -1)
         return $this->true; 

      if (($this->DetectWapWml() == $this->true) 
			|| ($this->DetectBrewDevice() == $this->true) 
			|| ($this->DetectOperaMobile() == $this->true))
         return $this->true;
         
      if ((stripos($this->useragent, $this->engineObigo) > -1)
			|| (stripos($this->useragent, $this->engineNetfront) > -1)
			|| (stripos($this->useragent, $this->engineUpBrowser) > -1)
			|| (stripos($this->useragent, $this->engineOpenWeb) > -1))
         return $this->true; 
         
      if (($this->DetectDangerHiptop() == $this->true) 
			|| ($this->DetectMidpCapable() == $this->true) 
			|| ($this->DetectMaemoTablet() == $this->true) 
			|| ($this->DetectArchos() == $this->true))
         return $this->true; 

       if ((stripos($this->useragent, $this->devicePda) > -1) &&
		 !(stripos($this->useragent, $this->disUpdate) > -1))
         return $this->true;
      
      //We also look for Kindle devices
      if ($this->DetectKindle() == $this->true ||
         $this->DetectAmazonSilk() == $this->true) 
         return $this->true;

      else
         return $this->false; 
   }
  
   //**************************
   // The longer and more thorough way to detect for a mobile device.
   //   Will probably detect most feature phones,
   //   smartphone-class devices, Internet Tablets, 
   //   Internet-enabled game consoles, etc.
   //   This ought to catch a lot of the more obscure and older devices, also --
   //   but no promises on thoroughness!
   function DetectMobileLong()
   {
      if ($this->DetectMobileQuick() == $this->true) 
         return $this->true; 
      if ($this->DetectGameConsole() == $this->true) 
         return $this->true; 
      if ($this->DetectSonyMylo() == $this->true) 
         return $this->true; 

       //Detect older phones from certain manufacturers and operators. 
       if (stripos($this->useragent, $this->uplink) > -1)
         return $this->true; 
       if (stripos($this->useragent, $this->manuSonyEricsson) > -1)
         return $this->true; 
       if (stripos($this->useragent, $this->manuericsson) > -1)
         return $this->true; 
       if (stripos($this->useragent, $this->manuSamsung1) > -1)
         return $this->true; 

       if (stripos($this->useragent, $this->svcDocomo) > -1)
         return $this->true; 
       if (stripos($this->useragent, $this->svcKddi) > -1)
         return $this->true; 
       if (stripos($this->useragent, $this->svcVodafone) > -1)
         return $this->true; 

      else
         return $this->false; 
   }


  //*****************************
  // For Mobile Web Site Design
  //*****************************

   //**************************
   // The quick way to detect for a tier of devices.
   //   This method detects for the new generation of
   //   HTML 5 capable, larger screen tablets.
   //   Includes iPad, Android (e.g., Xoom), BB Playbook, WebOS, etc.
   function DetectTierTablet()
   {
      if ($this->initCompleted == $this->true ||
          $this->isTierTablet == $this->true)
         return $this->isTierTablet;

      if (($this->DetectIpad() == $this->true) 
         || ($this->DetectAndroidTablet() == $this->true) 
         || ($this->DetectBlackBerryTablet() == $this->true) 
         || ($this->DetectWebOSTablet() == $this->true))
         return $this->true; 
      else
         return $this->false; 
   }


   //**************************
   // The quick way to detect for a tier of devices.
   //   This method detects for devices which can 
   //   display iPhone-optimized web content.
   //   Includes iPhone, iPod Touch, Android, Windows Phone 7 and 8, BB10, WebOS, Playstation Vita, etc.
   function DetectTierIphone()
   {
      if ($this->initCompleted == $this->true ||
          $this->isTierIphone == $this->true)
         return $this->isTierIphone;

      if (($this->DetectIphoneOrIpod() == $this->true)
			|| ($this->DetectAndroidPhone() == $this->true)
			|| ($this->DetectWindowsPhone() == $this->true)
			|| ($this->DetectBlackBerry10Phone() == $this->true)
			|| ($this->DetectPalmWebOS() == $this->true)
			|| ($this->DetectBada() == $this->true)
			|| ($this->DetectTizen() == $this->true)
			|| ($this->DetectGamingHandheld() == $this->true))
         return $this->true; 
      
      //Note: BB10 phone is in the previous paragraph
      if (($this->DetectBlackBerryWebKit() == $this->true) &&
		($this->DetectBlackBerryTouch() == $this->true))
         return $this->true;
      
      else
         return $this->false; 
   }
   
   //**************************
   // The quick way to detect for a tier of devices.
   //   This method detects for devices which are likely to be capable 
   //   of viewing CSS content optimized for the iPhone, 
   //   but may not necessarily support JavaScript.
   //   Excludes all iPhone Tier devices.
   function DetectTierRichCss()
   {
      if ($this->initCompleted == $this->true ||
          $this->isTierRichCss == $this->true)
         return $this->isTierRichCss;

      if ($this->DetectMobileQuick() == $this->true) 
      {
        //Exclude iPhone Tier and e-Ink Kindle devices
        if (($this->DetectTierIphone() == $this->true) ||
            ($this->DetectKindle() == $this->true))
           return $this->false;
           
        //The following devices are explicitly ok.
        if ($this->DetectWebkit() == $this->true) //Any WebKit
           return $this->true;
        if ($this->DetectS60OssBrowser() == $this->true)
           return $this->true;
           
        //Note: 'High' BlackBerry devices ONLY
        if ($this->DetectBlackBerryHigh() == $this->true)
           return $this->true;
        
        //Older Windows 'Mobile' isn't good enough for iPhone Tier. 
        if ($this->DetectWindowsMobile() == $this->true)
           return $this->true;
        if (stripos($this->useragent, $this->engineTelecaQ) > -1)
           return $this->true;
         
        //default
        else
           return $this->false;
      }
      else
         return $this->false; 
   }

   //**************************
   // The quick way to detect for a tier of devices.
   //   This method detects for all other types of phones,
   //   but excludes the iPhone and RichCSS Tier devices.
   function DetectTierOtherPhones()
   {
      if ($this->initCompleted == $this->true ||
          $this->isTierGenericMobile == $this->true)
         return $this->isTierGenericMobile;

      //Exclude devices in the other 2 categories 
      if (($this->DetectMobileLong() == $this->true)
		&& ($this->DetectTierIphone() == $this->false)
		&& ($this->DetectTierRichCss() == $this->false))
           return $this->true;
      else
         return $this->false; 
   }
      

}


//Was informed by a MobileESP user that it's a best practice 
//  to omit the closing ?&gt; marks here. They can sometimes
//  cause errors with HTML headers.