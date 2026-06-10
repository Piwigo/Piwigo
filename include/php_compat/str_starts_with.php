<?php
if (!function_exists('str_starts_with'))
{
  function str_starts_with(string $haystack, string $needle): bool
  {
    return strlen($needle) === 0 || strpos($haystack, $needle) === 0;
  }
}
