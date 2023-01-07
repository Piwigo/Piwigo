{html\_radios} {#language.function.html.radios}
==============

`{html_radios}` is a [custom function](#language.custom.functions) that
creates a HTML radio button group. It also takes care of which item is
selected by default as well.

   Attribute Name         Type                       Required                 Default  Description
  ---------------- ------------------- ------------------------------------- --------- -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        name             string                         No                    *radio*  Name of radio list
       values             array         Yes, unless using options attribute    *n/a*   An array of values for radio buttons
       output             array         Yes, unless using options attribute    *n/a*   An array of output for radio buttons
      selected           string                         No                    *empty*  The selected radio element
      options       associative array   Yes, unless using values and output    *n/a*   An associative array of values and output
     separator           string                         No                    *empty*  String of text to separate each radio item
       assign            string                         No                    *empty*  Assign radio tags to an array instead of output
       labels            boolean                        No                    *TRUE*   Add \<label\>-tags to the output
     label\_ids          boolean                        No                    *FALSE*  Add id-attributes to \<label\> and \<input\> to the output
       escape            boolean                        No                    *TRUE*   Escape the output / content (values are always escaped)
       strict            boolean                        No                    *FALSE*  Will make the \"extra\" attributes *disabled* and *readonly* only be set, if they were supplied with either boolean *TRUE* or string *\"disabled\"* and *\"readonly\"* respectively

-   Required attributes are `values` and `output`, unless you use
    `options` instead.

-   All output is XHTML compliant.

-   All parameters that are not in the list above are output as
    name/value-pairs inside each of the created `<input>`-tags.

<!-- -->


    <?php

    $smarty->assign('cust_ids', array(1000,1001,1002,1003));
    $smarty->assign('cust_names', array(
                                  'Joe Schmoe',
                                  'Jack Smith',
                                  'Jane Johnson',
                                  'Charlie Brown')
                                  );
    $smarty->assign('customer_id', 1001);

    ?>

      

Where template is:


    {html_radios name='id' values=$cust_ids output=$cust_names
           selected=$customer_id separator='<br />'}
       
      


    <?php

    $smarty->assign('cust_radios', array(
                                   1000 => 'Joe Schmoe',
                                   1001 => 'Jack Smith',
                                   1002 => 'Jane Johnson',
                                   1003 => 'Charlie Brown'));
    $smarty->assign('customer_id', 1001);

    ?>

      

Where template is:


    {html_radios name='id' options=$cust_radios
         selected=$customer_id separator='<br />'}

      

Both examples will output:


    <label><input type="radio" name="id" value="1000" />Joe Schmoe</label><br />
    <label><input type="radio" name="id" value="1001" checked="checked" />Jack Smith</label><br />
    <label><input type="radio" name="id" value="1002" />Jane Johnson</label><br />
    <label><input type="radio" name="id" value="1003" />Charlie Brown</label><br />

      


    <?php

    $sql = 'select type_id, types from contact_types order by type';
    $smarty->assign('contact_types',$db->getAssoc($sql));

    $sql = 'select contact_id, name, email, contact_type_id '
            .'from contacts where contact_id='.$contact_id;
    $smarty->assign('contact',$db->getRow($sql));

    ?>

      

The variable assigned from the database above would be output with the
template:


    {html_radios name='contact_type_id' options=$contact_types
         selected=$contact.contact_type_id separator='<br />'}

      

See also [`{html_checkboxes}`](#language.function.html.checkboxes) and
[`{html_options}`](#language.function.html.options)
