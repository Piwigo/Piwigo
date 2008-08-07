<?php
/*
  to use Menu class in piwigo, just replace the original menubar.inc.php file by
  this one
*/


$datas=array();

//-------------------------------------------------------------- categories
$datas['categories']=array(
    'NB_PICTURE' => $user['nb_total_images'],
    'MENU_CATEGORIES_CONTENT' => get_categories_menu(),
    'U_CATEGORIES' => make_index_url(array('section' => 'categories')),
    'U_UPLOAD' => get_upload_menu_link()
);

//------------------------------------------------------------------------ filter
if (!empty($conf['filter_pages']) and get_filter_page_value('used'))
{
  if ($filter['enabled'])
  {
    $datas['categories']['U_STOP_FILTER']=
      add_url_params(make_index_url(array()), array('filter' => 'stop'));
  }
  else
  {
    $datas['categories']['U_START_FILTER']=
      add_url_params(make_index_url(array()), array('filter' => 'start-recent-'.$user['recent_period']));
  }
}



//-------------------------------------------------------------- external links
$datas['links']=array();
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
    $datas['links'][]=$tpl_var;
  }
}





//------------------------------------------------------------------------ tags
$datas['tags']=array();
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
    $datas['tags'][]=
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
}



//---------------------------------------------------------- special categories
/*
  ** note for usage with Menu class **
  items of Special section are defined with a named key rather than a
  numeric key

  Example :
    $datas['special']=array(
      'favorite_cat' => array( ... ),
      'most_visited_cat' => array( ... ),
      'best_rated_cat' => array( ... ),
      [...]
    );

  This permits to easily find datas and modify content with a callback on the
  'loc_begin_menubar' event.

  Example :
    $section_special=$menu->section('mbSpecial');
    unset($section['ITEMS']['favorite_cat']);
    $menu->replace($section_special);

    this code permit to remove the items "favorite_cat" from the
    section "mbSpecial"
*/
$datas['special']=array();
if ( !is_a_guest() )
{
  $datas['special']['favorite_cat']=array(
      'URL' => make_index_url(array('section' => 'favorites')),
      'TITLE' => l10n('favorite_cat_hint'),
      'NAME' => l10n('favorite_cat')
      );
}
// most visited
  $datas['special']['most_visited_cat']=array(
    'URL' => make_index_url(array('section' => 'most_visited')),
    'TITLE' => l10n('most_visited_cat_hint'),
    'NAME' => l10n('most_visited_cat')
    );
// best rated
if ($conf['rate'])
{
  $datas['special']['best_rated_cat']=array(
      'URL' => make_index_url(array('section' => 'best_rated')),
      'TITLE' => l10n('best_rated_cat_hint'),
      'NAME' => l10n('best_rated_cat')
    );
}
// random
  $datas['special']['random_cat']=array(
    'URL' => get_root_url().'random.php',
    'TITLE' => l10n('random_cat_hint'),
    'NAME' => l10n('random_cat'),
    'REL'=> 'rel="nofollow"'
    );

// recent pics
  $datas['special']['recent_pics_cat']=array(
    'URL' => make_index_url(array('section' => 'recent_pics')),
    'TITLE' => l10n('recent_pics_cat_hint'),
    'NAME' => l10n('recent_pics_cat'),
    );
// recent cats
  $datas['special']['recent_cats_cat']=array(
    'URL' => make_index_url(array('section' => 'recent_cats')),
    'TITLE' => l10n('recent_cats_cat_hint'),
    'NAME' => l10n('recent_cats_cat'),
    );

// calendar
  $datas['special']['calendar']=array(
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
  );



//--------------------------------------------------------------- identification
$datas['identification']=array();
if (is_a_guest())
{
  $datas['identification']=array(
        'U_IDENTIFY' => get_root_url().'identification.php',
        'AUTHORIZE_REMEMBERING' => $conf['authorize_remembering'],
        'U_LOST_PASSWORD' => get_root_url().'password.php',
      );

  if ($conf['allow_user_registration'])
  {
    $datas['identification']['U_REGISTER']=get_root_url().'register.php';
  }
}
else
{
  $datas['identification']['USERNAME']= $user['username'];

  if (is_autorize_status(ACCESS_CLASSIC))
  {
    $datas['identification']['U_PROFILE']=get_root_url().'profile.php';
  }

  // the logout link has no meaning with Apache authentication : it is not
  // possible to logout with this kind of authentication.
  if (!$conf['apache_authentication'])
  {
    $datas['identification']['U_LOGOUT']= get_root_url().'?act=logout';
  }

  if (is_admin())
  {
    $datas['identification']['U_ADMIN']= get_root_url().'admin.php';
  }
}


//--------------------------------------------------------------- menu summaries
/*
  ** note for usage with Menu class **
  items of menu section are defined with a named key rather than a numeric key

  see notes from "sepcial categories" for more informations
*/
$datas['menu']=array();
//qsearch input zone visible y/n ; if set to 'n' the qsearch zone isn't visible
$datas['menu']['qsearch']='y';

// tags link
$datas['menu']['Tags']=array(
    'TITLE' => l10n('See available tags'),
    'NAME' => l10n('Tags'),
    'U_SUMMARY'=> get_root_url().'tags.php',
  );

// search link
$datas['menu']['Search']=array(
    'TITLE'=>l10n('hint_search'),
    'NAME'=>l10n('Search'),
    'U_SUMMARY'=> get_root_url().'search.php',
    'REL'=> 'rel="search"'
  );

// comments link
$datas['menu']['comments']=array(
    'TITLE'=>l10n('hint_comments'),
    'NAME'=>l10n('comments'),
    'U_SUMMARY'=> get_root_url().'comments.php',
  );

// about link
$datas['menu']['About']=array(
    'TITLE'     => l10n('about_page_title'),
    'NAME'      => l10n('About'),
    'U_SUMMARY' => get_root_url().'about.php',
  );

// notification
$datas['menu']['Notification']=array(
    'TITLE'=>l10n('RSS feed'),
    'NAME'=>l10n('Notification'),
    'U_SUMMARY'=> get_root_url().'notification.php',
    'REL'=> 'rel="nofollow"'
  );




$section = new Section('mbLinks', 'links', MENU_TEMPLATES_PATH.'menubar_links.tpl');
$section->set_items($datas['links']);
$menu->add($section->get());


$section = new Section('mbTags', 'Related tags', MENU_TEMPLATES_PATH.'menubar_tags.tpl');
$section->set_items($datas['tags']);
$menu->add($section->get());

$section = new Section('mbSpecial', 'special_categories', MENU_TEMPLATES_PATH.'menubar_special.tpl');
$section->set_items($datas['special']);
$menu->add($section->get());

$section = new Section('mbMenu', 'title_menu', MENU_TEMPLATES_PATH.'menubar_menu.tpl');
$section->set_items($datas['menu']);
$menu->add($section->get());

$section = new Section('mbIdentification', 'identification', MENU_TEMPLATES_PATH.'menubar_identification.tpl');
$section->set_items($datas['identification']);
$menu->add($section->get());

$section = new Section('mbCategories', 'Categories', MENU_TEMPLATES_PATH.'menubar_categories.tpl');
$section->set_items($datas['categories']);
$menu->add($section->get());


$menu->apply();




?>
