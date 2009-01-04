<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
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

// http://www.php.net/manual/fr/function.preg-last-error.php
// PHP 5 >= 5.2.0
if (!defined('PREG_NO_ERROR'))
  define('PREG_NO_ERROR', 0);
if (!defined('PREG_INTERNAL_ERROR'))
  define('PREG_INTERNAL_ERROR', 1);
if (!defined('PREG_BACKTRACK_LIMIT_ERROR'))
  define('PREG_BACKTRACK_LIMIT_ERROR', 2);
if (!defined('PREG_RECURSION_LIMIT_ERROR'))
  define('PREG_RECURSION_LIMIT_ERROR', 3);
if (!defined('PREG_BAD_UTF8_ERROR'))
  define('PREG_BAD_UTF8_ERROR', 4);

function preg_last_error()
{
  return PREG_NO_ERROR;
}
?>