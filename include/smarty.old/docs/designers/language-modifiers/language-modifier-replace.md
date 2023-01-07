replace {#language.modifier.replace}
=======

A simple search and replace on a variable. This is equivalent to the
PHP\'s [`str_replace()`](&url.php-manual;str_replace) function.

   Parameter Position    Type    Required   Default  Description
  -------------------- -------- ---------- --------- ---------------------------------------------
           1            string     Yes       *n/a*   This is the string of text to be replaced.
           2            string     Yes       *n/a*   This is the string of text to replace with.


    <?php

    $smarty->assign('articleTitle', "Child's Stool Great for Use in Garden.");

    ?>

       

Where template is:


    {$articleTitle}
    {$articleTitle|replace:'Garden':'Vineyard'}
    {$articleTitle|replace:' ':'   '}

       

Will output:


    Child's Stool Great for Use in Garden.
    Child's Stool Great for Use in Vineyard.
    Child's   Stool   Great   for   Use   in   Garden.

       

See also [`regex_replace`](#language.modifier.regex.replace) and
[`escape`](#language.modifier.escape).
