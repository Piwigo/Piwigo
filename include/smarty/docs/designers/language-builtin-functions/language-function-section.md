{section},{sectionelse} {#language.function.section}
=======================

A `{section}` is for looping over **sequentially indexed arrays of
data**, unlike [`{foreach}`](#language.function.foreach) which is used
to loop over a **single associative array**. Every `{section}` tag must
be paired with a closing `{/section}` tag.

> **Note**
>
> The [`{foreach}`](#language.function.foreach) loop can do everything a
> {section} loop can do, and has a simpler and easier syntax. It is
> usually preferred over the {section} loop.

> **Note**
>
> {section} loops cannot loop over associative arrays, they must be
> numerically indexed, and sequential (0,1,2,\...). For associative
> arrays, use the [`{foreach}`](#language.function.foreach) loop.

   Attribute Name    Type     Required   Default  Description
  ---------------- --------- ---------- --------- -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        name        string      Yes       *n/a*   The name of the section
        loop         mixed      Yes       *n/a*   Value to determine the number of loop iterations
       start        integer      No        *0*    The index position that the section will begin looping. If the value is negative, the start position is calculated from the end of the array. For example, if there are seven values in the loop array and start is -2, the start index is 5. Invalid values (values outside of the length of the loop array) are automatically truncated to the closest valid value.
        step        integer      No        *1*    The step value that will be used to traverse the loop array. For example, step=2 will loop on index 0,2,4, etc. If step is negative, it will step through the array backwards.
        max         integer      No       *n/a*   Sets the maximum number of times the section will loop.
        show        boolean      No      *TRUE*   Determines whether or not to show this section

**Option Flags:**

    Name    Description
  --------- ------------------------------------------
   nocache  Disables caching of the `{section}` loop

-   Required attributes are `name` and `loop`.

-   The `name` of the `{section}` can be anything you like, made up of
    letters, numbers and underscores, like [PHP
    variables](&url.php-manual;language.variables).

-   {section}\'s can be nested, and the nested `{section}` names must be
    unique from each other.

-   The `loop` attribute, usually an array of values, determines the
    number of times the `{section}` will loop. You can also pass an
    integer as the loop value.

-   When printing a variable within a `{section}`, the `{section}`
    `name` must be given next to variable name within \[brackets\].

-   `{sectionelse}` is executed when there are no values in the loop
    variable.

-   A `{section}` also has its own variables that handle `{section}`
    properties. These properties are accessible as:
    [`{$smarty.section.name.property}`](#language.variables.smarty.loops)
    where "name" is the attribute `name`.

-   `{section}` properties are [`index`](#section.property.index),
    [`index_prev`](#section.property.index.prev),
    [`index_next`](#section.property.index.next),
    [`iteration`](#section.property.iteration),
    [`first`](#section.property.first),
    [`last`](#section.property.last),
    [`rownum`](#section.property.rownum),
    [`loop`](#section.property.loop), [`show`](#section.property.show),
    [`total`](#section.property.total).

[`assign()`](#api.assign) an array to Smarty


    <?php
    $data = array(1000,1001,1002);
    $smarty->assign('custid',$data);
    ?>

The template that outputs the array


    {* this example will print out all the values of the $custid array *}
    {section name=customer loop=$custid}
    {section customer $custid} {* short-hand *}
      id: {$custid[customer]}<br />
    {/section}
    <hr />
    {*  print out all the values of the $custid array reversed *}
    {section name=foo loop=$custid step=-1}
    {section foo $custid step=-1} {* short-hand *}
      {$custid[foo]}<br />
    {/section}

      

The above example will output:


    id: 1000<br />
    id: 1001<br />
    id: 1002<br />
    <hr />
    id: 1002<br />
    id: 1001<br />
    id: 1000<br />

      


    {section name=foo start=10 loop=20 step=2}
      {$smarty.section.foo.index}
    {/section}
    <hr />
    {section name=bar loop=21 max=6 step=-2}
      {$smarty.section.bar.index}
    {/section}

The above example will output:

      
    10 12 14 16 18
    <hr />
    20 18 16 14 12 10

      

The `name` of the `{section}` can be anything you like, see [PHP
variables](&url.php-manual;language.variables). It is used to reference
the data within the `{section}`.


    {section name=anything loop=$myArray}
      {$myArray[anything].foo}
      {$name[anything]}
      {$address[anything].bar}
    {/section}

      

This is an example of printing an associative array of data with a
`{section}`. Following is the php script to assign the `$contacts` array
to Smarty.

      
    <?php
    $data = array(
              array('name' => 'John Smith', 'home' => '555-555-5555',
                    'cell' => '666-555-5555', 'email' => 'john@myexample.com'),
              array('name' => 'Jack Jones', 'home' => '777-555-5555',
                    'cell' => '888-555-5555', 'email' => 'jack@myexample.com'),
              array('name' => 'Jane Munson', 'home' => '000-555-5555',
                    'cell' => '123456', 'email' => 'jane@myexample.com')
            );
    $smarty->assign('contacts',$data);
    ?>

      

The template to output `$contacts`


    {section name=customer loop=$contacts}
    <p>
      name: {$contacts[customer].name}<br />
      home: {$contacts[customer].home}<br />
      cell: {$contacts[customer].cell}<br />
      e-mail: {$contacts[customer].email}
    </p>
    {/section}

      

The above example will output:


    <p>
      name: John Smith<br />
      home: 555-555-5555<br />
      cell: 666-555-5555<br />
      e-mail: john@myexample.com
    </p>
    <p>
      name: Jack Jones<br />
      home phone: 777-555-5555<br />
      cell phone: 888-555-5555<br />
      e-mail: jack@myexample.com
    </p>
    <p>
      name: Jane Munson<br />
      home phone: 000-555-5555<br />
      cell phone: 123456<br />
      e-mail: jane@myexample.com
    </p>

      

This example assumes that `$custid`, `$name` and `$address` are all
arrays containing the same number of values. First the php script that
assign\'s the arrays to Smarty.


    <?php

    $id = array(1001,1002,1003);
    $smarty->assign('custid',$id);

    $fullnames = array('John Smith','Jack Jones','Jane Munson');
    $smarty->assign('name',$fullnames);

    $addr = array('253 Abbey road', '417 Mulberry ln', '5605 apple st');
    $smarty->assign('address',$addr);

    ?>

The `loop` variable only determines the number of times to loop. You can
access ANY variable from the template within the `{section}`. This is
useful for looping multiple arrays. You can pass an array which will
determine the loop count by the array size, or you can pass an integer
to specify the number of loops.


    {section name=customer loop=$custid}
    <p>
      id: {$custid[customer]}<br />
      name: {$name[customer]}<br />
      address: {$address[customer]}
    </p>
    {/section}

      

The above example will output:


    <p>
      id: 1000<br />
      name: John Smith<br />
      address: 253 Abbey road
    </p>
    <p>
      id: 1001<br />
      name: Jack Jones<br />
      address: 417 Mulberry ln
    </p>
    <p>
      id: 1002<br />
      name: Jane Munson<br />
      address: 5605 apple st
    </p>

      

{section}\'s can be nested as deep as you like. With nested
{section}\'s, you can access complex data structures, such as
multi-dimensional arrays. This is an example `.php` script that
assigns the arrays.


    <?php

    $id = array(1001,1002,1003);
    $smarty->assign('custid',$id);

    $fullnames = array('John Smith','Jack Jones','Jane Munson');
    $smarty->assign('name',$fullnames);

    $addr = array('253 N 45th', '417 Mulberry ln', '5605 apple st');
    $smarty->assign('address',$addr);

    $types = array(
               array( 'home phone', 'cell phone', 'e-mail'),
               array( 'home phone', 'web'),
               array( 'cell phone')
             );
    $smarty->assign('contact_type', $types);

    $info = array(
               array('555-555-5555', '666-555-5555', 'john@myexample.com'),
               array( '123-456-4', 'www.example.com'),
               array( '0457878')
            );
    $smarty->assign('contact_info', $info);

    ?>
      

In this template, *\$contact\_type\[customer\]* is an array of contact
types for the current customer.


    {section name=customer loop=$custid}
    <hr>
      id: {$custid[customer]}<br />
      name: {$name[customer]}<br />
      address: {$address[customer]}<br />
      {section name=contact loop=$contact_type[customer]}
        {$contact_type[customer][contact]}: {$contact_info[customer][contact]}<br />
      {/section}
    {/section}

      

The above example will output:


    <hr>
      id: 1000<br />
      name: John Smith<br />
      address: 253 N 45th<br />
        home phone: 555-555-5555<br />
        cell phone: 666-555-5555<br />
        e-mail: john@myexample.com<br />
    <hr>
      id: 1001<br />
      name: Jack Jones<br />
      address: 417 Mulberry ln<br />
        home phone: 123-456-4<br />
        web: www.example.com<br />
    <hr>
      id: 1002<br />
      name: Jane Munson<br />
      address: 5605 apple st<br />
        cell phone: 0457878<br />

      

Results of a database search (eg ADODB or PEAR) are assigned to Smarty

      
    <?php
    $sql = 'select id, name, home, cell, email from contacts '
          ."where name like '$foo%' ";
    $smarty->assign('contacts', $db->getAll($sql));
    ?>

The template to output the database result in a HTML table


    <table>
    <tr><th>&nbsp;</th><th>Name></th><th>Home</th><th>Cell</th><th>Email</th></tr>
    {section name=co loop=$contacts}
      <tr>
        <td><a href="view.php?id={$contacts[co].id}">view<a></td>
        <td>{$contacts[co].name}</td>
        <td>{$contacts[co].home}</td>
        <td>{$contacts[co].cell}</td>
        <td>{$contacts[co].email}</td>
      <tr>
    {sectionelse}
      <tr><td colspan="5">No items found</td></tr>
    {/section}
    </table>

.index {#section.property.index}
------

`index` contains the current array index, starting with zero or the
`start` attribute if given. It increments by one or by the `step`
attribute if given.

> **Note**
>
> If the `step` and `start` properties are not modified, then this works
> the same as the [`iteration`](#section.property.iteration) property,
> except it starts at zero instead of one.

> **Note**
>
> `$custid[customer.index]` and `$custid[customer]` are identical.


    {section name=customer loop=$custid}
      {$smarty.section.customer.index} id: {$custid[customer]}<br />
    {/section}

      

The above example will output:


    0 id: 1000<br />
    1 id: 1001<br />
    2 id: 1002<br />

       

.index\_prev {#section.property.index.prev}
------------

`index_prev` is the previous loop index. On the first loop, this is set
to -1.

.index\_next {#section.property.index.next}
------------

`index_next` is the next loop index. On the last loop, this is still one
more than the current index, respecting the setting of the `step`
attribute, if given.


    <?php
    $data = array(1001,1002,1003,1004,1005);
    $smarty->assign('rows',$data);
    ?>

Template to output the above array in a table


    {* $rows[row.index] and $rows[row] are identical in meaning *}
    <table>
      <tr>
        <th>index</th><th>id</th>
        <th>index_prev</th><th>prev_id</th>
        <th>index_next</th><th>next_id</th>
      </tr>
    {section name=row loop=$rows}
      <tr>
        <td>{$smarty.section.row.index}</td><td>{$rows[row]}</td>
        <td>{$smarty.section.row.index_prev}</td><td>{$rows[row.index_prev]}</td>
        <td>{$smarty.section.row.index_next}</td><td>{$rows[row.index_next]}</td>
      </tr>
    {/section}
    </table>

      

The above example will output a table containing the following:


    index  id    index_prev prev_id index_next next_id
    0      1001  -1                 1          1002
    1      1002  0          1001    2          1003
    2      1003  1          1002    3          1004
    3      1004  2          1003    4          1005
    4      1005  3          1004    5

       

.iteration {#section.property.iteration}
----------

`iteration` contains the current loop iteration and starts at one.

> **Note**
>
> This is not affected by the `{section}` properties `start`, `step` and
> `max`, unlike the [`index`](#section.property.index) property.
> `iteration` also starts with one instead of zero unlike `index`.
> [`rownum`](#section.property.rownum) is an alias to `iteration`, they
> are identical.


    <?php
    // array of 3000 to 3015
    $id = range(3000,3015);
    $smarty->assign('arr',$id);
    ?>

Template to output every other element of the `$arr` array as `step=2`


    {section name=cu loop=$arr start=5 step=2}
      iteration={$smarty.section.cu.iteration}
      index={$smarty.section.cu.index}
      id={$custid[cu]}<br />
    {/section}

      

The above example will output:


    iteration=1 index=5 id=3005<br />
    iteration=2 index=7 id=3007<br />
    iteration=3 index=9 id=3009<br />
    iteration=4 index=11 id=3011<br />
    iteration=5 index=13 id=3013<br />
    iteration=6 index=15 id=3015<br />

       

Another example that uses the `iteration` property to output a table
header block every five rows.


    <table>
    {section name=co loop=$contacts}
      {if $smarty.section.co.iteration is div by 5}
        <tr><th>&nbsp;</th><th>Name></th><th>Home</th><th>Cell</th><th>Email</th></tr>
      {/if}
      <tr>
        <td><a href="view.php?id={$contacts[co].id}">view<a></td>
        <td>{$contacts[co].name}</td>
        <td>{$contacts[co].home}</td>
        <td>{$contacts[co].cell}</td>
        <td>{$contacts[co].email}</td>
      <tr>
    {/section}
    </table>

      

An that uses the `iteration` property to alternate a text color every
third row.

      
      <table>
      {section name=co loop=$contacts}
        {if $smarty.section.co.iteration is even by 3}
          <span style="color: #ffffff">{$contacts[co].name}</span>
        {else}
          <span style="color: #dddddd">{$contacts[co].name}</span>
        {/if}
      {/section}
      </table>
      
        

> **Note**
>
> The *\"is div by\"* syntax is a simpler alternative to the PHP mod
> operator syntax. The mod operator is allowed:
> `{if $smarty.section.co.iteration % 5 == 1}` will work just the same.

> **Note**
>
> You can also use *\"is odd by\"* to reverse the alternating.

.first {#section.property.first}
------

`first` is set to TRUE if the current `{section}` iteration is the
initial one.

.last {#section.property.last}
-----

`last` is set to TRUE if the current section iteration is the final one.

This example loops the `$customers` array, outputs a header block on the
first iteration and on the last outputs the footer block. Also uses the
[`total`](#section.property.total) property.


    {section name=customer loop=$customers}
      {if $smarty.section.customer.first}
        <table>
        <tr><th>id</th><th>customer</th></tr>
      {/if}

      <tr>
        <td>{$customers[customer].id}}</td>
        <td>{$customers[customer].name}</td>
      </tr>

      {if $smarty.section.customer.last}
        <tr><td></td><td>{$smarty.section.customer.total} customers</td></tr>
        </table>
      {/if}
    {/section}

      

.rownum {#section.property.rownum}
-------

`rownum` contains the current loop iteration, starting with one. It is
an alias to [`iteration`](#section.property.iteration), they work
identically.

.loop {#section.property.loop}
-----

`loop` contains the last index number that this {section} looped. This
can be used inside or after the `{section}`.


    {section name=customer loop=$custid}
      {$smarty.section.customer.index} id: {$custid[customer]}<br />
    {/section}
    There are {$smarty.section.customer.loop} customers shown above.

      

The above example will output:


    0 id: 1000<br />
    1 id: 1001<br />
    2 id: 1002<br />
    There are 3 customers shown above.

       

.show {#section.property.show}
-----

`show` is used as a parameter to section and is a boolean value. If
FALSE, the section will not be displayed. If there is a `{sectionelse}`
present, that will be alternately displayed.

Boolean `$show_customer_info` has been passed from the PHP application,
to regulate whether or not this section shows.


    {section name=customer loop=$customers show=$show_customer_info}
      {$smarty.section.customer.rownum} id: {$customers[customer]}<br />
    {/section}

    {if $smarty.section.customer.show}
      the section was shown.
    {else}
      the section was not shown.
    {/if}

      

The above example will output:


    1 id: 1000<br />
    2 id: 1001<br />
    3 id: 1002<br />

    the section was shown.

       

.total {#section.property.total}
------

`total` contains the number of iterations that this `{section}` will
loop. This can be used inside or after a `{section}`.


    {section name=customer loop=$custid step=2}
      {$smarty.section.customer.index} id: {$custid[customer]}<br />
    {/section}
       There are {$smarty.section.customer.total} customers shown above.

      

See also [`{foreach}`](#language.function.foreach),
[`{for}`](#language.function.for), [`{while}`](#language.function.while)
and [`$smarty.section`](#language.variables.smarty.loops).
