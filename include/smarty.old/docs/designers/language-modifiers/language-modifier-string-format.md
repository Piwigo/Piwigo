string\_format {#language.modifier.string.format}
==============

This is a way to format strings, such as decimal numbers and such. Use
the syntax for [`sprintf()`](&url.php-manual;sprintf) for the
formatting.

   Parameter Position    Type    Required   Default  Description
  -------------------- -------- ---------- --------- ---------------------------------------
           1            string     Yes       *n/a*   This is what format to use. (sprintf)


    <?php

    $smarty->assign('number', 23.5787446);

    ?>

       

Where template is:


    {$number}
    {$number|string_format:"%.2f"}
    {$number|string_format:"%d"}

       

Will output:


    23.5787446
    23.58
    23

       

See also [`date_format`](#language.modifier.date.format).
