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

class PwgRestRequestHandler
{
  function handleRequest(&$service)
  {
    $params = array();

    $param_array = $service->isPost() ? $_POST : $_GET;
    foreach ($param_array as $name => $value)
    {
      if ($name=='format')
        continue; // ignore - special keys
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
          new PwgError(WS_ERR_INVALID_METHOD, 'Missing "method" name')
        );
      return;
    }
    $resp = $service->invoke($method, $params);
    $service->sendResponse($resp);
  }
}

?>
