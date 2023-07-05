<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

define('PHPWG_ROOT_PATH', '../');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
include_once( PHPWG_ROOT_PATH.'tools/language/translation_validated.inc.php' );
$languages = array_keys(get_languages());
sort($languages);

$page['ref_compare'] = 'en_UK';
$page['ref_default_values'] = 'en_UK';

if (!isset($_GET['lang']))
{
  echo '<a href="?lang=all">All languages</a><br><br>';
  echo '<ul>';
  foreach ($languages as $language)
  {
    if ($page['ref_compare'] == $language)
    {
      continue;
    }
    echo '<li><a href="?lang='.$language.'">'.$language.'</a></li>';
  }
  echo '</ul>';
  exit();
}
else if (in_array($_GET['lang'], $languages))
{
  $languages = array($_GET['lang']);
}

$file_list = array('common', 'admin', 'install', 'upgrade');

$metalang = array();

// preload reference languages
$metalang[ $page['ref_compare'] ] = load_metalang($page['ref_compare'], $file_list);
$metalang[ $page['ref_default_values'] ] = load_metalang($page['ref_default_values'], $file_list);

foreach ($languages as $language)
{
  if (in_array($language, array($page['ref_compare'], $page['ref_default_values'])))
  {
    continue;
  }
  echo '<h2>'.$language.'</h2>';
  $metalang[$language] = load_metalang($language, $file_list);

  foreach ($file_list as $file)
  {
    if (isset($metalang[ $language ][$file]))
    {
      $missing_keys = array_diff(
        array_keys($metalang[ $page['ref_compare'] ][$file]),
        array_keys($metalang[ $language ][$file])
        );

      $output_missing = '';
      foreach ($missing_keys as $key)
      {
        $output_missing.= get_line_to_translate($file, $key);
      }

      // strings not "really" translated?
      $output_duplicated = '';
      $output_lost = '';
      foreach (array_keys($metalang[$language][$file]) as $key)
      {
        $exceptions = array('Level 0');
        if (in_array($key, $exceptions))
        {
          continue;
        }

        if (isset($validated_keys[$language]) and in_array($key, $validated_keys[$language]))
        {
          continue;
        }
        
        $local_value = $metalang[$language][$file][$key];
        if (!isset($metalang[ $page['ref_default_values'] ][$file][$key]))
        {
          $output_lost.= '#'.$key.'# does not exist in the reference language'."\n";
        }
        else
        {
          $ref_value = $metalang[ $page['ref_default_values'] ][$file][$key];
          if ($local_value == $ref_value)
          {
            $output_duplicated.= get_line_to_translate($file, $key);
          }
        }
      }

      echo '<h3>'.$file.'.lang.php</h3>';
      
      if ('' != $output_missing or '' != $output_duplicated)
      {
        $output = '';
        if ('' != $output_missing)
        {
          $output.= "// missing translations\n".$output_missing;
        }
        if ('' != $output_duplicated)
        {
          $output.= "\n// untranslated yet\n".$output_duplicated;
        }
        echo '<textarea style="width:100%;height:250px;">'.$output.'</textarea>';
      }

      if ('' != $output_lost)
      {
        echo '<pre>'.$output_lost.'</pre>';
      }
    }
    else
    {
      echo '<h3>'.$file.'.lang.php is missing</h3>';
    }
  }
}

function load_metalang($language, $file_list)
{
  global $lang, $user;
  
  $metalang = array();
  foreach ($file_list as $file)
  {
    $lang = array();
    $user['language'] = $language;
    if (load_language($file.'.lang', '', array('language'=>$language, 'no_fallback'=>true)))
    {
      $metalang[$file] = $lang;
    }
  }
  return $metalang;
}

function get_line_to_translate($file, $key)
{
  global $metalang, $page;
  
  $print_key = str_replace("'", '\\\'', $key);
  $print_value = str_replace("'", '\\\'', $metalang[ $page['ref_default_values'] ][$file][$key]);
  return '$'."lang['".$print_key."'] = '".$print_value."';\n";
}
?>