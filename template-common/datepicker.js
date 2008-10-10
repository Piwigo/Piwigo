// initialize controls
// buttonImageName: Directory and name of calendar picture
// day, month, year: selectors of visible date controls
// linked_date: selector of hidden linked dates control
// checked_on_change: selector of control to change "checked" attribut
// min_linked_date: selector of hidden linked date control witch give min value
// max_linked_date: selector of hidden linked date control witch give max value
function pwg_common_initialization_datepicker(buttonImageName, day, month, year, linked_date, checked_on_change, min_linked_date, max_linked_date)
{
  // return formated date with control values
  function pwg_get_fmt_from_ctrls()
  {
    return $(year).val() + "-" + $(month).val() + "-" + $(day).val();
  }

  // return if linked_date is valid date
  function is_valid_linked_value(linked_date_name)
  {
    array_date = $(linked_date_name).val().split('-');
    return (
      (array_date.length == 3) &&
      (array_date[0] != "") &&
      (array_date[1] != "") && (array_date[1] != "0") &&
      (array_date[2] != "") && (array_date[2] != "0")
      )
  }
  
  // Action on change date value
  function pwg_on_date_change()
  {
    pwg_check_date();
    if (checked_on_change != null)
    {
      $(checked_on_change).attr("checked", "true");
    }
  }

  // In order to desable element of list
  function pwg_disabled_selection()
  {
    array_date = $(linked_date).val().split('-');
    y = array_date[0];
    m = array_date[1];

    // Init list
    $(day + " option").attr("disabled", "");
    $(month + " option").attr("disabled", "");

    var daysInMonth = 32 - new Date(y, m - 1, 32).getDate();
    $(day + " option:gt(" + (daysInMonth) +")").attr("disabled", "disabled");

    if ((min_linked_date != null) && (is_valid_linked_value(min_linked_date) == true))
    {
      date_cmp = min_linked_date;
      op_cmp = "lt";
    }
    else if ((max_linked_date != null) && (is_valid_linked_value(max_linked_date) == true))
    {
      date_cmp = max_linked_date;
      op_cmp = "gt";
    }
    else
    {
      date_cmp = null;
      op_cmp = null;
    }

    if (op_cmp != null)
    {
      array_date = $(date_cmp).val().split('-');
      y_cmp = array_date[0];
      m_cmp = array_date[1];
      d_cmp = array_date[2];
      
      if (y == y_cmp)
      {
        $(month + " option:" + op_cmp + "(" + (m_cmp) +")").attr("disabled", "disabled");
        if (op_cmp ==  "lt")
        {
            $(month + " option:eq(" + (0) +")").attr("disabled", "");
        }

        if (m == m_cmp)
        {
          $(day + " option:" + op_cmp + "(" + (d_cmp) +")").attr("disabled", "disabled");
          if (op_cmp ==  "lt")
          {
            $(day + " option:eq(" + (0) +")").attr("disabled", "");
          }
        }
      }
    }
  }

  // Prevent selection of invalid dates through the select controls 
  function pwg_check_date()
  {
    last_date = $(linked_date).val();

    $(linked_date).val(pwg_get_fmt_from_ctrls());

    if ((min_linked_date != null) && (is_valid_linked_value(min_linked_date)))
    {
      cancel = ($(min_linked_date).datepicker("getDate") > $(linked_date).datepicker("getDate"));
    }
    else if ((max_linked_date != null) && (is_valid_linked_value(max_linked_date)))
    {
      cancel = ($(max_linked_date).datepicker("getDate") < $(linked_date).datepicker("getDate"));
    }
    else
    {
      cancel = false;
    }

    if (cancel)
    {
      array_date = last_date.split('-');
      $(year).val(array_date[0]);
      $(month).val(array_date[1]);
      $(day).val(array_date[2]);
      // check again
      pwg_check_date();
    }
  }

  jQuery().ready(function(){
    // Init hidden value
    $(linked_date).val(pwg_get_fmt_from_ctrls());

    // Init Datepicker
    jQuery(linked_date).datepicker({
      dateFormat:'yy-m-d',
      beforeShow:
        // Prepare to show a date picker linked to three select controls 
        function readLinked(input) { 
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
            pwg_on_date_change();
        },
      showOn: "both",
      buttonImage: buttonImageName,
      buttonImageOnly: true,
      buttonText: ""
      });

    // Check showed controls
    jQuery(day + ", " + month + ", " + year).change(
      function ()
      {
        pwg_on_date_change();
      });

    // Check showed controls
    jQuery(day + ", " + month + ", " + year).focus(
      function ()
      {
        pwg_disabled_selection();
      });

    // In order to init linked input
    pwg_check_date();
   });

}
