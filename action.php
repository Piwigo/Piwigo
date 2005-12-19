<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
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

function force_download ($filename)
{
//TODO : messages in "lang"
  $filename = realpath($filename);

  $file_extension = strtolower(substr(strrchr($filename,"."),1));

  switch ($file_extension) {
      case "jpe": case "jpeg":
      case "jpg": $ctype="image/jpg"; break;
      case "png": $ctype="image/png"; break;
      case "gif": $ctype="image/gif"; break;
      case "pdf": $ctype="application/pdf"; break;
      case "zip": $ctype="application/zip"; break;
      case "php": 
        // never allow download of php scripts to protect our conf files
        die('Hacking attempt!'); break;
      default: $ctype="application/octet-stream";
  }

  if (!file_exists($filename)) {
      die("NO FILE HERE");
  }

  header("Pragma: public");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Cache-Control: private",false);
  header("Content-Type: $ctype");
  header("Content-Disposition: attachment; filename=\""
         .basename($filename)."\";");
  header("Content-Transfer-Encoding: binary");
  header("Content-Length: ".@filesize($filename));
  set_time_limit(0);
  @readfile("$filename") or die("File not found.");
}

//--------------------------------------------------------- download big picture
if ( isset( $_GET['dwn'] ) )
{
//TODO : verify the path begins with './gallerie' and doesn't contains any '..'
// in order to avoid hacking atempts
  force_download($_GET['dwn']);
}

?>
