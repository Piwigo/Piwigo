{\$smarty} reserved variable {#language.variables.smarty}
============================

The PHP reserved `{$smarty}` variable can be used to access several
environment and request variables. The full list of them follows.

Request variables {#language.variables.smarty.request}
-----------------

The [request variables](&url.php-manual;reserved.variables) such as
`$_GET`, `$_POST`, `$_COOKIE`, `$_SERVER`, `$_ENV` and `$_SESSION` can
be accessed as demonstrated in the examples below:


    {* display value of page from URL ($_GET) http://www.example.com/index.php?page=foo *}
    {$smarty.get.page}

    {* display the variable "page" from a form ($_POST['page']) *}
    {$smarty.post.page}

    {* display the value of the cookie "username" ($_COOKIE['username']) *}
    {$smarty.cookies.username}

    {* display the server variable "SERVER_NAME" ($_SERVER['SERVER_NAME'])*}
    {$smarty.server.SERVER_NAME}

    {* display the system environment variable "PATH" *}
    {$smarty.env.PATH}

    {* display the php session variable "id" ($_SESSION['id']) *}
    {$smarty.session.id}

    {* display the variable "username" from merged get/post/cookies/server/env *}
    {$smarty.request.username}

       

> **Note**
>
> For historical reasons `{$SCRIPT_NAME}` is short-hand for
> `{$smarty.server.SCRIPT_NAME}`.
>
>
>     <a href="{$SCRIPT_NAME}?page=smarty">click me</a>
>     <a href="{$smarty.server.SCRIPT_NAME}?page=smarty">click me</a>

> **Note**
>
> Although Smarty provides direct access to PHP super globals for
> convenience, it should be used with caution. Directly accessing super
> globals mixes underlying application code structure with templates. A
> good practice is to assign specific needed values to template vars.

{\$smarty.now} {#language.variables.smarty.now}
--------------

The current [timestamp](&url.php-manual;function.time) can be accessed
with `{$smarty.now}`. The value reflects the number of seconds passed
since the so-called Epoch on January 1, 1970, and can be passed directly
to the [`date_format`](#language.modifier.date.format) modifier for
display. Note that [`time()`](&url.php-manual;function.time) is called
on each invocation; eg a script that takes three seconds to execute with
a call to `$smarty.now` at start and end will show the three second
difference.

::: {.informalexample}

    {* use the date_format modifier to show current date and time *}
    {$smarty.now|date_format:'%Y-%m-%d %H:%M:%S'}

       
:::

{\$smarty.const} {#language.variables.smarty.const}
----------------

You can access PHP constant values directly. See also [smarty
constants](#smarty.constants).

::: {.informalexample}

    <?php
    // the constant defined in php
    define('MY_CONST_VAL','CHERRIES');
    ?>
:::

Output the constant in a template with

::: {.informalexample}

    {$smarty.const.MY_CONST_VAL}
:::

> **Note**
>
> Although Smarty provides direct access to PHP constants for
> convenience, it is typically avoided as this is mixing underlying
> application code structure into the templates. A good practice is to
> assign specific needed values to template vars.

{\$smarty.capture} {#language.variables.smarty.capture}
------------------

Template output captured via the built-in
[`{capture}..{/capture}`](#language.function.capture) function can be
accessed using the `{$smarty.capture}` variable. See the
[`{capture}`](#language.function.capture) page for more information.

{\$smarty.config} {#language.variables.smarty.config}
-----------------

`{$smarty.config}` variable can be used to refer to loaded [config
variables](#language.config.variables). `{$smarty.config.foo}` is a
synonym for `{#foo#}`. See the
[{config\_load}](#language.function.config.load) page for more info.

{\$smarty.section} {#language.variables.smarty.loops}
------------------

The `{$smarty.section}` variables can be used to refer to
[`{section}`](#language.function.section) loop properties. These have
some very useful values such as `.first`, `.index`, etc.

> **Note**
>
> The `{$smarty.foreach}` variable is no longer used with the new
> [`{foreach}`](#language.function.foreach) syntax, but is still
> supported with Smarty 2.x style foreach syntax.

{\$smarty.template} {#language.variables.smarty.template}
-------------------

Returns the name of the current template being processed (without the
directory).

{\$smarty.template\_object} {#language.variables.smarty.template_object}
---------------------------

Returns the template object of the current template being processed.

{\$smarty.current\_dir} {#language.variables.smarty.current_dir}
-----------------------

Returns the name of the directory for the current template being
processed.

{\$smarty.version} {#language.variables.smarty.version}
------------------

Returns the version of Smarty the template was compiled with.


    <div id="footer">Powered by Smarty {$smarty.version}</div>

{\$smarty.block.child} {#language.variables.smarty.block.child}
----------------------

Returns block text from child template. See [Template
interitance](#advanced.features.template.inheritance).

{\$smarty.block.parent} {#language.variables.smarty.block.parent}
-----------------------

Returns block text from parent template. See [Template
interitance](#advanced.features.template.inheritance)

{\$smarty.ldelim}, {\$smarty.rdelim} {#language.variables.smarty.ldelim}
------------------------------------

These variables are used for printing the left-delimiter and
right-delimiter value literally, the same as
[`{ldelim},{rdelim}`](#language.function.ldelim).

See also [assigned variables](#language.assigned.variables) and [config
variables](#language.config.variables)
