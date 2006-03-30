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

function phpWGOpenWindow(theURL,winName,features)
{
  window.open(theURL,winName,features);
}

function popuphelp(url)
{
  window.open(
    url,
    'dc_popup',
    'alwaysRaised=yes,dependent=yes,toolbar=no,height=420,width=500,menubar=no,resizable=yes,scrollbars=yes,status=no'
  );
}

