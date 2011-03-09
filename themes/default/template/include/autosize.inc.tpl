{combine_script id='jquery.autogrow' load='async' require='jquery' path='themes/default/js/plugins/jquery.autogrow-textarea.js'}
{* Auto size and auto grow textarea *}
{footer_script require='jquery.autogrow'}{literal}
jQuery(document).ready(function(){
	jQuery('textarea').css('overflow-y', 'hidden');
	// Auto size and auto grow for all text area
	jQuery('textarea').autogrow();
});
{/literal}{/footer_script}