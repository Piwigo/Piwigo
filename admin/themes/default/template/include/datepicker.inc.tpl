{combine_script id='jquery.ui.timepicker-addon' load='footer' require='jquery.ui.datepicker,jquery.ui.slider' path="themes/default/js/ui/jquery.ui.timepicker-addon.js"}
{combine_script id='datepicker.js' load='footer' require='jquery.ui.timepicker-addon' path='admin/themes/default/js/datepicker.js'}

{assign var="datepicker_language" value="themes/default/js/ui/i18n/jquery.ui.datepicker-`$lang_info.code`.js"}
{if "PHPWG_ROOT_PATH"|@constant|@cat:$datepicker_language|@file_exists}
{combine_script id="jquery.ui.datepicker-$lang_info.code" load='footer' require='jquery.ui.datepicker' path=$datepicker_language}
{/if}

{assign var="timepicker_language" value="themes/default/js/ui/i18n/jquery.ui.timepicker-`$lang_info.code`.js"}
{if "PHPWG_ROOT_PATH"|@constant|@cat:$datepicker_language|@file_exists}
{combine_script id="jquery.ui.timepicker-$lang_info.code" load='footer' require='jquery.ui.timepicker-addon' path=$timepicker_language}
{/if}

{combine_css path="themes/default/js/ui/theme/jquery.ui.slider.css"}
{combine_css path="themes/default/js/ui/theme/jquery.ui.datepicker.css"}
{combine_css path="themes/default/js/ui/theme/jquery.ui.timepicker-addon.css"}