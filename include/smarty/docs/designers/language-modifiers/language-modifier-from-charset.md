from\_charset {#language.modifier.from_charset}
=============

`from_charset` is used to transcode a string from a given charset to the
internal charset. This is the exact opposite of the [to\_charset
modifier](#language.modifier.to_charset).

   Parameter Position    Type    Required                                                       Possible Values                                                          Default     Description
  -------------------- -------- ---------- -------------------------------------------------------------------------------------------------------------------------- -------------- ---------------------------------------------------------------
           1            string      No      `ISO-8859-1`, `UTF-8`, and any character set supported by [`mb_convert_encoding()`](&url.php-manual;mb_convert_encoding)   `ISO-8859-1`  The charset encoding the value is supposed to be decoded from

> **Note**
>
> Charset encoding should be handled by the application itself. This
> modifier should only be used in cases where the application cannot
> anticipate that a certain string is required in another encoding.

See also [Charset Encoding](#charset), [from\_charset
modifier](#language.modifier.from_charset).
