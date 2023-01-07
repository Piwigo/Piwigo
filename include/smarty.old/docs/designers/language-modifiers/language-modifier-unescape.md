unescape {#language.modifier.unescape}
========

`unescape` is used to decode `entity`, `html` and `htmlall`. It counters
the effects of the [escape modifier](#language.modifier.escape) for the
given types.

   Parameter Position    Type    Required                                                Possible Values                                                 Default  Description
  -------------------- -------- ---------- ------------------------------------------------------------------------------------------------------------ --------- ------------------------------------------------------------------------------------------------------------------------------
           1            string      No                                             `html`, `htmlall`, `entity`,                                          `html`   This is the escape format to use.
           2            string      No      `ISO-8859-1`, `UTF-8`, and any character set supported by [`htmlentities()`](&url.php-manual;htmlentities)   `UTF-8`  The character set encoding passed to html\_entity\_decode() or htmlspecialchars\_decode() or mb\_convert\_encoding() et. al.


    <?php

    $smarty->assign('articleTitle',
                    "Germans use &quot;&Uuml;mlauts&quot; and pay in &euro;uro"
                    );

    ?>

       

These are example `unescape` template lines followed by the output


    {$articleTitle}
    Germans use &quot;&Uuml;mlauts&quot; and pay in &euro;uro

    {$articleTitle|unescape:"html"}
    Germans use "&Uuml;mlauts" and pay in &euro;uro

    {$articleTitle|unescape:"htmlall"}
    Germans use "Ümlauts" and pay in €uro

       

See also [escaping smarty parsing](#language.escaping), [escape
modifier](#language.modifier.escape).
