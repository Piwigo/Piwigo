<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

class PwgRestRequestHandler extends PwgRequestHandler
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
		if ( empty($method) && isset($_GET['method']) )
		{
			$method = $_GET['method'];
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
