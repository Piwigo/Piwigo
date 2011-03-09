{footer_script require='jquery'}{literal}
jQuery(document).ready(function(){
  jQuery(".tagSelection label").click(function () {
    var parent = jQuery(this).parent('li');
    var checkbox = jQuery(this).children("input[type=checkbox]");

    if (jQuery(checkbox).is(':checked')) {
      jQuery(parent).addClass("tagSelected"); 
    }
    else {
      jQuery(parent).removeClass('tagSelected'); 
    }
  });
});
{/literal}{/footer_script}
