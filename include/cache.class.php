<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
  Provides a persistent cache mechanism across multiple page loads/sessions etc...
*/
abstract class PersistentCache
{
  var $default_lifetime = 86400;
  protected $instance_key = PHPWG_VERSION;

  /**
  @return a key that can be safely be used with get/set methods
  */
  function make_key($key)
  {
    if ( is_array($key) )
    {
      $key = implode('&', $key);
    }
    $key .= $this->instance_key;
    return md5($key);
  }

  /**
  Searches for a key in the persistent cache and fills corresponding value.
  @param string $key
  @param out mixed $value
  @return false if the $key is not found in cache ($value is not modified in this case)
  */
  abstract function get($key, &$value);

  /**
  Sets a key/value pair in the persistent cache.
  @param string $key - it should be the return value of make_key function
  @param mixed $value
  @param int $lifetime
  @return false on error
  */
  abstract function set($key, $value, $lifetime=null);

  /**
  Purge the persistent cache.
  @param boolean $all - if false only expired items will be purged
  */
  abstract function purge($all);
}


/**
  Implementation of a persistent cache using files.
*/
class PersistentFileCache extends PersistentCache
{
  private $dir;

  function __construct()
  {
    global $conf;
    $this->dir = PHPWG_ROOT_PATH.$conf['data_location'].'cache/';
  }

  function get($key, &$value)
  {
    $loaded = @file_get_contents($this->dir.$key.'.cache');
    if ($loaded !== false && ($loaded=unserialize($loaded)) !== false)
    {
      if ($loaded['expire'] > time())
      {
        $value = $loaded['data'];
        return true;
      }
    }
    return false;
  }

  function set($key, $value, $lifetime=null)
  {
    if ($lifetime === null)
    {
      $lifetime = $this->default_lifetime;
    }

    if (rand() % 97 == 0)
    {
      $this->purge(false);
    }

    $serialized = serialize( array(
        'expire' => time() + $lifetime,
        'data' => $value
      ));

    if (false === @file_put_contents($this->dir.$key.'.cache', $serialized))
    {
      mkgetdir($this->dir, MKGETDIR_DEFAULT&~MKGETDIR_DIE_ON_ERROR);
      if (false === @file_put_contents($this->dir.$key.'.cache', $serialized))
      {
        return false;
      }
    }
    return true;
  }

  function purge($all)
  {
    $files = glob($this->dir.'*.cache');
    if (empty($files))
    {
      return;
    }

    $limit = time() - $this->default_lifetime;
    foreach ($files as $file)
    {
      if ($all || @filemtime($file) < $limit)
        @unlink($file);
    }
  }

}

?>