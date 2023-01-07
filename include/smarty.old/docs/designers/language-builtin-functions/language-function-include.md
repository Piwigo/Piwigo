{include} {#language.function.include}
=========

`{include}` tags are used for including other templates in the current
template. Any variables available in the current template are also
available within the included template.

-   The `{include}` tag must have the `file` attribute which contains
    the template resource path.

-   Setting the optional `assign` attribute specifies the template
    variable that the output of `{include}` is assigned to, instead of
    being displayed. Similar to [`{assign}`](#language.function.assign).

-   Variables can be passed to included templates as
    [attributes](#language.syntax.attributes). Any variables explicitly
    passed to an included template are only available within the scope
    of the included file. Attribute variables override current template
    variables, in the case when they are named the same.

-   You can use all variables from the including template inside the
    included template. But changes to variables or new created variables
    inside the included template have local scope and are not visible
    inside the including template after the `{include}` statement. This
    default behaviour can be changed for all variables assigned in the
    included template by using the scope attribute at the `{include}`
    statement or for individual variables by using the scope attribute
    at the [`{assign}`](#language.function.assign) statement. The later
    is useful to return values from the included template to the
    including template.

-   Use the syntax for [template resources](#resources) to `{include}`
    files outside of the [`$template_dir`](#variable.template.dir)
    directory.

**Attributes:**

   Attribute Name         Type        Required   Default  Description
  ----------------- ---------------- ---------- --------- --------------------------------------------------------------------------------------------------
        file             string         Yes       *n/a*   The name of the template file to include
       assign            string          No       *n/a*   The name of the variable that the output of include will be assigned to
   cache\_lifetime      integer          No       *n/a*   Enable caching of this subtemplate with an individual cache lifetime
     compile\_id     string/integer      No       *n/a*   Compile this subtemplate with an individual compile\_id
      cache\_id      string/integer      No       *n/a*   Enable caching of this subtemplate with an individual cache\_id
        scope            string          No       *n/a*   Define the scope of all in the subtemplate assigned variables: \'parent\',\'root\' or \'global\'
    \[var \...\]      \[var type\]       No       *n/a*   variable to pass local to template

**Option Flags:**

    Name    Description
  --------- -------------------------------------------------------------------------------------
   nocache  Disables caching of this subtemplate
   caching  Enable caching of this subtemplate
   inline   If set merge the compile code of the subtemplate into the compiled calling template


    <html>
    <head>
      <title>{$title}</title>
    </head>
    <body>
    {include file='page_header.tpl'}

    {* body of template goes here, the $tpl_name variable
       is replaced with a value eg 'contact.tpl'
    *}
    {include file="$tpl_name.tpl"}

    {* using shortform file attribute *}
    {include 'page_footer.tpl'}
    </body>
    </html>

      


    {include 'links.tpl' title='Newest links' links=$link_array}
    {* body of template goes here *}
    {include 'footer.tpl' foo='bar'}

      

The template above includes the example `links.tpl` below


    <div id="box">
    <h3>{$title}{/h3>
    <ul>
    {foreach from=$links item=l}
    .. do stuff  ...
    </foreach}
    </ul>
    </div>

Variables assigned in the included template will be seen in the
including template.


    {include 'sub_template.tpl' scope=parent}
    ...
    {* display variables assigned in sub_template *}
    {$foo}<br>
    {$bar}<br>
    ...

      

The template above includes the example `sub_template.tpl` below


    ...
    {assign var=foo value='something'}
    {assign var=bar value='value'}
    ...

The included template will not be cached.


    {include 'sub_template.tpl' nocache}
    ...

      

In this example included template will be cached with an individual
cache lifetime of 500 seconds.


    {include 'sub_template.tpl' cache_lifetime=500}
    ...

      

In this example included template will be cached independent of the
global cahing setting.


    {include 'sub_template.tpl' caching}
    ...

      

This example assigns the contents of `nav.tpl` to the `$navbar`
variable, which is then output at both the top and bottom of the page.

     
    <body>
      {include 'nav.tpl' assign=navbar}
      {include 'header.tpl' title='Smarty is cool'}
        {$navbar}
        {* body of template goes here *}
        {$navbar}
      {include 'footer.tpl'}
    </body>

       

This example includes another template relative to the directory of the
current template.


       {include 'template-in-a-template_dir-directory.tpl'}
       {include './template-in-same-directory.tpl'}
       {include '../template-in-parent-directory.tpl'}

        


    {* absolute filepath *}
    {include file='/usr/local/include/templates/header.tpl'}

    {* absolute filepath (same thing) *}
    {include file='file:/usr/local/include/templates/header.tpl'}

    {* windows absolute filepath (MUST use "file:" prefix) *}
    {include file='file:C:/www/pub/templates/header.tpl'}

    {* include from template resource named "db" *}
    {include file='db:header.tpl'}

    {* include a $variable template - eg $module = 'contacts' *}
    {include file="$module.tpl"}

    {* wont work as its single quotes ie no variable substitution *}
    {include file='$module.tpl'}

    {* include a multi $variable template - eg amber/links.view.tpl *}
    {include file="$style_dir/$module.$view.tpl"}

      

See also [`{include_php}`](#language.function.include.php),
[`{insert}`](#language.function.insert),
[`{php}`](#language.function.php), [template resources](#resources) and
[componentized templates](#tips.componentized.templates).
