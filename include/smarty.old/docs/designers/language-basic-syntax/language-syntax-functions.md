Functions {#language.syntax.functions}
=========

Every Smarty tag either prints a [variable](#language.variables) or
invokes some sort of function. These are processed and displayed by
enclosing the function and its [attributes](#language.syntax.attributes)
within delimiters like so: `{funcname attr1="val1" attr2="val2"}`.


    {config_load file="colors.conf"}

    {include file="header.tpl"}
    {insert file="banner_ads.tpl" title="My Site"}

    {if $logged_in}
        Welcome, <span style="color:{#fontColor#}">{$name}!</span>
    {else}
        hi, {$name}
    {/if}

    {include file="footer.tpl"}

      

-   Both [built-in functions](#language.builtin.functions) and [custom
    functions](#language.custom.functions) have the same syntax within
    templates.

-   Built-in functions are the **inner** workings of Smarty, such as
    [`{if}`](#language.function.if),
    [`{section}`](#language.function.section) and
    [`{strip}`](#language.function.strip). There should be no need to
    change or modify them.

-   Custom functions are **additional** functions implemented via
    [plugins](#plugins). They can be modified to your liking, or you can
    create new ones. [`{html_options}`](#language.function.html.options)
    is an example of a custom function.

See also [`registerPlugin()`](#api.register.plugin)
