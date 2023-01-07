{eval} {#language.function.eval}
======

`{eval}` is used to evaluate a variable as a template. This can be used
for things like embedding template tags/variables into variables or
tags/variables into config file variables.

If you supply the `assign` attribute, the output of the `{eval}`
function will be assigned to this template variable instead of being
output to the template.

   Attribute Name    Type    Required   Default  Description
  ---------------- -------- ---------- --------- ------------------------------------------------------
        var         mixed      Yes       *n/a*   Variable (or string) to evaluate
       assign       string      No       *n/a*   The template variable the output will be assigned to

> **Note**
>
> -   Evaluated variables are treated the same as templates. They follow
>     the same escapement and security features just as if they were
>     templates.
>
> -   Evaluated variables are compiled on every invocation, the compiled
>     versions are not saved! However if you have [caching](#caching)
>     enabled, the output will be cached with the rest of the template.
>
> -   If the content to evaluate doesn\'t change often, or is used
>     repeatedly, consider using
>     `{include file="string:{$template_code}"}` instead. This may cache
>     the compiled state and thus doesn\'t have to run the (comparably
>     slow) compiler on every invocation.
>
The contents of the config file, `setup.conf`.


    emphstart = <strong>
    emphend = </strong>
    title = Welcome to {$company}'s home page!
    ErrorCity = You must supply a {#emphstart#}city{#emphend#}.
    ErrorState = You must supply a {#emphstart#}state{#emphend#}.

      

Where the template is:


    {config_load file='setup.conf'}

    {eval var=$foo}
    {eval var=#title#}
    {eval var=#ErrorCity#}
    {eval var=#ErrorState# assign='state_error'}
    {$state_error}

      

The above template will output:


    This is the contents of foo.
    Welcome to Foobar Pub & Grill's home page!
    You must supply a <strong>city</strong>.
    You must supply a <strong>state</strong>.

      

This outputs the server name (in uppercase) and IP. The assigned
variable `$str` could be from a database query.

     
    <?php
    $str = 'The server name is {$smarty.server.SERVER_NAME|upper} '
           .'at {$smarty.server.SERVER_ADDR}';
    $smarty->assign('foo',$str);
    ?>
     
       

Where the template is:

     
        {eval var=$foo}
     
       
