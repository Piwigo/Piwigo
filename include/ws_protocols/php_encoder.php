<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

class PwgSerialPhpEncoder extends PwgResponseEncoder
{
  function encodeResponse($response)
  {
    if ($response instanceof PwgError)
    {
      return serialize(
        array(
          'stat' => 'fail',
          'err' => $response->code(),
          'message' => $response->message(),
          )
      );
    }
    parent::flattenResponse($response);
    return serialize(
        array(
          'stat' => 'ok',
          'result' => $response
      )
    );
  }

  function getContentType()
  {
    return 'text/plain';
  }
}

?>
