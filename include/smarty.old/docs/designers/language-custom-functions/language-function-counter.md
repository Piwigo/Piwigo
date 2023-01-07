{counter} {#language.function.counter}
=========

`{counter}` is used to print out a count. `{counter}` will remember the
count on each iteration. You can adjust the number, the interval and the
direction of the count, as well as determine whether or not to print the
value. You can run multiple counters concurrently by supplying a unique
name for each one. If you do not supply a name, the name "default" will
be used.

If you supply the `assign` attribute, the output of the `{counter}`
function will be assigned to this template variable instead of being
output to the template.

   Attribute Name    Type     Required    Default   Description
  ---------------- --------- ---------- ----------- ------------------------------------------------------
        name        string       No      *default*  The name of the counter
       start        number       No         *1*     The initial number to start counting from
        skip        number       No         *1*     The interval to count by
     direction      string       No        *up*     The direction to count (up/down)
       print        boolean      No       *TRUE*    Whether or not to print the value
       assign       string       No        *n/a*    the template variable the output will be assigned to


    {* initialize the count *}
    {counter start=0 skip=2}<br />
    {counter}<br />
    {counter}<br />
    {counter}<br />

      

this will output:


    0<br />
    2<br />
    4<br />
    6<br />

      
