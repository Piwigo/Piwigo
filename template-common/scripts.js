function SelectAll( formulaire )
{
len = formulaire.elements.length;
var i=0;
for( i = 0; i < len; i++)
{
  if ( formulaire.elements[i].type=='checkbox'
	   && formulaire.elements[i].name != 'copie')
  {
	formulaire.elements[i].checked = true;
  }
}
}

function DeselectAll( formulaire )
{
len = formulaire.elements.length;
var i=0;
for( i = 0; i < len; i++)
{
  if ( formulaire.elements[i].type=='checkbox'
	   && formulaire.elements[i].name != 'copie')
  {
	formulaire.elements[i].checked = false;
  }
}
}

function Inverser( formulaire )
{
len = formulaire.elements.length;
var i=0;
for( i=0; i<len; i++)
{
  if ( formulaire.elements[i].type=='checkbox'
	   && formulaire.elements[i].name != 'copie')
  {
	formulaire.elements[i].checked = !formulaire.elements[i].checked;
  }
}
}

function verifieAndOpen()
{
  var ok=1;
  if (!img.complete)
  {
    // sometime the image loading is not complete
    // especially with KHTML and Opera 
    setTimeout("verifieAndOpen()",200)
  }
  else
  {
  /* give more space for scrollbars (10 for FF, 40 for IE) */
    width=img.width +40;
    height=img.height +40;
    window.open(owURL,owName,owFeatures  + ',width=' + width + ',height=' + height);
  }
}

function phpWGOpenWindow(theURL,winName,features)
{
  img = new Image()
  img.src = theURL;
  owURL=theURL;
  owName=winName;
  owFeatures=features;
  verifieAndOpen();
}

function popuphelp(url)
{
  window.open(
    url,
    'dc_popup',
    'alwaysRaised=yes,dependent=yes,toolbar=no,height=420,width=500,menubar=no,resizable=yes,scrollbars=yes,status=no'
  );
}

