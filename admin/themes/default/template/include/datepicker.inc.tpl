{combine_script id='datepicker.js' load='footer' require='jquery.ui.datepicker' path='themes/default/js/datepicker.js'}

{assign var="datepicker_language" value="themes/default/js/ui/i18n/jquery.ui.datepicker-`$lang_info.code`.js"}

{if "PHPWG_ROOT_PATH"|@constant|@cat:$datepicker_language|@file_exists}
{combine_script id="jquery.ui.datepicker-$lang_info.code" load='footer' path=$datepicker_language}
{/if}

{combine_css path="themes/default/js/ui/theme/jquery.ui.datepicker.css"}

{footer_script require='jquery.ui.datepicker,datepicker.js'}
function pwg_initialization_datepicker(day, month, year, linked_date, checked_on_change, min_linked_date, max_linked_date)
{ldelim}
  return pwg_common_initialization_datepicker(
    "{$ROOT_URL}{$themeconf.admin_icon_dir}/datepicker.png",
    day, month, year, linked_date, checked_on_change, min_linked_date, max_linked_date);
};
{/footer_script}
