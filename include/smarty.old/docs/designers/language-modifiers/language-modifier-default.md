default {#language.modifier.default}
=======

This is used to set a default value for a variable. If the variable is
unset or an empty string, the given default value is printed instead.
Default takes the one argument.

   Parameter Position    Type    Required   Default  Description
  -------------------- -------- ---------- --------- ---------------------------------------------------------------
           1            string      No      *empty*  This is the default value to output if the variable is empty.


    <?php

    $smarty->assign('articleTitle', 'Dealers Will Hear Car Talk at Noon.');
    $smarty->assign('email', '');

    ?>

       

Where template is:


    {$articleTitle|default:'no title'}
    {$myTitle|default:'no title'}
    {$email|default:'No email address available'}

       

Will output:


    Dealers Will Hear Car Talk at Noon.
    no title
    No email address available

       

See also the [default variable handling](#tips.default.var.handling) and
the [blank variable handling](#tips.blank.var.handling) pages.
