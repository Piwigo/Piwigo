<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
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

function xmlrpc_encode($data)
{
  switch (gettype($data))
  {
    case 'boolean':
      return '<boolean>'.($data ? '1' : '0').'</boolean>';
    case 'integer':
      return '<int>'.$data.'</int>';
    case 'double':
      return '<double>'.$data.'</double>';
    case 'string':
      return '<string>'.htmlspecialchars($data).'</string>';
    case 'object':
    case 'array':
      $is_array = range(0, count($data) - 1) === array_keys($data);
      if ($is_array)
      {
        $return = '<array><data>'."\n";
        foreach ($data as $item)
        {
          $return .= '  <value>'.xmlrpc_encode($item)."</value>\n";
        }
        $return .= '</data></array>';
      }
      else
      {
        $return = '<struct>'."\n";
        foreach ($data as $name => $value)
        {
					$name = htmlspecialchars($name);
          $return .= "  <member><name>$name</name><value>";
          $return .= xmlrpc_encode($value)."</value></member>\n";
        }
        $return .= '</struct>';
      }
      return $return;
  }
}

class PwgXmlRpcEncoder extends PwgResponseEncoder
{
  function encodeResponse($response)
  {
    $respClass = strtolower( @get_class($response) );
    if ($respClass=='pwgerror')
    {
      $code = $response->code();
      $msg = htmlspecialchars($response->message());
      $ret = <<<EOD
<methodResponse>
  <fault>
    <value>
      <struct>
        <member>
          <name>faultCode</name>
          <value><int>{$code}</int></value>
        </member>
        <member>
          <name>faultString</name>
          <value><string>{$msg}</string></value>
        </member>
      </struct>
    </value>
  </fault>
</methodResponse>
EOD;
      return $ret;
    }

    parent::flattenResponse($response);
    $ret = xmlrpc_encode($response);
    $ret = <<<EOD
<methodResponse>
  <params>
    <param>
      <value>
        $ret
      </value>
    </param>
  </params>
</methodResponse>
EOD;
    return $ret;
  }

  function getContentType()
  {
    return 'text/xml';
  }
}

?>
