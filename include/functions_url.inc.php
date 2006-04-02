<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
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


/**
 * returns a prefix for each url link on displayed page
 * @return string
 */
function get_root_url()
{
  global $page;
  if ( isset($page['root_path']) )
  {
    return $page['root_path'];
  }
  return PHPWG_ROOT_PATH;
}

/**
 * adds one or more _GET style parameters to an url
 * example: add_url_params('/x', array('a'=>'b')) returns /x?a=b
 * add_url_params('/x?cat_id=10', array('a'=>'b')) returns /x?cat_id=10&amp;a=b
 * @param string url
 * @param array params
 * @return string
 */
function add_url_params($url, $params)
{
  if ( !empty($params) )
  {
    assert( is_array($params) );
    $is_first = true;
    foreach($params as $param=>$val)
    {
      if ($is_first)
      {
        $is_first = false;
        $url .= ( strstr($url, '?')===false ) ? '?' :'&amp;';
      }
      else
      {
        $url .= '&amp;';
      }
      $url .= $param;
      if (isset($val))
      {
        $url .= '='.$val;
      }
    }
  }
  return $url;
}

/**
 * build an index URL for a specific section
 *
 * @param array
 * @return string
 */
function make_index_URL($params = array())
{
  global $conf;
  $url = get_root_url().'index';
  if ($conf['php_extension_in_urls'])
  {
    $url .= '.php';
  }
  if ($conf['question_mark_in_urls'])
  {
    $url .= '?';
  }
  $url.= make_section_in_URL($params);
  $url = add_well_known_params_in_url($url, $params);
  return $url;
}

/**
 * build an index URL with current page parameters, but with redefinitions
 * and removes.
 *
 * duplicate_index_URL(array('category' => 12), array('start')) will create
 * an index URL on the current section (categories), but on a redefined
 * category and without the start URL parameter.
 *
 * @param array redefined keys
 * @param array removed keys
 * @return string
 */
function duplicate_index_URL($redefined = array(), $removed = array())
{
  return make_index_URL(
    params_for_duplication($redefined, $removed)
    );
}

/**
 * returns $page global array with key redefined and key removed
 *
 * @param array redefined keys
 * @param array removed keys
 * @return array
 */
function params_for_duplication($redefined, $removed)
{
  global $page;

  if (count($removed) > 0)
  {
    $params = array();

    foreach ($page as $page_item_key => $page_item_value)
    {
      if (!in_array($page_item_key, $removed))
      {
        $params[$page_item_key] = $page_item_value;
      }
    }
  }
  else
  {
    $params = $page;
  }

  foreach ($redefined as $redefined_param => $redefined_value)
  {
    $params[$redefined_param] = $redefined_value;
  }

  return $params;
}

/**
 * create a picture URL with current page parameters, but with redefinitions
 * and removes. See duplicate_index_URL.
 *
 * @param array redefined keys
 * @param array removed keys
 * @return string
 */
function duplicate_picture_URL($redefined = array(), $removed = array())
{
  return make_picture_URL(
    params_for_duplication($redefined, $removed)
    );
}

/**
 * create a picture URL on a specific section for a specific picture
 *
 * @param array
 * @return string
 */
function make_picture_URL($params)
{
  global $conf;
  if (!isset($params['image_id']))
  {
    die('make_picture_URL: image_id is a required parameter');
  }

  $url = get_root_url().'picture';
  if ($conf['php_extension_in_urls'])
  {
    $url .= '.php';
  }
  if ($conf['question_mark_in_urls'])
  {
    $url .= '?';
  }
  $url.= '/';
  switch ( $conf['picture_url_style'] )
  {
    case 'id-file':
      $url .= $params['image_id'];
      if ( isset($params['image_file']) )
      {
        $url .= '-'.get_filename_wo_extension($params['image_file']);
      }
      break;
    case 'file':
      if ( isset($params['image_file'])
           and !is_numeric($params['image_file']) )
      {
        $url .= get_filename_wo_extension($params['image_file']);
      }
      else
      {
        $url .= $params['image_id'];
      }
      break;
    default:
      $url .= $params['image_id'];
  }
  $url .= make_section_in_URL($params);
  $url = add_well_known_params_in_url($url, $params);
  return $url;
}

/**
 *adds to the url the chronology and start parameters
*/
function add_well_known_params_in_url($url, $params)
{
  if ( isset($params['chronology_field']) )
  {
    $url .= '/'. $params['chronology_field'];
    $url .= '-'. $params['chronology_style'];
    if ( isset($params['chronology_view']) )
    {
      $url .= '-'. $params['chronology_view'];
    }
    if ( !empty($params['chronology_date']) )
    {
      $url .= '-'. implode('-', $params['chronology_date'] );
    }
  }

  if (isset($params['start']) and $params['start'] > 0)
  {
    $url.= '/start-'.$params['start'];
  }
  return $url;
}

/**
 * return the section token of an index or picture URL.
 *
 * Depending on section, other parameters are required (see function code
 * for details)
 *
 * @param array
 * @return string
 */
function make_section_in_URL($params)
{
  $section_string = '';

  $section_of = array(
    'category' => 'categories',
    'tags'     => 'tags',
    'list'     => 'list',
    'search'   => 'search',
    );

  foreach ($section_of as $param => $section)
  {
    if (isset($params[$param]))
    {
      $params['section'] = $section;
    }
  }

  if (!isset($params['section']))
  {
    $params['section'] = 'categories';
  }

  switch($params['section'])
  {
    case 'categories' :
    {
      if (!isset($params['category']))
      {
        $section_string.= '/categories';
      }
      else
      {
        $section_string.= '/category/'.$params['category'];
      }

      break;
    }
    case 'tags' :
    {
      if (!isset($params['tags']) or count($params['tags']) == 0)
      {
        die('make_section_in_URL: require at least one tag');
      }

      $section_string.= '/tags';

      foreach ($params['tags'] as $tag)
      {
        $section_string.= '/'.$tag['id'];
        
        if (isset($tag['url_name']))
        {
          $section_string.= '-'.$tag['url_name'];
        }
      }

      break;
    }
    case 'search' :
    {
      if (!isset($params['search']))
      {
        die('make_section_in_URL: require a search identifier');
      }

      $section_string.= '/search/'.$params['search'];

      break;
    }
    case 'list' :
    {
      if (!isset($params['list']))
      {
        die('make_section_in_URL: require a list of items');
      }

      $section_string.= '/list/'.implode(',', $params['list']);

      break;
    }
    default :
    {
      $section_string.= '/'.$params['section'];
    }
  }

  return $section_string;
}
?>