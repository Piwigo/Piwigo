<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | file          : $Id$
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

//--------------------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
include(PHPWG_ROOT_PATH.'include/section_init.inc.php');

trigger_action('loc_begin_index');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_GUEST);

//---------------------------------------------- change of image display order
if (isset($_GET['image_order']))
{
  if ( (int)$_GET['image_order'] > 0)
  {
    pwg_set_session_var('image_order', (int)$_GET['image_order']);
  }
  else
  {
    pwg_unset_session_var('image_order');
  }
  redirect(
    duplicate_index_url(
      array(),        // nothing to redefine
      array('start')  // changing display order goes back to section first page
      )
    );
}
//-------------------------------------------------------------- initialization
// detection of the start picture to display
if (!isset($page['start']))
{
  $page['start'] = 0;
}

// access authorization check
if (isset($page['category']))
{
  check_restrictions($page['category']['id']);
}

if ( count($page['items']) > $user['nb_image_page'])
{
  $page['navigation_bar'] = create_navigation_bar(
    duplicate_index_url(array(), array('start')),
    count($page['items']),
    $page['start'],
    $user['nb_image_page'],
    true
    );
}
else
{
  $page['navigation_bar'] = '';
}

// caddie filling :-)
if (isset($_GET['caddie']))
{
  fill_caddie($page['items']);
  redirect(duplicate_index_url());
}

//----------------------------------------------------- template initialization
//
// Start output of page
//
$title = $page['title'];
$page['body_id'] = 'theCategoryPage';

$template->set_filenames( array('index'=>'index.tpl') );
//-------------------------------------------------------------- category title
$template_title = $page['title'];
if ( count($page['items']) > 0)
{
  $template_title.= ' ['.count($page['items']).']';
}
$template->assign_var('TITLE', $template_title);

if (isset($page['flat']) or isset($page['chronology_field']))
{
  $template->assign_block_vars(
    'mode_normal',
    array(
      'URL' => duplicate_index_url( array(), array('chronology_field', 'start', 'flat') )
      )
    );
}

if (!isset($page['flat']) and 'categories' == $page['section'])
{
  $template->assign_block_vars(
    'flat',
    array(
      'URL' => duplicate_index_url(array('flat' => ''), array('start', 'chronology_field'))
      )
    );
}

if (!isset($page['chronology_field']))
{
  $chronology_params =
      array(
          'chronology_field' => 'created',
          'chronology_style' => 'monthly',
          'chronology_view' => 'list',
      );
  $template->assign_block_vars(
    'mode_created',
    array(
      'URL' => duplicate_index_url( $chronology_params, array('start', 'flat') )
      )
    );

  $chronology_params['chronology_field'] = 'posted';
  $template->assign_block_vars(
    'mode_posted',
    array(
      'URL' => duplicate_index_url( $chronology_params, array('start', 'flat') )
      )
    );
}
else
{
  if ($page['chronology_field'] == 'created')
  {
    $chronology_field = 'posted';
  }
  else
  {
    $chronology_field = 'created';
  }
  $url = duplicate_index_url(
            array('chronology_field'=>$chronology_field ),
            array('chronology_date', 'start', 'flat')
          );
  $template->assign_block_vars(
    'mode_'.$chronology_field,
    array('URL' => $url )
    );
}
// include menubar
include(PHPWG_ROOT_PATH.'include/menubar.inc.php');

if ('search' == $page['section'])
{
  $template->assign_block_vars(
    'search_rules',
    array(
      'URL' => get_root_url().'search_rules.php?search_id='.$page['search'],
      )
    );
}

if (isset($page['category']) and is_admin())
{
  $template->assign_block_vars(
    'edit',
    array(
      'URL' =>
        get_root_url().'admin.php?page=cat_modify'
        .'&amp;cat_id='.$page['category']['id']
      )
    );
}

if (is_admin() and !empty($page['items']) )
{
  $template->assign_block_vars(
    'caddie',
    array(
      'URL' =>
         add_url_params(duplicate_index_url(), array('caddie'=>1) )
      )
    );
}

if ( $page['section']=='search' and $page['start']==0 and
    !isset($page['chronology_field']) and isset($page['qsearch_details']) )
{
  $template->assign_var('QUERY_SEARCH',
    htmlspecialchars($page['qsearch_details']['q']) );

  $found_cat_ids = array_merge(
      (array)@$page['qsearch_details']['matching_cats_no_images'],
      (array)@$page['qsearch_details']['matching_cats'] );
  if (count($found_cat_ids))
  {
    $hints = array();
    $query = '
SELECT id, name, permalink FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', $found_cat_ids).')
  ORDER BY name
  LIMIT 10';
    $result = pwg_query($query);
    while ( $row = mysql_fetch_assoc($result) )
    {
      $hints[] = get_cat_display_name( array($row) );
    }
    $template->assign_block_vars( 'category_search_results',
        array(
            'CONTENT' => implode(' &mdash; ', $hints)
          )
      );
  }

  $tags = find_tags( (array)@$page['qsearch_details']['matching_tags'] );
  if (count($tags))
  {
    usort($tags, 'name_compare');
    $hints = array();
    foreach ( $tags as $tag )
    {
      $hints[] =
        '<a href="' . make_index_url(array('tags'=>array($tag))) . '">'
        .$tag['name']
        .'</a>';
    }
    $template->assign_block_vars( 'tag_search_results',
        array(
            'CONTENT' => implode(' &mdash; ', $hints)
          )
      );
  }
}

//------------------------------------------------------ main part : thumbnails
if ( 0==$page['start']
    and !isset($page['flat'])
    and !isset($page['chronology_field'])
    and ('recent_cats'==$page['section'] or 'categories'==$page['section'])
  )
{
  include(PHPWG_ROOT_PATH.'include/category_cats.inc.php');
}
if ( !empty($page['items']) )
{
  include(PHPWG_ROOT_PATH.'include/category_default.inc.php');
}
//------------------------------------------------------- category informations

// navigation bar
if ($page['navigation_bar'] != '')
{
  $template->assign_block_vars(
    'cat_infos.navigation',
    array(
      'NAV_BAR' => $page['navigation_bar'],
      )
    );
}

if ( count($page['items']) > 0
    and $page['section'] != 'most_visited'
    and $page['section'] != 'best_rated')
{
  // image order
  $template->assign_block_vars( 'preferred_image_order', array() );

  $order_idx = pwg_get_session_var( 'image_order', 0 );

  $orders = get_category_preferred_image_orders();
  for ($i = 0; $i < count($orders); $i++)
  {
    if ($orders[$i][2])
    {
      $template->assign_block_vars(
        'preferred_image_order.order',
        array(
          'DISPLAY' => $orders[$i][0],
          'URL' => add_url_params( duplicate_index_url(), array('image_order'=>$i) ),
          'SELECTED_OPTION' => ($order_idx==$i ? 'SELECTED' : ''),
          )
        );
    }
  }
}

// category comment
if (isset($page['comment']) and $page['comment'] != '')
{
  $template->assign_block_vars(
    'cat_infos.comment',
    array(
      'COMMENTS' => $page['comment']
      )
    );
}
//------------------------------------------------------------ log informations
pwg_log();

include(PHPWG_ROOT_PATH.'include/page_header.php');
trigger_action('loc_end_index');
$template->parse('index');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
