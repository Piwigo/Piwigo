{include\_php} {#language.function.include.php}
==============

> **Note**
>
> `{include_php}` is deprecated from Smarty, use registered plugins to
> properly insulate presentation from the application code. As of Smarty
> 3.1 the `{include_php}` tags are only available from [SmartyBC](#bc).

   Attribute Name    Type     Required   Default  Description
  ---------------- --------- ---------- --------- ----------------------------------------------------------------------------------
        file        string      Yes       *n/a*   The name of the php file to include as absolute path
        once        boolean      No      *TRUE*   whether or not to include the php file more than once if included multiple times
       assign       string       No       *n/a*   The name of the variable that the output of include\_php will be assigned to

**Option Flags:**

    Name    Description
  --------- ----------------------------------------
   nocache  Disables caching of inluded PHP script

`{include_php}` tags are used to include a php script in your template.
The path of the attribute `file` can be either absolute, or relative to
[`$trusted_dir`](#variable.trusted.dir). If security is enabled, then
the script must be located in the `$trusted_dir` path of the securty
policy. See the [Security](#advanced.features.security) section for
details.

By default, php files are only included once even if called multiple
times in the template. You can specify that it should be included every
time with the `once` attribute. Setting once to FALSE will include the
php script each time it is included in the template.

You can optionally pass the `assign` attribute, which will specify a
template variable name that the output of `{include_php}` will be
assigned to instead of displayed.

The smarty object is available as `$_smarty_tpl->smarty` within the PHP
script that you include.

The `load_nav.php` file:


    <?php

    // load in variables from a mysql db and assign them to the template
    require_once('database.class.php');
    $db = new Db();
    $db->query('select url, name from navigation order by name');
    $this->assign('navigation', $db->getRows());

    ?>

      

where the template is:


    {* absolute path, or relative to $trusted_dir *}
    {include_php file='/path/to/load_nav.php'}
    {include_php '/path/to/load_nav.php'}             {* short-hand *}

    {foreach item='nav' from=$navigation}
      <a href="{$nav.url}">{$nav.name}</a><br />
    {/foreach}

      

See also [`{include}`](#language.function.include),
[`$trusted_dir`](#variable.trusted.dir),
[`{php}`](#language.function.php),
[`{capture}`](#language.function.capture), [template
resources](#resources) and [componentized
templates](#tips.componentized.templates)
