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

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

$script = script_basename();

if (($script != 'install' and $script != 'upgrade')
  or version_compare(PHP_VERSION, REQUIRED_PHP_VERSION, '>='))
{
  die('Nothing to do here...');
}

function initPHP5()
{
  include(PHPWG_ROOT_PATH.'install/hosting.php');
  $htaccess = PHPWG_ROOT_PATH.'.htaccess';
  
  if ((file_exists($htaccess) and (!is_readable($htaccess) or !is_writable($htaccess)))
    or !($my_hostname = @gethostbyaddr($_SERVER['SERVER_ADDR'])))
  {
    return false;
  }

  foreach ($hosting as $hostname => $rule)
  {
    if (preg_match('!'.preg_quote($hostname).'$!',$my_hostname))
    {
      if (false !== ($fh = @fopen($htaccess,"ab")))
      {
        fwrite($fh,"\n".$rule);
        fclose($fh);
        return true;
      }
    }
  }
  return false;
}

function openPage()
{
  global $script;

  $title = 'Piwigo '.PHPWG_VERSION.' - '.l10n(ucwords($script));

  header('Content-Type: text/html; charset=UTF-8');

  echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-script-type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="shortcut icon" type="image/x-icon" href="themes/default/icon/favicon.ico">
<link rel="stylesheet" type="text/css" href="admin/themes/roma/default-colors.css">
<link rel="stylesheet" type="text/css" href="admin/themes/roma/theme.css">
<style type="text/css">
body {
  background:url("admin/themes/roma/images/bottom-left-bg.jpg") no-repeat fixed left bottom #111111;
}

.content {
 background:url("admin/themes/roma/images/fillet.png") repeat-x scroll left top #222222;
 width: 800px;
 min-height: 0px !important;
 margin: auto;
 text-align: left;
 padding: 25px;
}

#headbranch  {
  background:url("admin/themes/roma/images/top-left-bg.jpg") no-repeat scroll left top transparent;
}

#theHeader {
  display: block;
  background:url("admin/themes/roma/images/piwigo_logo_sombre_214x100.png") no-repeat scroll 245px top transparent;
}

.content h2 {
  display:block;
  font-size:28px;
  height:104px;
  width:54%;
  color:#666666;
  letter-spacing:-1px;
  margin:0 30px 3px 20px;
  overflow:hidden;
  position:absolute;
  right:0;
  text-align:right;
  top:0;
  width:770px;
  text-align:right;
  text-transform:none; 
}

table { margin: 0 0 15px 0; }
td {  padding: 3px 10px; }
h1 { text-align: left; }
</style>
<title>'.$title.'</title>
</head>

<body>
<div id="headbranch"></div>
<div id="the_page">
<div id="theHeader"></div>
<div id="content" class="content">

<h2>'.$title.'</h2>';
}

function closePage()
{
  echo '
</div>
<div style="text-align: center">'.sprintf(l10n('Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'), PHPWG_URL.'/forum').'</div>
</div>
</body>
</html>';
}

if (isset($_GET['setphp5']))
{
  // Try to configure php5
  if (initPHP5())
  {
    header('Location: '.$script.'.php?language='.$language);
  }
  else
  {
    openPage();
    echo '
<h1>'.l10n('Sorry!').'</h1>
<p>
'.l10n('Piwigo was not able to configure PHP 5.').'<br>
'.l10n("You may referer to your hosting provider's support and see how you could switch to PHP 5 by yourself.").'<br>
'.l10n('Hope to see you back soon.').'
</p>';
    closePage();
  }
}
else
{
  openPage();
  echo '
  <table>
  <tr>
    <td>'.l10n('Language').'</td>
    <td>
      <select name="language" onchange="document.location = \''.$script.'.php?language=\'+this.options[this.selectedIndex].value;">';
  foreach ($languages->fs_languages as $code => $fs_language)
  {
    echo '
      <option label="'.$fs_language['name'].'" value="'.$code.'" '.($code == $language ? 'selected="selected"' : '') .'>'.$fs_language['name'].'</option>';
  }
  echo '
      </select>
    </td>
  </tr>
</table>

<h1>'.l10n('PHP 5 is required').'</h1>
<p>
'.sprintf(l10n('It appears your webhost is currently running PHP %s.'), PHP_VERSION).'<br>
'.l10n('Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.').'<br>
'.l10n('Note you can change your configuration by yourself and restart Piwigo after that.').'<br>
</p>
<p style="text-align: center;"><br>
<input type="button" value="'.l10n('Try to configure PHP 5').'" onClick="document.location = \''.$script.'.php?language='.$language.'&amp;setphp5=\';">
</p>';
  closePage();
}

exit();
?>