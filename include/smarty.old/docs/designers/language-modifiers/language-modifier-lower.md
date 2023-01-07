lower {#language.modifier.lower}
=====

This is used to lowercase a variable. This is equivalent to the PHP
[`strtolower()`](&url.php-manual;strtolower) function.


    <?php

    $smarty->assign('articleTitle', 'Two Convicts Evade Noose, Jury Hung.');

    ?>

       

Where template is:


    {$articleTitle}
    {$articleTitle|lower}

       

This will output:


    Two Convicts Evade Noose, Jury Hung.
    two convicts evade noose, jury hung.

       

See also [`upper`](#language.modifier.upper) and
[`capitalize`](#language.modifier.capitalize).
