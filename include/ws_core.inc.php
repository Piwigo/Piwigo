<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
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

define( 'WS_TYPE_BOOL',           0x01 );
define( 'WS_TYPE_INT',            0x02 );
define( 'WS_TYPE_FLOAT',          0x04 );
define( 'WS_TYPE_POSITIVE',       0x10 );
define( 'WS_TYPE_NOTNULL',        0x20 );
define( 'WS_TYPE_ID', WS_TYPE_INT | WS_TYPE_POSITIVE | WS_TYPE_NOTNULL);

define( 'WS_ERR_INVALID_METHOD',  501 );
define( 'WS_ERR_MISSING_PARAM',   1002 );
define( 'WS_ERR_INVALID_PARAM',   1003 );

define( 'WS_XML_ATTRIBUTES', 'attributes_xml_');

/**
 * PwgError object can be returned from any web service function implementation.
 */
class PwgError
{
  private $_code;
  private $_codeText;

  function __construct($code, $codeText)
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
  function __construct($arr, $itemName, $xmlAttributes=array() )
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
  function __construct($content, $xmlAttributes=null, $xmlElements=null )
  {
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
abstract class PwgRequestHandler
{
  /** Virtual abstract method. Decodes the request (GET or POST) handles the
   * method invocation as well as response sending.
   */
  abstract function handleRequest(&$service);
}

/**
 *
 * Base class for web service response encoder.
 */
abstract class PwgResponseEncoder
{
  /** encodes the web service response to the appropriate output format
   * @param response mixed the unencoded result of a service method call
   */
  abstract function encodeResponse($response);

  /** default "Content-Type" http header for this kind of response format
   */
  abstract function getContentType();

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
  static function flattenResponse(&$value)
  {
    self::flatten($value);
  }

  private static function flatten(&$value)
  {
    if (is_object($value))
    {
      $class = strtolower( @get_class($value) );
      if ($class == 'pwgnamedarray')
      {
        $value = $value->_content;
      }
      if ($class == 'pwgnamedstruct')
      {
        $value = $value->_content;
      }
    }

    if (!is_array($value))
      return;

    if (self::is_struct($value))
    {
      if ( isset($value[WS_XML_ATTRIBUTES]) )
      {
        $value = array_merge( $value, $value[WS_XML_ATTRIBUTES] );
        unset( $value[WS_XML_ATTRIBUTES] );
      }
    }

    foreach ($value as $key=>&$v)
    {
      self::flatten($v);
    }
  }
}



class PwgServer
{
  var $_requestHandler;
  var $_requestFormat;
  var $_responseEncoder;
  var $_responseFormat;

  var $_methods = array();

  function __construct()
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

    // add reflection methods
    $this->addMethod(
        'reflection.getMethodList',
        array('PwgServer', 'ws_getMethodList')
        );
    $this->addMethod(
        'reflection.getMethodDetails',
        array('PwgServer', 'ws_getMethodDetails'),
        array('methodName')
        );

    trigger_notify('ws_add_methods', array(&$this) );
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
    trigger_notify('sendResponse', $encodedResponse );
  }

  /**
   * Registers a web service method.
   * @param methodName string - the name of the method as seen externally
   * @param callback mixed - php method to be invoked internally
   * @param params array - map of allowed parameter names with options
   *    @option mixed default (optional)
   *    @option int flags (optional)
   *      possible values: WS_PARAM_ALLOW_ARRAY, WS_PARAM_FORCE_ARRAY, WS_PARAM_OPTIONAL
   *    @option int type (optional)
   *      possible values: WS_TYPE_BOOL, WS_TYPE_INT, WS_TYPE_FLOAT, WS_TYPE_ID
   *                       WS_TYPE_POSITIVE, WS_TYPE_NOTNULL
   *    @option int|float maxValue (optional)
   * @param description string - a description of the method.
   * @param include_file string - a file to be included befaore the callback is executed
   * @param options array
   *    @option bool hidden (optional) - if true, this method won't be visible by reflection.getMethodList
   *    @option bool admin_only (optional)
   *    @option bool post_only (optional)
   */
  function addMethod($methodName, $callback, $params=array(), $description='', $include_file='', $options=array())
  {
    if (!is_array($params))
    {
      $params = array();
    }

    if ( range(0, count($params) - 1) === array_keys($params) )
    {
      $params = array_flip($params);
    }

    foreach( $params as $param=>$data)
    {
      if ( !is_array($data) )
      {
        $params[$param] = array('flags'=>0,'type'=>0);
      }
      else
      {
        if ( !isset($data['flags']) )
        {
          $data['flags'] = 0;
        }
        if ( array_key_exists('default', $data) )
        {
          $data['flags'] |= WS_PARAM_OPTIONAL;
        }
        if ( !isset($data['type']) )
        {
          $data['type'] = 0;
        }
        $params[$param] = $data;
      }
    }

    $this->_methods[$methodName] = array(
      'callback'    => $callback,
      'description' => $description,
      'signature'   => $params,
      'include'     => $include_file,
      'options'     => $options,
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
  
  /**
   * @since 2.6
   */
  function getMethodOptions($methodName)
  {
    $options = @$this->_methods[$methodName]['options'];
    return isset($options) ? $options : array();
  }

  static function isPost()
  {
    return isset($HTTP_RAW_POST_DATA) or !empty($_POST);
  }

  static function makeArrayParam(&$param)
  {
    if ( $param==null )
    {
      $param = array();
    }
    else
    {
      if ( !is_array($param) )
      {
        $param = array($param);
      }
    }
  }
  
  static function checkType(&$param, $type, $name)
  {
    $opts = array();
    $msg = '';
    if ( self::hasFlag($type, WS_TYPE_POSITIVE | WS_TYPE_NOTNULL) )
    {
      $opts['options']['min_range'] = 1;
      $msg = ' positive and not null';
    }
    else if ( self::hasFlag($type, WS_TYPE_POSITIVE) )
    {
      $opts['options']['min_range'] = 0;
      $msg = ' positive';
    }
    
    if ( is_array($param) )
    {
      if ( self::hasFlag($type, WS_TYPE_BOOL) )
      {
        foreach ($param as &$value)
        {
          if ( ($value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) === null )
          {
            return new PwgError(WS_ERR_INVALID_PARAM, $name.' must only contain booleans' );
          }
        }
        unset($value);
      }
      else if ( self::hasFlag($type, WS_TYPE_INT) )
      {
        foreach ($param as &$value)
        {
          if ( ($value = filter_var($value, FILTER_VALIDATE_INT, $opts)) === false )
          {
            return new PwgError(WS_ERR_INVALID_PARAM, $name.' must only contain'.$msg.' integers' );
          }
        }
        unset($value);
      }
      else if ( self::hasFlag($type, WS_TYPE_FLOAT) )
      {
        foreach ($param as &$value)
        {
          if (
            ($value = filter_var($value, FILTER_VALIDATE_FLOAT)) === false
            or ( isset($opts['options']['min_range']) and $value < $opts['options']['min_range'] )
          ) {
            return new PwgError(WS_ERR_INVALID_PARAM, $name.' must only contain'.$msg.' floats' );
          }
        }
        unset($value);
      }
    }
    else if ( $param !== '' )
    {
      if ( self::hasFlag($type, WS_TYPE_BOOL) )
      {
        if ( ($param = filter_var($param, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) === null )
        {
          return new PwgError(WS_ERR_INVALID_PARAM, $name.' must be a boolean' );
        }
      }
      else if ( self::hasFlag($type, WS_TYPE_INT) )
      {
        if ( ($param = filter_var($param, FILTER_VALIDATE_INT, $opts)) === false )
        {
          return new PwgError(WS_ERR_INVALID_PARAM, $name.' must be an'.$msg.' integer' );
        }
      }
      else if ( self::hasFlag($type, WS_TYPE_FLOAT) )
      {
        if (
          ($param = filter_var($param, FILTER_VALIDATE_FLOAT)) === false
          or ( isset($opts['options']['min_range']) and $param < $opts['options']['min_range'] )
        ) {
          return new PwgError(WS_ERR_INVALID_PARAM, $name.' must be a'.$msg.' float' );
        }
      }
    }
    
    return null;
  }
  
  static function hasFlag($val, $flag)
  {
    return ($val & $flag) == $flag;
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

    if ( $method == null )
    {
      return new PwgError(WS_ERR_INVALID_METHOD, 'Method name is not valid');
    }
    
    if ( isset($method['options']['post_only']) and $method['options']['post_only'] and !self::isPost() )
    {
      return new PwgError(405, 'This method requires HTTP POST');
    }
    
    if ( isset($method['options']['admin_only']) and $method['options']['admin_only'] and !is_admin() )
    {
      return new PwgError(401, 'Access denied');
    }

    // parameter check and data correction
    $signature = $method['signature'];
    $missing_params = array();
    
    foreach ($signature as $name => $options)
    {
      $flags = $options['flags'];
      
      // parameter not provided in the request
      if ( !array_key_exists($name, $params) )
      {
        if ( !self::hasFlag($flags, WS_PARAM_OPTIONAL) )
        {
          $missing_params[] = $name;
        }
        else if ( array_key_exists('default', $options) )
        {
          $params[$name] = $options['default'];
          if ( self::hasFlag($flags, WS_PARAM_FORCE_ARRAY) )
          {
            self::makeArrayParam($params[$name]);
          }
        }
      }
      // parameter provided but empty
      else if ( $params[$name]==='' and !self::hasFlag($flags, WS_PARAM_OPTIONAL) )
      {
        $missing_params[] = $name;
      }
      // parameter provided - do some basic checks
      else
      {
        $the_param = $params[$name];
        
        if ( is_array($the_param) and !self::hasFlag($flags, WS_PARAM_ACCEPT_ARRAY) )
        {
          return new PwgError(WS_ERR_INVALID_PARAM, $name.' must be scalar' );
        }
        
        if ( self::hasFlag($flags, WS_PARAM_FORCE_ARRAY) )
        {
          self::makeArrayParam($the_param);
        }
        
        if ( $options['type'] > 0 )
        {
          if ( ($ret = self::checkType($the_param, $options['type'], $name)) !== null )
          {
            return $ret;
          }
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
    
    $result = trigger_change('ws_invoke_allowed', true, $methodName, $params);
    
    $is_error = false;
    if ($result instanceof PwgError)
    {
      $is_error = true;
    }

    if (!$is_error)
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
    $methods = array_filter($service->_methods,
      function($m) { return empty($m["options"]["hidden"]) || !$m["options"]["hidden"];} );
    return array('methods' => new PwgNamedArray( array_keys($methods),'method' ) );
  }

  /**
   * WS reflection method implementation: gets information about a given method
   */
  static function ws_getMethodDetails($params, &$service)
  {
    $methodName = $params['methodName'];
    
    if (!$service->hasMethod($methodName))
    {
      return new PwgError(WS_ERR_INVALID_PARAM, 'Requested method does not exist');
    }
    
    $res = array(
      'name' => $methodName,
      'description' => $service->getMethodDescription($methodName),
      'params' => array(),
      'options' => $service->getMethodOptions($methodName),
    );
    
    foreach ($service->getMethodSignature($methodName) as $name => $options)
    {
      $param_data = array(
        'name' => $name,
        'optional' => self::hasFlag($options['flags'], WS_PARAM_OPTIONAL),
        'acceptArray' => self::hasFlag($options['flags'], WS_PARAM_ACCEPT_ARRAY),
        'type' => 'mixed',
        );
      
      if (isset($options['default']))
      {
        $param_data['defaultValue'] = $options['default'];
      }
      if (isset($options['maxValue']))
      {
        $param_data['maxValue'] = $options['maxValue'];
      }
      if (isset($options['info']))
      {
        $param_data['info'] = $options['info'];
      }
      
      if ( self::hasFlag($options['type'], WS_TYPE_BOOL) )
      {
        $param_data['type'] = 'bool';
      }
      else if ( self::hasFlag($options['type'], WS_TYPE_INT) )
      {
        $param_data['type'] = 'int';
      }
      else if ( self::hasFlag($options['type'], WS_TYPE_FLOAT) )
      {
        $param_data['type'] = 'float';
      }
      if ( self::hasFlag($options['type'], WS_TYPE_POSITIVE) )
      {
        $param_data['type'].= ' positive';
      }
      if ( self::hasFlag($options['type'], WS_TYPE_NOTNULL) )
      {
        $param_data['type'].= ' notnull';
      }
      
      $res['params'][] = $param_data;
    }
    return $res;
  }
}
?>
