regex\_replace {#language.modifier.regex.replace}
==============

A regular expression search and replace on a variable. Use the
[`preg_replace()`](&url.php-manual;preg_replace) syntax from the PHP
manual.

> **Note**
>
> Although Smarty supplies this regex convenience modifier, it is
> usually better to apply regular expressions in PHP, either via custom
> functions or modifiers. Regular expressions are considered application
> code and are not part of presentation logic.

Parameters

   Parameter Position    Type    Required   Default  Description
  -------------------- -------- ---------- --------- ------------------------------------------------
           1            string     Yes       *n/a*   This is the regular expression to be replaced.
           2            string     Yes       *n/a*   This is the string of text to replace with.


    <?php

    $smarty->assign('articleTitle', "Infertility unlikely to\nbe passed on, experts say.");

    ?>

       

Where template is:


    {* replace each carriage return, tab and new line with a space *}

    {$articleTitle}
    {$articleTitle|regex_replace:"/[\r\t\n]/":" "}

       

Will output:


    Infertility unlikely to
    be passed on, experts say.
    Infertility unlikely to be passed on, experts say.

       

See also [`replace`](#language.modifier.replace) and
[`escape`](#language.modifier.escape).
