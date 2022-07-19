strip {#language.modifier.strip}
=====

This replaces all spaces, newlines and tabs with a single space, or with
the supplied string.

> **Note**
>
> If you want to strip blocks of template text, use the built-in
> [`{strip}`](#language.function.strip) function.


    <?php
    $smarty->assign('articleTitle', "Grandmother of\neight makes\t    hole in one.");
    $smarty->display('index.tpl');
    ?>

       

Where template is:


    {$articleTitle}
    {$articleTitle|strip}
    {$articleTitle|strip:'&nbsp;'}

       

Will output:


    Grandmother of
    eight makes        hole in one.
    Grandmother of eight makes hole in one.
    Grandmother&nbsp;of&nbsp;eight&nbsp;makes&nbsp;hole&nbsp;in&nbsp;one.

       

See also [`{strip}`](#language.function.strip) and
[`truncate`](#language.modifier.truncate).
