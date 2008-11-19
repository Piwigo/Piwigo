<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 Piwigo team    http://phpwebgallery.net |
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

add_event_handler('loc_end_page_header', 'set_admin_advice_add_css' );

// Add a XHTML tag in HEAD section
function set_admin_advice_add_css()
{
  global $template, $page;
  if ( isset($page['body_id']) and $page['body_id']=='theAdminPage'
    and $page['page'] == 'intro'
    )
  {// This Plugin works only on the Admin page
    $template->append(
      'head_elements',
      '<link rel="stylesheet" type="text/css" '
                    . 'href="'.PHPWG_PLUGINS_PATH.'admin_advices/default-layout.css">'
     );
    add_event_handler('loc_begin_page_tail', 'set_admin_advice' );
  }
}

// Build an advice on the Admin Intro page
function set_admin_advice()
{
  global $page, $user, $template, $conf, $prefixeTable, $lang;
  $my_path = dirname(__FILE__).'/';

//  Include language advices
  foreach ($conf as $key => $value)
  {
    if ( is_string($value) )
    {
      $bool = ($value == 'false') ? false : $value;
      $bool = ($value == 'true') ? true : $bool;
      $conf[$key] = $bool;
    }
  }
  $adv = array();
  ini_set('error_reporting', E_ALL);
  ini_set('display_errors', true);

  include_once( $my_path."adv_set.php" );

  $cases = range(0,count($lang['Adv_case'])-1);
  srand ((double) microtime() * 10000000);
  shuffle($cases);


  $cond = false;  
  foreach ($cases as $id)
  {
    if (!isset($adv['c'][$id])) $adv['c'][$id] = true;
    if (!isset($adv['n'][$id])) $adv['c'][$id] = false;
    if (!isset($lang['Adv_case'][$id])) $adv['c'][$id] = false;
    $cond = $adv['c'][$id];
    if ($cond) break;
  }
  $confk = $adv['n'][$id];
  if (substr($confk,0,2) == '**') $confk = substr($confk,2);
   else $confk = '$conf[' . "'$confk']";
  $advice_text = (isset($adv['v'][$id])) ? $adv['v'][$id] : '';
  $more = $lang['Adv_case'][$id];

  $template->set_filenames(array(
    'admin_advice' => $my_path.'admin_advices.tpl')
    );

// Mysql status
  $result = pwg_query('SHOW TABLE STATUS ;');
  $pwgspacef = $spacef = $pwgsize = $size = 0; 
  $len = strlen($prefixeTable);
  $check = array();
  while ($row = mysql_fetch_array($result))
  {
    $size += ($row['Data_length'] + $row['Index_length']);
    $spacef += $row['Data_free'];
    if ( substr( $row['Name'], 0, $len ) == $prefixeTable ) { 
      $pwgsize += ($row['Data_length'] + $row['Index_length']);
      $pwgspacef += $row['Data_free'];
      $check[] = (string) $row['Check_time'];
    }
  }
  $size .= ' bytes';
  $pwgsize .= ' bytes';
  $spacef .= ' bytes';
  $pwgspacef .= ' bytes';
  if ($size > 1024) $size = round($size / 1024, 1) . ' Kb';
  if ($size > 1024) $size = round($size / 1024, 1) . ' Mb';
  if ($pwgsize > 1024) $pwgsize = round($pwgsize / 1024, 1) . ' Kb';
  if ($pwgsize > 1024) $pwgsize = round($pwgsize / 1024, 1) . ' Mb';
  if ($spacef > 1024) $spacef = round($spacef / 1024, 1) . ' Kb';
  if ($spacef > 1024) $spacef = round($spacef / 1024, 1) . ' Mb';
  if ($pwgspacef > 1024) $pwgspacef = round($pwgspacef / 1024, 1) . ' Kb';
  if ($pwgspacef > 1024) $pwgspacef = round($pwgspacef / 1024, 1) . ' Mb';
  $check = array_flip(array_flip($check));
  rsort($check);
  $end = end($check);
  $prev = prev($check);
  $first = $check[0];
  $checkon = '';
  if (empty($end)) $end = $prev;
  if ($end == $first) $checkon .= 'Last table check on: %s';
  else $checkon .= 'Most recent table check on: %s - oldest: %s';
  $checkon = sprintf($checkon, $first, $end);
  $template->assign(
    array(
      'prefixTable' => $prefixeTable,
      'pwgsize' => $pwgsize,
      'size' => $size,
      'checked_tables' => $checkon,
      'pwgspacef' => $pwgspacef,
      'spacef' => $spacef,
      'U_maintenance' => get_root_url()
        . 'admin.php?page=maintenance&amp;action=database',
      )
    );

//  If there is an advice
  if ( $cond )
  {

// Random Thumbnail
    $query = '
SELECT *
FROM '.IMAGES_TABLE.'
ORDER BY RAND(NOW())
LIMIT 0, 1
;';
    $result = pwg_query($query);
    $row = mysql_fetch_assoc($result);
    if ( is_array($row) )
    {
      $url_modify = get_root_url().'admin.php?page=picture_modify'
                  .'&amp;image_id='.$row['id'];
      $query = '
SELECT * FROM '.IMAGE_TAG_TABLE.'
WHERE image_id =  ' . $row['id'] .'
;';
      $tag_count = mysql_num_rows(pwg_query($query));
      $template->assign('thumbnail',
         array(
           'IMAGE'              => get_thumbnail_url($row),
           'IMAGE_ALT'          => $row['file'],
           'IMAGE_TITLE'        => $row['name'],
           'METADATA'           => (empty($row['date_metadata_update'])) ?
                                   'un' : '',
           'NAME'               => (empty($row['name'])) ?
                                   'un' : '',
           'COMMENT'            => (empty($row['comment'])) ?
                                   'un' : '',
           'AUTHOR'             => (empty($row['author'])) ?
                                   'un' : '',
           'CREATE_DATE'        => (empty($row['date_creation'])) ?
                                   'un' : '',
           'TAGS'               => ($tag_count == 0) ?
                                   'un' : '',
           'NUM_TAGS'           => $tag_count,
           'U_MODIFY'           => $url_modify,
         )
       );
    }
    //$advice_text = array_shift($adv);
    $template->assign(
      array(
        'ADVICE_ABOUT' => $confk,
        'ADVICE_TEXT'  => $advice_text,
         )
      );
  $template->assign('More', $more );
  $template->pparse('admin_advice');
  }  
}
?>
