<?php
// http://www.php.net/manual/en/function.array-intersect-key.php
// PHP 5 >= 5.1.0RC1
function array_intersect_key()
{
  $args = func_get_args();
  if (count($args) < 2) {
      trigger_error('Wrong parameter count for array_intersect_key()', E_USER_WARNING);
      return;
  }

  // Check arrays
  $array_count = count($args);
  for ($i = 0; $i !== $array_count; $i++) {
      if (!is_array($args[$i])) {
          trigger_error('array_intersect_key() Argument #' . ($i + 1) . ' is not an array', E_USER_WARNING);
          return;
      }
  }

  // Compare entries
  $result = array();
  foreach ($args[0] as $key1 => $value1) {
      for ($i = 1; $i !== $array_count; $i++) {
          foreach ($args[$i] as $key2 => $value2) {
              if ((string) $key1 === (string) $key2) {
                  $result[$key1] = $value1;
              }
          }
      }
  }

  return $result;
}
?>