<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
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

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

function history_tabsheet()
{
  global $page, $link_start;

  // TabSheet
  $tabsheet = new tabsheet();
  $tabsheet->set_id('history');
  $tabsheet->select($page['page']);
  $tabsheet->assign();
}

function history_compare($a, $b)
{
  return strcmp($a['date'].$a['time'], $b['date'].$b['time']);
}

function get_history($data, $search, $types)
{
  if (isset($search['fields']['filename']))
  {
    $query = '
SELECT
    id
  FROM '.IMAGES_TABLE.'
  WHERE file LIKE \''.$search['fields']['filename'].'\'
;';
    $search['image_ids'] = array_from_query($query, 'id');
  }
  
  // echo '<pre>'; print_r($search); echo '</pre>';
  
  $clauses = array();

  if (isset($search['fields']['date-after']))
  {
    array_push(
      $clauses,
      "date >= '".$search['fields']['date-after']."'"
      );
  }

  if (isset($search['fields']['date-before']))
  {
    array_push(
      $clauses,
      "date <= '".$search['fields']['date-before']."'"
      );
  }

  if (isset($search['fields']['types']))
  {
    $local_clauses = array();
    
    foreach ($types as $type) {
      if (in_array($type, $search['fields']['types'])) {
        $clause = 'image_type ';
        if ($type == 'none')
        {
          $clause.= 'IS NULL';
        }
        else
        {
          $clause.= "= '".$type."'";
        }
        
        array_push($local_clauses, $clause);
      }
    }
    
    if (count($local_clauses) > 0)
    {
      array_push(
        $clauses,
        implode(' OR ', $local_clauses)
        );
    }
  }

  if (isset($search['fields']['user'])
      and $search['fields']['user'] != -1)
  {
    array_push(
      $clauses,
      'user_id = '.$search['fields']['user']
      );
  }

  if (isset($search['fields']['image_id']))
  {
    array_push(
      $clauses,
      'image_id = '.$search['fields']['image_id']
      );
  }
  
  if (isset($search['fields']['filename']))
  {
    if (count($search['image_ids']) == 0)
    {
      // a clause that is always false
      array_push($clauses, '1 = 2 ');
    }
    else
    {
      array_push(
        $clauses,
        'image_id IN ('.implode(', ', $search['image_ids']).')'
        );
    }
  }

  if (isset($search['fields']['ip']))
  {
    $clauses[] = 'IP LIKE "'.$search['fields']['ip'].'"';
  }
  
  $clauses = prepend_append_array_items($clauses, '(', ')');

  $where_separator =
    implode(
      "\n    AND ",
      $clauses
      );
  
  $query = '
SELECT
    date,
    time,
    user_id,
    IP,
    section,
    category_id,
    tag_ids,
    image_id,
    image_type
  FROM '.HISTORY_TABLE.'
  WHERE '.$where_separator.'
;';

  // LIMIT '.$conf['nb_logs_page'].' OFFSET '.$page['start'].'

  $result = pwg_query($query);

  while ($row = pwg_db_fetch_assoc($result))
  {
    array_push($data, $row);
  }

  return $data;
}

add_event_handler('get_history', 'get_history', EVENT_HANDLER_PRIORITY_NEUTRAL, 3);
trigger_action('functions_history_included');

?>
