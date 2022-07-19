{capture} {#language.function.capture}
=========

`{capture}` is used to collect the output of the template between the
tags into a variable instead of displaying it. Any content between
`{capture name='foo'}` and `{/capture}` is collected into the variable
specified in the `name` attribute.

The captured content can be used in the template from the variable
[`$smarty.capture.foo`](#language.variables.smarty.capture) where "foo"
is the value passed in the `name` attribute. If you do not supply the
`name` attribute, then "default" will be used as the name ie
`$smarty.capture.default`.

`{capture}'s` can be nested.

**Attributes:**

   Attribute Name    Type    Required   Default  Description
  ---------------- -------- ---------- --------- ----------------------------------------------------------------------
        name        string     Yes       *n/a*   The name of the captured block
       assign       string      No       *n/a*   The variable name where to assign the captured output to
       append       string      No       *n/a*   The name of an array variable where to append the captured output to

**Option Flags:**

    Name    Description
  --------- -----------------------------------------
   nocache  Disables caching of this captured block

> **Note**
>
> Be careful when capturing [`{insert}`](#language.function.insert)
> output. If you have [`$caching`](#caching) enabled and you have
> [`{insert}`](#language.function.insert) commands that you expect to
> run within cached content, do not capture this content.


    {* we don't want to print a div tag unless content is displayed *}
    {capture name="banner"}
    {capture "banner"} {* short-hand *}
      {include file="get_banner.tpl"}
    {/capture}

    {if $smarty.capture.banner ne ""}
    <div id="banner">{$smarty.capture.banner}</div>
    {/if}

       

This example demonstrates the capture function.


    {capture name=some_content assign=popText}
    {capture some_content assign=popText} {* short-hand *}
    The server is {$my_server_name|upper} at {$my_server_addr}<br>
    Your ip is {$my_ip}.
    {/capture}
    <a href="#">{$popText}</a>

         

This example also demonstrates how multiple calls of capture can be used
to create an array with captured content.


    {capture append="foo"}hello{/capture}I say just {capture append="foo"}world{/capture}
    {foreach $foo as $text}{$text} {/foreach}

         

The above example will output:


    I say just hello world

      

See also [`$smarty.capture`](#language.variables.smarty.capture),
[`{eval}`](#language.function.eval),
[`{fetch}`](#language.function.fetch), [`fetch()`](#api.fetch) and
[`{assign}`](#language.function.assign).
