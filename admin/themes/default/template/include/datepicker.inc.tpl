{combine_script id='datepicker.js' load='footer' require='jquery.ui.datepicker' path='admin/themes/default/js/datepicker.js'}

{assign var="datepicker_language" value="themes/default/js/ui/i18n/jquery.ui.datepicker-`$lang_info.code`.js"}

{if "PHPWG_ROOT_PATH"|@constant|@cat:$datepicker_language|@file_exists}
{combine_script id="jquery.ui.datepicker-$lang_info.code" load='footer' path=$datepicker_language}
{/if}

{combine_css path="themes/default/js/ui/theme/jquery.ui.datepicker.css"}