{combine_script id='jquery' load='async' path='themes/default/js/jquery.packed.js'}
{combine_script id='jquery.ui' load='async' require='jquery' path='themes/default/js/ui/packed/ui.core.packed.js' }
{combine_script id='jquery.ui.resizable' load='async' require='jquery.ui' path='themes/default/js/ui/packed/ui.resizable.packed.js' }
{footer_script require='jquery.ui.resizable'}{literal}
jQuery(document).ready(function(){
	// Resize possible for double select list
	jQuery(".doubleSelect select.categoryList").resizable({
		handles: "w,e",
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