strip\_tags {#language.modifier.strip.tags}
===========

This strips out markup tags, basically anything between `<` and `>`.

   Parameter Position   Type   Required   Default  Description
  -------------------- ------ ---------- --------- ----------------------------------------------------------------
           1            bool      No       TRUE    This determines whether the tags are replaced by \' \' or \'\'


    <?php

    $smarty->assign('articleTitle',
                    "Blind Woman Gets <font face=\"helvetica\">New
    Kidney</font> from Dad she Hasn't Seen in <b>years</b>."
                   );

    ?>

       

Where template is:


    {$articleTitle}
    {$articleTitle|strip_tags} {* same as {$articleTitle|strip_tags:true} *}
    {$articleTitle|strip_tags:false}

       

Will output:


    Blind Woman Gets <font face="helvetica">New Kidney</font> from Dad she Hasn't Seen in <b>years</b>.
    Blind Woman Gets  New Kidney  from Dad she Hasn't Seen in  years .
    Blind Woman Gets New Kidney from Dad she Hasn't Seen in years.

       

See also [`replace`](#language.modifier.replace) and
[`regex_replace`](#language.modifier.regex.replace).
