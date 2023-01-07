{textformat} {#language.function.textformat}
============

`{textformat}` is a [block function](#plugins.block.functions) used to
format text. It basically cleans up spaces and special characters, and
formats paragraphs by wrapping at a boundary and indenting lines.

You can set the parameters explicitly, or use a preset style. Currently
"email" is the only available style.

   Attribute Name    Type     Required       Default       Description
  ---------------- --------- ---------- ------------------ ----------------------------------------------------------------------------------------
       style        string       No           *n/a*        Preset style
       indent       number       No            *0*         The number of chars to indent every line
   indent\_first    number       No            *0*         The number of chars to indent the first line
    indent\_char    string       No      *(single space)*  The character (or string of chars) to indent with
        wrap        number       No            *80*        How many characters to wrap each line to
     wrap\_char     string       No           *\\n*        The character (or string of chars) to break each line with
     wrap\_cut      boolean      No          *FALSE*       If TRUE, wrap will break the line at the exact character instead of at a word boundary
       assign       string       No           *n/a*        The template variable the output will be assigned to


       {textformat wrap=40}

       This is foo.
       This is foo.
       This is foo.
       This is foo.
       This is foo.
       This is foo.

       This is bar.

       bar foo bar foo     foo.
       bar foo bar foo     foo.
       bar foo bar foo     foo.
       bar foo bar foo     foo.
       bar foo bar foo     foo.
       bar foo bar foo     foo.
       bar foo bar foo     foo.

       {/textformat}


      

The above example will output:



       This is foo. This is foo. This is foo.
       This is foo. This is foo. This is foo.

       This is bar.

       bar foo bar foo foo. bar foo bar foo
       foo. bar foo bar foo foo. bar foo bar
       foo foo. bar foo bar foo foo. bar foo
       bar foo foo. bar foo bar foo foo.

      


       {textformat wrap=40 indent=4}

       This is foo.
       This is foo.
       This is foo.
       This is foo.
       This is foo.
       This is foo.

       This is bar.

       bar foo bar foo     foo.
       bar foo bar foo     foo.
       bar foo bar foo     foo.
       bar foo bar foo     foo.
       bar foo bar foo     foo.
       bar foo bar foo     foo.
       bar foo bar foo     foo.

       {/textformat}


      

The above example will output:



       This is foo. This is foo. This is
       foo. This is foo. This is foo. This
       is foo.

       This is bar.

       bar foo bar foo foo. bar foo bar foo
       foo. bar foo bar foo foo. bar foo
       bar foo foo. bar foo bar foo foo.
       bar foo bar foo foo. bar foo bar
       foo foo.

      


       {textformat wrap=40 indent=4 indent_first=4}

       This is foo.
       This is foo.
       This is foo.
       This is foo.
       This is foo.
       This is foo.

       This is bar.

       bar foo bar foo     foo.
       bar foo bar foo     foo.
       bar foo bar foo     foo.
       bar foo bar foo     foo.
       bar foo bar foo     foo.
       bar foo bar foo     foo.
       bar foo bar foo     foo.

       {/textformat}

      

The above example will output:



       This is foo. This is foo. This
       is foo. This is foo. This is foo.
       This is foo.

       This is bar.

       bar foo bar foo foo. bar foo bar
       foo foo. bar foo bar foo foo. bar
       foo bar foo foo. bar foo bar foo
       foo. bar foo bar foo foo. bar foo
       bar foo foo.

      


       {textformat style="email"}

       This is foo.
       This is foo.
       This is foo.
       This is foo.
       This is foo.
       This is foo.

       This is bar.

       bar foo bar foo     foo.
       bar foo bar foo     foo.
       bar foo bar foo     foo.
       bar foo bar foo     foo.
       bar foo bar foo     foo.
       bar foo bar foo     foo.
       bar foo bar foo     foo.

       {/textformat}


      

The above example will output:



       This is foo. This is foo. This is foo. This is foo. This is foo. This is
       foo.

       This is bar.

       bar foo bar foo foo. bar foo bar foo foo. bar foo bar foo foo. bar foo
       bar foo foo. bar foo bar foo foo. bar foo bar foo foo. bar foo bar foo
       foo.


      

See also [`{strip}`](#language.function.strip) and
[`wordwrap`](#language.modifier.wordwrap).
