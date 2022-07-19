{foreach},{foreachelse} {#language.function.foreach}
=======================

`{foreach}` is used for looping over arrays of data. `{foreach}` has a
simpler and cleaner syntax than the
[`{section}`](#language.function.section) loop, and can also loop over
associative arrays.

`{foreach $arrayvar as $itemvar}`

`{foreach $arrayvar as $keyvar=>$itemvar}`

> **Note**
>
> This foreach syntax does not accept any named attributes. This syntax
> is new to Smarty 3, however the Smarty 2.x syntax
> `{foreach from=$myarray key="mykey" item="myitem"}` is still
> supported.

-   `{foreach}` loops can be nested.

-   The `array` variable, usually an array of values, determines the
    number of times `{foreach}` will loop. You can also pass an integer
    for arbitrary loops.

-   `{foreachelse}` is executed when there are no values in the `array`
    variable.

-   `{foreach}` properties are [`@index`](#foreach.property.index),
    [`@iteration`](#foreach.property.iteration),
    [`@first`](#foreach.property.first),
    [`@last`](#foreach.property.last),
    [`@show`](#foreach.property.show),
    [`@total`](#foreach.property.total).

-   `{foreach}` constructs are [`{break}`](#foreach.construct.break),
    [`{continue}`](#foreach.construct.continue).

-   Instead of specifying the `key` variable you can access the current
    key of the loop item by `{$item@key}` (see examples below).

> **Note**
>
> The `$var@property` syntax is new to Smarty 3, however when using the
> Smarty 2 `{foreach from=$myarray key="mykey" item="myitem"}` style
> syntax, the `$smarty.foreach.name.property` syntax is still supported.

> **Note**
>
> Although you can retrieve the array key with the syntax
> `{foreach $myArray as $myKey => $myValue}`, the key is always
> available as `$myValue@key` within the foreach loop.

**Option Flags:**

    Name    Description
  --------- ------------------------------------------
   nocache  Disables caching of the `{foreach}` loop


    <?php
    $arr = array('red', 'green', 'blue');
    $smarty->assign('myColors', $arr);
    ?>

      

Template to output `$myColors` in an un-ordered list


    <ul>
    {foreach $myColors as $color}
        <li>{$color}</li>
    {/foreach}
    </ul>

      

The above example will output:


    <ul>
        <li>red</li>
        <li>green</li>
        <li>blue</li>
    </ul>

      


    <?php
    $people = array('fname' => 'John', 'lname' => 'Doe', 'email' => 'j.doe@example.com');
    $smarty->assign('myPeople', $people);
    ?>

      

Template to output `$myArray` as key/value pairs.


    <ul>
    {foreach $myPeople as $value}
       <li>{$value@key}: {$value}</li>
    {/foreach}
    </ul>

      

The above example will output:


    <ul>
        <li>fname: John</li>
        <li>lname: Doe</li>
        <li>email: j.doe@example.com</li>
    </ul>

      

Assign an array to Smarty, the key contains the key for each looped
value.


    <?php
     $smarty->assign('contacts', array(
                                 array('phone' => '555-555-1234',
                                       'fax' => '555-555-5678',
                                       'cell' => '555-555-0357'),
                                 array('phone' => '800-555-4444',
                                       'fax' => '800-555-3333',
                                       'cell' => '800-555-2222')
                                 ));
    ?>

      

The template to output `$contact`.


    {* key always available as a property *}
    {foreach $contacts as $contact}
      {foreach $contact as $value}
        {$value@key}: {$value}
      {/foreach}
    {/foreach}

    {* accessing key the PHP syntax alternate *}
    {foreach $contacts as $contact}
      {foreach $contact as $key => $value}
        {$key}: {$value}
      {/foreach}
    {/foreach}

      

Either of the above examples will output:


      phone: 555-555-1234
      fax: 555-555-5678
      cell: 555-555-0357
      phone: 800-555-4444
      fax: 800-555-3333
      cell: 800-555-2222

      

A database (PDO) example of looping over search results. This example is
looping over a PHP iterator instead of an array().


    <?php 
      include('Smarty.class.php'); 

      $smarty = new Smarty; 

      $dsn = 'mysql:host=localhost;dbname=test'; 
      $login = 'test'; 
      $passwd = 'test'; 

      // setting PDO to use buffered queries in mysql is 
      // important if you plan on using multiple result cursors 
      // in the template. 

      $db = new PDO($dsn, $login, $passwd, array( 
         PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true)); 

      $res = $db->prepare("select * from users"); 
      $res->execute(); 
      $res->setFetchMode(PDO::FETCH_LAZY); 

      // assign to smarty 
      $smarty->assign('res',$res); 

      $smarty->display('index.tpl');?>
    ?>

      


    {foreach $res as $r} 
      {$r.id} 
      {$r.name}
    {foreachelse}
      .. no results .. 
    {/foreach}

      

The above is assuming the results contain the columns named `id` and
`name`.

What is the advantage of an iterator vs. looping over a plain old array?
With an array, all the results are accumulated into memory before being
looped. With an iterator, each result is loaded/released within the
loop. This saves processing time and memory, especially for very large
result sets.

\@index {#foreach.property.index}
-------

`index` contains the current array index, starting with zero.


    {* output empty row on the 4th iteration (when index is 3) *}
    <table>
    {foreach $items as $i}
      {if $i@index eq 3}
         {* put empty table row *}
         <tr><td>nbsp;</td></tr>
      {/if}
      <tr><td>{$i.label}</td></tr>
    {/foreach}
    </table>

      

\@iteration {#foreach.property.iteration}
-----------

`iteration` contains the current loop iteration and always starts at
one, unlike [`index`](#foreach.property.index). It is incremented by one
on each iteration.

The *\"is div by\"* operator can be used to detect a specific iteration.
Here we bold-face the name every 4th iteration.


    {foreach $myNames as $name}
      {if $name@iteration is div by 4}
        <b>{$name}</b>
      {/if}
      {$name}
    {/foreach}

The *\"is even by\"* and *\"is odd by\"* operators can be used to
alternate something every so many iterations. Choosing between even or
odd rotates which one starts. Here we switch the font color every 3rd
iteration.

     
     {foreach $myNames as $name}
       {if $name@iteration is even by 3}
         <span style="color: #000">{$name}</span>
       {else}
         <span style="color: #eee">{$name}</span>
       {/if}
     {/foreach}
     
     

This will output something similar to this:


        <span style="color: #000">...</span>
        <span style="color: #000">...</span>
        <span style="color: #000">...</span>
        <span style="color: #eee">...</span>
        <span style="color: #eee">...</span>
        <span style="color: #eee">...</span>
        <span style="color: #000">...</span>
        <span style="color: #000">...</span>
        <span style="color: #000">...</span>
        <span style="color: #eee">...</span>
        <span style="color: #eee">...</span>
        <span style="color: #eee">...</span>
        ...

       

\@first {#foreach.property.first}
-------

`first` is TRUE if the current `{foreach}` iteration is the initial one.
Here we display a table header row on the first iteration.


    {* show table header at first iteration *}
    <table>
    {foreach $items as $i}
      {if $i@first}
        <tr>
          <th>key</td>
          <th>name</td>
        </tr>
      {/if}
      <tr>
        <td>{$i@key}</td>
        <td>{$i.name}</td>
      </tr>
    {/foreach}
    </table>

      

\@last {#foreach.property.last}
------

`last` is set to TRUE if the current `{foreach}` iteration is the final
one. Here we display a horizontal rule on the last iteration.


    {* Add horizontal rule at end of list *}
    {foreach $items as $item}
      <a href="#{$item.id}">{$item.name}</a>{if $item@last}<hr>{else},{/if}
    {foreachelse}
      ... no items to loop ...
    {/foreach}

      

\@show {#foreach.property.show}
------

The show `show` property can be used after the execution of a
`{foreach}` loop to detect if data has been displayed or not. `show` is
a boolean value.


    <ul>
    {foreach $myArray as $name}
        <li>{$name}</li>
    {/foreach}
    </ul>
    {if $name@show} do something here if the array contained data {/if}

\@total {#foreach.property.total}
-------

`total` contains the number of iterations that this `{foreach}` will
loop. This can be used inside or after the `{foreach}`.


    {* show number of rows at end *}
    {foreach $items as $item}
      {$item.name}<hr/>
      {if $item@last}
        <div id="total">{$item@total} items</div>
      {/if}
    {foreachelse}
     ... no items to loop ...
    {/foreach}

See also [`{section}`](#language.function.section),
[`{for}`](#language.function.for) and
[`{while}`](#language.function.while)

{break} {#foreach.construct.break}
-------

`{break}` aborts the iteration of the array

     
      {$data = [1,2,3,4,5]}
      {foreach $data as $value}
        {if $value == 3}
          {* abort iterating the array *}
          {break}
        {/if}
        {$value}
      {/foreach}
      {*
        prints: 1 2
      *}
     
       

{continue} {#foreach.construct.continue}
----------

`{continue}` leaves the current iteration and begins with the next
iteration.

     
      {$data = [1,2,3,4,5]}
      {foreach $data as $value}
        {if $value == 3}
          {* skip this iteration *}
          {continue}
        {/if}
        {$value}
      {/foreach}
      {*
        prints: 1 2 4 5
      *}
     
       
