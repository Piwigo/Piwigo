{html\_select\_time} {#language.function.html.select.time}
====================

`{html_select_time}` is a [custom function](#language.custom.functions)
that creates time dropdowns for you. It can display any or all of hour,
minute, second and meridian.

The `time` attribute can have different formats. It can be a unique
timestamp, a string of the format `YYYYMMDDHHMMSS` or a string that is
parseable by PHP\'s [`strtotime()`](&url.php-manual;strtotime).

      Attribute Name                                                                                                                         Type                                                                                                                      Required                        Default                        Description
  ----------------------- ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- ---------- ---------------------------------------------------- -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
          prefix                                                                                                                            string                                                                                                                        No                            Time\_                        What to prefix the var name with
           time            [timestamp](&url.php-manual;function.time), [DateTime](&url.php-manual;class.DateTime), mysql timestamp or any string parsable by [`strtotime()`](&url.php-manual;strtotime), arrays as produced by this function if field\_array is set.      No      current [timestamp](&url.php-manual;function.time)  What date/time to pre-select. If an array is given, the attributes field\_array and prefix are used to identify the array elements to extract hour, minute, second and meridian from.
      display\_hours                                                                                                                        boolean                                                                                                                       No                             TRUE                         Whether or not to display hours
     display\_minutes                                                                                                                       boolean                                                                                                                       No                             TRUE                         Whether or not to display minutes
     display\_seconds                                                                                                                       boolean                                                                                                                       No                             TRUE                         Whether or not to display seconds
     display\_meridian                                                                                                                      boolean                                                                                                                       No                             TRUE                         Whether or not to display meridian (am/pm)
      use\_24\_hours                                                                                                                        boolean                                                                                                                       No                             TRUE                         Whether or not to use 24 hour clock
     minute\_interval                                                                                                                       integer                                                                                                                       No                              1                           Number interval in minute dropdown
     second\_interval                                                                                                                       integer                                                                                                                       No                              1                           Number interval in second dropdown
       hour\_format                                                                                                                         string                                                                                                                        No                            \%02d                         What format the hour label should be in (sprintf)
    hour\_value\_format                                                                                                                     string                                                                                                                        No                            \%20d                         What format the hour value should be in (sprintf)
      minute\_format                                                                                                                        string                                                                                                                        No                            \%02d                         What format the minute label should be in (sprintf)
   minute\_value\_format                                                                                                                    string                                                                                                                        No                            \%20d                         What format the minute value should be in (sprintf)
      second\_format                                                                                                                        string                                                                                                                        No                            \%02d                         What format the second label should be in (sprintf)
   second\_value\_format                                                                                                                    string                                                                                                                        No                            \%20d                         What format the second value should be in (sprintf)
       field\_array                                                                                                                         string                                                                                                                        No                             n/a                          Outputs values to array of this name
        all\_extra                                                                                                                          string                                                                                                                        No                             null                         Adds extra attributes to select/input tags if given
        hour\_extra                                                                                                                         string                                                                                                                        No                             null                         Adds extra attributes to select/input tags if given
       minute\_extra                                                                                                                        string                                                                                                                        No                             null                         Adds extra attributes to select/input tags if given
       second\_extra                                                                                                                        string                                                                                                                        No                             null                         Adds extra attributes to select/input tags if given
      meridian\_extra                                                                                                                       string                                                                                                                        No                             null                         Adds extra attributes to select/input tags if given
     field\_separator                                                                                                                       string                                                                                                                        No                             \\n                          String printed between different fields
     option\_separator                                                                                                                      string                                                                                                                        No                             \\n                          String printed between different options of a field
          all\_id                                                                                                                           string                                                                                                                        No                             null                         Adds id-attribute to all select/input tags if given
         hour\_id                                                                                                                           string                                                                                                                        No                             null                         Adds id-attribute to select/input tags if given
        minute\_id                                                                                                                          string                                                                                                                        No                             null                         Adds id-attribute to select/input tags if given
        second\_id                                                                                                                          string                                                                                                                        No                             null                         Adds id-attribute to select/input tags if given
       meridian\_id                                                                                                                         string                                                                                                                        No                             null                         Adds id-attribute to select/input tags if given
        all\_empty                                                                                                                          string                                                                                                                        No                             null                         If supplied then the first element of any select-box has this value as it\'s label and "" as it\'s value. This is useful to make the select-boxes read "Please select" for example.
        hour\_empty                                                                                                                         string                                                                                                                        No                             null                         If supplied then the first element of the hour\'s select-box has this value as it\'s label and "" as it\'s value. This is useful to make the select-box read "Please select an hour" for example.
       minute\_empty                                                                                                                        string                                                                                                                        No                             null                         If supplied then the first element of the minute\'s select-box has this value as it\'s label and "" as it\'s value. This is useful to make the select-box read "Please select an minute" for example.
       second\_empty                                                                                                                        string                                                                                                                        No                             null                         If supplied then the first element of the second\'s select-box has this value as it\'s label and "" as it\'s value. This is useful to make the select-box read "Please select an second" for example.
      meridian\_empty                                                                                                                       string                                                                                                                        No                             null                         If supplied then the first element of the meridian\'s select-box has this value as it\'s label and "" as it\'s value. This is useful to make the select-box read "Please select an meridian" for example.


    {html_select_time use_24_hours=true}

      

At 9:20 and 23 seconds in the morning the template above would output:


    <select name="Time_Hour">
    <option value="00">00</option>
    <option value="01">01</option>
    ... snipped ....
    <option value="08">08</option>
    <option value="09" selected>09</option>
    <option value="10">10</option>
    ... snipped ....
    <option value="22">22</option>
    <option value="23">23</option>
    </select>
    <select name="Time_Minute">
    <option value="00">00</option>
    <option value="01">01</option>
    ... snipped ....
    <option value="19">19</option>
    <option value="20" selected>20</option>
    <option value="21">21</option>
    ... snipped ....
    <option value="58">58</option>
    <option value="59">59</option>
    </select>
    <select name="Time_Second">
    <option value="00">00</option>
    <option value="01">01</option>
    ... snipped ....
    <option value="22">22</option>
    <option value="23" selected>23</option>
    <option value="24">24</option>
    ... snipped ....
    <option value="58">58</option>
    <option value="59">59</option>
    </select>
    <select name="Time_Meridian">
    <option value="am" selected>AM</option>
    <option value="pm">PM</option>
    </select>

      

See also [`$smarty.now`](#language.variables.smarty.now),
[`{html_select_date}`](#language.function.html.select.date) and the
[date tips page](#tips.dates).
