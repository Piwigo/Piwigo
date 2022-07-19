count\_words {#language.modifier.count.words}
============

This is used to count the number of words in a variable.


    <?php

    $smarty->assign('articleTitle', 'Dealers Will Hear Car Talk at Noon.');

    ?>

       

Where template is:


    {$articleTitle}
    {$articleTitle|count_words}

       

This will output:


    Dealers Will Hear Car Talk at Noon.
    7

       

See also [`count_characters`](#language.modifier.count.characters),
[`count_paragraphs`](#language.modifier.count.paragraphs) and
[`count_sentences`](#language.modifier.count.sentences).
