{for} {#language.function.for}
=====

The `{for}{forelse}` tag is used to create simple loops. The following
different formarts are supported:

-   `{for $var=$start to $end}` simple loop with step size of 1.

-   `{for $var=$start to $end step $step}` loop with individual step
    size.

`{forelse}` is executed when the loop is not iterated.

**Attributes:**

   Attribute Name   Shorthand    Type     Required   Default  Description
  ---------------- ----------- --------- ---------- --------- --------------------------------
        max            n/a      integer      No       *n/a*   Limit the number of iterations

**Option Flags:**

    Name    Description
  --------- --------------------------------------
   nocache  Disables caching of the `{for}` loop


    <ul>
    {for $foo=1 to 3}
        <li>{$foo}</li>
    {/for}
    </ul>

      

The above example will output:


    <ul>
        <li>1</li>
        <li>2</li>
        <li>3</li>
    </ul>

      


    $smarty->assign('to',10);

      


    <ul>
    {for $foo=3 to $to max=3}
        <li>{$foo}</li>
    {/for}
    </ul>

      

The above example will output:


    <ul>
        <li>3</li>
        <li>4</li>
        <li>5</li>
    </ul>

      


    $smarty->assign('start',10);
    $smarty->assign('to',5);

      


    <ul>
    {for $foo=$start to $to}
        <li>{$foo}</li>
    {forelse}
      no iteration
    {/for}
    </ul>

      

The above example will output:


      no iteration

      

See also [`{foreach}`](#language.function.foreach),
[`{section}`](#language.function.section) and
[`{while}`](#language.function.while)
