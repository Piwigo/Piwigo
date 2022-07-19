Built-in Functions {#language.builtin.functions}
==================

## Table of contents
- [{$var=...}](./language-builtin-functions/language-function-shortform-assign.md)
- [{append}](./language-builtin-functions/language-function-append.md)
- [{assign}](./language-builtin-functions/language-function-assign.md)
- [{block}](./language-builtin-functions/language-function-block.md)
- [{call}](./language-builtin-functions/language-function-call.md)
- [{capture}](./language-builtin-functions/language-function-capture.md)
- [{config_load}](./language-builtin-functions/language-function-config.load)
- [{debug}](./language-builtin-functions/language-function-debug.md)
- [{extends}](./language-builtin-functions/language-function-extends.md)
- [{for}](./language-builtin-functions/language-function-for.md)
- [{foreach},{foreachelse}](./language-builtin-functions/language-function-foreach.md)
- [{function}](./language-builtin-functions/language-function-function.md)
- [{if},{elseif},{else}](./language-builtin-functions/language-function-if.md)
- [{include}](./language-builtin-functions/language-function-include.md)
- [{include_php}](./language-builtin-functions/language-function-include.php)
- [{insert}](./language-builtin-functions/language-function-insert.md)
- [{ldelim},{rdelim}](./language-builtin-functions/language-function-ldelim.md)
- [{literal}](./language-builtin-functions/language-function-literal.md)
- [{nocache}](./language-builtin-functions/language-function-nocache.md)
- [{section},{sectionelse}](./language-builtin-functions/language-function-section.md)
- [{setfilter}](./language-builtin-functions/language-function-setfilter.md)
- [{strip}](./language-builtin-functions/language-function-strip.md)
- [{while}](./language-builtin-functions/language-function-while.md)

Smarty comes with several built-in functions. These built-in functions
are the integral part of the smarty template engine. They are compiled
into corresponding inline PHP code for maximum performance.

You cannot create your own [custom
functions](./language-custom-functions.md) with the same name; and you
should not need to modify the built-in functions.

A few of these functions have an `assign` attribute which collects the
result the function to a named template variable instead of being
output; much like the [`{assign}`](./language-builtin-functions/language-function-assign.md) function.
