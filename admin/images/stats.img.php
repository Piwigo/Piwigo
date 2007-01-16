<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-12-04 23:08:35 +0100 (lun, 04 déc 2006) $
// | last modifier : $Author: rub $
// | revision      : $Revision: 1635 $
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
//----------------------------------------------------------- include
define('PHPWG_ROOT_PATH','../../');
define('IN_ADMIN', true);
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once( 'phpBarGraph.php' );

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

//------------------------------------------------ variable definition
$outputFormat = "png";
$legend = $lang['stats_global_graph_title'];
$imageHeight = 256;
$imageWidth = 500;

foreach (array('day', 'month', 'year') as $key)
{
  if (isset($_GET[$key]))
  {
    $page[$key] = (int)$_GET[$key];
  }
}

if (isset($page['day']))
{
  if (!isset($page['month']))
  {
    die('[stats.img.php] month is missing in URL');
  }
}

if (isset($page['month']))
{
  if (!isset($page['year']))
  {
    die('[stats.img.php] year is missing in URL');
  }
}

$query = '
SELECT
  nb_pages AS y,';

$min_x = null;
$max_x = null;

if (isset($page['day']))
{
  $query.= '
  hour AS x
  FROM '.HISTORY_SUMMARY_TABLE.'
  WHERE year = '.$page['year'].'
    AND month = '.$page['month'].'
    AND day = '.$page['day'].'
    AND hour IS NOT NULL
  ORDER BY hour ASC
';

  $min_x = 0;
  $max_x = 23;
}
elseif (isset($page['month']))
{
  $query.= '
  day AS x
  FROM '.HISTORY_SUMMARY_TABLE.'
  WHERE year = '.$page['year'].'
    AND month = '.$page['month'].'
    AND day IS NOT NULL
    AND hour IS NULL
  ORDER BY day ASC
';

  $min_x = 1;
  $max_x = 31;
}
elseif (isset($page['year']))
{
  $query.= '
  month AS x
  FROM '.HISTORY_SUMMARY_TABLE.'
  WHERE year = '.$page['year'].'
    AND month IS NOT NULL
    AND day IS NULL
  ORDER BY month ASC
';

  $min_x = 1;
  $max_x = 12;
}
else
{
  $query.= '
  year AS x
  FROM '.HISTORY_SUMMARY_TABLE.'
  WHERE year IS NOT NULL
    AND month IS NULL
  ORDER BY year ASC
';
}

//------------------------------------------------ Image definition
$image = ImageCreate($imageWidth, $imageHeight);

// Fill it with your favorite background color..
$backgroundColor = ImageColorAllocate($image, 184, 184, 184);
ImageFill($image, 0, 0, $backgroundColor);
$white = ImageColorAllocate($image, 0, 0, 0);

// Interlace the image..
Imageinterlace($image, 1);

// Create a new BarGraph..
$myBarGraph = new PhpBarGraph;

// Set the starting x position
$myBarGraph->SetX(10);

// Set the starting y position
$myBarGraph->SetY(10);

// Set how wide the bargraph will be
$myBarGraph->SetWidth($imageWidth-20);

// Set how tall the bargraph will be
$myBarGraph->SetHeight($imageHeight-20);

// Set this to zero if you don't want to show any. These are the vertical
// bars to help see the values.
// $myBarGraph->SetNumOfValueTicks(3);


// You can try uncommenting these lines below for different looks.
//
// The default is true. Setting this to false will cause phpBarGraph to not
// print the labels of each bar.
$myBarGraph->SetShowLabels(true);

// The default is true. Setting this to false will cause phpBarGraph to not
// print the values of each bar.
$myBarGraph->SetShowValues(false);

// The default is true. Setting this to false will cause phpBarGraph to not
// print the border of each bar.
$myBarGraph->SetBarBorder(true);

// The default is true. Setting this to false will cause phpBarGraph to not
// print each bar as a gradient.
$myBarGraph->SetShowFade(true);

// The default is true. Setting this to false will cause phpBarGraph to not
// print the outside box.
$myBarGraph->SetShowOuterBox(true);

// The default is 10. This changes the space inbetween each bar.
$myBarGraph->SetBarSpacing(5);


// Add Values to the bargraph..
$result = pwg_query($query);
$datas = array();
while ($row = mysql_fetch_array($result))
{
  $datas[$row['x']] = $row['y'];
}

if (!isset($min_x) and !isset($max_x))
{
  $min_x = min(array_keys($datas));
  $max_x = max(array_keys($datas));
}

for ($i = $min_x; $i <= $max_x; $i++)
{
  if (!isset($datas[$i]))
  {
    $datas[$i] = 0;
  }

  $myBarGraph->AddValue($i, $datas[$i]);
}

// Set the colors of the bargraph..
//
// This is the color on the top of every bar.
$myBarGraph->SetStartBarColor("6666ff");

// This is the color on the bottom of every bar. This is not used when
// SetShowFade() is set to false.
$myBarGraph->SetEndBarColor("2222aa");

// This is the color all the lines and text are printed out with.
$myBarGraph->SetLineColor("000000");

// Print the BarGraph to the image..
$myBarGraph->DrawBarGraph($image);
Imagestring($image, 2, 2, $imageHeight-14, $legend, $white);
   
//------------------------------------------------ Image output
if ($outputFormat == "png")
{
  header("Content-type: image/png");
  ImagePNG($image);
}
else if (in_array($outputFormat, array("jpg", "jpeg")))
{
  header("Content-type: image/jpeg");
  Imagejpeg($image);
}
// Destroy the image.
Imagedestroy($image);
?> 