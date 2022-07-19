capitalize {#language.modifier.capitalize}
==========

This is used to capitalize the first letter of all words in a variable.
This is similar to the PHP [`ucwords()`](&url.php-manual;ucwords)
function.

   Parameter Position    Type     Required   Default  Description
  -------------------- --------- ---------- --------- -----------------------------------------------------------------------------------------------------------
           1            boolean      No       FALSE   This determines whether or not words with digits will be uppercased
           2            boolean      No       FALSE   This determines whether or not Capital letters within words should be lowercased, e.g. \"aAa\" to \"Aaa\"


    <?php

    $smarty->assign('articleTitle', 'next x-men film, x3, delayed.');

    ?>

       

Where the template is:


    {$articleTitle}
    {$articleTitle|capitalize}
    {$articleTitle|capitalize:true}

       

Will output:


    next x-men film, x3, delayed.
    Next X-Men Film, x3, Delayed.
    Next X-Men Film, X3, Delayed.

       

See also [`lower`](#language.modifier.lower) and
[`upper`](#language.modifier.upper)
