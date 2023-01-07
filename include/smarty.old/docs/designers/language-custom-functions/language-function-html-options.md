{html\_options} {#language.function.html.options}
===============

`{html_options}` is a [custom function](#language.custom.functions) that
creates the html `<select><option>` group with the assigned data. It
takes care of which item(s) are selected by default as well.

   Attribute Name         Type                       Required                 Default  Description
  ---------------- ------------------- ------------------------------------- --------- -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
       values             array         Yes, unless using options attribute    *n/a*   An array of values for dropdown
       output             array         Yes, unless using options attribute    *n/a*   An array of output for dropdown
      selected        string/array                      No                    *empty*  The selected option element(s)
      options       associative array   Yes, unless using values and output    *n/a*   An associative array of values and output
        name             string                         No                    *empty*  Name of select group
       strict            boolean                        No                    *FALSE*  Will make the \"extra\" attributes *disabled* and *readonly* only be set, if they were supplied with either boolean *TRUE* or string *\"disabled\"* and *\"readonly\"* respectively

-   Required attributes are `values` and `output`, unless you use the
    combined `options` instead.

-   If the optional `name` attribute is given, the `<select></select>`
    tags are created, otherwise ONLY the `<option>` list is generated.

-   If a given value is an array, it will treat it as an html
    `<optgroup>`, and display the groups. Recursion is supported with
    `<optgroup>`.

-   All parameters that are not in the list above are printed as
    name/value-pairs inside the `<select>` tag. They are ignored if the
    optional `name` is not given.

-   All output is XHTML compliant.

<!-- -->


    <?php
    $smarty->assign('myOptions', array(
                                    1800 => 'Joe Schmoe',
                                    9904 => 'Jack Smith',
                                    2003 => 'Charlie Brown')
                                    );
    $smarty->assign('mySelect', 9904);
    ?>

      

The following template will generate a drop-down list. Note the presence
of the `name` attribute which creates the `<select>` tags.


    {html_options name=foo options=$myOptions selected=$mySelect}

      

Output of the above example would be:


    <select name="foo">
    <option value="1800">Joe Schmoe</option>
    <option value="9904" selected="selected">Jack Smith</option>
    <option value="2003">Charlie Brown</option>
    </select>


    <?php
    $smarty->assign('cust_ids', array(56,92,13));
    $smarty->assign('cust_names', array(
                                  'Joe Schmoe',
                                  'Jane Johnson',
                                  'Charlie Brown'));
    $smarty->assign('customer_id', 92);
    ?>

      

The above arrays would be output with the following template (note the
use of the php [`count()`](&url.php-manual;function.count) function as a
modifier to set the select size).


    <select name="customer_id" size="{$cust_names|@count}">
       {html_options values=$cust_ids output=$cust_names selected=$customer_id}
    </select>

      

The above example would output:


    <select name="customer_id" size="3">
        <option value="56">Joe Schmoe</option>
        <option value="92" selected="selected">Jane Johnson</option>
        <option value="13">Charlie Brown</option>
    </select>


      


    <?php

    $sql = 'select type_id, types from contact_types order by type';
    $smarty->assign('contact_types',$db->getAssoc($sql));

    $sql = 'select contact_id, name, email, contact_type_id
            from contacts where contact_id='.$contact_id;
    $smarty->assign('contact',$db->getRow($sql));

    ?>

Where a template could be as follows. Note the use of the
[`truncate`](#language.modifier.truncate) modifier.


    <select name="type_id">
        <option value='null'>-- none --</option>
        {html_options options=$contact_types|truncate:20 selected=$contact.type_id}
    </select>

      


    <?php
    $arr['Sport'] = array(6 => 'Golf', 9 => 'Cricket',7 => 'Swim');
    $arr['Rest']  = array(3 => 'Sauna',1 => 'Massage');
    $smarty->assign('lookups', $arr);
    $smarty->assign('fav', 7);
    ?>

      

The script above and the following template


    {html_options name=foo options=$lookups selected=$fav}

      

would output:


    <select name="foo">
    <optgroup label="Sport">
    <option value="6">Golf</option>
    <option value="9">Cricket</option>
    <option value="7" selected="selected">Swim</option>
    </optgroup>
    <optgroup label="Rest">
    <option value="3">Sauna</option>
    <option value="1">Massage</option>
    </optgroup>
    </select>

See also [`{html_checkboxes}`](#language.function.html.checkboxes) and
[`{html_radios}`](#language.function.html.radios)
