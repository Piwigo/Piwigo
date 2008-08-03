<?php

/* -----------------------------------------------------------------------------
  class name: translate
  class version: 2.0.1
  date: 2008-05-25
  ------------------------------------------------------------------------------
  author: grum at grum.dnsalias.com
  << May the Little SpaceFrog be with you >>
  ------------------------------------------------------------------------------

   this classes provides base functions to manage call to "translate.google.com"

   release 2.x use durect call to Google Translate AJAX API, so all functions
    from PHP class are removed (except for "can_translate")
   class call API in HTML header, and provide a .js file manage API call

    - constructor translate()
    - (public) function can_translate($from, $to)   //v1.1

  version 1.1.1
    - google have changed HTML code for translation page ; change search string of translation result
    - bug corrected : if language given with uppercase, force them to lowercase
  version 2.0.0
    - use of Google Translate javascript API
        >>  http://code.google.com/apis/ajaxlanguage/

   ---------------------------------------------------------------------- */
class translate
{
  var $language_list;

  function translate()
  {
    //alloweds from->to transations languages
    $this->language_list=array_flip(
      array(
        'ar', //arabic
        'bg', //Bulgarian
        'zh', //Chinese (simplified)
        'hr', //Croatian
        'cs', //Czech
        'da', //Danish
        "nl", //Dutch
        'en', //English
        'fi', //Finnish
        "fr", //French
        "de", //German
        "el", //Greek
        'hi', //Hindi
        "it", //Italian
        "ja", //Japanese
        "ko", //Korean
        'no', //Norwegian
        'pl', //Polish
        "pt", //Portuguese
        'ro', //Romanian
        "ru", //Russian
        "es", //Spanish
        'sv' //Swedish
      )
    );
    add_event_handler('loc_end_page_header', array(&$this, 'load_JS'));
  }

  function load_JS()
  {
    global $template;

    $googleload='
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript" src="plugins/'.basename(dirname(__FILE__)).'/google_translate.js"></script>';

    $template->append('head_elements', $googleload);


  }

  function can_translate($from, $to)
  {
    if(isset($this->language_list[strtolower($from)])&&isset($this->language_list[strtolower($to)]))
    {
      return(true);
    }
    else
    {
      return(false);
    }
  }
  

/*
  theses methods are removed for direct use of the Google AJAX API (better choice for
  performance)

  how to :
    create one instance of this classe
    classe can be used by server to know authorized language to be translated
    all translations have to be made with javascript functions

  =8<===========================================================================

  function set_languages($lang)
  {
    $pair=explode('|', strtolower($lang));

    if(isset($this->language_list[$pair[0]])&&isset($this->language_list[$pair[1]]))
    {
      $this->from_to_lang=strtolower($lang);
    }
    return($this->from_to_lang);
  }

  function get_languages()
  {
    return($this->from_to_lang);
  }

  function set_input_charset($charset)
  {
    $this->input_charset=$charset;
  }

  function get_input_charset($charset)
  {
    return($this->input_charset);
  }

  function set_output_charset($charset)
  {
    $this->output_charset=$charset;
  }

  function get_output_charset($charset)
  {
    return($this->output_charset);
  }

  function do_translation($text)
  {
    if(ini_get('allow_url_fopen')!="1")
    {
      return("");
    }

    $req="http://translate.google.com/translate_t?text=".urlencode($text).
         "&langpair=".strtolower($this->from_to_lang);
    if($this->input_charset!="")
    {
      $req.="&ie=".$this->input_charset;
    }
    if($this->output_charset!="")
    {
      $req.="&oe=".$this->output_charset;
    }

    $handle=fopen($req, "r");
    if($handle)
    {
      $contents="";
      while (!feof($handle))
      {
        $contents .= fread($handle, 4196);
      }
      fclose($handle);

      $search="<div id=result_box dir=\"ltr\">";
      $p = strpos($contents, $search);
      if($p>0)
      {
        $contents=substr($contents, $p+strlen($search));
        $search="</div>";
        $p = strpos($contents, $search);
        $contents=substr($contents, 0, $p);
      }
      else
      {
        $contents="";
      }

      return($contents);
    }
    else
    {
      return("");
    }
  }
*/

} //class

?>