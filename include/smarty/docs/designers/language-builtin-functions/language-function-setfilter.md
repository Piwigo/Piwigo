{setfilter} {#language.function.setfilter}
===========

The `{setfilter}...{/setfilter}` block tag allows the definition of
template instance\'s variable filters.

SYNTAX: {setfilter filter1\|filter2\|filter3\....}\...{/setfilter}

The filter can be:

-   A variable filter plugin specified by it\'s name.

-   A modidier specified by it\'s name and optional additional
    parameter.

`{setfilter}...{/setfilter}` blocks can be nested. The filter definition
of inner blocks does replace the definition of the outer block.

Template instance filters run in addition to other modifiers and
filters. They run in the following order: modifier, default\_modifier,
\$escape\_html, registered variable filters, autoloaded variable
filters, template instance\'s variable filters. Everything after
default\_modifier can be disabled with the `nofilter` flag.


    <script>
    {setfilter filter1}
      {$foo} {* filter1 runs on output of $foo *}
      {setfilter filter2|mod:true}
        {$bar} {* filter2 and modifier mod runs on output of $bar *}
      {/setfilter}
      {$buh} {* filter1 runs on output of $buh *}
    {/setfilter}
    {$blar} {* no template instance filter runs on output of $blar}
    </script>

      

> **Note**
>
> The setting of template instance filters does not effect the output of
> included subtemplates.
