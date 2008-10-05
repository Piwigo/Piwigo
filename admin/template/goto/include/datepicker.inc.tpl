{* $Id$ *}
{known_script id="jquery" src=$ROOT_URL|@cat:"template-common/lib/jquery.packed.js"}
{known_script id="jquery.ui" src=$ROOT_URL|@cat:"template-common/lib/ui/ui.core.packed.js"}
{known_script id="jquery.ui.datepicker" src=$ROOT_URL|@cat:"template-common/lib/ui/ui.datepicker.packed.js"}
{known_script id="jquery.ui.datepicker-$lang_info.code" src=$ROOT_URL|@cat:"template-common/lib/ui/i18n/ui.datepicker-"|@cat:$lang_info.code|@cat:".js"}

{html_head}
<link rel="stylesheet" type="text/css" href="{$ROOT_URL}admin/template/{$themeconf.template}/datepicker.css">
{/html_head}

{literal}
<script type="text/javascript">
// return formated date with control values
// day, month, year: selectors of visible date controls
function pwg_get_fmt_datepicker(day, month, year)
{
  return $(year).val() + "-" + $(month).val() + "-" + $(day).val();
}

// initialize controls
// day, month, year: selectors of visible date controls
// linked_date: selector of hidden linked dates control
// min_linked_date: selector of hidden linked date control witch give min value
// max_linked_date: selector of hidden linked date control witch give max value
function pwg_initialization_datepicker(day, month, year, linked_date, min_linked_date, max_linked_date)
{
  // Prevent selection of invalid dates through the select controls 
  function pwg_check_date()
  {
    array_date = $(linked_date).val().split('-');
    y = array_date[0];
    m = array_date[1];
    d = array_date[2];

    $(linked_date).val(pwg_get_fmt_datepicker(day, month, year));

    if ((min_linked_date != null) && ($(min_linked_date).datepicker("getDate") != null))
    {
      cancel = ($(min_linked_date).datepicker("getDate") > $(linked_date).datepicker("getDate"));
    }
    else if ((max_linked_date != null) && ($(max_linked_date).datepicker("getDate") != null))
    {
      cancel = ($(max_linked_date).datepicker("getDate") < $(linked_date).datepicker("getDate"));
    }
    else
    {
      cancel = false;
    }

    if (cancel)
    {
      $(year).val(y);
      $(month).val(m);
      $(day).val(d);
      // check again
      pwg_check_date();
    }
    else
    {
      var daysInMonth = 32 - new Date($(year).val(), $(month).val() - 1, 32).getDate();
      $(day + " option").attr("disabled", "");
      $(day + " option:gt(" + (daysInMonth) +")").attr("disabled", "disabled");
    }
  }

  jQuery().ready(function(){
    // Init hidden value
    $(linked_date).val(pwg_get_fmt_datepicker(day, month, year));

    // Init Datepicker
    jQuery(linked_date).datepicker({
      dateFormat:'yy-m-d',
      beforeShow:
        // Prepare to show a date picker linked to three select controls 
        function readLinked(input) { 
            //$(linked_date).val(pwg_get_fmt_datepicker(day, month, year));
            if (min_linked_date != null)
            {
              return {minDate: $(min_linked_date).datepicker("getDate")};
            }
            else if (max_linked_date != null)
            {
              return {maxDate: $(max_linked_date).datepicker("getDate")};
            }
            else
            {
              return {};
            }
        },
      onSelect:
        // Update three select controls to match a date picker selection 
        function updateLinked(date) {
            if (date.length == 0)
            {
              $(year).val("");
              $(month).val("0");
              $(day).val("0");
            }
            else
            {
              array_date = date.split('-');
              $(year).val(array_date[0]);
              $(month).val(array_date[1]);
              $(day).val(array_date[2]);
            }
            pwg_check_date();
        },
      showOn: "both",
{/literal}
      buttonImage: "{$ROOT_URL}admin/template/{$themeconf.template}/icon/datepicker.png", 
{literal}
      buttonImageOnly: true
      });

    // Check showed controls
    jQuery(day + ", " + month + ", " + year).change(
      function ()
      {
        pwg_check_date();
      });

    // In order to desable element of list
    pwg_check_date();
   });

}
</script>
{/literal}
