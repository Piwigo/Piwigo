count\_characters {#language.modifier.count.characters}
=================

This is used to count the number of characters in a variable.

   Parameter Position    Type     Required   Default  Description
  -------------------- --------- ---------- --------- -------------------------------------------------------------------------------
           1            boolean      No       FALSE   This determines whether or not to include whitespace characters in the count.


    <?php

    $smarty->assign('articleTitle', 'Cold Wave Linked to Temperatures.');

    ?>

       

Where template is:


    {$articleTitle}
    {$articleTitle|count_characters}
    {$articleTitle|count_characters:true}

       

Will output:


    Cold Wave Linked to Temperatures.
    29
    33

       

See also [`count_words`](#language.modifier.count.words),
[`count_sentences`](#language.modifier.count.sentences) and
[`count_paragraphs`](#language.modifier.count.paragraphs).
