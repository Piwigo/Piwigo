<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
check_status(ACCESS_ADMINISTRATOR);

$sections = explode('/', $_GET['section'] );
for ($i=0; $i<count($sections); $i++)
{
  if (empty($sections[$i]))
  {
    unset($sections[$i]);
    $i--;
    continue;
  }

  if ($sections[$i] == '..' or !preg_match('/^[a-zA-Z0-9_\.-]+$/', $sections[$i]))
  {
    die('invalid section token ['.htmlentities($sections[$i]).']');
  }
}

if (count($sections)<2)
{
  die('Invalid plugin URL');
}

$plugin_id = $sections[0];

if (!preg_match('/^[\w-]+$/', $plugin_id))
{
  die('Invalid plugin identifier');
}

if ( !isset($pwg_loaded_plugins[$plugin_id]) )
{
  die('Invalid URL - plugin '.$plugin_id.' not active');
}

$filename = PHPWG_PLUGINS_PATH.implode('/', $sections);
if (is_file($filename))
{
  include_once($filename);
}
else
{
  die('Missing file '.htmlentities($filename));
}
?>