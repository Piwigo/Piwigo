<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
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
 * This file is included by the main page to show the menu bar
 *
 */

include_once(PHPWG_ROOT_PATH.'include/block.class.php');

initialize_menu();

function initialize_menu()
{
  global $page, $conf, $user, $template, $filter;

  $menu = new BlockManager("menubar");
  $menu->load_registered_blocks();
  $menu->prepare_display();

//--------------------------------------------------------------- external links
  if ( ($block=$menu->get_block('mbLinks')) and !empty($conf['links']) )
  {
    $data = array();
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
        $data[] = $tpl_var;
      }
    }
    $block->template = 'menubar_links.tpl';
    $block->data = $data;
  }

//-------------------------------------------------------------- categories
  $block = $menu->get_block('mbCategories');
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

  if ( $block!=null )
  {
    $block->data = array(
      'NB_PICTURE' => $user['nb_total_images'],
      'MENU_CATEGORIES' => get_categories_menu(),
      'U_CATEGORIES' => make_index_url(array('section' => 'categories')),
      'U_UPLOAD' => get_upload_menu_link()
    );
    $block->template = 'menubar_categories.tpl';
  }

//------------------------------------------------------------------------ tags
  $block = $menu->get_block('mbTags');
  if ( $block!=null and 'tags'==@$page['section'] and !empty($page['items']) )
  {
    $tags = get_common_tags($page['items'],
          $conf['menubar_tag_cloud_items_number'], $page['tag_ids']);
    $tags = add_level_to_tags($tags);

    foreach ($tags as $tag)
    {
      $block->data[] =
        array_merge( $tag,
          array(
            'URL' => make_index_url(
              array(
                'tags' => array($tag)
                )
              ),
            'U_ADD' => make_index_url(
                  array(
                    'tags' => array_merge(
                      $page['tags'],
                      array($tag)
                      )
                    )
                  ),
            )
        );
    }
    $block->template = 'menubar_tags.tpl';
  }

//----------------------------------------------------------- special categories
  if ( ($block = $menu->get_block('mbSpecials')) != null )
  {
    if ( !is_a_guest() )
    {// favorites
      $block->data['favorites'] =
        array(
          'URL' => make_index_url(array('section' => 'favorites')),
          'TITLE' => l10n('display my favorites pictures'),
          'NAME' => l10n('My favorites')
          );
    }

    $block->data['most_visited'] =
      array(
        'URL' => make_index_url(array('section' => 'most_visited')),
        'TITLE' => l10n('display most visited pictures'),
        'NAME' => l10n('Most visited')
      );

    if ($conf['rate'])
    {
       $block->data['best_rated'] =
        array(
          'URL' => make_index_url(array('section' => 'best_rated')),
          'TITLE' => l10n('display best rated items'),
          'NAME' => l10n('Best rated')
        );
    }

    $block->data['random'] =
      array(
        'URL' => get_root_url().'random.php',
        'TITLE' => l10n('display a set of random pictures'),
        'NAME' => l10n('Random pictures'),
        'REL'=> 'rel="nofollow"'
      );

    $block->data['recent_pics'] =
      array(
        'URL' => make_index_url(array('section' => 'recent_pics')),
        'TITLE' => l10n('display most recent pictures'),
        'NAME' => l10n('Recent pictures'),
      );

    $block->data['recent_cats'] =
      array(
        'URL' => make_index_url(array('section' => 'recent_cats')),
        'TITLE' => l10n('display recently updated categories'),
        'NAME' => l10n('Recent categories'),
      );


    $block->data['calendar'] =
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
        'TITLE' => l10n('display each day with pictures, month per month'),
        'NAME' => l10n('Calendar'),
        'REL'=> 'rel="nofollow"'
      );
    $block->template = 'menubar_specials.tpl';
  }


//---------------------------------------------------------------------- summary
  if ( ($block=$menu->get_block('mbMenu')) != null )
  {
    // quick search block will be displayed only if data['qsearch'] is set
    // to "yes"
    $block->data['qsearch']=true;

    // tags link
    $block->data['tags'] =
      array(
        'TITLE' => l10n('See available tags'),
        'NAME' => l10n('Tags'),
        'URL'=> get_root_url().'tags.php',
      );

    // search link
    $block->data['search'] =
      array(
        'TITLE'=>l10n('search'),
        'NAME'=>l10n('Search'),
        'URL'=> get_root_url().'search.php',
        'REL'=> 'rel="search"'
      );

    // comments link
    $block->data['comments'] =
      array(
        'TITLE'=>l10n('See last users comments'),
        'NAME'=>l10n('Comments'),
        'URL'=> get_root_url().'comments.php',
      );

    // about link
    $block->data['about'] =
      array(
        'TITLE'     => l10n('About Piwigo'),
        'NAME'      => l10n('About'),
        'URL' => get_root_url().'about.php',
      );

    // notification
    $block->data['rss'] =
      array(
        'TITLE'=>l10n('RSS feed'),
        'NAME'=>l10n('Notification'),
        'URL'=> get_root_url().'notification.php',
        'REL'=> 'rel="nofollow"'
      );
    $block->template = 'menubar_menu.tpl';
  }


//--------------------------------------------------------------- identification
  if (is_a_guest())
  {
    $template->assign(
        array(
          'U_LOGIN' => get_root_url().'identification.php',
          'U_LOST_PASSWORD' => get_root_url().'password.php',
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
    $template->assign('USERNAME', stripslashes($user['username']));
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
  if ( ($block=$menu->get_block('mbIdentification')) != null )
  {
    $block->template = 'menubar_identification.tpl';
  }
  $menu->apply('MENUBAR',  'menubar.tpl' );
}
?>
