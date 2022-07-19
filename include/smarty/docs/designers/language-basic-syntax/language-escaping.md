Escaping Smarty Parsing {#language.escaping}
=======================

It is sometimes desirable or even necessary to have Smarty ignore
sections it would otherwise parse. A classic example is embedding
Javascript or CSS code in a template. The problem arises as those
languages use the { and } characters which are also the default
[delimiters](#language.function.ldelim) for Smarty.

> **Note**
>
> A good practice for avoiding escapement altogether is by separating
> your Javascript/CSS into their own files and use standard HTML methods
> to access them. This will also take advantage of browser script
> caching. When you need to embed Smarty variables/functions into your
> Javascript/CSS, then the following applies.

In Smarty templates, the { and } braces will be ignored so long as they
are surrounded by white space. This behavior can be disabled by setting
the Smarty class variable [`$auto_literal`](#variable.auto.literal) to
false.


    <script>
       // the following braces are ignored by Smarty
       // since they are surrounded by whitespace
       function foobar {
        alert('foobar!');
       }
       // this one will need literal escapement
       {literal}
        function bazzy {alert('foobar!');}
       {/literal}
    </script>
      
     

[`{literal}..{/literal}`](#language.function.literal) blocks are used
for escaping blocks of template logic. You can also escape the braces
individually with
[`{ldelim}`](#language.function.ldelim),[`{rdelim}`](#language.function.ldelim)
tags or
[`{$smarty.ldelim}`,`{$smarty.rdelim}`](#language.variables.smarty.ldelim)
variables.

Smarty\'s default delimiters { and } cleanly represent presentational
content. However if another set of delimiters suit your needs better,
you can change them with Smarty\'s
[`$left_delimiter`](#variable.left.delimiter) and
[`$right_delimiter`](#variable.right.delimiter) values.

> **Note**
>
> Changing delimiters affects ALL template syntax and escapement. Be
> sure to clear out cache and compiled files if you decide to change
> them.


    <?php

    $smarty->left_delimiter = '<!--{';
    $smarty->right_delimiter = '}-->';

    $smarty->assign('foo', 'bar');
    $smarty->assign('name', 'Albert');
    $smarty->display('example.tpl');

    ?>

      

Where the template is:


    Welcome <!--{$name}--> to Smarty
    <script language="javascript">
      var foo = <!--{$foo}-->;
      function dosomething() {
        alert("foo is " + foo);
      }
      dosomething();
    </script>

      
