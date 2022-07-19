escape {#language.modifier.escape}
======

`escape` is used to encode or escape a variable to `html`, `url`,
`single quotes`, `hex`, `hexentity`, `javascript` and `mail`. By default
its `html`.

   Parameter Position    Type     Required                                                Possible Values                                                 Default  Description
  -------------------- --------- ---------- ------------------------------------------------------------------------------------------------------------ --------- -------------------------------------------------------------------------------------
           1            string       No             `html`, `htmlall`, `url`, `urlpathinfo`, `quotes`, `hex`, `hexentity`, `javascript`, `mail`           `html`   This is the escape format to use.
           2            string       No      `ISO-8859-1`, `UTF-8`, and any character set supported by [`htmlentities()`](&url.php-manual;htmlentities)   `UTF-8`  The character set encoding passed to htmlentities() et. al.
           3            boolean      No                                                        FALSE                                                       TRUE    Double encode entites from &amp; to &amp;amp; (applys to `html` and `htmlall` only)


    <?php

    $smarty->assign('articleTitle',
                    "'Stiff Opposition Expected to Casketless Funeral Plan'"
                    );
    $smarty->assign('EmailAddress','smarty@example.com');

    ?>

       

These are example `escape` template lines followed by the output


    {$articleTitle}
    'Stiff Opposition Expected to Casketless Funeral Plan'

    {$articleTitle|escape}
    &#039;Stiff Opposition Expected to Casketless Funeral Plan&#039;

    {$articleTitle|escape:'html'}    {* escapes  & " ' < > *}
    &#039;Stiff Opposition Expected to Casketless Funeral Plan&#039;

    {$articleTitle|escape:'htmlall'} {* escapes ALL html entities *}
    &#039;Stiff Opposition Expected to Casketless Funeral Plan&#039;

    <a href="?title={$articleTitle|escape:'url'}">click here</a>
    <a
    href="?title=%27Stiff%20Opposition%20Expected%20to%20Casketless%20Funeral%20Plan%27">click here</a>

    {$articleTitle|escape:'quotes'}
    \'Stiff Opposition Expected to Casketless Funeral Plan\'

    <a href="mailto:{$EmailAddress|escape:"hex"}">{$EmailAddress|escape:"hexentity"}</a>
    {$EmailAddress|escape:'mail'}    {* this converts to email to text *}
    <a href="mailto:%62%6f%..snip..%65%74">&#x62;&#x6f;&#x62..snip..&#x65;&#x74;</a>

    {'mail@example.com'|escape:'mail'}
    smarty [AT] example [DOT] com

       


    {* the "rewind" parameter registers the current location *}
    <a href="$my_path?page=foo&rewind=$my_uri|urlencode}">click here</a>

       

This snippet is useful for emails, but see also
[`{mailto}`](#language.function.mailto)


    {* email address mangled *}
    <a href="mailto:{$EmailAddress|escape:'hex'}">{$EmailAddress|escape:'mail'}</a>

       

See also [escaping smarty parsing](#language.escaping),
[`{mailto}`](#language.function.mailto) and the [obfuscating email
addresses](#tips.obfuscating.email) page.
