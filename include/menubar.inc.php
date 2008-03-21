<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2008 PhpWebGallery Team - http://phpwebgallery.net |
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

/**
 * This file is included by the main page to show the menu bar
 *
 */
$template->set_filenames(
  array(
    'menubar' => 'menubar.tpl',
    )
  );

trigger_action('loc_begin_menubar');

$template->assign(
  array(
    'NB_PICTURE' => $user['nb_total_images'],
    'MENU_CATEGORIES_CONTENT' => get_categories_menu(),
    'U_CATEGORIES' => make_index_url(array('section' => 'categories')),
    'U_LOST_PASSWORD' => get_root_url().'password.php',
    )
  );

//-------------------------------------------------------------- external links
foreach ($conf['links'] as $url => $url_data)
{
  if (!is_array($url_data))
  {
    $url_data = array('label' => $url_data);
  }

  if
    (
      (!isset($url_data['eval_visible']))
      or
      (eval($url_data['eval_visible']))
    )
  {
    $tpl_var = array(
        'URL' => $url,
        'LABEL' => $url_data['label']
      );

    if (!isset($url_data['new_window']) or $url_data['new_window'])
    {
      $tpl_var['new_window'] =
        array(
          'NAME' => (isset($url_data['nw_name']) ? $url_data['nw_name'] : ''),
          'FEATURES' => (isset($url_data['nw_features']) ? $url_data['nw_features'] : '')
        );
    }
    $template->append('links', $tpl_var);
  }
}

//------------------------------------------------------------------------ filter
if (!empty($conf['filter_pages']) and get_filter_page_value('used'))
{
  if ($filter['enabled'])
  {
    $template->assign(
      'U_STOP_FILTER',
      add_url_params(make_index_url(array()), array('filter' => 'stop'))
      );
  }
  else
  {
    $template->assign(
      'U_START_FILTER',
      add_url_params(make_index_url(array()), array('filter' => 'start-recent-'.$user['recent_period']))
      );
  }
}

//------------------------------------------------------------------------ tags
if ('tags' == @$page['section'])
{
  // display tags associated to currently tagged items, less current tags
  $tags = array();
  if ( !empty($page['items']) )
  {
    $tags = get_common_tags($page['items'],
        $conf['menubar_tag_cloud_items_number'], $page['tag_ids']);
  }

  $tags = add_level_to_tags($tags);

  foreach ($tags as $tag)
  {
    $template->append(
      'related_tags',
      array(
        'U_TAG' => make_index_url(
          array(
            'tags' => array($tag)
            )
          ),

        'NAME' => $tag['name'],

        'CLASS' => 'tagLevel'.$tag['level'],

        'add' => array(

            'URL' => make_index_url(
              array(
                'tags' => array_merge(
                  $page['tags'],
                  array($tag)
                  )
                )
              ),
            'COUNTER' => $tag['counter'],
            )
        )
      );
  }
}
//---------------------------------------------------------- special categories
// favorites categories
if ( !is_a_guest() )
{
  $template->append(
    'special_categories',
    array(
      'URL' => make_index_url(array('section' => 'favorites')),
      'TITLE' => l10n('favorite_cat_hint'),
      'NAME' => l10n('favorite_cat')
      ));
}
// most visited
$template->append(
  'special_categories',
  array(
    'URL' => make_index_url(array('section' => 'most_visited')),
    'TITLE' => l10n('most_visited_cat_hint'),
    'NAME' => l10n('most_visited_cat')
    ));
// best rated
if ($conf['rate'])
{
  $template->append(
    'special_categories',
    array(
      'URL' => make_index_url(array('section' => 'best_rated')),
      'TITLE' => l10n('best_rated_cat_hint'),
      'NAME' => l10n('best_rated_cat')
      )
    );
}
// random
$template->append(
  'special_categories',
  array(
    'URL' => get_root_url().'random.php',
    'TITLE' => l10n('random_cat_hint'),
    'NAME' => l10n('random_cat'),
    'REL'=> 'rel="nofollow"'
    ));

// recent pics
$template->append(
  'special_categories',
  array(
    'URL' => make_index_url(array('section' => 'recent_pics')),
    'TITLE' => l10n('recent_pics_cat_hint'),
    'NAME' => l10n('recent_pics_cat'),
    ));
// recent cats
$template->append(
  'special_categories',
  array(
    'URL' => make_index_url(array('section' => 'recent_cats')),
    'TITLE' => l10n('recent_cats_cat_hint'),
    'NAME' => l10n('recent_cats_cat'),
    ));

// calendar
$template->append(
  'special_categories',
  array(
    'URL' =>
      make_index_url(
        array(
          'chronology_field' => ($conf['calendar_datefield']=='date_available'
                                  ? 'posted' : 'created'),
           'chronology_style'=> 'monthly',
           'chronology_view' => 'calendar'
        )
      ),
    'TITLE' => l10n('calendar_hint'),
    'NAME' => l10n('calendar'),
    'REL'=> 'rel="nofollow"'
    )
  );
//--------------------------------------------------------------------- summary

if (is_a_guest())
{
  $template->assign(
      array(
        'U_IDENTIFY' => get_root_url().'identification.php',
        'AUTHORIZE_REMEMBERING' => $conf['authorize_remembering']
      )
    );

  if ($conf['allow_user_registration'])
  {
    $template->assign( 'U_REGISTER', get_root_url().'register.php');
  }
}
else
{
  $template->assign('USERNAME', $user['username']);

  if (is_autorize_status(ACCESS_CLASSIC))
  {
    $template->assign('U_PROFILE', get_root_url().'profile.php');
  }

  // the logout link has no meaning with Apache authentication : it is not
  // possible to logout with this kind of authentication.
  if (!$conf['apache_authentication'])
  {
    $template->assign('U_LOGOUT', get_root_url().'?act=logout');
  }

  if (is_admin())
  {
    $template->assign('U_ADMIN', get_root_url().'admin.php');
  }
}

// tags link
$template->append(
  'summaries',
  array(
    'TITLE' => l10n('See available tags'),
    'NAME' => l10n('Tags'),
    'U_SUMMARY'=> get_root_url().'tags.php',
    )
  );

// search link
$template->append(
  'summaries',
  array(
    'TITLE'=>l10n('hint_search'),
    'NAME'=>l10n('Search'),
    'U_SUMMARY'=> get_root_url().'search.php',
    'REL'=> 'rel="search"'
    )
  );

// comments link
$template->append(
  'summaries',
  array(
    'TITLE'=>l10n('hint_comments'),
    'NAME'=>l10n('comments'),
    'U_SUMMARY'=> get_root_url().'comments.php',
    )
  );

// about link
$template->append(
  'summaries',
  array(
    'TITLE'     => l10n('about_page_title'),
    'NAME'      => l10n('About'),
    'U_SUMMARY' => get_root_url().'about.php',
    )
  );

// notification
$template->append(
  'summaries',
  array(
    'TITLE'=>l10n('RSS feed'),
    'NAME'=>l10n('Notification'),
    'U_SUMMARY'=> get_root_url().'notification.php',
    'REL'=> 'rel="nofollow"'
    )
  );

if (isset($page['category']) and $page['category']['uploadable'] )
{ // upload a picture in the category
  $url = get_root_url().'upload.php?cat='.$page['category']['id'];
  $template->assign('U_UPLOAD', $url);
}

trigger_action('loc_end_menubar');
$template->assign_var_from_handle('MENUBAR', 'menubar');
?>
