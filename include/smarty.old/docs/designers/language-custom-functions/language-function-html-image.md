{html\_image} {#language.function.html.image}
=============

`{html_image}` is a [custom function](#language.custom.functions) that
generates an HTML `<img>` tag. The `height` and `width` are
automatically calculated from the image file if they are not supplied.

   Attribute Name    Type    Required          Default         Description
  ---------------- -------- ---------- ----------------------- ---------------------------------------
        file        string     Yes              *n/a*          name/path to image
       height       string      No      *actual image height*  Height to display image
       width        string      No      *actual image width*   Width to display image
      basedir       string      no      *web server doc root*  Directory to base relative paths from
        alt         string      no              *""*           Alternative description of the image
        href        string      no              *n/a*          href value to link the image to
    path\_prefix    string      no              *n/a*          Prefix for output path

-   `basedir` is the base directory that relative image paths are based
    from. If not given, the web server\'s document root
    `$_ENV['DOCUMENT_ROOT']` is used as the base. If security is
    enabled, then the image must be located in the `$secure_dir` path of
    the securty policy. See the [Security](#advanced.features.security)
    section for details.

-   `href` is the href value to link the image to. If link is supplied,
    an `<a href="LINKVALUE"><a>` tag is placed around the image tag.

-   `path_prefix` is an optional prefix string you can give the output
    path. This is useful if you want to supply a different server name
    for the image.

-   All parameters that are not in the list above are printed as
    name/value-pairs inside the created `<img>` tag.

> **Note**
>
> `{html_image}` requires a hit to the disk to read the image and
> calculate the height and width. If you don\'t use template
> [caching](#caching), it is generally better to avoid `{html_image}`
> and leave image tags static for optimal performance.


    {html_image file='pumpkin.jpg'}
    {html_image file='/path/from/docroot/pumpkin.jpg'}
    {html_image file='../path/relative/to/currdir/pumpkin.jpg'}

      

Example output of the above template would be:


    <img src="pumpkin.jpg" alt="" width="44" height="68" />
    <img src="/path/from/docroot/pumpkin.jpg" alt="" width="44" height="68" />
    <img src="../path/relative/to/currdir/pumpkin.jpg" alt="" width="44" height="68" />

      
