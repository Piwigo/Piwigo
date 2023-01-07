count\_paragraphs {#language.modifier.count.paragraphs}
=================

This is used to count the number of paragraphs in a variable.


    <?php

    $smarty->assign('articleTitle',
                     "War Dims Hope for Peace. Child's Death Ruins Couple's Holiday.\n\n
                     Man is Fatally Slain. Death Causes Loneliness, Feeling of Isolation."
                    );

    ?>

       

Where template is:


    {$articleTitle}
    {$articleTitle|count_paragraphs}

       

Will output:


    War Dims Hope for Peace. Child's Death Ruins Couple's Holiday.

    Man is Fatally Slain. Death Causes Loneliness, Feeling of Isolation.
    2

       

See also [`count_characters`](#language.modifier.count.characters),
[`count_sentences`](#language.modifier.count.sentences) and
[`count_words`](#language.modifier.count.words).
