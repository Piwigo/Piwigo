upper {#language.modifier.upper}
=====

This is used to uppercase a variable. This is equivalent to the PHP
[`strtoupper()`](&url.php-manual;strtoupper) function.


    <?php
    $smarty->assign('articleTitle', "If Strike isn't Settled Quickly it may Last a While.");
    ?>

       

Where template is:


    {$articleTitle}
    {$articleTitle|upper}

       

Will output:


    If Strike isn't Settled Quickly it may Last a While.
    IF STRIKE ISN'T SETTLED QUICKLY IT MAY LAST A WHILE.

       

See also [`lower`](#language.modifier.lower) and
[`capitalize`](#language.modifier.capitalize).
