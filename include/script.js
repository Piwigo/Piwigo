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

