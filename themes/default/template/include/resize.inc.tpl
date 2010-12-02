{combine_script id='jquery' load='footer' path='themes/default/js/jquery.packed.js'}
{combine_script id='jquery.ui' load='footer' require='jquery' path='themes/default/js/ui/packed/ui.core.packed.js'}
{combine_script id='jquery.ui.resizable' load='footer' require='jquery.ui' path='themes/default/js/ui/packed/ui.resizable.packed.js'}

{* Resize possible *}
{footer_script}{literal}
  jQuery().ready(function(){
    // Resize possible for list
    jQuery(".categoryList").resizable({
      handles: "all",
      animate: true,
      animateDuration: "slow",
      animateEasing: "swing",
      preventDefault: true,
      preserveCursor: true,
      autoHide: true,
      ghost: true
    });
  });
{/literal}{/footer_script}

