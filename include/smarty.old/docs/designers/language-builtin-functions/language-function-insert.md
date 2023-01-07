{insert} {#language.function.insert}
========

> **Note**
>
> `{insert}` tags are deprecated from Smarty, and should not be used.
> Put your PHP logic in PHP scripts or plugin functions instead.

> **Note**
>
> As of Smarty 3.1 the `{insert}` tags are only available from
> [SmartyBC](#bc).

`{insert}` tags work much like [`{include}`](#language.function.include)
tags, except that `{insert}` tags are NOT cached when template
[caching](#caching) is enabled. They will be executed on every
invocation of the template.

   Attribute Name       Type       Required   Default  Description
  ---------------- -------------- ---------- --------- ----------------------------------------------------------------------------------
        name           string        Yes       *n/a*   The name of the insert function (insert\_`name`) or insert plugin
       assign          string         No       *n/a*   The name of the template variable the output will be assigned to
       script          string         No       *n/a*   The name of the php script that is included before the insert function is called
    \[var \...\]    \[var type\]      No       *n/a*   variable to pass to insert function

Let\'s say you have a template with a banner slot at the top of the
page. The banner can contain any mixture of HTML, images, flash, etc. so
we can\'t just use a static link here, and we don\'t want this contents
cached with the page. In comes the {insert} tag: the template knows
\#banner\_location\_id\# and \#site\_id\# values (gathered from a
[config file](#config.files)), and needs to call a function to get the
banner contents.

    {* example of fetching a banner *}
    {insert name="getBanner" lid=#banner_location_id# sid=#site_id#}
    {insert "getBanner" lid=#banner_location_id# sid=#site_id#} {* short-hand *}

In this example, we are using the name "getBanner" and passing the
parameters \#banner\_location\_id\# and \#site\_id\#. Smarty will look
for a function named insert\_getBanner() in your PHP application,
passing the values of \#banner\_location\_id\# and \#site\_id\# as the
first argument in an associative array. All {insert} function names in
your application must be prepended with \"insert\_\" to remedy possible
function name-space conflicts. Your insert\_getBanner() function should
do something with the passed values and return the results. These
results are then displayed in the template in place of the {insert} tag.
In this example, Smarty would call this function:
insert\_getBanner(array(\"lid\" =\> \"12345\",\"sid\" =\> \"67890\"));
and display the returned results in place of the {insert} tag.

-   If you supply the `assign` attribute, the output of the `{insert}`
    tag will be assigned to this template variable instead of being
    output to the template.

    > **Note**
    >
    > Assigning the output to a template variable isn\'t too useful with
    > [caching](#variable.caching) enabled.

-   If you supply the `script` attribute, this php script will be
    included (only once) before the `{insert}` function is executed.
    This is the case where the insert function may not exist yet, and a
    php script must be included first to make it work.

    The path can be either absolute, or relative to
    [`$trusted_dir`](#variable.trusted.dir). If security is enabled,
    then the script must be located in the `$trusted_dir` path of the
    securty policy. See the [Security](#advanced.features.security)
    section for details.

The Smarty object is passed as the second argument. This way you can
reference and modify information in the Smarty object from within the
`{insert}` function.

If no PHP script can be found Smarty is looking for a corresponding
insert plugin.

> **Note**
>
> It is possible to have portions of the template not cached. If you
> have [caching](#caching) turned on, `{insert}` tags will not be
> cached. They will run dynamically every time the page is created, even
> within cached pages. This works good for things like banners, polls,
> live weather, search results, user feedback areas, etc.

See also [`{include}`](#language.function.include)
