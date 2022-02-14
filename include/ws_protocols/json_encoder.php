<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

class PwgJsonEncoder extends PwgResponseEncoder
{
  function encodeResponse($response)
  {
    if (is_object($response) and strtolower(get_class($response)) == 'pwgerror')
    {
      return json_encode(
        array(
          'stat' => 'fail',
          'err' => $response->code(),
          'message' => $response->message(),
          )
      );
    }
    parent::flattenResponse($response);
    return json_encode(
        array(
          'stat' => 'ok',
          'result' => $response,
      )
    );
  }

  function getContentType()
  {
    return 'text/json';
  }
}

?>
