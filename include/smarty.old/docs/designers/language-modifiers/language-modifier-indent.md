indent {#language.modifier.indent}
======

This indents a string on each line, default is 4. As an optional
parameter, you can specify the number of characters to indent. As an
optional second parameter, you can specify the character to use to
indent with eg use `"\t"` for a tab.

   Parameter Position    Type     Required     Default    Description
  -------------------- --------- ---------- ------------- ---------------------------------------------------
           1            integer      No           4       This determines how many characters to indent to.
           2            string       No      (one space)  This is the character used to indent with.


    <?php

    $smarty->assign('articleTitle',
                    'NJ judge to rule on nude beach.
    Sun or rain expected today, dark tonight.
    Statistics show that teen pregnancy drops off significantly after 25.'
                    );
    ?>

       

Where template is:


    {$articleTitle}

    {$articleTitle|indent}

    {$articleTitle|indent:10}

    {$articleTitle|indent:1:"\t"}

       

Will output:


    NJ judge to rule on nude beach.
    Sun or rain expected today, dark tonight.
    Statistics show that teen pregnancy drops off significantly after 25.

        NJ judge to rule on nude beach.
        Sun or rain expected today, dark tonight.
        Statistics show that teen pregnancy drops off significantly after 25.

              NJ judge to rule on nude beach.
              Sun or rain expected today, dark tonight.
              Statistics show that teen pregnancy drops off significantly after 25.

            NJ judge to rule on nude beach.
            Sun or rain expected today, dark tonight.
            Statistics show that teen pregnancy drops off significantly after 25.

       

See also [`strip`](#language.modifier.strip),
[`wordwrap`](#language.modifier.wordwrap) and
[`spacify`](#language.modifier.spacify).
