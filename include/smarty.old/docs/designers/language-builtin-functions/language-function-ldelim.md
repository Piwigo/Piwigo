{ldelim},{rdelim} {#language.function.ldelim}
=================

`{ldelim}` and `{rdelim}` are used for [escaping](#language.escaping)
template delimiters, by default **{** and **}**. You can also use
[`{literal}{/literal}`](#language.function.literal) to escape blocks of
text eg Javascript or CSS. See also the complementary
[`{$smarty.ldelim}`](#language.variables.smarty.ldelim).


    {* this will print literal delimiters out of the template *}

    {ldelim}funcname{rdelim} is how functions look in Smarty!

       

The above example will output:


    {funcname} is how functions look in Smarty!

       

Another example with some Javascript


    <script language="JavaScript">
    function foo() {ldelim}
        ... code ...
    {rdelim}
    </script>

       

will output


    <script language="JavaScript">
    function foo() {
        .... code ...
    }
    </script>

       


    <script language="JavaScript" type="text/javascript">
        function myJsFunction(){ldelim}
            alert("The server name\n{$smarty.server.SERVER_NAME}\n{$smarty.server.SERVER_ADDR}");
        {rdelim}
    </script>
    <a href="javascript:myJsFunction()">Click here for Server Info</a>

See also [`{literal}`](#language.function.literal) and [escaping Smarty
parsing](#language.escaping).
