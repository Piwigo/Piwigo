{debug} {#language.function.debug}
=======

`{debug}` dumps the debug console to the page. This works regardless of
the [debug](#chapter.debugging.console) settings in the php script.
Since this gets executed at runtime, this is only able to show the
[assigned](#api.assign) variables; not the templates that are in use.
However, you can see all the currently available variables within the
scope of a template.

   Attribute Name    Type    Required     Default     Description
  ---------------- -------- ---------- -------------- ---------------------------------
       output       string      No      *javascript*  output type, html or javascript

See also the [debugging console page](#chapter.debugging.console).
