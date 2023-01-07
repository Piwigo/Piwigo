{math} {#language.function.math}
======

`{math}` allows the template designer to do math equations in the
template.

-   Any numeric template variables may be used in the equations, and the
    result is printed in place of the tag.

-   The variables used in the equation are passed as parameters, which
    can be template variables or static values.

-   +, -, /, \*, abs, ceil, cos, exp, floor, log, log10, max, min, pi,
    pow, rand, round, sin, sqrt, srans and tan are all valid operators.
    Check the PHP documentation for further information on these
    [math](&url.php-manual;eval) functions.

-   If you supply the `assign` attribute, the output of the `{math}`
    function will be assigned to this template variable instead of being
    output to the template.

> **Note**
>
> `{math}` is an expensive function in performance due to its use of the
> php [`eval()`](&url.php-manual;eval) function. Doing the math in PHP
> is much more efficient, so whenever possible do the math calculations
> in the script and [`assign()`](#api.assign) the results to the
> template. Definitely avoid repetitive `{math}` function calls, eg
> within [`{section}`](#language.function.section) loops.

   Attribute Name    Type     Required   Default  Description
  ---------------- --------- ---------- --------- --------------------------------------------------
      equation      string      Yes       *n/a*   The equation to execute
       format       string       No       *n/a*   The format of the result (sprintf)
        var         numeric     Yes       *n/a*   Equation variable value
       assign       string       No       *n/a*   Template variable the output will be assigned to
    \[var \...\]    numeric     Yes       *n/a*   Equation variable value

**Example a:**


       {* $height=4, $width=5 *}

       {math equation="x + y" x=$height y=$width}

      

The above example will output:


       9

      

**Example b:**


       {* $row_height = 10, $row_width = 20, #col_div# = 2, assigned in template *}

       {math equation="height * width / division"
       height=$row_height
       width=$row_width
       division=#col_div#}

      

The above example will output:


       100

      

**Example c:**


       {* you can use parenthesis *}

       {math equation="(( x + y ) / z )" x=2 y=10 z=2}

      

The above example will output:


       6

      

**Example d:**


       {* you can supply a format parameter in sprintf format *}

       {math equation="x + y" x=4.4444 y=5.0000 format="%.2f"}
       
      

The above example will output:


       9.44

      
