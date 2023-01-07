{config\_load} {#language.function.config.load}
==============

`{config_load}` is used for loading config
[`#variables#`](#language.config.variables) from a [configuration
file](#config.files) into the template.

**Attributes:**

   Attribute Name    Type    Required   Default  Description
  ---------------- -------- ---------- --------- ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        file        string     Yes       *n/a*   The name of the config file to include
      section       string      No       *n/a*   The name of the section to load
       scope        string      no      *local*  How the scope of the loaded variables are treated, which must be one of local, parent or global. local means variables are loaded into the local template context. parent means variables are loaded into both the local context and the parent template that called it. global means variables are available to all templates.

The `example.conf` file.


    #this is config file comment

    # global variables
    pageTitle = "Main Menu"
    bodyBgColor = #000000
    tableBgColor = #000000
    rowBgColor = #00ff00

    #customer variables section
    [Customer]
    pageTitle = "Customer Info"

      

and the template


    {config_load file="example.conf"}
    {config_load "example.conf"}  {* short-hand *}

    <html>
    <title>{#pageTitle#|default:"No title"}</title>
    <body bgcolor="{#bodyBgColor#}">
    <table border="{#tableBorderSize#}" bgcolor="{#tableBgColor#}">
       <tr bgcolor="{#rowBgColor#}">
          <td>First</td>
          <td>Last</td>
          <td>Address</td>
       </tr>
    </table>
    </body>
    </html>

      

[Config Files](#config.files) may also contain sections. You can load
variables from within a section with the added attribute `section`. Note
that global config variables are always loaded along with section
variables, and same-named section variables overwrite the globals.

> **Note**
>
> Config file *sections* and the built-in template function called
> [`{section}`](#language.function.section) have nothing to do with each
> other, they just happen to share a common naming convention.


    {config_load file='example.conf' section='Customer'}
    {config_load 'example.conf' 'Customer'} {* short-hand *}

    <html>
    <title>{#pageTitle#}</title>
    <body bgcolor="{#bodyBgColor#}">
    <table border="{#tableBorderSize#}" bgcolor="{#tableBgColor#}">
       <tr bgcolor="{#rowBgColor#}">
          <td>First</td>
          <td>Last</td>
          <td>Address</td>
       </tr>
    </table>
    </body>
    </html>

      

See [`$config_overwrite`](#variable.config.overwrite) to create arrays
of config file variables.

See also the [config files](#config.files) page, [config
variables](#language.config.variables) page,
[`$config_dir`](#variable.config.dir),
[`getConfigVars()`](#api.get.config.vars) and
[`configLoad()`](#api.config.load).
