{* was used in double_select.tpl until Piwigo 2.8, no longer used in Piwigo core *}
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