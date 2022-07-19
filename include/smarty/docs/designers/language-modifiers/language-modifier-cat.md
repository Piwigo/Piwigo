cat {#language.modifier.cat}
===

This value is concatenated to the given variable.

   Parameter Position    Type    Required   Default  Description
  -------------------- -------- ---------- --------- -----------------------------------------------
           1            string      No      *empty*  This value to catenate to the given variable.


    <?php

    $smarty->assign('articleTitle', "Psychics predict world didn't end");

    ?>

       

Where template is:


    {$articleTitle|cat:' yesterday.'}

       

Will output:


    Psychics predict world didn't end yesterday.

       
