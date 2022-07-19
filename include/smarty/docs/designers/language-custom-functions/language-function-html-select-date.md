{html\_select\_date} {#language.function.html.select.date}
====================

`{html_select_date}` is a [custom function](#language.custom.functions)
that creates date dropdowns. It can display any or all of year, month,
and day. All parameters that are not in the list below are printed as
name/value-pairs inside the `<select>` tags of day, month and year.

      Attribute Name                                                                                                                        Type                                                                                                                      Required                        Default                        Description
  ---------------------- ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- ---------- ---------------------------------------------------- --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
          prefix                                                                                                                           string                                                                                                                        No                            Date\_                        What to prefix the var name with
           time           [timestamp](&url.php-manual;function.time), [DateTime](&url.php-manual;class.DateTime), mysql timestamp or any string parsable by [`strtotime()`](&url.php-manual;strtotime), arrays as produced by this function if field\_array is set.      No      current [timestamp](&url.php-manual;function.time)  What date/time to pre-select. If an array is given, the attributes field\_array and prefix are used to identify the array elements to extract year, month and day from. Omitting this parameter or supplying a falsy value will select the current date. To prevent date selection, pass in NULL
       start\_year                                                                                                                         string                                                                                                                        No                         current year                     The first year in the dropdown, either year number, or relative to current year (+/- N)
        end\_year                                                                                                                          string                                                                                                                        No                     same as start\_year                  The last year in the dropdown, either year number, or relative to current year (+/- N)
      display\_days                                                                                                                        boolean                                                                                                                       No                             TRUE                         Whether to display days or not
     display\_months                                                                                                                       boolean                                                                                                                       No                             TRUE                         Whether to display months or not
      display\_years                                                                                                                       boolean                                                                                                                       No                             TRUE                         Whether to display years or not
       month\_names                                                                                                                         array                                                                                                                        No                             null                         List of strings to display for months. array(1 =\> \'Jan\', ..., 12 =\> \'Dec\')
      month\_format                                                                                                                        string                                                                                                                        No                             \%B                          What format the month should be in (strftime)
       day\_format                                                                                                                         string                                                                                                                        No                            \%02d                         What format the day output should be in (sprintf)
    day\_value\_format                                                                                                                     string                                                                                                                        No                             \%d                          What format the day value should be in (sprintf)
      year\_as\_text                                                                                                                       boolean                                                                                                                       No                            FALSE                         Whether or not to display the year as text
      reverse\_years                                                                                                                       boolean                                                                                                                       No                            FALSE                         Display years in reverse order
       field\_array                                                                                                                        string                                                                                                                        No                             null                         If a name is given, the select boxes will be drawn such that the results will be returned to PHP in the form of name\[Day\], name\[Year\], name\[Month\].
        day\_size                                                                                                                          string                                                                                                                        No                             null                         Adds size attribute to select tag if given
       month\_size                                                                                                                         string                                                                                                                        No                             null                         Adds size attribute to select tag if given
        year\_size                                                                                                                         string                                                                                                                        No                             null                         Adds size attribute to select tag if given
        all\_extra                                                                                                                         string                                                                                                                        No                             null                         Adds extra attributes to all select/input tags if given
        day\_extra                                                                                                                         string                                                                                                                        No                             null                         Adds extra attributes to select/input tags if given
       month\_extra                                                                                                                        string                                                                                                                        No                             null                         Adds extra attributes to select/input tags if given
       year\_extra                                                                                                                         string                                                                                                                        No                             null                         Adds extra attributes to select/input tags if given
         all\_id                                                                                                                           string                                                                                                                        No                             null                         Adds id-attribute to all select/input tags if given
         day\_id                                                                                                                           string                                                                                                                        No                             null                         Adds id-attribute to select/input tags if given
        month\_id                                                                                                                          string                                                                                                                        No                             null                         Adds id-attribute to select/input tags if given
         year\_id                                                                                                                          string                                                                                                                        No                             null                         Adds id-attribute to select/input tags if given
       field\_order                                                                                                                        string                                                                                                                        No                             MDY                          The order in which to display the fields
     field\_separator                                                                                                                      string                                                                                                                        No                             \\n                          String printed between different fields
   month\_value\_format                                                                                                                    string                                                                                                                        No                             \%m                          strftime() format of the month values, default is %m for month numbers.
        all\_empty                                                                                                                         string                                                                                                                        No                             null                         If supplied then the first element of any select-box has this value as it\'s label and "" as it\'s value. This is useful to make the select-boxes read "Please select" for example.
       year\_empty                                                                                                                         string                                                                                                                        No                             null                         If supplied then the first element of the year\'s select-box has this value as it\'s label and "" as it\'s value. This is useful to make the select-box read "Please select a year" for example. Note that you can use values like "-MM-DD" as time-attribute to indicate an unselected year.
       month\_empty                                                                                                                        string                                                                                                                        No                             null                         If supplied then the first element of the month\'s select-box has this value as it\'s label and "" as it\'s value. . Note that you can use values like "YYYY\--DD" as time-attribute to indicate an unselected month.
        day\_empty                                                                                                                         string                                                                                                                        No                             null                         If supplied then the first element of the day\'s select-box has this value as it\'s label and "" as it\'s value. Note that you can use values like "YYYY-MM-" as time-attribute to indicate an unselected day.

> **Note**
>
> There is an useful php function on the [date tips page](#tips.dates)
> for converting `{html_select_date}` form values to a timestamp.

Template code


    {html_select_date}

      

This will output:


    <select name="Date_Month">
    <option value="1">January</option>
    <option value="2">February</option>
    <option value="3">March</option>
      ..... snipped .....
    <option value="10">October</option>
    <option value="11">November</option>
    <option value="12" selected="selected">December</option>
    </select>
    <select name="Date_Day">
    <option value="1">01</option>
    <option value="2">02</option>
    <option value="3">03</option>
      ..... snipped .....
    <option value="11">11</option>
    <option value="12">12</option>
    <option value="13" selected="selected">13</option>
    <option value="14">14</option>
    <option value="15">15</option>
      ..... snipped .....
    <option value="29">29</option>
    <option value="30">30</option>
    <option value="31">31</option>
    </select>
    <select name="Date_Year">
    <option value="2006" selected="selected">2006</option>
    </select>

      


    {* start and end year can be relative to current year *}
    {html_select_date prefix='StartDate' time=$time start_year='-5'
       end_year='+1' display_days=false}

      

With 2000 as the current year the output:


    <select name="StartDateMonth">
    <option value="1">January</option>
    <option value="2">February</option>
    .... snipped ....
    <option value="11">November</option>
    <option value="12" selected="selected">December</option>
    </select>
    <select name="StartDateYear">
    <option value="1995">1995</option>
    .... snipped ....
    <option value="1999">1999</option>
    <option value="2000" selected="selected">2000</option>
    <option value="2001">2001</option>
    </select>

      

See also [`{html_select_time}`](#language.function.html.select.time),
[`date_format`](#language.modifier.date.format),
[`$smarty.now`](#language.variables.smarty.now) and the [date tips
page](#tips.dates).
