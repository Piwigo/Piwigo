function SelectAll( formulaire )
{
var len = formulaire.elements.length;
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
var len = formulaire.elements.length;
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
var len = formulaire.elements.length;
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

function phpWGOpenWindow(theURL,winName,features)
{
  img = new Image();
  img.src = theURL;
  if (img.complete)
  {
    var width=img.width +40;
    var height=img.height +40;
  }
  else
  {
    var width=640;
    var height=480;
    img.onload = resizeWindowToFit;
  }
  newWin = window.open(theURL,winName,features+',left=2,top=1,width=' + width + ',height=' + height);
}

function resizeWindowToFit()
{
  newWin.resizeTo( img.width+50, img.height+100);
}

function popuphelp(url)
{
  window.open(
    url,
    'dc_popup',
    'alwaysRaised=yes,dependent=yes,toolbar=no,height=420,width=500,menubar=no,resizable=yes,scrollbars=yes,status=no'
  );
}

