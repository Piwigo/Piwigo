{if empty($load_mode)}{$load_mode='footer'}{/if}
{combine_script id='jquery.ui.timepicker-addon' load=$load_mode require='jquery.ui.datepicker,jquery.ui.slider' path="themes/default/js/ui/jquery.ui.timepicker-addon.js"}

{$require='jquery.ui.timepicker-addon'}
{assign var="datepicker_language" value="themes/default/js/ui/i18n/jquery.ui.datepicker-`$lang_info.jquery_code`.js"}
{if "PHPWG_ROOT_PATH"|@constant|@cat:$datepicker_language|@file_exists}
{combine_script id="jquery.ui.datepicker-`$lang_info.jquery_code`" load=$load_mode require='jquery.ui.datepicker' path=$datepicker_language}
{$require=$require|cat:",jquery.ui.datepicker-`$lang_info.jquery_code`"}
{/if}

{assign var="timepicker_language" value="themes/default/js/ui/i18n/jquery.ui.timepicker-`$lang_info.jquery_code`.js"}
{if "PHPWG_ROOT_PATH"|@constant|@cat:$datepicker_language|@file_exists}
{combine_script id="jquery.ui.timepicker-`$lang_info.jquery_code`" load=$load_mode require='jquery.ui.timepicker-addon' path=$timepicker_language}
{$require=$require|cat:",jquery.ui.timepicker-`$lang_info.jquery_code`"}
{/if}

{combine_script id='datepicker' load=$load_mode require=$require path='admin/themes/default/js/datepicker.js'}

{combine_css path="themes/default/js/ui/theme/jquery.ui.theme.css"}
{combine_css path="themes/default/js/ui/theme/jquery.ui.slider.css"}
{combine_css path="themes/default/js/ui/theme/jquery.ui.datepicker.css"}
{combine_css path="themes/default/js/ui/theme/jquery.ui.timepicker-addon.css"}