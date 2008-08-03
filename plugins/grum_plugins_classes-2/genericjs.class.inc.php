<?php

/* -----------------------------------------------------------------------------
  class name: genericjs
  class version: 2.0
  date: 2008-07-20
  ------------------------------------------------------------------------------
  author: grum at grum.dnsalias.com
  << May the Little SpaceFrog be with you >>
  ------------------------------------------------------------------------------

   this classes provides base functions to add genericjs.js file into html page

   > see genericjs.js file to know javascript functions added 

    - constructor genericjs()
   ---------------------------------------------------------------------- */


class genericjs
{
  function genericjs()
  {
    add_event_handler('loc_end_page_header', array(&$this, 'load_JS'));
  }

  function load_JS()
  {
    global $template;

    $name='plugins/'.basename(dirname(__FILE__)).'/genericjs.js';

    $template->append('head_elements', '<script src="'.$name.'" type="text/javascript"></script>');

  }

} //class

$genericjs=new genericjs();

?>