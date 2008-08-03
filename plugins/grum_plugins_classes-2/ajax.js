/* -----------------------------------------------------------------------------
  file: ajax.js
  file version: 1.1.0
  date: 2008-05-25
  ------------------------------------------------------------------------------
  author: grum at grum.dnsalias.com
  << May the Little SpaceFrog be with you >>
  ------------------------------------------------------------------------------

   this classes provides base functions to add ajax into html page

    + create_httpobject provide a simple function to create an HTML request to a
      server ; return an XMLHttpRequest object (or compatible object for IE)

    + tHttpObject is a class providing :
        - an XMLHttpRequest object
        - 

  ------------------------------------------------------------------------------
  HISTORY VERSION
  v1.0.1  + [create_httpobject] overrideMimeType unknown by IE 7.0 ;
  v1.1.0  + add create_httpobject2 with mimetype parameter
              
   -------------------------------------------------------------------------- */


  function create_httpobject(requesttype, charset, ajaxurl, async)
  {
    return(create_httpobject2(requesttype, charset, ajaxurl, async, ''));
  }

  function create_httpobject2(requesttype, charset, ajaxurl, async, mimetype)
  {
    if (window.XMLHttpRequest)
    {
      // IE7 & FF method
      http_request = new XMLHttpRequest();
    }
    else
    {
      //Other IE method.....
      if (window.ActiveXObject)
      {
        try
        {
          http_request = new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch (e)
        {
          try
          {
            http_request = new ActiveXObject("Microsoft.XMLHTTP");
          }
          catch (e)
          {
            window.alert("Your browser is unable to use XMLHTTPRequest");
          } // try-catch
        } // try-catch
      }
    } // if-else

    if(charset=='') { charset='utf-8'; }

    http_request.onreadystatechange = function() {  };
    http_request.open(requesttype.toUpperCase(), ajaxurl, async);

    if(mimetype=='')
    {
      mimetype='text/html';
    }

    try
    {
      http_request.overrideMimeType(mimetype+'; charset='+charset);
    }
    catch(e)
    {
    }

    if(requesttype.toUpperCase()=='POST')
    {
      http_request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    }

    //method to restitute an XML object ; needed for compatibility between FF&IE
    http_request.XML = httpobject_responseXML;

    return(http_request);
  }


  function httpobject_responseXML()
  {
    if (document.implementation && document.implementation.createDocument)
    {
      //ff method
      return(this.responseXML);
    }
    else
    {
      //ie method
      return(xmlCreateFromString(this.responseText));
    }
  }