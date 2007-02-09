<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

function get_php_files($path, $to_ignore=array(), $recursive=true )
{
  $files = array();
  if (is_dir($path))
  {
    if ($contents = opendir($path))
    {
      while (($node = readdir($contents)) !== false)
      {
        if ($node != '.' and $node != '..' and $node != '.svn'
            and !in_array($node, $to_ignore) )
        {
          if ( $recursive and is_dir($path.'/'.$node) )
          {
            $files = array_merge($files, get_php_files($path.'/'.$node, $to_ignore));
            
          }
          if ( is_file($path.'/'.$node) )
          {
            $files[] = $path.'/'.$node;
          }
        }
      }
      closedir($contents);
    }
  }
  return $files;
}

$files = array();
$files = array_merge( $files, get_php_files('.', array(), false) );
$files = array_merge( $files, get_php_files('./include') );
$files = array_merge( $files, get_php_files('./admin') );
$files = array_unique($files);

$events = array();
foreach ($files as $file)
{
  $code = file_get_contents($file);
  $code = preg_replace( '#\?'.'>.*<\?php#m', '', $code);
  $code = preg_replace( '#\/\*.*\*\/#m', '', $code);
  $code = preg_replace( '#\/\/.*#', '', $code);
  
  $count = preg_match_all(
    '#[^a-zA-Z_$-]trigger_(action|event)\s*\(\s*([^,)]+)#m',
    $code, $matches
    );

  for ($i=0; $i<$count; $i++)
  {
    $type = $matches[1][$i];
    $name = preg_replace( '#^[\'"]?([^\'"]*)[\'"]?$#', '$1', $matches[2][$i]);
    array_push($events, array($type,$name,$file) );
  }
}

$sort= isset($_GET['sort']) ? $_GET['sort'] : 1;
usort(
  $events,
  create_function( '$a,$b', 'return $a['.$sort.']>$b['.$sort.'];' )
  );

global $template;

$url = get_admin_plugin_menu_link(__FILE__);

$template->assign_vars( array(
  'NB_EVENTS' => count($events),
  'U_SORT0' => add_url_params($url, array('sort'=>0) ),
  'U_SORT1' => add_url_params($url, array('sort'=>1) ),
  'U_SORT2' => add_url_params($url, array('sort'=>2) ),
  ) );

foreach ($events as $e)
{
  $template->assign_block_vars( 'event', array(
    'TYPE' => $e[0],
    'NAME' => $e[1],
    'FILE' => $e[2],
    )
  );
}

$template->set_filenames( array('event_list' => dirname(__FILE__).'/event_list.tpl' ) );
$template->assign_var_from_handle( 'ADMIN_CONTENT', 'event_list');
?>
