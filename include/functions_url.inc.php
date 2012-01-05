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


/**
 * returns a prefix for each url link on displayed page
 * and return an empty string for current path
 * @return string
 */
function get_root_url()
{
  global $page;
  if ( isset($page['root_path']) )
  {
    $root_url = $page['root_path'];
  }
  else
  {// TODO - add HERE the possibility to call PWG functions from external scripts
    $root_url = PHPWG_ROOT_PATH;
  }
  if ( strncmp($root_url, './', 2) != 0 )
  {
    return $root_url;
  }
  else
  {
    return (string)substr($root_url, 2);
  }
}

/**
 * returns the absolute url to the root of PWG
 * @param boolean with_scheme if false - does not add http://toto.com
 */
function get_absolute_root_url($with_scheme=true)
{
  // TODO - add HERE the possibility to call PWG functions from external scripts
  $url = '';
  if ($with_scheme)
  {
    if (isset($_SERVER['HTTPS']) &&
	((strtolower($_SERVER['HTTPS']) == 'on') or ($_SERVER['HTTPS'] == 1)))
    {
      $url .= 'https://';
    }
    else
    {
      $url .= 'http://';
    }
    $url .= $_SERVER['HTTP_HOST'];
    if ($_SERVER['SERVER_PORT'] != 80)
    {
      $url_port = ':'.$_SERVER['SERVER_PORT'];
      if (strrchr($url, ':') != $url_port)
      {
        $url .= $url_port;
      }
    }
  }
  $url .= cookie_path();
  return $url;
}

/**
 * adds one or more _GET style parameters to an url
 * example: add_url_params('/x', array('a'=>'b')) returns /x?a=b
 * add_url_params('/x?cat_id=10', array('a'=>'b')) returns /x?cat_id=10&amp;a=b
 * @param string url
 * @param array params
 * @return string
 */
function add_url_params($url, $params, $arg_separator='&amp;' )
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
        $url .= ( strpos($url, '?')===false ) ? '?' : $arg_separator;
      }
      else
      {
        $url .= $arg_separator;
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
function make_index_url($params = array())
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

  $url_before_params = $url;
  
  $url.= make_section_in_url($params);
  $url = add_well_known_params_in_url($url, $params);

  if ($url == $url_before_params)
  {
    $url = get_absolute_root_url();
  }
  
  return $url;
}

/**
 * build an index URL with current page parameters, but with redefinitions
 * and removes.
 *
 * duplicate_index_url( array(
 *   'category' => array('id'=>12, 'name'=>'toto'),
 *   array('start')
 * ) will create an index URL on the current section (categories), but on
 * a redefined category and without the start URL parameter.
 *
 * @param array redefined keys
 * @param array removed keys
 * @return string
 */
function duplicate_index_url($redefined = array(), $removed = array())
{
  return make_index_url(
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

  $params = $page;

  foreach ($removed as $param_key)
  {
    unset($params[$param_key]);
  }

  foreach ($redefined as $redefined_param => $redefined_value)
  {
    $params[$redefined_param] = $redefined_value;
  }

  return $params;
}

/**
 * create a picture URL with current page parameters, but with redefinitions
 * and removes. See duplicate_index_url.
 *
 * @param array redefined keys
 * @param array removed keys
 * @return string
 */
function duplicate_picture_url($redefined = array(), $removed = array())
{
  return make_picture_url(
    params_for_duplication($redefined, $removed)
    );
}

/**
 * create a picture URL on a specific section for a specific picture
 *
 * @param array
 * @return string
 */
function make_picture_url($params)
{
  global $conf;

  isset($params['image_id']) or fatal_error('make_picture_url: image_id is a required parameter');

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
      if ( isset($params['image_file']) )
      {
        $fname_wo_ext = get_filename_wo_extension($params['image_file']);
        if ( ord($fname_wo_ext)>ord('9') or !preg_match('/^\d+(-|$)/', $fname_wo_ext) )
        {
          $url .= $fname_wo_ext;
          break;
        }
      }
    default:
      $url .= $params['image_id'];
  }
  if ( !isset($params['category'] ) )
  {// make urls shorter ...
    unset( $params['flat'] );
  }
  $url .= make_section_in_url($params);
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

  if (isset($params['flat']))
  {
    $url.= '/flat';
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
function make_section_in_url($params)
{
  global $conf;
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
    $params['section'] = 'none';
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
        is_array($params['category']) or trigger_error(
            'make_section_in_url wrong type for category', E_USER_WARNING
            );
        is_numeric($params['category']['id']) or trigger_error(
            'make_section_in_url category id not numeric', E_USER_WARNING
            );
        isset($params['category']['name']) or trigger_error(
            'make_section_in_url category name not set', E_USER_WARNING
            );

        array_key_exists('permalink', $params['category']) or trigger_error(
            'make_section_in_url category permalink not set', E_USER_WARNING
            );

        $section_string.= '/category/';
        if ( empty($params['category']['permalink']) )
        {
          $section_string.= $params['category']['id'];
          if ( $conf['category_url_style']=='id-name' )
          {
            $section_string.= '-'.str2url($params['category']['name']);
          }
        }
        else
        {
          $section_string.= $params['category']['permalink'];
        }
      }

      break;
    }
    case 'tags' :
    {
      if (!isset($params['tags']) or count($params['tags']) == 0)
      {
        fatal_error('make_section_in_url: require at least one tag');
      }

      $section_string.= '/tags';

      foreach ($params['tags'] as $tag)
      {
        switch ( $conf['tag_url_style'] )
        {
          case 'id':
            $section_string.= '/'.$tag['id'];
            break;
          case 'tag':
            if (isset($tag['url_name']) and !is_numeric($tag['url_name']) )
            {
              $section_string.= '/'.$tag['url_name'];
              break;
            }
          default:
            $section_string.= '/'.$tag['id'];
            if (isset($tag['url_name']))
            {
              $section_string.= '-'.$tag['url_name'];
            }
        }
      }

      break;
    }
    case 'search' :
    {
      isset($params['search']) or fatal_error('make_section_in_url: require a search identifier');
      $section_string.= '/search/'.$params['search'];
      break;
    }
    case 'list' :
    {
      isset($params['list']) or fatal_error('make_section_in_url: require a list of items');
      $section_string.= '/list/'.implode(',', $params['list']);
      break;
    }
    case 'none' :
    {
      break;
    }
    default :
    {
      $section_string.= '/'.$params['section'];
    }
  }

  return $section_string;
}

/**
 * the reverse of make_section_in_url
 * returns the 'section' (categories/tags/...) and the data associated with it
 *
 * Depending on section, other parameters are returned (category/tags/list/...)
 *
 * @param array of url tokens to parse
 * @param int the index in the array of url tokens; in/out
 * @return array
 */
function parse_section_url( $tokens, &$next_token)
{
  $page=array();
  if (strncmp(@$tokens[$next_token], 'categor', 7)==0 )
  {
    $page['section'] = 'categories';
    $next_token++;

    if (isset($tokens[$next_token]) )
    {
      if (preg_match('/^(\d+)(?:-(.+))?$/', $tokens[$next_token], $matches))
      {
        if ( isset($matches[2]) )
          $page['hit_by']['cat_url_name'] = $matches[2];
        $page['category'] = $matches[1];
        $next_token++;
      }
      else
      {// try a permalink
        $maybe_permalinks = array();
        $current_token = $next_token;
        while ( isset($tokens[$current_token])
            and strpos($tokens[$current_token], 'created-')!==0
            and strpos($tokens[$current_token], 'posted-')!==0
            and strpos($tokens[$next_token], 'start-')!==0
            and $tokens[$current_token] != 'flat')
        {
          if (empty($maybe_permalinks))
          {
            array_push($maybe_permalinks, $tokens[$current_token]);
          }
          else
          {
            array_push($maybe_permalinks,
                $maybe_permalinks[count($maybe_permalinks)-1]
                . '/' . $tokens[$current_token]
              );
          }
          $current_token++;
        }

        if ( count($maybe_permalinks) )
        {
          $cat_id = get_cat_id_from_permalinks($maybe_permalinks, $perma_index);
          if ( isset($cat_id) )
          {
            $next_token += $perma_index+1;
            $page['category'] = $cat_id;
            $page['hit_by']['cat_permalink'] = $maybe_permalinks[$perma_index];
          }
          else
          {
            page_not_found('Permalink for album not found');
          }
        }
      }
    }

    if (isset($page['category']))
    {
      $result = get_cat_info($page['category']);
      if (empty($result))
      {
        page_not_found('Requested category does not exist' );
      }
      $page['category']=$result;
    }
  }
  elseif ( 'tags' == @$tokens[$next_token] )
  {
    global $conf;

    $page['section'] = 'tags';
    $page['tags'] = array();

    $next_token++;
    $i = $next_token;

    $requested_tag_ids = array();
    $requested_tag_url_names = array();

    while (isset($tokens[$i]))
    {
      if (strpos($tokens[$i], 'created-')===0
           or strpos($tokens[$i], 'posted-')===0
           or strpos($tokens[$i], 'start-')===0 )
        break;

      if ( $conf['tag_url_style'] != 'tag' and preg_match('/^(\d+)(?:-(.*)|)$/', $tokens[$i], $matches) )
      {
        array_push($requested_tag_ids, $matches[1]);
      }
      else
      {
        array_push($requested_tag_url_names, $tokens[$i]);
      }
      $i++;
    }
    $next_token = $i;

    if ( empty($requested_tag_ids) && empty($requested_tag_url_names) )
    {
      bad_request('at least one tag required');
    }

    $page['tags'] = find_tags($requested_tag_ids, $requested_tag_url_names);
    if ( empty($page['tags']) )
    {
      page_not_found('Requested tag does not exist', get_root_url().'tags.php' );
    }
  }
  elseif ( 'favorites' == @$tokens[$next_token] )
  {
    $page['section'] = 'favorites';
    $next_token++;
  }
  elseif ('most_visited' == @$tokens[$next_token])
  {
    $page['section'] = 'most_visited';
    $next_token++;
  }
  elseif ('best_rated' == @$tokens[$next_token])
  {
    $page['section'] = 'best_rated';
    $next_token++;
  }
  elseif ('recent_pics' == @$tokens[$next_token])
  {
    $page['section'] = 'recent_pics';
    $next_token++;
  }
  elseif ('recent_cats' == @$tokens[$next_token])
  {
    $page['section'] = 'recent_cats';
    $next_token++;
  }
  elseif ('search' == @$tokens[$next_token])
  {
    $page['section'] = 'search';
    $next_token++;

    preg_match('/(\d+)/', @$tokens[$next_token], $matches);
    if (!isset($matches[1]))
    {
      bad_request('search identifier is missing');
    }
    $page['search'] = $matches[1];
    $next_token++;
  }
  elseif ('list' == @$tokens[$next_token])
  {
    $page['section'] = 'list';
    $next_token++;

    $page['list'] = array();

    // No pictures
    if (empty($tokens[$next_token]))
    {
      // Add dummy element list
      array_push($page['list'], -1);
    }
    // With pictures list
    else
    {
      if (!preg_match('/^\d+(,\d+)*$/', $tokens[$next_token]))
      {
        bad_request('wrong format on list GET parameter');
      }
      foreach (explode(',', $tokens[$next_token]) as $image_id)
      {
        array_push($page['list'], $image_id);
      }
    }
    $next_token++;
  }
  return $page;
}

/**
 * the reverse of add_well_known_params_in_url
 * parses start, flat and chronology from url tokens
*/
function parse_well_known_params_url($tokens, &$i)
{
  $page = array();
  while (isset($tokens[$i]))
  {
    if ( 'flat' == $tokens[$i] )
    {
      // indicate a special list of images
      $page['flat'] = true;
    }
    elseif (strpos($tokens[$i], 'created-')===0 or strpos($tokens[$i], 'posted-')===0)
    {
      $chronology_tokens = explode('-', $tokens[$i] );

      $page['chronology_field'] = $chronology_tokens[0];

      array_shift($chronology_tokens);
      $page['chronology_style'] = $chronology_tokens[0];

      array_shift($chronology_tokens);
      if ( count($chronology_tokens)>0 )
      {
        if ('list'==$chronology_tokens[0] or
            'calendar'==$chronology_tokens[0])
        {
          $page['chronology_view'] = $chronology_tokens[0];
          array_shift($chronology_tokens);
        }
        $page['chronology_date'] = $chronology_tokens;
      }
    }
    elseif (preg_match('/^start-(\d+)/', $tokens[$i], $matches))
    {
      $page['start'] = $matches[1];
    }
    $i++;
  }
  return $page;
}


/**
 * @param id image id
 * @param what_part string one of 'e' (element), 'r' (representative)
 */
function get_action_url($id, $what_part, $download)
{
  $params = array(
        'id' => $id,
        'part' => $what_part,
      );
  if ($download)
  {
    $params['download'] = null;
  }
  
  return add_url_params(get_root_url().'action.php', $params);
}

/*
 * @param element_info array containing element information from db;
 * at least 'id', 'path' should be present
 */
function get_element_url($element_info)
{
  $url = $element_info['path'];
  if ( !url_is_remote($url) )
  {
    $url = embellish_url(get_root_url().$url);
  }
  return $url;
}


/**
 * Indicate to build url with full path
 *
 * @param null
 * @return null
 */
function set_make_full_url()
{
  global $page;

  if (!isset($page['save_root_path']))
  {
    if (isset($page['root_path']))
    {
      $page['save_root_path']['path'] = $page['root_path'];
    }
    $page['save_root_path']['count'] = 1;
    $page['root_path'] = get_absolute_root_url();
  }
  else
  {
    $page['save_root_path']['count'] += 1;
  }
}

/**
 * Restore old parameter to build url with full path
 *
 * @param null
 * @return null
 */
function unset_make_full_url()
{
  global $page;

  if (isset($page['save_root_path']))
  {
    if ($page['save_root_path']['count'] == 1)
    {
      if (isset($page['save_root_path']['path']))
      {
        $page['root_path'] = $page['save_root_path']['path'];
      }
      else
      {
        unset($page['root_path']);
      }
      unset($page['save_root_path']);
    }
    else
    {
      $page['save_root_path']['count'] -= 1;
    }
  }
}

/**
 * Embellish the url argument
 *
 * @param $url
 * @return $url embellished
 */
function embellish_url($url)
{
  $url = str_replace('/./', '/', $url);
  while ( ($dotdot = strpos($url, '/../', 1) ) !== false )
  {
    $before = strrpos($url, '/', -(strlen($url)-$dotdot+1) );
    if ($before !== false)
    {
      $url = substr_replace($url, '', $before, $dotdot-$before+3);
    }
    else
      break;
  }
  return $url;
}

/**
 * Returns the 'home page' of this gallery
 */
function get_gallery_home_url()
{
  global $conf;
  if (!empty($conf['gallery_url']))
  {
    if (url_is_remote($conf['gallery_url']) or $conf['gallery_url'][0]=='/' )
    {
      return $conf['gallery_url'];
    }
    return get_root_url().$conf['gallery_url'];
  }
  else
  {
    return make_index_url();
  }
}
?>