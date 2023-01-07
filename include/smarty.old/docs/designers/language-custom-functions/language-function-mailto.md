{mailto} {#language.function.mailto}
========

`{mailto}` automates the creation of a `mailto:` anchor links and
optionally encodes them. Encoding emails makes it more difficult for web
spiders to lift email addresses off of a site.

> **Note**
>
> Javascript is probably the most thorough form of encoding, although
> you can use hex encoding too.

   Attribute Name    Type    Required   Default  Description
  ---------------- -------- ---------- --------- -----------------------------------------------------------------------------------------------
      address       string     Yes       *n/a*   The e-mail address
        text        string      No       *n/a*   The text to display, default is the e-mail address
       encode       string      No      *none*   How to encode the e-mail. Can be one of `none`, `hex`, `javascript` or `javascript_charcode`.
         cc         string      No       *n/a*   Email addresses to carbon copy, separate entries by a comma.
        bcc         string      No       *n/a*   Email addresses to blind carbon copy, separate entries by a comma
      subject       string      No       *n/a*   Email subject
     newsgroups     string      No       *n/a*   Newsgroups to post to, separate entries by a comma.
     followupto     string      No       *n/a*   Addresses to follow up to, separate entries by a comma.
       extra        string      No       *n/a*   Any extra information you want passed to the link, such as style sheet classes


    {mailto address="me@example.com"}
    <a href="mailto:me@example.com" >me@example.com</a>

    {mailto address="me@example.com" text="send me some mail"}
    <a href="mailto:me@example.com" >send me some mail</a>

    {mailto address="me@example.com" encode="javascript"}
    <script type="text/javascript" language="javascript">
       eval(unescape('%64%6f% ... snipped ...%61%3e%27%29%3b'))
    </script>

    {mailto address="me@example.com" encode="hex"}
    <a href="mailto:%6d%65.. snipped..3%6f%6d">&#x6d;&..snipped...#x6f;&#x6d;</a>

    {mailto address="me@example.com" subject="Hello to you!"}
    <a href="mailto:me@example.com?subject=Hello%20to%20you%21" >me@example.com</a>

    {mailto address="me@example.com" cc="you@example.com,they@example.com"}
    <a href="mailto:me@example.com?cc=you@example.com,they@example.com" >me@example.com</a>

    {mailto address="me@example.com" extra='class="email"'}
    <a href="mailto:me@example.com" class="email">me@example.com</a>

    {mailto address="me@example.com" encode="javascript_charcode"}
    <script type="text/javascript" language="javascript">
        {document.write(String.fromCharCode(60,97, ... snipped ....60,47,97,62))}
    </script>

See also [`escape`](#language.modifier.escape),
[`{textformat}`](#language.function.textformat) and [obfuscating email
addresses](#tips.obfuscating.email).
