count\_sentences {#language.modifier.count.sentences}
================

This is used to count the number of sentences in a variable. A sentence
being delimited by a dot, question- or exclamation-mark (.?!).


    <?php

    $smarty->assign('articleTitle',
                     'Two Soviet Ships Collide - One Dies.
                     Enraged Cow Injures Farmer with Axe.'
                     );

    ?>

       

Where template is:


    {$articleTitle}
    {$articleTitle|count_sentences}

       

Will output:


    Two Soviet Ships Collide - One Dies. Enraged Cow Injures Farmer with Axe.
    2

       

See also [`count_characters`](#language.modifier.count.characters),
[`count_paragraphs`](#language.modifier.count.paragraphs) and
[`count_words`](#language.modifier.count.words).
