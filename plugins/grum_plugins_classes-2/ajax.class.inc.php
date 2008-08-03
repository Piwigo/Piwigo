<?php

/* -----------------------------------------------------------------------------
  class name: ajax
  class version: 2.0
  date: 2008-07-20
  ------------------------------------------------------------------------------
  author: grum at grum.dnsalias.com
  << May the Little SpaceFrog be with you >>
  ------------------------------------------------------------------------------

   this classes provides base functions to add ajax.js file into html page ;
   just instanciate an ajax object, and call return_result
    $ajax_content_to_be_returned = "...............";
    $ajax = new ajax();
    $ajax->return_result($ajax_content_to_be_returned);

    - constructor ajax()
    - function return_result($str)
   ---------------------------------------------------------------------- */



class ajax
{
  function ajax()
  {
    add_event_handler('loc_end_page_header', array(&$this, 'load_JS'));
  }

  function load_JS()
  {
    global $template;

    $name='plugins/'.basename(dirname(__FILE__)).'/ajax.js';

    $template->append('head_elements', '<script src="'.$name.'" type="text/javascript"></script>');
  }

  function return_result($str)
  {
    //$chars=get_html_translation_table(HTML_ENTITIES, ENT_NOQUOTES);
    $chars['<']='<';
    $chars['>']='>';
    $chars['&']='&';
    exit(strtr($str, $chars));
  }
} //class

/*
 it's better to make $ajax instance into the plugin object, otherwise an object
 made here cannot be acceeded..
*/
//$ajax=new ajax();

?>