<?php

/* -----------------------------------------------------------------------------
  class name: pages_navigation
  class version: 1.0
  date: 2007-11-17
  ------------------------------------------------------------------------------
  author: grum at grum.dnsalias.com
  << May the Little SpaceFrog be with you >>
  ------------------------------------------------------------------------------

   this classes provides base functions to manage pages navigation

    - constructor pages_navigation($url)
    - (public) function set_nb_items($nbitems)
    - (public) function get_nb_items()
    - (public) function set_nb_items_per_page($nbitems)
    - (public) function get_nb_items_per_page()
    - (public) function get_nb_pages()
    - (public) function set_current_page($page)
    - (public) function get_current_page()
    - (public) function set_base_url($url)
    - (public) function get_base_url()
    - (public) function make_navigation()
    - (public) function make_navigation_function()
    - (private) function calc_nb_pages()
   ---------------------------------------------------------------------- */
class pages_navigation
{
  var $nbitems;
  var $nbitemsperpages;
  var $nbpages;
  var $currentpage;
  var $baseurl;
  var $pagevarurl;
  var $options;

  function pages_navigation()
  {
    $this->nbitems=0;
    $this->nbitemsperpages=0;
    $this->nbpages=0;
    $this->currentpage=0;
    $this->baseurl='';
    $this->pagevarurl='';
    $this->options=array(
      'prev_next' => true,
      'first_last' => true,
      'display_all' => true,
      'number_displayed' => 2 //number of page displayed before and after current page
    );
  }

  /*
    define value for total number of items
  */
  function set_nb_items($nbitems)
  {
    if($nbitems!=$this->nbitems)
    {
      $this->nbitems=$nbitems;
      $this->calc_nb_pages();
    }
    return($nbitems);
  }

  function get_nb_items()
  {
    return($nbitems);
  }

  /*
    define value for number of items displayed per pages
  */
  function set_nb_items_per_page($nbitems)
  {
    if(($nbitems!=$this->nbitemsperpages)&&($nbitems>0))
    {
      $this->nbitemsperpages=$nbitems;
      $this->calc_nb_pages();
    }
    return($this->nbitemsperpages);
  }

  function get_nb_items_per_page()
  {
    return($this->nbitemsperpages);
  }

  /*
    return numbers of pages
  */
  function get_nb_pages()
  {
    return($this->nbpages);
  }

  /*
    define the current page number
  */
  function set_current_page($page)
  {
    if(($page!=$this->currentpage)&&($page<=$this->nbpages)&&($page>0))
    {
      $this->currentpage=$page;
    }
    return($this->currentpage);
  }

  /*
    returns the current page number
  */
  function get_current_page()
  {
    return($this->currentpage);
  }

  /*
    define the value for url
    ex: "http://mysite.com/admin.php?var1=xxx&var2=xxx"
  */
  function set_base_url($url)
  {
    if($url!=$this->baseurl)
    {
      $this->baseurl=$url;
    }
    return($this->baseurl);
  }

  function get_base_url()
  {
    return($this->baseurl);
  }

  /*
    define the value for variables's name
    ex: url = "http://mysite.com/admin.php?var1=xxx&var2=xxx"
        pagevar = "pagenumber"
    url made is "http://mysite.com/admin.php?var1=xxx&var2=xxx&pagenumber=xxx"
  */
  function set_pagevar_url($var)
  {
    if($var!=$this->pagevarurl)
    {
      $this->pagevarurl=$var;
    }
    return($this->pagevarurl);
  }

  function get_pagevar_url()
  {
    return($this->pagevarurl);
  }


  /*
    returns an html formatted string
  */
  function make_navigation($functionname='')
  {
    $text='';
    if(($this->options['display_all'])||($this->options['number_displayed']>=$this->nbpages))
    {
      for($i=1;$i<=$this->nbpages;$i++)
      {
        if($i!=$this->currentpage)
        {
          if($functionname=='')
          {
            $text.='<a href="'.$this->baseurl.'&'.$this->pagevarurl.'='.$i.'">'.$i.'</a>&nbsp;';
          }
          else
          {
            $text.='<a style="cursor:pointer;" onclick="'.$functionname.'('.$i.');">'.$i.'</a>&nbsp;';
          }
        }
        else
        {
          $text.=$i.'&nbsp;';
        }
      }
    }
    else
    {
      for($i=$this->currentpage-$this->options['number_displayed'];$i<=$this->currentpage+$this->options['number_displayed'];$i++)
      {
        if(($i>0)&&($i<=$this->nbpages))
        {
          if($i!=$this->currentpage)
          {
            if($functionname=='')
            {
              $text.='<a href="'.$this->baseurl.'&'.$this->pagevarurl.'='.$i.'">'.$i.'</a>&nbsp;';
            }
            else
            {
              $text.='<a style="cursor:pointer;" onclick="'.$functionname.'('.$i.');">'.$i.'</a>&nbsp;';
            }
          }
          else
          {
            $text.=$i.'&nbsp;';
          }
        }
      }
      if($this->currentpage-$this->options['number_displayed']>0)
      {
        $text='&nbsp;...&nbsp;'.$text;
      }
      if($this->currentpage+$this->options['number_displayed']<$this->nbpages)
      {
        $text.='&nbsp;...&nbsp;';
      }
    }

    if($this->options['prev_next'])
    {
      $prevp='';
      $nextp='';
      if($this->currentpage>1)
      {
        if($functionname=='')
        {
          $prevp='<a href="'.$this->baseurl.'&'.$this->pagevarurl.'='.($this->currentpage-1).'"> Prev </a>';
        }
        else
        {
          $prevp='<a style="cursor:pointer;" onclick="'.$functionname.'('.($this->currentpage-1).');"> Prev </a>';
        }
      }
      if($this->currentpage<$this->nbpages)
      {
        if($functionname=='')
        {
          $nextp='<a href="'.$this->baseurl.'&'.$this->pagevarurl.'='.($this->currentpage+1).'"> Next </a>';
        }
        else
        {
          $nextp='<a style="cursor:pointer;" onclick="'.$functionname.'('.($this->currentpage+1).');"> Next </a>';
        }
      }

      $text=$prevp.$text.$nextp;
    }

    if($this->options['first_last'])
    {
      $firstp='';
      $lastp='';
      if($this->currentpage>1)
      {
        if($functionname=='')
        {
          $firstp='<a href="'.$this->baseurl.'&'.$this->pagevarurl.'=1"> First </a>';
        }
        else
        {
          $firstp='<a style="cursor:pointer;" onclick="'.$functionname.'(1);"> First </a>';
        }
      }
      if($this->currentpage<$this->nbpages)
      {
        if($functionname=='')
        {
          $lastp='<a href="'.$this->baseurl.'&'.$this->pagevarurl.'='.$this->nbpages.'"> Last </a>';
        }
        else
        {
          $lastp='<a style="cursor:pointer;" onclick="'.$functionname.'('.$this->nbpages.');"> Last </a>';
        }
      }

      $text=$firstp.$text.$lastp;
    }

    return($text);
  }


  /*
    calculate the number of pages...
  */
  function calc_nb_pages()
  {
    if($this->nbitemsperpages>0)
    {
      $this->nbpages=ceil($this->nbitems/$this->nbitemsperpages);
    }
  }

} //class

?>