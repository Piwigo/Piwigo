
// Correctly handle PNG transparency in Win IE 5.5 or higher.
// http://homepage.ntlworld.com/bobosola. Updated 02-March-2004
// 15-Jully-2006 : chrisaga use \" instead of ' in imgTitle
//		 : to fix ' display in tooltips
//		 : keep the alt attribute

function correctPNG() 
   {
   for(var i=0; i<document.images.length; i++)
      {
      var img = document.images[i]
      //if (img.className == "button" || img.className == "icon")
          {
	  var imgName = img.src.toUpperCase()
	  if (imgName.substring(imgName.length-3, imgName.length) == "PNG")
	     {
		 var imgID = (img.id) ? "id='" + img.id + "' " : ""
		 var imgClass = (img.className) ? "class='" + img.className + "' " : ""
		 //var imgTitle = (img.title) ? "title=\"" + img.title + "\" " : "alt=\"" + img.alt + "\" "
		 var imgTitle = (img.title) ? "title=\"" + img.title + "\" " : "";
		 imgTitle = imgTitle + (img.alt) ? "title=\"" + img.alt + "\" " : "";
		 var imgStyle = "display:inline-block;" + img.style.cssText 
		 if (img.align == "left") imgStyle = "float:left;" + imgStyle
		 if (img.align == "right") imgStyle = "float:right;" + imgStyle
		 if (img.parentElement.href) imgStyle = "cursor:hand;" + imgStyle		
		 var strNewHTML = "<span " + imgID + imgClass + imgTitle
		 + " style=\"" + "width:" + img.width + "px; height:" + img.height + "px;" + imgStyle + ";"
	     + "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader"
		 + "(src=\'" + img.src + "\', sizingMethod='scale');\"></span>" 
		 img.outerHTML = strNewHTML
		 i = i-1
	     }
          }
      }
   }
window.attachEvent("onload", correctPNG);
