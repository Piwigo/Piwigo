{extends} {#language.function.extends}
=========

`{extends}` tags are used in child templates in template inheritance for
extending parent templates. For details see section of [Template
Interitance](#advanced.features.template.inheritance).

-   The `{extends}` tag must be on the first line of the template.

-   If a child template extends a parent template with the `{extends}`
    tag it may contain only `{block}` tags. Any other template content
    is ignored.

-   Use the syntax for [template resources](#resources) to extend files
    outside of the [`$template_dir`](#variable.template.dir) directory.

> **Note**
>
> When extending a variable parent like `{extends file=$parent_file}`,
> make sure you include `$parent_file` in the
> [`$compile_id`](#variable.compile.id). Otherwise Smarty cannot
> distinguish between different `$parent_file`s.

**Attributes:**

   Attribute Name    Type    Required   Default  Description
  ---------------- -------- ---------- --------- -------------------------------------------------
        file        string     Yes       *n/a*   The name of the template file which is extended


    {extends file='parent.tpl'}
    {extends 'parent.tpl'}  {* short-hand *}

      

See also [Template Interitance](#advanced.features.template.inheritance)
and [`{block}`](#language.function.block).
