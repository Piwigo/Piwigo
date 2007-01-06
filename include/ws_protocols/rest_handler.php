<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $URL: svn+ssh://rvelices@svn.gna.org/svn/phpwebgallery/trunk/action.php $
// | last update   : $Date: 2006-12-21 18:49:12 -0500 (Thu, 21 Dec 2006) $
// | last modifier : $Author: rvelices $
// | revision      : $Rev: 1678 $
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

class PwgRestRequestHandler
{
  function handleRequest(&$service)
  {
    $params = array();

    $param_array = $service->isPost() ? $_POST : $_GET;
    foreach ($param_array as $name => $value)
    {
      if ($name=='format')
        continue;
      if ($name=='method')
      {
        $method = $value;
      }
      else
      {
        $params[$name]=$value;
      }
    }

    if ( empty($method) )
    {
      $service->sendResponse(
          new PwgError(400, 'Missing "method" name')
        );
      return;
    }
    $resp = $service->invoke($method, $params);
    $service->sendResponse($resp);
  }
}

?>
