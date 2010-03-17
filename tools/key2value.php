#!/usr/bin/php -qn
<?php
if (isset($_SERVER['argc']) && $_SERVER['argc']<=1) 
{
  help();
}

$language = trim($_SERVER['argv'][1]);
if (!is_dir("language/$language")) 
{
  help();
}

$Files = array('common', 'admin', 'install', 'upgrade');
$exclude_keys = array('user_status_admin', 'user_status_generic', 
		      'user_status_guest', 'user_status_normal',
		      'user_status_webmaster', 'Level 0',
		      'Level 1', 'Level 2', 'Level 4', 'Level 8',
		      'chronology_monthly_calendar', 'chronology_monthly_list',
		      'chronology_weekly_list');

foreach ($Files as $file)
{
  $lang_file = sprintf('language/%s/%s.lang.php', $language, $file);
  $en_file = sprintf('language/en_UK/%s.lang.php', $file);
  include $lang_file;
  $source_lang = $lang;
  unset($lang);
  include $en_file;

  try 
  {
    $fh = fopen($lang_file, 'w+');

    fwrite($fh, copyright());

    if ($file == 'common')
    {
      foreach ($lang_info as $key => $value)
      {
	fwrite($fh, sprintf("\$lang_info['%s'] = \"%s\";\n", 
			    $key, 
			    $value
			    )
	       );		
      }
    }
    fwrite($fh, "\n\n");

    foreach ($lang as $key => $value) 
    {
      if (is_array($value))
      {
	foreach ($value as $k => $v)
	{
	fwrite($fh, sprintf("\$lang['%s'][%s] = \"%s\";\n", 
			    str_replace("'", "\'", trim($key)),
			    trim($k), 
			    str_replace('"', '\"', trim($source_lang[$key][$k]))
			    )
	       );
	}
      }
      elseif (in_array($key, $exclude_keys))
      {
	fwrite($fh, sprintf("\$lang['%s'] = \"%s\";\n", 
			    str_replace("'", "\'", trim($key)), 
			    str_replace('"', '\"', trim($source_lang[$key]))
			    )
	       );	
      }
      else 
      {
	fwrite($fh, sprintf("\$lang['%s'] = \"%s\";\n", 
			    str_replace("'", "\'", trim($value)), 
			    str_replace('"', '\"', trim($source_lang[$key]))
			    )
	       );
      }
    }
    fwrite($fh, '?>');
    fclose($fh);
  } 
  catch (Exception $e)
  {
    print $e->getMessage();
  }
}

function help()
{
  echo "\n";
  echo 'usage : ', basename($_SERVER['argv'][0]), " <LANGUAGE CODE>\n";
  echo "\n";
  exit(1);
}

function copyright()
{
  return 
'<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
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

';
}
?>