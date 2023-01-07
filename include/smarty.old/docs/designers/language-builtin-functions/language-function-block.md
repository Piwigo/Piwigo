{block} {#language.function.block}
=======

`{block}` is used to define a named area of template source for template
inheritance. For details see section of [Template
Interitance](#advanced.features.template.inheritance).

The `{block}` template source area of a child template will replace the
correponding areas in the parent template(s).

Optionally `{block}` areas of child and parent templates can be merged
into each other. You can append or prepend the parent `{block}` content
by using the `append` or `prepend` option flag with the childs `{block}`
definition. With the {\$smarty.block.parent} the `{block}` content of
the parent template can be inserted at any location of the child
`{block}` content. {\$smarty.block.child} inserts the `{block}` content
of the child template at any location of the parent `{block}`.

`{blocks}'s` can be nested.

**Attributes:**

   Attribute Name    Type    Required   Default  Description
  ---------------- -------- ---------- --------- ---------------------------------------
        name        string     Yes       *n/a*   The name of the template source block

**Option Flags (in child templates only):**

    Name    Description
  --------- -------------------------------------------------------------------------------------------
   append   The `{block}` content will be be appended to the content of the parent template `{block}`
   prepend  The `{block}` content will be prepended to the content of the parent template `{block}`
    hide    Ignore the block content if no child block of same name is existing.
   nocache  Disables caching of the `{block}` content

parent.tpl


    <html>
      <head>
        <title>{block name="title"}Default Title{/block}</title>
        <title>{block "title"}Default Title{/block}</title>  {* short-hand  *}
      </head>
    </html>

      

child.tpl


    {extends file="parent.tpl"} 
    {block name="title"}
    Page Title
    {/block}

      

The result would look like


    <html>
      <head>
        <title>Page Title</title>
      </head>
    </html>

parent.tpl


    <html>
      <head>
        <title>{block name="title"}Title - {/block}</title>
      </head>
    </html>

      

child.tpl


    {extends file="parent.tpl"} 
    {block name="title" prepend}
    Page Title
    {/block}

      

The result would look like


    <html>
      <head>
        <title>Title - Page Title</title>
      </head>
    </html>

parent.tpl


    <html>
      <head>
        <title>{block name="title"} is my title{/block}</title>
      </head>
    </html>

      

child.tpl


    {extends file="parent.tpl"} 
    {block name="title" append}
    Page Title
    {/block}

      

The result would look like


    <html>
      <head>
        <title>Page title is my titel</title>
      </head>
    </html>

parent.tpl


    <html>
      <head>
        <title>{block name="title"}The {$smarty.block.child} was inserted here{/block}</title>
      </head>
    </html>

      

child.tpl


    {extends file="parent.tpl"} 
    {block name="title"}
    Child Title
    {/block}

      

The result would look like


    <html>
      <head>
        <title>The Child Title was inserted here</title>
      </head>
    </html>

parent.tpl


    <html>
      <head>
        <title>{block name="title"}Parent Title{/block}</title>
      </head>
    </html>

      

child.tpl


    {extends file="parent.tpl"} 
    {block name="title"}
    You will see now - {$smarty.block.parent} - here
    {/block}

      

The result would look like


    <html>
      <head>
        <title>You will see now - Parent Title - here</title>
      </head>
    </html>

See also [Template
Inheritance](#advanced.features.template.inheritance),
[`$smarty.block.parent`](#language.variables.smarty.block.parent),
[`$smarty.block.child`](#language.variables.smarty.block.child), and
[`{extends}`](#language.function.extends)
