<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
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
  check_restrictions($page['category']);
}

if (isset($page['cat_nb_images'])
    and $page['cat_nb_images'] > $user['nb_image_page'])
{
  $page['navigation_bar'] = create_navigation_bar(
    duplicate_index_url(array(), array('start')),
    $page['cat_nb_images'],
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
  // redirect();
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
if (isset($page['cat_nb_images']) and $page['cat_nb_images'] > 0)
{
  $template_title.= ' ['.$page['cat_nb_images'].']';
}

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

$template->assign_vars(
  array(
    'TITLE' => $template_title
    )
  );

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
        .'&amp;cat_id='.$page['category']
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

if ( $page['section']=='search' and $page['start']==0 )
{
  $tags = get_common_tags($page['items'],
      $conf['content_tag_cloud_items_number'], null);
  if ( count($tags)>1 )
  {
    $template->assign_block_vars('related_tags', array() );

    $tags = add_level_to_tags($tags);
    foreach ($tags as $tag)
    {
      $template->assign_block_vars(
      'related_tags.tag', array(
        'URL' => make_index_url(
          array(
            'tags' => array($tag)
            )
          ),
        'NAME' => $tag['name'],
        'TITLE' => sprintf(
          l10n('%d pictures are also linked to current tags'),
          $tag['counter']
          ),
        'CLASS' => 'tagLevel'.$tag['level']
        )
      );
    }
  }
}

//------------------------------------------------------ main part : thumbnails
if (isset($page['thumbnails_include']))
{
  include(PHPWG_ROOT_PATH.$page['thumbnails_include']);
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

if (isset($page['cat_nb_images']) and $page['cat_nb_images'] > 0
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
  $header_infos['COMMENT'] = strip_tags($page['comment']);
}
//------------------------------------------------------------ log informations
pwg_log();

include(PHPWG_ROOT_PATH.'include/page_header.php');
trigger_action('loc_end_index');
$template->parse('index');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
