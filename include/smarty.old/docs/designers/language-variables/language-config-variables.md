Variables loaded from config files {#language.config.variables}
==================================

Variables that are loaded from the [config files](#config.files) are
referenced by enclosing them within `#hash_marks#`, or with the smarty
variable [`$smarty.config`](#language.variables.smarty.config). The
later syntax is useful for embedding into quoted attribute values, or
accessing variable values such as \$smarty.config.\$foo.

Example config file - `foo.conf`:


    pageTitle = "This is mine"
    bodyBgColor = '#eeeeee'
    tableBorderSize = 3
    tableBgColor = "#bbbbbb"
    rowBgColor = "#cccccc"

        

A template demonstrating the `#hash#` method:


    {config_load file='foo.conf'}
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

        

A template demonstrating the
[`$smarty.config`](#language.variables.smarty.config) method:


    {config_load file='foo.conf'}
    <html>
    <title>{$smarty.config.pageTitle}</title>
    <body bgcolor="{$smarty.config.bodyBgColor}">
    <table border="{$smarty.config.tableBorderSize}" bgcolor="{$smarty.config.tableBgColor}">
    <tr bgcolor="{$smarty.config.rowBgColor}">
        <td>First</td>
        <td>Last</td>
        <td>Address</td>
    </tr>
    </table>
    </body>
    </html>

        

Both examples would output:


    <html>
    <title>This is mine</title>
    <body bgcolor="#eeeeee">
    <table border="3" bgcolor="#bbbbbb">
    <tr bgcolor="#cccccc">
        <td>First</td>
        <td>Last</td>
        <td>Address</td>
    </tr>
    </table>
    </body>
    </html>

        

Config file variables cannot be used until after they are loaded in from
a config file. This procedure is explained later in this document under
[`{config_load}`](#language.function.config.load).

See also [variables](#language.syntax.variables) and [\$smarty reserved
variables](#language.variables.smarty)
