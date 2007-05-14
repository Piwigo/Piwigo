<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | file          : $Id$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

// cookie_path returns the path to use for the PhpWebGallery cookie.
// If PhpWebGallery is installed on :
// http://domain.org/meeting/gallery/category.php
// cookie_path will return : "/meeting/gallery"
function cookie_path()
{
  if ( isset($_SERVER['REDIRECT_SCRIPT_NAME']) and
       !empty($_SERVER['REDIRECT_SCRIPT_NAME']) )
  {
    $scr = $_SERVER['REDIRECT_SCRIPT_NAME'];
  }
  else if ( isset($_SERVER['REDIRECT_URL']) )
  {
    // mod_rewrite is activated for upper level directories. we must set the
    // cookie to the path shown in the browser otherwise it will be discarded.
    if 
      ( 
        isset($_SERVER['PATH_INFO']) and !empty($_SERVER['PATH_INFO']) and
        ($_SERVER['REDIRECT_URL'] !== $_SERVER['PATH_INFO']) and
        (substr($_SERVER['REDIRECT_URL'],-strlen($_SERVER['PATH_INFO']))
            == $_SERVER['PATH_INFO'])
      )
    {
      $scr = substr($_SERVER['REDIRECT_URL'], 0, 
        strlen($_SERVER['REDIRECT_URL'])-strlen($_SERVER['PATH_INFO']));
    }
    else
    {
      $scr = $_SERVER['REDIRECT_URL'];
    }
  }
  else
  {
    $scr = $_SERVER['SCRIPT_NAME'];
  }

  $scr = substr($scr,0,strrpos( $scr,'/'));

  // add a trailing '/' if needed
  if ((strlen($scr) == 0) or ($scr{strlen($scr)-1} !== '/'))
  {
    $scr .= '/';
  }
  
  if ( substr(PHPWG_ROOT_PATH,0,3)=='../')
  { // this is maybe a plugin inside pwg directory
    // TODO - what if it is an external script outside PWG ?
    $scr = $scr.PHPWG_ROOT_PATH;
    while (1)
    {
      $new = preg_replace('#[^/]+/\.\.(/|$)#', '', $scr);
      if ($new==$scr)
      {
        break;
      }
      $scr=$new;
    }
  }
  return $scr;
}

/**
 * persistently stores a variable in pwg cookie
 * @return boolean true on success
 * @see pwg_get_cookie_var
 */
function pwg_set_cookie_var($var, $value)
{
  $_COOKIE['pwg_'.$var] = $value;
  return
    setcookie('pwg_'.$var, $value, 
      strtotime('+10 years'), cookie_path());
}

/**
 * retrieves the value of a persistent variable in pwg cookie
 * @return mixed
 * @see pwg_set_cookie_var
 */
function pwg_get_cookie_var($var, $default = null)
{
  if (isset($_COOKIE['pwg_'.$var]))
  {
    return $_COOKIE['pwg_'.$var];
  }
  else
  {
    return $default;
  }
}

?>
