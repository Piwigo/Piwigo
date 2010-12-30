<?php
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

//--------------------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
include(PHPWG_ROOT_PATH.'include/section_init.inc.php');

// Check Access and exit when user status is not ok
check_status(ACCESS_GUEST);

if (!isset($page['start']))
{
  $page['start'] = 0;
}

// access authorization check
if (isset($page['category']))
{
  check_restrictions($page['category']['id']);
}

trigger_action('loc_begin_index');

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

$page['navigation_bar'] = array();
if (count($page['items']) > $user['nb_image_page'])
{
  $page['navigation_bar'] = create_navigation_bar(
    duplicate_index_url(array(), array('start')),
    count($page['items']),
    $page['start'],
    $page['nb_image_page'],
    true
    );
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
if (count($page['items']) > 0)
{
  $template_title.= ' ['.count($page['items']).']';
}
$template->assign('TITLE', $template_title);

if (isset($page['flat']) or isset($page['chronology_field']))
{
  $template->assign(
    'U_MODE_NORMAL',
    duplicate_index_url( array(), array('chronology_field', 'start', 'flat') )
    );
}

if ($conf['index_flat_icon'] and !isset($page['flat']) and 'categories' == $page['section'])
{
  $template->assign(
    'U_MODE_FLAT',
    duplicate_index_url(array('flat' => ''), array('start', 'chronology_field'))
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
  if ($conf['index_created_date_icon'])
  {
    $template->assign(
      'U_MODE_CREATED',
      duplicate_index_url( $chronology_params, array('start', 'flat') )
      );
  }
  if ($conf['index_posted_date_icon'])
  {
    $chronology_params['chronology_field'] = 'posted';
    $template->assign(
      'U_MODE_POSTED',
      duplicate_index_url( $chronology_params, array('start', 'flat') )
      );
  }
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
  if ($conf['index_'.$chronology_field.'_date_icon'])
  {
    $url = duplicate_index_url(
              array('chronology_field'=>$chronology_field ),
              array('chronology_date', 'start', 'flat')
            );
    $template->assign(
        'U_MODE_'.strtoupper($chronology_field),
        $url
      );
  }
}

if ('search' == $page['section'])
{
  $template->assign(
    'U_SEARCH_RULES',
    get_root_url().'search_rules.php?search_id='.$page['search']
    );
}

if (isset($page['category']) and is_admin())
{
  $template->assign(
    'U_EDIT',
     get_root_url().'admin.php?page=cat_modify'
        .'&amp;cat_id='.$page['category']['id']
    );
}

if (is_admin() and !empty($page['items']))
{
  $template->assign(
    'U_CADDIE',
     add_url_params(duplicate_index_url(), array('caddie'=>1) )
    );
}

if ( $page['section']=='search' and $page['start']==0 and
    !isset($page['chronology_field']) and isset($page['qsearch_details']) )
{
  $template->assign('QUERY_SEARCH',
    htmlspecialchars($page['qsearch_details']['q']) );

  $cats = array_merge(
      (array)@$page['qsearch_details']['matching_cats_no_images'],
      (array)@$page['qsearch_details']['matching_cats'] );
  if (count($cats))
  {
    usort($cats, 'name_compare');
    $hints = array();
    foreach ( $cats as $cat )
    {
      $hints[] = get_cat_display_name( array($cat), '', false );
    }
    $template->assign( 'category_search_results', $hints);
  }

  $tags = (array)@$page['qsearch_details']['matching_tags'];
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
    $template->assign( 'tag_search_results', $hints);
  }
}

// navigation bar
$template->assign( 'navbar', $page['navigation_bar'] );

if ( $conf['index_sort_order_input']
    and count($page['items']) > 0
    and $page['section'] != 'most_visited'
    and $page['section'] != 'best_rated')
{
  // image order
  $order_idx = pwg_get_session_var( 'image_order', 0 );

  $url = add_url_params(
          duplicate_index_url(),
          array('image_order' => '')
        );
  foreach (get_category_preferred_image_orders() as $order_id => $order)
  {
    if ($order[2])
    {
      $template->append(
        'image_orders',
        array(
          'DISPLAY' => $order[0],
          'URL' => $url.$order_id,
          'SELECTED' => ($order_idx == $order_id ? true:false),
          )
        );
    }
  }
}

// category comment
if ($page['start']==0 and !isset($page['chronology_field']) and !empty($page['comment']) )
{
  $template->assign('CONTENT_DESCRIPTION', $page['comment'] );
}

// include menubar
include( PHPWG_ROOT_PATH.'include/menubar.inc.php');

//------------------------------------------------------ main part : thumbnails
if ( 0==$page['start']
    and !isset($page['flat'])
    and !isset($page['chronology_field'])
    and ('recent_cats'==$page['section'] or 'categories'==$page['section'])
    and (!isset($page['category']['count_categories']) or $page['category']['count_categories']>0 )
  )
{
  include(PHPWG_ROOT_PATH.'include/category_cats.inc.php');
}
if ( !empty($page['items']) )
{
  include(PHPWG_ROOT_PATH.'include/category_default.inc.php');
}
//------------------------------------------------------- category informations

// slideshow
// execute after init thumbs in order to have all picture informations
if (!empty($page['cat_slideshow_url']))
{
  if (isset($_GET['slideshow']))
  {
    redirect($page['cat_slideshow_url']);
  }
  elseif ($conf['index_slideshow_icon'])
  {
    $template->assign('U_SLIDESHOW', $page['cat_slideshow_url']);
  }
}

include(PHPWG_ROOT_PATH.'include/page_header.php');
trigger_action('loc_end_index');
$template->pparse('index');
//------------------------------------------------------------ log informations
pwg_log();
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
