nl2br {#language.modifier.nl2br}
=====

All `"\n"` line breaks will be converted to html `<br />` tags in the
given variable. This is equivalent to the PHP\'s
[`nl2br()`](&url.php-manual;nl2br) function.


    <?php

    $smarty->assign('articleTitle',
                    "Sun or rain expected\ntoday, dark tonight"
                    );

    ?>

       

Where the template is:


    {$articleTitle|nl2br}

       

Will output:


    Sun or rain expected<br />today, dark tonight

       

See also [`word_wrap`](#language.modifier.wordwrap),
[`count_paragraphs`](#language.modifier.count.paragraphs) and
[`count_sentences`](#language.modifier.count.sentences).
