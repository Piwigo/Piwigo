{footer_script require='jquery'}{literal}
jQuery(document).ready(function(){
  jQuery(".tagSelection").on("click", "label", function () {
    var parent = jQuery(this).parent('li');
    var checkbox = jQuery(this).children("input[type=checkbox]");

    if (jQuery(checkbox).is(':checked')) {
      parent.addClass("tagSelected"); 
    }
    else {
      parent.removeClass('tagSelected'); 
    }
  });
});
{/literal}{/footer_script}
