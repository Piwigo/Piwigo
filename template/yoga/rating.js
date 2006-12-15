makeNiceRatingForm();

function makeNiceRatingForm()
{
  var form = document.getElementById('rateForm');
  if (!form) return; //? template changed
  gRatingButtons = form.getElementsByTagName('input');

  gUserRating = "";
  for (var i=0; i<gRatingButtons.length; i++)
  {
    if ( gRatingButtons[i].type=="button" )
    {
      gUserRating = gRatingButtons[i].value;
      break;
    }
  }

  for (var i=0; i<gRatingButtons.length; i++)
  {
    var rateButton = gRatingButtons[i];
    rateButton.initialRateValue = rateButton.value; // save it as a property

    rateButton.value = ""; //hide the text IE/Opera
    rateButton.style.textIndent = "-50px"; //hide the text FF

    if (i!=gRatingButtons.length-1 && rateButton.nextSibling.nodeType == 3 /*TEXT_NODE*/)
      rateButton.parentNode.removeChild(rateButton.nextSibling);
    if (i>0 && rateButton.previousSibling.nodeType == 3 /*TEXT_NODE*/)
      rateButton.parentNode.removeChild(rateButton.previousSibling);

    if(window.addEventListener){ // Mozilla, Netscape, Firefox
      rateButton.addEventListener("click", updateRating, false );
      rateButton.addEventListener("mouseout", resetRatingStarDisplay, false );
      rateButton.addEventListener("mouseover", updateRatingStarDisplayEvt, false );
    }
    else if(window.attachEvent) { // IE
      rateButton.attachEvent("onclick", updateRating);
      rateButton.attachEvent("onmouseout", resetRatingStarDisplay);
      rateButton.attachEvent("onmouseover", updateRatingStarDisplayEvt);
    }
  }
  resetRatingStarDisplay();
}

function resetRatingStarDisplay()
{
  updateRatingStarDisplay( gUserRating );
}

function updateRatingStarDisplay(userRating)
{
  for (i=0; i<gRatingButtons.length; i++)
  {
    var rateButton = gRatingButtons[i];
    if (userRating!=="" && userRating>=rateButton.initialRateValue )
    {
      rateButton.className = "rateButtonStarFull";
    }
    else
    {
      rateButton.className = "rateButtonStarEmpty";
    }
  }
}

function updateRatingStarDisplayEvt(e)
{
  if (e.target)
    updateRatingStarDisplay(e.target.initialRateValue);
  else //IE
    updateRatingStarDisplay(e.srcElement.initialRateValue);
}

function updateRating(e)
{
  if (e.target)
    var rateButton = e.target;
  else //IE
    var rateButton = e.srcElement;
  if (rateButton.initialRateValue == gUserRating)
    return false; //nothing to do
  // some ajax here one day would be nice
  rateButton.value = rateButton.initialRateValue; // put back real value
  return true;
}