<?php

/* -----------------------------------------------------------------------------
  class name: css
  class version: 2.0
  date: 2008-07-13

  ------------------------------------------------------------------------------
  Author     : Grum
    email    : grum@grum.dnsalias.com
    website  : http://photos.grum.dnsalias.com
    PWG user : http://forum.phpwebgallery.net/profile.php?id=3706

    << May the Little SpaceFrog be with you ! >>
  ------------------------------------------------------------------------------

   this classes provides base functions to manage css
    classe consider that $filename is under plugins/ directory


    - constructor css($filename)
    - (public) function css_file_exists()
    - (public) function make_CSS($css)
    - (public) function apply_CSS()
   ---------------------------------------------------------------------- */
class css
{
  private $filename;

  public function css($filename)
  {
    $this->filename=$filename;
  }

  /*
    make the css file
  */
  public function make_CSS($css)
  {
    if($css!="")
    {
      $handle=fopen($this->filename, "w");
      if($handle)
      {
        fwrite($handle, $css);
        fclose($handle);
      }
    }
  }

  /*
    return true if css file exists
  */
  public function css_file_exists()
  {
    return(file_exists($this->filename));
  }

  /*
    put a link in the template to load the css file
    this function have to be called in a 'loc_end_page_header' trigger

    if $text="", insert link to css file, otherwise insert directly a <style> markup
  */
  public function apply_CSS()
  {
    global $template;

    if($this->css_file_exists())
    {
      $template->append('head_elements', '<link rel="stylesheet" type="text/css" href="plugins/'.basename(dirname($this->filename))."/".basename($this->filename).'">');
    }
  }
} //class

?>