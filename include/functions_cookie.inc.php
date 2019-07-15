<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * @package functions\cookie
 */


/**
 * Returns the path to use for the Piwigo cookie.
 * If Piwigo is installed on :
 * http://domain.org/meeting/gallery/
 * it will return : "/meeting/gallery"
 *
 * @return string
 */
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
 * Persistently stores a variable in pwg cookie.
 * Set $value to null to delete the cookie.
 *
 * @param string $car
 * @param mixed $value
 * @param int|null $expire
 * @return bool
 */
function pwg_set_cookie_var($var, $value, $expire=null)
{
  if ($value==null or $expire===0)
  {
    unset($_COOKIE['pwg_'.$var]);
    return setcookie('pwg_'.$var, false, 0, cookie_path());

  }
  else
  {
    $_COOKIE['pwg_'.$var] = $value;
    $expire = is_numeric($expire) ? $expire : strtotime('+10 years');
    return setcookie('pwg_'.$var, $value, $expire, cookie_path());
  }
}

/**
 * Retrieves the value of a persistent variable in pwg cookie
 * @see pwg_set_cookie_var
 *
 * @param string $var
 * @param mixed $default
 * @return mixed
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