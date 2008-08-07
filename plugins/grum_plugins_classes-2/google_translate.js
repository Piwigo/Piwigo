/* -----------------------------------------------------------------------------
  file: google_translate.js
  file version: 2.0.0
  date: 2008-05-25
  ------------------------------------------------------------------------------
  author: grum at grum.dnsalias.com
  << May the Little SpaceFrog be with you >>
  ------------------------------------------------------------------------------

   this classes provides base functions to use Google Translate AJAX API
    >>  http://code.google.com/apis/ajaxlanguage/

  ------------------------------------------------------------------------------
  HISTORY VERSION
  v2.0.0  + adapted for piwigo
          + add of a 5th&6th parameters for the google_translate function
              
   -------------------------------------------------------------------------- */

  google.load("language", "1");


  var global_google_translate_plugin_objdest;
  var global_google_translate_plugin_objproperty;
  var global_google_translate_plugin_objcallback;
  var global_google_translate_plugin_objcallback_param;


  function google_translate(text, pfrom, pto, objdest, objproperty)
  {
    /*
      ** args needed **
      1st arg : text to translate
      2nd arg : translate from lang ("en", "fr", "es", ...)
      3rd arg : translate to lang ("en", "fr", "es", ...)
      4th arg : target of result (id)
      5th arg : affected propertie ('value' or 'innerHTML')
      ** facultative args **
      6th arg : pointer on a function definition (callback is made when
                translation is done ; notice that translation is made asynchronous)
      7th arg : arg for the callback (or array of arg if callbakc need more than
                one parameter)
    */
    if(arguments.length>=6)
    {
      global_google_translate_plugin_objcallback=arguments[5];
    }
    else
    {
      global_google_translate_plugin_objcallback=null;
    }

    if(arguments.length>=7)
    {
      if(arguments[6].pop)
      {
        global_google_translate_plugin_objcallback_param=arguments[6];
      }
      else
      {
        global_google_translate_plugin_objcallback_param=new Array(arguments[6]);
      }
    }
    else
    {
      global_google_translate_plugin_objcallback_param=null;
    }


    global_google_translate_plugin_objdest = objdest;
    global_google_translate_plugin_objproperty = objproperty;
    google.language.translate(text, pfrom, pto, google_translate_do);
  }


  function google_translate_do(result)
  {
    if (!result.error)
    {
      if(global_google_translate_plugin_objproperty=='value')
      {
        global_google_translate_plugin_objdest.value = result.translation;
      }
      else if(global_google_translate_plugin_objproperty=='innerHTML')
      {
        global_google_translate_plugin_objdest.innerHTML = result.translation;
      }
      if(global_google_translate_plugin_objcallback!=null)
      {
        if(global_google_translate_plugin_objcallback_param!=null)
        {
          global_google_translate_plugin_objcallback.apply(null, global_google_translate_plugin_objcallback_param);
        }
        else
        {
          global_google_translate_plugin_objcallback();
        }
      }
    }    
  }

