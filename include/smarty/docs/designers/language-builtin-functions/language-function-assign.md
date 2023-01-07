{assign} {#language.function.assign}
========

`{assign}` is used for assigning template variables **during the
execution of a template**.

> **Note**
>
> Assignment of variables in-template is essentially placing application
> logic into the presentation that may be better handled in PHP. Use at
> your own discretion.

> **Note**
>
> See also the [`short-form`](#language.function.shortform.assign)
> method of assigning template vars.

**Attributes:**

   Attribute Name    Type    Required   Default  Description
  ---------------- -------- ---------- --------- -----------------------------------------------------------------------
        var         string     Yes       *n/a*   The name of the variable being assigned
       value        string     Yes       *n/a*   The value being assigned
       scope        string      No       *n/a*   The scope of the assigned variable: \'parent\',\'root\' or \'global\'

**Option Flags:**

    Name    Description
  --------- -----------------------------------------------------
   nocache  Assigns the variable with the \'nocache\' attribute


    {assign var="name" value="Bob"}
    {assign "name" "Bob"} {* short-hand *}

    The value of $name is {$name}.

      

The above example will output:


    The value of $name is Bob.

      


    {assign var="name" value="Bob" nocache}
    {assign "name" "Bob" nocache} {* short-hand *}

    The value of $name is {$name}.

      

The above example will output:


    The value of $name is Bob.

      


    {assign var=running_total value=$running_total+$some_array[$row].some_value}

      

Variables assigned in the included template will be seen in the
including template.


    {include file="sub_template.tpl"}
    ...
    {* display variable assigned in sub_template *}
    {$foo}<br>
    ...

      

The template above includes the example `sub_template.tpl` below


    ...
    {* foo will be known also in the including template *}
    {assign var="foo" value="something" scope=parent}
    {* bar is assigned only local in the including template *}
    {assign var="bar" value="value"}
    ...

You can assign a variable to root of the current root tree. The variable
is seen by all templates using the same root tree.


    {assign var=foo value="bar" scope="root"}

      

A global variable is seen by all templates.


    {assign var=foo value="bar" scope="global"}
    {assign "foo" "bar" scope="global"} {* short-hand *}

      

To access `{assign}` variables from a php script use
[`getTemplateVars()`](#api.get.template.vars). Here\'s the template that
creates the variable `$foo`.


    {assign var="foo" value="Smarty"}

The template variables are only available after/during template
execution as in the following script.


    <?php

    // this will output nothing as the template has not been executed
    echo $smarty->getTemplateVars('foo');

    // fetch the template to a variable
    $whole_page = $smarty->fetch('index.tpl');

    // this will output 'smarty' as the template has been executed
    echo $smarty->getTemplateVars('foo');

    $smarty->assign('foo','Even smarter');

    // this will output 'Even smarter'
    echo $smarty->getTemplateVars('foo');

    ?>

The following functions can also *optionally* assign template variables.

[`{capture}`](#language.function.capture),
[`{include}`](#language.function.include),
[`{insert}`](#language.function.insert),
[`{counter}`](#language.function.counter),
[`{cycle}`](#language.function.cycle),
[`{eval}`](#language.function.eval),
[`{fetch}`](#language.function.fetch),
[`{math}`](#language.function.math),
[`{textformat}`](#language.function.textformat)

See also [`{$var=...}`](#language.function.shortform.assign),
[`assign()`](#api.assign) and
[`getTemplateVars()`](#api.get.template.vars).
