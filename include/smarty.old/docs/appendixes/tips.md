Tips & Tricks {#tips}
=============

Blank Variable Handling {#tips.blank.var.handling}
=======================

There may be times when you want to print a default value for an empty
variable instead of printing nothing, such as printing `&nbsp;` so that
html table backgrounds work properly. Many would use an
[`{if}`](#language.function.if) statement to handle this, but there is a
shorthand way with Smarty, using the
[`default`](#language.modifier.default) variable modifier.

> **Note**
>
> "Undefined variable" errors will show an E\_NOTICE if not disabled in
> PHP\'s [`error_reporting()`](&url.php-manual;error_reporting) level or
> Smarty\'s [`$error_reporting`](#variable.error.reporting) property and
> a variable had not been assigned to Smarty.


    {* the long way *}
    {if $title eq ''}
       &nbsp;
    {else}
       {$title}
    {/if}

    {* the short way *}
    {$title|default:'&nbsp;'}

        

See also [`default`](#language.modifier.default) modifier and [default
variable handling](#tips.default.var.handling).

Default Variable Handling {#tips.default.var.handling}
=========================

If a variable is used frequently throughout your templates, applying the
[`default`](#language.modifier.default) modifier every time it is
mentioned can get a bit ugly. You can remedy this by assigning the
variable its default value with the
[`{assign}`](#language.function.assign) function.


    {* do this somewhere at the top of your template *}
    {assign var='title' value=$title|default:'no title'}

    {* if $title was empty, it now contains the value "no title" when you use it *}
    {$title}

        

See also [`default`](#language.modifier.default) modifier and [blank
variable handling](#tips.blank.var.handling).

Passing variable title to header template {#tips.passing.vars}
=========================================

When the majority of your templates use the same headers and footers, it
is common to split those out into their own templates and
[`{include}`](#language.function.include) them. But what if the header
needs to have a different title, depending on what page you are coming
from? You can pass the title to the header as an
[attribute](#language.syntax.attributes) when it is included.

`mainpage.tpl` - When the main page is drawn, the title of "Main Page"
is passed to the `header.tpl`, and will subsequently be used as the
title.


    {include file='header.tpl' title='Main Page'}
    {* template body goes here *}
    {include file='footer.tpl'}

        

`archives.tpl` - When the archives page is drawn, the title will be
"Archives". Notice in the archive example, we are using a variable from
the `archives_page.conf` file instead of a hard coded variable.


    {config_load file='archive_page.conf'}

    {include file='header.tpl' title=#archivePageTitle#}
    {* template body goes here *}
    {include file='footer.tpl'}

        

`header.tpl` - Notice that "Smarty News" is printed if the `$title`
variable is not set, using the [`default`](#language.modifier.default)
variable modifier.


    <html>
    <head>
    <title>{$title|default:'Smarty News'}</title>
    </head>
    <body>

        

`footer.tpl`


    </body>
    </html>

        

Dates {#tips.dates}
=====

As a rule of thumb, always pass dates to Smarty as
[timestamps](&url.php-manual;time). This allows template designers to
use the [`date_format`](#language.modifier.date.format) modifier for
full control over date formatting, and also makes it easy to compare
dates if necessary.


    {$startDate|date_format}

        

This will output:


    Jan 4, 2009

        


    {$startDate|date_format:"%Y/%m/%d"}

        

This will output:


    2009/01/04

        

Dates can be compared in the template by timestamps with:


    {if $order_date < $invoice_date}
       ...do something..
    {/if}

        

When using [`{html_select_date}`](#language.function.html.select.date)
in a template, the programmer will most likely want to convert the
output from the form back into timestamp format. Here is a function to
help you with that.


    <?php

    // this assumes your form elements are named
    // startDate_Day, startDate_Month, startDate_Year

    $startDate = makeTimeStamp($startDate_Year, $startDate_Month, $startDate_Day);

    function makeTimeStamp($year='', $month='', $day='')
    {
       if(empty($year)) {
           $year = strftime('%Y');
       }
       if(empty($month)) {
           $month = strftime('%m');
       }
       if(empty($day)) {
           $day = strftime('%d');
       }

       return mktime(0, 0, 0, $month, $day, $year);
    }
    ?>

        

See also [`{html_select_date}`](#language.function.html.select.date),
[`{html_select_time}`](#language.function.html.select.time),
[`date_format`](#language.modifier.date.format) and
[`$smarty.now`](#language.variables.smarty.now),

WAP/WML {#tips.wap}
=======

WAP/WML templates require a php [Content-Type
header](&url.php-manual;header) to be passed along with the template.
The easist way to do this would be to write a custom function that
prints the header. If you are using [caching](#caching), that won\'t
work so we\'ll do it using the [`{insert}`](#language.function.insert)
tag; remember `{insert}` tags are not cached! Be sure that there is
nothing output to the browser before the template, or else the header
may fail.


    <?php

    // be sure apache is configure for the .wml extensions!
    // put this function somewhere in your application, or in Smarty.addons.php
    function insert_header($params)
    {
       // this function expects $content argument
       if (empty($params['content'])) {
           return;
       }
       header($params['content']);
       return;
    }

    ?>

        

your Smarty template *must* begin with the insert tag :


    {insert name=header content="Content-Type: text/vnd.wap.wml"}

    <?xml version="1.0"?>
    <!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">

    <!-- begin new wml deck -->
    <wml>
     <!-- begin first card -->
     <card>
      <do type="accept">
       <go href="#two"/>
      </do>
      <p>
       Welcome to WAP with Smarty!
       Press OK to continue...
      </p>
     </card>
     <!-- begin second card -->
     <card id="two">
      <p>
       Pretty easy isn't it?
      </p>
     </card>
    </wml>

        

Componentized Templates {#tips.componentized.templates}
=======================

Traditionally, programming templates into your applications goes as
follows: First, you accumulate your variables within your PHP
application, (maybe with database queries.) Then, you instantiate your
Smarty object, [`assign()`](#api.assign) the variables and
[`display()`](#api.display) the template. So lets say for example we
have a stock ticker on our template. We would collect the stock data in
our application, then assign these variables in the template and display
it. Now wouldn\'t it be nice if you could add this stock ticker to any
application by merely including the template, and not worry about
fetching the data up front?

You can do this by writing a custom plugin for fetching the content and
assigning it to a template variable.

`function.load_ticker.php` - drop file in
[`$plugins directory`](#variable.plugins.dir)


    <?php

    // setup our function for fetching stock data
    function fetch_ticker($symbol)
    {
       // put logic here that fetches $ticker_info
       // from some ticker resource
       return $ticker_info;
    }

    function smarty_function_load_ticker($params, $smarty)
    {
       // call the function
       $ticker_info = fetch_ticker($params['symbol']);

       // assign template variable
       $smarty->assign($params['assign'], $ticker_info);
    }
    ?>

        

`index.tpl`


    {load_ticker symbol='SMARTY' assign='ticker'}

    Stock Name: {$ticker.name} Stock Price: {$ticker.price}

        

See also [`{include_php}`](#language.function.include.php),
[`{include}`](#language.function.include) and
[`{php}`](#language.function.php).

Obfuscating E-mail Addresses {#tips.obfuscating.email}
============================

Do you ever wonder how your email address gets on so many spam mailing
lists? One way spammers collect email addresses is from web pages. To
help combat this problem, you can make your email address show up in
scrambled javascript in the HTML source, yet it it will look and work
correctly in the browser. This is done with the
[`{mailto}`](#language.function.mailto) plugin.


    <div id="contact">Send inquiries to
    {mailto address=$EmailAddress encode='javascript' subject='Hello'}
    </div>

        

> **Note**
>
> This method isn\'t 100% foolproof. A spammer could conceivably program
> his e-mail collector to decode these values, but not likely\....
> hopefully..yet \... wheres that quantum computer :-?.

See also [`escape`](#language.modifier.escape) modifier and
[`{mailto}`](#language.function.mailto).
