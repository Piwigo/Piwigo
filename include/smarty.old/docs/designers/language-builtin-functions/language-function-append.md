{append} {#language.function.append}
========

`{append}` is used for creating or appending template variable arrays
**during the execution of a template**.

> **Note**
>
> Assignment of variables in-template is essentially placing application
> logic into the presentation that may be better handled in PHP. Use at
> your own discretion.

**Attributes:**

   Attribute Name    Type    Required   Default  Description
  ---------------- -------- ---------- --------- ----------------------------------------------------------------------------------------------------
        var         string     Yes       *n/a*   The name of the variable being assigned
       value        string     Yes       *n/a*   The value being assigned
       index        string      No       *n/a*   The index for the new array element. If not specified the value is append to the end of the array.
       scope        string      No       *n/a*   The scope of the assigned variable: \'parent\',\'root\' or \'global\'

**Option Flags:**

    Name    Description
  --------- -----------------------------------------------------
   nocache  Assigns the variable with the \'nocache\' attribute


    {append var='name' value='Bob' index='first'}
    {append var='name' value='Meyer' index='last'}
    // or 
    {append 'name' 'Bob' index='first'} {* short-hand *}
    {append 'name' 'Meyer' index='last'} {* short-hand *}

    The first name is {$name.first}.<br>
    The last name is {$name.last}.

      

The above example will output:


    The first name is Bob.
    The last name is Meyer.

      

See also [`append()`](#api.append) and
[`getTemplateVars()`](#api.get.template.vars).
