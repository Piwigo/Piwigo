{\$var=\...} {#language.function.shortform.assign}
============

This is a short-hand version of the {assign} function. You can assign
values directly to the template, or assign values to array elements too.

> **Note**
>
> Assignment of variables in-template is essentially placing application
> logic into the presentation that may be better handled in PHP. Use at
> your own discretion.

The following attributes can be added to the tag:

**Attributes:**

   Attribute Name   Shorthand    Type    Required   Default  Description
  ---------------- ----------- -------- ---------- --------- -----------------------------------------------------------------------
       scope           n/a      string      No       *n/a*   The scope of the assigned variable: \'parent\',\'root\' or \'global\'

**Option Flags:**

    Name    Description
  --------- -----------------------------------------------------
   nocache  Assigns the variable with the \'nocache\' attribute


    {$name='Bob'}

    The value of $name is {$name}.

      

The above example will output:


    The value of $name is Bob.

      


    {$running_total=$running_total+$some_array[row].some_value}

      


    {$user.name="Bob"}

      


    {$user.name.first="Bob"}

      


    {$users[]="Bob"}

      

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
    {$foo="something" scope=parent}
    {* bar is assigned only local in the including template *}
    {$bar="value"}
    ...

See also [`{assign}`](#language.function.assign) and
[`{append}`](#language.function.append)
