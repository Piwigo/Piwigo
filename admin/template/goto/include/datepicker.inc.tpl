{* $Id$ *}
{known_script id="jquery" src=$ROOT_URL|@cat:"template-common/lib/jquery.packed.js"}
{known_script id="jquery.ui" src=$ROOT_URL|@cat:"template-common/lib/ui/ui.core.packed.js"}
{known_script id="jquery.ui.datepicker" src=$ROOT_URL|@cat:"template-common/lib/ui/ui.datepicker.packed.js"}
{known_script id="jquery.ui.datepicker-$lang_info.code" src=$ROOT_URL|@cat:"template-common/lib/ui/i18n/ui.datepicker-"|@cat:$lang_info.code|@cat:".js"}
{known_script id="datepicker.js" src=$ROOT_URL|@cat:"template-common/datepicker.js"}

{html_head}
<link rel="stylesheet" type="text/css" href="{$ROOT_URL}template-common/lib/ui/ui.datepicker.css">
{/html_head}

<script type="text/javascript">
function pwg_initialization_datepicker(day, month, year, linked_date, checked_on_change, min_linked_date, max_linked_date)
{ldelim}
  return pwg_common_initialization_datepicker(
    "{$ROOT_URL}admin/template/{$themeconf.template}/icon/datepicker.png",
    day, month, year, linked_date, checked_on_change, min_linked_date, max_linked_date);
}
</script>
