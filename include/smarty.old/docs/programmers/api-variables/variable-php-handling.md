\$php\_handling {#variable.php.handling}
===============

This tells Smarty how to handle PHP code embedded in the templates.
There are four possible settings, the default being
`Smarty::PHP_PASSTHRU`. Note that this does NOT affect php code within
[`{php}{/php}`](#language.function.php) tags in the template.

-   `Smarty::PHP_PASSTHRU` - Smarty echos tags as-is.

-   `Smarty::PHP_QUOTE` - Smarty quotes the tags as html entities.

-   `Smarty::PHP_REMOVE` - Smarty removes the tags from the templates.

-   `Smarty::PHP_ALLOW` - Smarty will execute the tags as PHP code.

> **Note**
>
> Embedding PHP code into templates is highly discouraged. Use [custom
> functions](#plugins.functions) or [modifiers](#plugins.modifiers)
> instead.
