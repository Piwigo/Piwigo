<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2013 Piwigo Team                  http://piwigo.org |
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

/**** WEB SERVICE CORE CLASSES************************************************
 * PwgServer - main object - the link between web service methods, request
 *  handler and response encoder
 * PwgRequestHandler - base class for handlers
 * PwgResponseEncoder - base class for response encoders
 * PwgError, PwgNamedArray, PwgNamedStruct - can be used by web service functions
 * as return values
 */


define( 'WS_PARAM_ACCEPT_ARRAY',  0x010000 );
define( 'WS_PARAM_FORCE_ARRAY',   0x030000 );
define( 'WS_PARAM_OPTIONAL',      0x040000 );

define( 'WS_ERR_INVALID_METHOD',  501 );
define( 'WS_ERR_MISSING_PARAM',   1002 );
define( 'WS_ERR_INVALID_PARAM',   1003 );

define( 'WS_XML_ATTRIBUTES', 'attributes_xml_');
define( 'WS_XML_CONTENT', 'content_xml_');

/**
 * PwgError object can be returned from any web service function implementation.
 */
class PwgError
{
  private $_code;
  private $_codeText;

  function PwgError($code, $codeText)
  {
    if ($code>=400 and $code<600)
    {
      set_status_header($code, $codeText);
    }

    $this->_code = $code;
    $this->_codeText = $codeText;
  }

  function code() { return $this->_code; }
  function message() { return $this->_codeText; }
}

/**
 * Simple wrapper around an array (keys are consecutive integers starting at 0).
 * Provides naming clues for xml output (xml attributes vs. xml child elements?)
 * Usually returned by web service function implementation.
 */
class PwgNamedArray
{
  /*private*/ var $_content;
  /*private*/ var $_itemName;
  /*private*/ var $_xmlAttributes;

  /**
   * Constructs a named array
   * @param arr array (keys must be consecutive integers starting at 0)
   * @param itemName string xml element name for values of arr (e.g. image)
   * @param xmlAttributes array of sub-item attributes that will be encoded as
   *      xml attributes instead of xml child elements
   */
  function PwgNamedArray($arr, $itemName, $xmlAttributes=array() )
  {
    $this->_content = $arr;
    $this->_itemName = $itemName;
    $this->_xmlAttributes = array_flip($xmlAttributes);
  }
}
/**
 * Simple wrapper around a "struct" (php array whose keys are not consecutive
 * integers starting at 0). Provides naming clues for xml output (what is xml
 * attributes and what is element)
 */
class PwgNamedStruct
{
  /*private*/ var $_content;
  /*private*/ var $_name;
  /*private*/ var $_xmlAttributes;

  /**
   * Constructs a named struct (usually returned by web service function
   * implementation)
   * @param name string - containing xml element name
   * @param content array - the actual content (php array)
   * @param xmlAttributes array - name of the keys in $content that will be
   *    encoded as xml attributes (if null - automatically prefer xml attributes
   *    whenever possible)
   */
  function PwgNamedStruct($name, $content, $xmlAttributes=null, $xmlElements=null )
  {
    $this->_name = $name;
    $this->_content = $content;
    if ( isset($xmlAttributes) )
    {
      $this->_xmlAttributes = array_flip($xmlAttributes);
    }
    else
    {
      $this->_xmlAttributes = array();
      foreach ($this->_content as $key=>$value)
      {
        if (!empty($key) and (is_scalar($value) or is_null($value)) )
        {
          if ( empty($xmlElements) or !in_array($key,$xmlElements) )
          {
            $this->_xmlAttributes[$key]=1;
          }
        }
      }
    }
  }
}


/**
 * Abstract base class for request handlers.
 */
class PwgRequestHandler
{
  /** Virtual abstract method. Decodes the request (GET or POST) handles the
   * method invocation as well as response sending.
   */
  function handleRequest(&$server) { assert(false); }
}

/**
 *
 * Base class for web service response encoder.
 */
class PwgResponseEncoder
{
  /** encodes the web service response to the appropriate output format
   * @param response mixed the unencoded result of a service method call
   */
  function encodeResponse($response) { assert(false); }

  /** default "Content-Type" http header for this kind of response format
   */
  function getContentType() { assert(false); }

  /**
   * returns true if the parameter is a 'struct' (php array type whose keys are
   * NOT consecutive integers starting with 0)
   */
  static function is_struct(&$data)
  {
    if (is_array($data) )
    {
      if (range(0, count($data) - 1) !== array_keys($data) )
      { # string keys, unordered, non-incremental keys, .. - whatever, make object
        return true;
      }
    }
    return false;
  }

  /**
   * removes all XML formatting from $response (named array, named structs, etc)
   * usually called by every response encoder, except rest xml.
   */
  static function flattenResponse(&$response)
  {
    PwgResponseEncoder::_mergeAttributesAndContent($response);
    PwgResponseEncoder::_removeNamedArray($response);
    PwgResponseEncoder::_removeNamedStruct($response);
    if (is_array($response))
    { // need to call 2 times (first time might add new arrays)
      array_walk_recursive($response, array('PwgResponseEncoder', '_remove_named_callback') );
      array_walk_recursive($response, array('PwgResponseEncoder', '_remove_named_callback') );
    }
//print_r($response);
    PwgResponseEncoder::_mergeAttributesAndContent($response);
  }

  private static function _remove_named_callback(&$value, $key)
  {
    do
    {
      $changed = 0;
      $changed += PwgResponseEncoder::_removeNamedArray($value);
      $changed += PwgResponseEncoder::_removeNamedStruct($value);
  //    print_r('walk '.$key."<br>\n");
    }
    while ($changed);
  }

  private static function _mergeAttributesAndContent(&$value)
  {
    if ( !is_array($value) )
      return;
/*    $first_key = '';
    if (count($value)) { $ak = array_keys($value); $first_key = $ak[0]; }

    print_r( '_mergeAttributesAndContent is_struct='.PwgResponseEncoder::is_struct($value)
      .' count='.count($value)
      .' first_key='.$first_key
      ."<br>\n"
      );*/
    $ret = 0;
    if (PwgResponseEncoder::is_struct($value))
    {
      if ( isset($value[WS_XML_ATTRIBUTES]) )
      {
        $value = array_merge( $value, $value[WS_XML_ATTRIBUTES] );
        unset( $value[WS_XML_ATTRIBUTES] );
        $ret=1;
      }
      if ( isset($value[WS_XML_CONTENT]) )
      {
        $cont_processed = 0;
        if ( count($value)==1 )
        {
          $value = $value[WS_XML_CONTENT];
          $cont_processed=1;
        }
        else
        {
          if (PwgResponseEncoder::is_struct($value[WS_XML_CONTENT]))
          {
            $value = array_merge( $value, $value[WS_XML_CONTENT] );
            unset( $value[WS_XML_CONTENT] );
            $cont_processed=1;
          }
        }
        $ret += $cont_processed;
        if (!$cont_processed)
        {
          $value['_content'] = $value[WS_XML_CONTENT];
          unset( $value[WS_XML_CONTENT] );
          $ret++;
        }
      }
    }

    foreach ($value as $key=>$v)
    {
      if ( PwgResponseEncoder::_mergeAttributesAndContent($v) )
      {
        $value[$key]=$v;
        $ret++;
      }
    }
    return $ret;
  }

  private static function _removeNamedArray(&$value)
  {
    if ( strtolower( @get_class($value) ) =='pwgnamedarray')
    {
      $value = $value->_content;
      return 1;
    }
    return 0;
  }

  private static function _removeNamedStruct(&$value)
  {
    if ( strtolower( @get_class($value) ) =='pwgnamedstruct')
    {
      if ( isset($value->_content['']) )
      {
        $unknown = $value->_content[''];
        unset( $value->_content[''] );
        $value->_content[$value->_name] = $unknown;
      }
      $value = $value->_content;
      return 1;
    }
    return 0;
  }
}



class PwgServer
{
  var $_requestHandler;
  var $_requestFormat;
  var $_responseEncoder;
  var $_responseFormat;

  var $_methods = array();

  function PwgServer()
  {
  }

  /**
   *  Initializes the request handler.
   */
  function setHandler($requestFormat, &$requestHandler)
  {
    $this->_requestHandler = &$requestHandler;
    $this->_requestFormat = $requestFormat;
  }

  /**
   *  Initializes the request handler.
   */
  function setEncoder($responseFormat, &$encoder)
  {
    $this->_responseEncoder = &$encoder;
    $this->_responseFormat = $responseFormat;
  }

  /**
   * Runs the web service call (handler and response encoder should have been
   * created)
   */
  function run()
  {
    if ( is_null($this->_responseEncoder) )
    {
      set_status_header(400);
      @header("Content-Type: text/plain");
      echo ("Cannot process your request. Unknown response format.
Request format: ".@$this->_requestFormat." Response format: ".@$this->_responseFormat."\n");
      var_export($this);
      die(0);
    }

    if ( is_null($this->_requestHandler) )
    {
      $this->sendResponse( new PwgError(400, 'Unknown request format') );
      return;
    }

    $this->addMethod('reflection.getMethodList',
        array('PwgServer', 'ws_getMethodList'),
        null, '' );
    $this->addMethod('reflection.getMethodDetails',
        array('PwgServer', 'ws_getMethodDetails'),
        array('methodName'),'');

    trigger_action('ws_add_methods', array(&$this) );
    uksort( $this->_methods, 'strnatcmp' );
    $this->_requestHandler->handleRequest($this);
  }

  /**
   * Encodes a response and sends it back to the browser.
   */
  function sendResponse($response)
  {
    $encodedResponse = $this->_responseEncoder->encodeResponse($response);
    $contentType = $this->_responseEncoder->getContentType();

    @header('Content-Type: '.$contentType.'; charset='.get_pwg_charset());
    print_r($encodedResponse);
    trigger_action('sendResponse', $encodedResponse );
  }

  /**
   * Registers a web service method.
   * @param methodName string - the name of the method as seen externally
   * @param callback mixed - php method to be invoked internally
   * @param params array - map of allowed parameter names with optional default
   * values and parameter flags. Example of $params:
   * array( 'param1' => array('default'=>523, 'flags'=>WS_PARAM_FORCE_ARRAY) ) .
   * Possible parameter flags are:
   * WS_PARAM_ALLOW_ARRAY - this parameter can be an array
   * WS_PARAM_FORCE_ARRAY - if this parameter is scalar, force it to an array
   *  before invoking the method
   * @param description string - a description of the method.
   */
  function addMethod($methodName, $callback, $params=array(), $description, $include_file='')
  {
    if (!is_array($params))
    {
      $params = array();
    }

    if ( range(0, count($params) - 1) === array_keys($params) )
    {
      $params = array_flip($params);
    }

    foreach( $params as $param=>$options)
    {
      if ( !is_array($options) )
      {
        $params[$param] = array('flags'=>0);
      }
      else
      {
        $flags = isset($options['flags']) ? $options['flags'] : 0;
        if ( array_key_exists('default', $options) )
        {
          $flags |= WS_PARAM_OPTIONAL;
        }
        if ( $flags & WS_PARAM_FORCE_ARRAY )
        {
          $flags |= WS_PARAM_ACCEPT_ARRAY;
        }
        $options['flags'] = $flags;
        $params[$param] = $options;
      }
    }

    $this->_methods[$methodName] = array(
      'callback'    => $callback,
      'description' => $description,
      'signature'   => $params,
      'include'     => $include_file,
      );
  }

  function hasMethod($methodName)
  {
    return isset($this->_methods[$methodName]);
  }

  function getMethodDescription($methodName)
  {
    $desc = @$this->_methods[$methodName]['description'];
    return isset($desc) ? $desc : '';
  }

  function getMethodSignature($methodName)
  {
    $signature = @$this->_methods[$methodName]['signature'];
    return isset($signature) ? $signature : array();
  }

  /*static*/ function isPost()
  {
    return isset($HTTP_RAW_POST_DATA) or !empty($_POST);
  }

  /*static*/ function makeArrayParam(&$param)
  {
    if ( $param==null )
    {
      $param = array();
    }
    else
    {
      if (! is_array($param) )
      {
        $param = array($param);
      }
    }
  }

  /**
   *  Invokes a registered method. Returns the return of the method (or
   *  a PwgError object if the method is not found)
   *  @param methodName string the name of the method to invoke
   *  @param params array array of parameters to pass to the invoked method
   */
  function invoke($methodName, $params)
  {
    $method = @$this->_methods[$methodName];

    if ( $method==null )
    {
      return new PwgError(WS_ERR_INVALID_METHOD, 'Method name is not valid');
    }

    // parameter check and data coercion !
    $signature = $method['signature'];
    $missing_params = array();
    foreach($signature as $name=>$options)
    {
      $flags = $options['flags'];
      if ( !array_key_exists($name, $params) )
      {// parameter not provided in the request
        if ( !($flags&WS_PARAM_OPTIONAL) )
        {
          $missing_params[] = $name;
        }
        else if ( array_key_exists('default',$options) )
        {
          $params[$name] = $options['default'];
          if ( ($flags&WS_PARAM_FORCE_ARRAY) )
          {
            $this->makeArrayParam( $params[$name] );
          }
        }
      }
      else
      {// parameter provided - do some basic checks
        $the_param = $params[$name];
        if ( is_array($the_param) and ($flags&WS_PARAM_ACCEPT_ARRAY)==0 )
        {
          return new PwgError(WS_ERR_INVALID_PARAM, $name.' must be scalar' );
        }
        if ( ($flags&WS_PARAM_FORCE_ARRAY) )
        {
          $this->makeArrayParam( $the_param );
        }
        if ( isset($options['maxValue']) and $the_param>$options['maxValue'])
        {
          $the_param = $options['maxValue'];
        }
        $params[$name] = $the_param;
      }
    }
    if (count($missing_params))
    {
      return new PwgError(WS_ERR_MISSING_PARAM, 'Missing parameters: '.implode(',',$missing_params));
    }
    $result = trigger_event('ws_invoke_allowed', true, $methodName, $params);
    if ( strtolower( @get_class($result) )!='pwgerror')
    {
      if ( !empty($method['include']) )
      {
        include_once( $method['include'] );
      }
      $result = call_user_func_array($method['callback'], array($params, &$this) );
    }
    return $result;
  }

  /**
   * WS reflection method implementation: lists all available methods
   */
  static function ws_getMethodList($params, &$service)
  {
    return array('methods' => new PwgNamedArray( array_keys($service->_methods),'method' ) );
  }

  /**
   * WS reflection method implementation: gets information about a given method
   */
  static function ws_getMethodDetails($params, &$service)
  {
    $methodName = $params['methodName'];
    if (!$service->hasMethod($methodName))
    {
      return new PwgError(WS_ERR_INVALID_PARAM,
            'Requested method does not exist');
    }
    $res = array(
      'name' => $methodName,
      'description' => $service->getMethodDescription($methodName),
      'params' => array(),
    );
    $signature = $service->getMethodSignature($methodName);
    foreach ($signature as $name => $options)
    {
      $param_data = array(
        'name' => $name,
        'optional' => ($options['flags']&WS_PARAM_OPTIONAL)?true:false,
        'acceptArray' => ($options['flags']&WS_PARAM_ACCEPT_ARRAY)?true:false,
        );
      if (isset($options['default']))
      {
        $param_data['defaultValue'] = $options['default'];
      }
      $res['params'][] = $param_data;
    }
    return $res;
  }
}
?>