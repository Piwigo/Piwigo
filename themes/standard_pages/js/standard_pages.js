let modeCookie = getCookie("mode"); 
if("" != modeCookie)
{
  toggle_mode(modeCookie);
}
else
{
  let prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  console.log(prefersDark)
  toggle_mode(prefersDark ? "dark" : "light");
}

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
  let newMode = event.matches ? "dark" : "light";
  toggle_mode(newMode);
});

jQuery( document ).ready(function() {
  jQuery("#selected-language").textContent = selected_language;

  //Override empty input message
  jQuery("form").on("submit", function (e) {
    let isValid = true;

    jQuery(".column-flex").each(function (i) {
      // Because we overid the default browser error message 
      // we need to distinguish which fields are now required
      // To do this we use data-required="true" on the input
      let input = $(this).find("input");
      if($(input).data("required") == true)
      {
        let input = jQuery(this).find("input");
        let errorMessage = jQuery(this).find(".error-message");
        if (!input.val().trim()) {
          
          e.preventDefault(); 
          input[0].setCustomValidity(""); // Override browser tooltip (empty space hides it)
          errorMessage.show(); 
          isValid = false;
        } else {
          input[0].setCustomValidity("");
          errorMessage.hide();
        }
      }
    });
  
    return isValid;
  });
  
    // Hide error message and reset validation on input
    jQuery(".column-flex input").on("input", function () {
      let errorMessage = jQuery(this).closest(".column-flex").find(".error-message");
      jQuery(this)[0].setCustomValidity(""); // Reset browser tooltip
      errorMessage.hide();
    });
  

  // Hide error message when user starts typing
  jQuery(".column-flex input").on("input", function () {
    jQuery(this).closest(".column-flex").find(".error-message").hide();
  });

});

function toggle_mode(mode) {
  setCookie("mode",mode,30);
  if ("dark" == mode)
  {
    //Dark mode
    jQuery( "#toggle_mode_light" ).hide(); 
    jQuery( "#toggle_mode_dark" ).show(); 
    jQuery( "#mode" ).addClass("dark");
    jQuery( "#mode" ).removeClass("light"); 
    jQuery( "#piwigo-logo" ).attr("src", url_logo_dark);
  }
  else
  {
    //Light mode
    jQuery( "#toggle_mode_dark" ).hide();
    jQuery( "#toggle_mode_light" ).show();
    jQuery( "#mode" ).addClass("light");
    jQuery( "#mode" ).removeClass("dark"); 
    jQuery( "#piwigo-logo" ).attr("src", url_logo_light);
  }
}

function setCookie(cname, cvalue, exdays) {
  const d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  let expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
  if (cname == "lang")
  {
    location.reload();
  }
}

function getCookie(cname) {
  let name = cname + "=";
  let decodedCookie = decodeURIComponent(document.cookie);
  let ca = decodedCookie.split(';');
  for(let i = 0; i <ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

jQuery(".togglePassword").click(function(e){
  var toggle = jQuery(e.target);
  var input = (jQuery(toggle).siblings('input'))[0];
  if (input.type === "password") {
    input.type = "text";
    jQuery(toggle).css("color", "#ff7700");
  } else {
    input.type = "password";
    jQuery(toggle).css("color","#898989");
  }
});

jQuery("#other-languages a").click(function(e){
  let clickedUrl = new URL(jQuery(e.target).attr('href'));
  let selectedLang = clickedUrl.searchParams.get("lang");

  if (selectedLang) {
    setCookie('lang',selectedLang,1);
  }
});