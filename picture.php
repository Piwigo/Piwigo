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

define('PHPWG_ROOT_PATH','./');
include_once(PHPWG_ROOT_PATH.'include/common.inc.php');
include(PHPWG_ROOT_PATH.'include/section_init.inc.php');
include_once(PHPWG_ROOT_PATH.'include/functions_picture.inc.php');

// Check Access and exit when user status is not ok
check_status(ACCESS_GUEST);

// access authorization check
if (isset($page['category']))
{
  check_restrictions($page['category']['id']);
}

$page['rank_of'] = array_flip($page['items']);

// if this image_id doesn't correspond to this category, an error message is
// displayed, and execution is stopped
if ( !isset($page['rank_of'][$page['image_id']]) )
{
  $query = '
SELECT id, file, level
  FROM '.IMAGES_TABLE.'
  WHERE ';
  if ($page['image_id']>0)
  {
    $query .= 'id = '.$page['image_id'];
  }
  else
  {// url given by file name
    assert( !empty($page['image_file']) );
    $query .= 'file LIKE \'' .
      str_replace(array('_','%'), array('/_','/%'), $page['image_file'] ).
      '.%\' ESCAPE \'/\' LIMIT 1';
  }
  if ( ! ( $row = pwg_db_fetch_assoc(pwg_query($query)) ) )
  {// element does not exist
    page_not_found( 'The requested image does not exist',
      duplicate_index_url()
      );
  }
  if ($row['level']>$user['level'])
  {
    access_denied();
  }

  $page['image_id'] = $row['id'];
  $page['image_file'] =  $row['file'];
  if ( !isset($page['rank_of'][$page['image_id']]) )
  {// the image can still be non accessible (filter/cat perm) and/or not in the set
    global $filter;
    if ( !empty($filter['visible_images']) and
      !in_array($page['image_id'], explode(',',$filter['visible_images']) ) )
    {
      page_not_found( 'The requested image is filtered',
          duplicate_index_url()
        );
    }
    if ('categories'==$page['section'] and !isset($page['category']) )
    {// flat view - all items
      access_denied();
    }
    else
    {// try to see if we can access it differently
      $query = '
SELECT id
  FROM '.IMAGES_TABLE.' INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON id=image_id
  WHERE id='.$page['image_id']
        . get_sql_condition_FandF(
            array('forbidden_categories' => 'category_id'),
            " AND"
          ).'
  LIMIT 1';
      if ( pwg_db_num_rows( pwg_query($query) ) == 0 )
      {
        access_denied();
      }
      else
      {
        if ('best_rated'==$page['section'])
        {
          $page['rank_of'][$page['image_id']] = count($page['items']);
          array_push($page['items'], $page['image_id'] );
        }
        else
        {
          $url = make_picture_url(
              array(
                'image_id' => $page['image_id'],
                'image_file' => $page['image_file'],
                'section' => 'categories',
                'flat' => true,
              )
            );
          set_status_header( 'recent_pics'==$page['section'] ? 301 : 302);
          redirect_http( $url );
        }
      }
    }
  }
}

// There is cookie, so we must handle it at the beginning
if ( isset($_GET['metadata']) )
{
  if ( pwg_get_session_var('show_metadata') == null )
	{
		pwg_set_session_var('show_metadata', 1 );
	} else {
  	pwg_unset_session_var('show_metadata');
	}
}

// add default event handler for rendering element content
add_event_handler(
  'render_element_content',
  'default_picture_content',
  EVENT_HANDLER_PRIORITY_NEUTRAL,
  2
  );
// add default event handler for rendering element description
add_event_handler('render_element_description', 'nl2br');

trigger_action('loc_begin_picture');

// this is the default handler that generates the display for the element
function default_picture_content($content, $element_info)
{
  if ( !empty($content) )
  {// someone hooked us - so we skip;
    return $content;
  }

  if (isset($_COOKIE['picture_deriv']))
  {
    if ( array_key_exists($_COOKIE['picture_deriv'], ImageStdParams::get_defined_type_map()) )
    {
      pwg_set_session_var('picture_deriv', $_COOKIE['picture_deriv']);
    }
    setcookie('picture_deriv', false, 0, cookie_path() );
  }
  $deriv_type = pwg_get_session_var('picture_deriv', IMG_LARGE);
  $selected_derivative = $element_info['derivatives'][$deriv_type];

  $unique_derivatives = array();
  $show_original = isset($element_info['element_url']);
  $added = array();
  foreach($element_info['derivatives'] as $type => $derivative)
  {
    $url = $derivative->get_url();
    if (isset($added[$url]))
      continue;
    $added[$url] = 1;
    $show_original &= !($derivative->same_as_source());
    $unique_derivatives[$type]= $derivative;
  }

  global $page, $template;

  if ($show_original)
  {
    $template->assign( 'U_ORIGINAL', $element_info['element_url'] );
  }

  $template->append('current', array(
      'selected_derivative' => $selected_derivative,
      'unique_derivatives' => $unique_derivatives,
    ), true);


  $template->set_filenames(
    array('default_content'=>'picture_content.tpl')
    );

  $template->assign( array(
      'ALT_IMG' => $element_info['file'],
      'COOKIE_PATH' => cookie_path(),
      )
    );
  return $template->parse( 'default_content', true);
}

// +-----------------------------------------------------------------------+
// |                            initialization                             |
// +-----------------------------------------------------------------------+

// caching first_rank, last_rank, current_rank in the displayed
// section. This should also help in readability.
$page['first_rank']   = 0;
$page['last_rank']    = count($page['items']) - 1;
$page['current_rank'] = $page['rank_of'][ $page['image_id'] ];

// caching current item : readability purpose
$page['current_item'] = $page['image_id'];

if ($page['current_rank'] != $page['first_rank'])
{
  // caching first & previous item : readability purpose
  $page['previous_item'] = $page['items'][ $page['current_rank'] - 1 ];
  $page['first_item'] = $page['items'][ $page['first_rank'] ];
}

if ($page['current_rank'] != $page['last_rank'])
{
  // caching next & last item : readability purpose
  $page['next_item'] = $page['items'][ $page['current_rank'] + 1 ];
  $page['last_item'] = $page['items'][ $page['last_rank'] ];
}

$url_up = duplicate_index_url(
  array(
    'start' =>
      floor($page['current_rank'] / $page['nb_image_page'])
      * $page['nb_image_page']
    ),
  array(
    'start',
    )
  );

$url_self = duplicate_picture_url();

// +-----------------------------------------------------------------------+
// |                                actions                                |
// +-----------------------------------------------------------------------+

/**
 * Actions are favorite adding, user comment deletion, setting the picture
 * as representative of the current category...
 *
 * Actions finish by a redirection
 */

if (isset($_GET['action']))
{
  switch ($_GET['action'])
  {
    case 'add_to_favorites' :
    {
      $query = '
INSERT INTO '.FAVORITES_TABLE.'
  (image_id,user_id)
  VALUES
  ('.$page['image_id'].','.$user['id'].')
;';
      pwg_query($query);

      redirect($url_self);

      break;
    }
    case 'remove_from_favorites' :
    {
      $query = '
DELETE FROM '.FAVORITES_TABLE.'
  WHERE user_id = '.$user['id'].'
    AND image_id = '.$page['image_id'].'
;';
      pwg_query($query);

      if ('favorites' == $page['section'])
      {
        redirect($url_up);
      }
      else
      {
        redirect($url_self);
      }

      break;
    }
    case 'set_as_representative' :
    {
      if (is_admin() and isset($page['category']))
      {
        $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET representative_picture_id = '.$page['image_id'].'
  WHERE id = '.$page['category']['id'].'
;';
        pwg_query($query);

        $query = '
UPDATE '.USER_CACHE_CATEGORIES_TABLE.'
  SET user_representative_picture_id = NULL
  WHERE user_id = '.$user['id'].'
    AND cat_id = '.$page['category']['id'].'
;';
        pwg_query($query);
      }

      redirect($url_self);

      break;
    }
    case 'add_to_caddie' :
    {
      fill_caddie(array($page['image_id']));
      redirect($url_self);
      break;
    }
    case 'rate' :
    {
      include_once(PHPWG_ROOT_PATH.'include/functions_rate.inc.php');
      rate_picture($page['image_id'], $_POST['rate']);
      redirect($url_self);
    }
    case 'edit_comment' :
    {
      check_pwg_token();
      include_once(PHPWG_ROOT_PATH.'include/functions_comment.inc.php');
      check_input_parameter('comment_to_edit', $_GET, false, PATTERN_ID);
      $author_id = get_comment_author_id($_GET['comment_to_edit']);

      if (can_manage_comment('edit', $author_id))
      {
        if (!empty($_POST['content']))
        {
          $comment_action = update_user_comment(
            array(
              'comment_id' => $_GET['comment_to_edit'],
              'image_id' => $page['image_id'],
              'content' => $_POST['content']
              ),
            $_POST['key']
            );

          $perform_redirect = false;
          switch ($comment_action)
          {
            case 'moderate':
              $_SESSION['page_infos'][] = l10n('An administrator must authorize your comment before it is visible.');
            case 'validate':
              $_SESSION['page_infos'][] = l10n('Your comment has been registered');
              $perform_redirect = true;
              break;
            case 'reject':
              $_SESSION['page_errors'][] = l10n('Your comment has NOT been registered because it did not pass the validation rules');
              $perform_redirect = true;
              break;
            default:
              trigger_error('Invalid comment action '.$comment_action, E_USER_WARNING);
          }

          if ($perform_redirect)
          {
            redirect($url_self);
          }
          unset($_POST['content']);
          break;
        }
        else
        {
          $edit_comment = $_GET['comment_to_edit'];
          break;
        }
      }
    }
    case 'delete_comment' :
    {
      check_pwg_token();

      include_once(PHPWG_ROOT_PATH.'include/functions_comment.inc.php');

      check_input_parameter('comment_to_delete', $_GET, false, PATTERN_ID);

      $author_id = get_comment_author_id($_GET['comment_to_delete']);

      if (can_manage_comment('delete', $author_id))
      {
        delete_user_comment($_GET['comment_to_delete']);
      }

      redirect($url_self);
    }
    case 'validate_comment' :
    {
      check_pwg_token();

      include_once(PHPWG_ROOT_PATH.'include/functions_comment.inc.php');

      check_input_parameter('comment_to_validate', $_GET, false, PATTERN_ID);

      $author_id = get_comment_author_id($_GET['comment_to_validate']);

      if (can_manage_comment('validate', $author_id))
      {
        validate_user_comment($_GET['comment_to_validate']);
      }

      redirect($url_self);
    }

  }
}

//---------- incrementation of the number of hits, we do this only if no action
if (trigger_event('allow_increment_element_hit_count', !isset($_POST['content']) ) )
{
  $query = '
UPDATE
  '.IMAGES_TABLE.'
  SET hit = hit+1
  WHERE id = '.$page['image_id'].'
;';
  pwg_query($query);
}
//---------------------------------------------------------- related categories
$query = '
SELECT category_id,uppercats,commentable,global_rank
  FROM '.IMAGE_CATEGORY_TABLE.'
    INNER JOIN '.CATEGORIES_TABLE.' ON category_id = id
  WHERE image_id = '.$page['image_id'].'
'.get_sql_condition_FandF
  (
    array
      (
        'forbidden_categories' => 'category_id',
        'visible_categories' => 'category_id'
      ),
    'AND'
  ).'
;';
$result = pwg_query($query);
$related_categories = array();
while ($row = pwg_db_fetch_assoc($result))
{
  $row['commentable'] = get_boolean($row['commentable']);
  array_push($related_categories, $row);
}
usort($related_categories, 'global_rank_compare');
//-------------------------first, prev, current, next & last picture management
$picture = array();

$ids = array($page['image_id']);
if (isset($page['previous_item']))
{
  array_push($ids, $page['previous_item']);
  array_push($ids, $page['first_item']);
}
if (isset($page['next_item']))
{
  array_push($ids, $page['next_item']);
  array_push($ids, $page['last_item']);
}

$query = '
SELECT *
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $ids).')
;';

$result = pwg_query($query);

while ($row = pwg_db_fetch_assoc($result))
{
  if (isset($page['previous_item']) and $row['id'] == $page['previous_item'])
  {
    $i = 'previous';
  }
  elseif (isset($page['next_item']) and $row['id'] == $page['next_item'])
  {
    $i = 'next';
  }
  elseif (isset($page['first_item']) and $row['id'] == $page['first_item'])
  {
    $i = 'first';
  }
  elseif (isset($page['last_item']) and $row['id'] == $page['last_item'])
  {
    $i = 'last';
  }
  else
  {
    $i = 'current';
  }

  $row['src_image'] = new SrcImage($row);
  $row['derivatives'] = DerivativeImage::get_all($row['src_image']);

  if ($i=='current')
  {
    $row['element_path'] = get_element_path($row);

    if ( $row['src_image']->is_original() )
    {// we have a photo
      if ( $user['enabled_high']=='true' )
      {
        $row['element_url'] = $row['src_image']->get_url();
        $row['download_url'] = get_action_url($row['id'], 'e', true);
      }
    }
    else
    { // not a pic - need download link
      $row['download_url'] = $row['element_url'] = get_element_url($row);;
    }
  }

  $row['url'] = duplicate_picture_url(
    array(
      'image_id' => $row['id'],
      'image_file' => $row['file'],
      ),
    array(
      'start',
      )
    );

  $picture[$i] = $row;
  $picture[$i]['TITLE'] = render_element_name($row);

  if ('previous'==$i and $page['previous_item']==$page['first_item'])
  {
    $picture['first'] = $picture[$i];
  }
  if ('next'==$i and $page['next_item']==$page['last_item'])
  {
    $picture['last'] = $picture[$i];
  }
}

$slideshow_params = array();
$slideshow_url_params = array();

if (isset($_GET['slideshow']))
{
  $page['slideshow'] = true;
  $page['meta_robots'] = array('noindex'=>1, 'nofollow'=>1);

  $slideshow_params = decode_slideshow_params($_GET['slideshow']);
  $slideshow_url_params['slideshow'] = encode_slideshow_params($slideshow_params);

  if ($slideshow_params['play'])
  {
    $id_pict_redirect = '';
    if (isset($page['next_item']))
    {
      $id_pict_redirect = 'next';
    }
    else
    {
      if ($slideshow_params['repeat'] and isset($page['first_item']))
      {
        $id_pict_redirect = 'first';
      }
    }

    if (!empty($id_pict_redirect))
    {
      // $refresh, $url_link and $title are required for creating
      // an automated refresh page in header.tpl
      $refresh = $slideshow_params['period'];
      $url_link = add_url_params(
          $picture[$id_pict_redirect]['url'],
          $slideshow_url_params
        );
    }
  }
}
else
{
  $page['slideshow'] = false;
}
if ($page['slideshow'] and $conf['light_slideshow'])
{
  $template->set_filenames( array('slideshow' => 'slideshow.tpl'));
}
else
{
  $template->set_filenames( array('picture' => 'picture.tpl'));
}

$title =  $picture['current']['TITLE'];
$title_nb = ($page['current_rank'] + 1).'/'.count($page['items']);

// metadata
$url_metadata = duplicate_picture_url();
$url_metadata = add_url_params( $url_metadata, array('metadata'=>null) );


// do we have a plugin that can show metadata for something else than images?
$metadata_showable = trigger_event(
  'get_element_metadata_available',
  (
    ($conf['show_exif'] or $conf['show_iptc'])
    and !$picture['current']['src_image']->is_mimetype()
    ),
  $picture['current']
  );

if ( $metadata_showable and pwg_get_session_var('show_metadata') )
{
  $page['meta_robots']=array('noindex'=>1, 'nofollow'=>1);
}


$page['body_id'] = 'thePicturePage';

// allow plugins to change what we computed before passing data to template
$picture = trigger_event('picture_pictures_data', $picture);

//------------------------------------------------------- navigation management
foreach (array('first','previous','next','last', 'current') as $which_image)
{
  if (isset($picture[$which_image]))
  {
    $template->assign(
      $which_image,
      array_merge(
        $picture[$which_image],
        array(
          'THUMB_SRC' => $picture[$which_image]['derivatives'][IMG_THUMB]->get_url(),
          // Params slideshow was transmit to navigation buttons
          'U_IMG' =>
            add_url_params(
              $picture[$which_image]['url'], $slideshow_url_params),
          )
        )
      );
  }
}
if ($conf['picture_download_icon'] and !empty($picture['current']['download_url']))
{
  $template->append('current', array('U_DOWNLOAD' => $picture['current']['download_url']), true);
}


if ($page['slideshow'])
{
  $tpl_slideshow = array();

  //slideshow end
  $template->assign(
    array(
      'U_SLIDESHOW_STOP' => $picture['current']['url'],
      )
    );

  foreach (array('repeat', 'play') as $p)
  {
    $var_name =
      'U_'
      .($slideshow_params[$p] ? 'STOP_' : 'START_')
      .strtoupper($p);

    $tpl_slideshow[$var_name] =
          add_url_params(
            $picture['current']['url'],
            array('slideshow' =>
              encode_slideshow_params(
                array_merge($slideshow_params,
                  array($p => ! $slideshow_params[$p]))
                )
              )
          );
  }

  foreach (array('dec', 'inc') as $op)
  {
    $new_period = $slideshow_params['period'] + ((($op == 'dec') ? -1 : 1) * $conf['slideshow_period_step']);
    $new_slideshow_params =
      correct_slideshow_params(
        array_merge($slideshow_params,
                  array('period' => $new_period)));

    if ($new_slideshow_params['period'] === $new_period)
    {
      $var_name = 'U_'.strtoupper($op).'_PERIOD';
      $tpl_slideshow[$var_name] =
            add_url_params(
              $picture['current']['url'],
              array('slideshow' => encode_slideshow_params($new_slideshow_params)
                  )
          );
    }
  }
  $template->assign('slideshow', $tpl_slideshow );
}
elseif ($conf['picture_slideshow_icon'])
{
  $template->assign(
    array(
      'U_SLIDESHOW_START' =>
        add_url_params(
          $picture['current']['url'],
          array( 'slideshow'=>''))
      )
    );
}

$template->assign(
  array(
    'SECTION_TITLE' => $page['title'],
    'PHOTO' => $title_nb,
    'SHOW_PICTURE_NAME_ON_TITLE' => $conf['show_picture_name_on_title'],
    'IS_HOME' => ('categories'==$page['section'] and !isset($page['category']) ),

    'LEVEL_SEPARATOR' => $conf['level_separator'],

    'U_UP' => $url_up,
    'DISPLAY_NAV_BUTTONS' => $conf['picture_navigation_icons'],
    'DISPLAY_NAV_THUMB' => $conf['picture_navigation_thumb']
    )
  );

if ($conf['picture_metadata_icon'])
{
  $template->assign('U_METADATA', $url_metadata);
}


//------------------------------------------------------- upper menu management

// admin links
if (is_admin())
{
  if (isset($page['category']))
  {
    $template->assign(
      array(
        'U_SET_AS_REPRESENTATIVE' => add_url_params($url_self,
                    array('action'=>'set_as_representative')
                 )
        )
      );
  }

  $url_admin =
    get_root_url().'admin.php?page=picture_modify'
    .'&amp;cat_id='.(isset($page['category']) ? $page['category']['id'] : '')
    .'&amp;image_id='.$page['image_id'];

  $template->assign(
    array(
      'U_CADDIE' => add_url_params($url_self,
                  array('action'=>'add_to_caddie')
               ),
      'U_ADMIN' => $url_admin,
      )
    );

  $template->assign('available_permission_levels', get_privacy_level_options());
}

// favorite manipulation
if (!is_a_guest() and $conf['picture_favorite_icon'])
{
  // verify if the picture is already in the favorite of the user
  $query = '
SELECT COUNT(*) AS nb_fav
  FROM '.FAVORITES_TABLE.'
  WHERE image_id = '.$page['image_id'].'
    AND user_id = '.$user['id'].'
;';
  $row = pwg_db_fetch_assoc( pwg_query($query) );
	$is_favorite = $row['nb_fav'] != 0;

  $template->assign(
    'favorite',
    array(
			'IS_FAVORITE' => $is_favorite,
      'U_FAVORITE'    => add_url_params(
        $url_self,
        array('action'=> !$is_favorite ? 'add_to_favorites' : 'remove_from_favorites' )
        ),
      )
    );
}

//--------------------------------------------------------- picture information
// legend
if (isset($picture['current']['comment'])
    and !empty($picture['current']['comment']))
{
  $template->assign(
      'COMMENT_IMG',
        trigger_event('render_element_description',
          $picture['current']['comment'])
      );
}

// author
if (!empty($picture['current']['author']))
{
  $infos['INFO_AUTHOR'] =
// FIXME because of search engine partial rewrite, giving the author
// name threw GET is not supported anymore. This feature should come
// back later, with a better design
//     '<a href="'.
//       PHPWG_ROOT_PATH.'category.php?cat=search'.
//       '&amp;search=author:'.$picture['current']['author']
//       .'">'.$picture['current']['author'].'</a>';
    $picture['current']['author'];
}

// creation date
if (!empty($picture['current']['date_creation']))
{
  $val = format_date($picture['current']['date_creation']);
  $url = make_index_url(
    array(
      'chronology_field'=>'created',
      'chronology_style'=>'monthly',
      'chronology_view'=>'list',
      'chronology_date' => explode('-', substr($picture['current']['date_creation'], 0, 10))
      )
    );
  $infos['INFO_CREATION_DATE'] =
    '<a href="'.$url.'" rel="nofollow">'.$val.'</a>';
}

// date of availability
$val = format_date($picture['current']['date_available']);
$url = make_index_url(
  array(
    'chronology_field'=>'posted',
    'chronology_style'=>'monthly',
    'chronology_view'=>'list',
    'chronology_date' => explode(
      '-',
      substr($picture['current']['date_available'], 0, 10)
      )
    )
  );
$infos['INFO_POSTED_DATE'] = '<a href="'.$url.'" rel="nofollow">'.$val.'</a>';

// size in pixels
if ($picture['current']['src_image']->is_original() and isset($picture['current']['width']) )
{
  $infos['INFO_DIMENSIONS'] =
    $picture['current']['width'].'*'.$picture['current']['height'];
}

// filesize
if (!empty($picture['current']['filesize']))
{
  $infos['INFO_FILESIZE'] =
    sprintf(l10n('%d Kb'), $picture['current']['filesize']);
}

// number of visits
$infos['INFO_VISITS'] = $picture['current']['hit'];

// file
$infos['INFO_FILE'] = $picture['current']['file'];

$template->assign($infos);
$template->assign('display_info', unserialize($conf['picture_informations']));

// related tags
$tags = get_common_tags( array($page['image_id']), -1);
if ( count($tags) )
{
  foreach ($tags as $tag)
  {
    $template->append(
        'related_tags',
        array_merge( $tag,
          array(
            'URL' => make_index_url(
                      array(
                        'tags' => array($tag)
                        )
                      ),
            'U_TAG_IMAGE' => duplicate_picture_url(
                      array(
                        'section' => 'tags',
                        'tags' => array($tag)
                        )
                    )
          )
        )
      );
  }
}

// related categories
if ( count($related_categories)==1 and
    isset($page['category']) and
    $related_categories[0]['category_id']==$page['category']['id'] )
{ // no need to go to db, we have all the info
  $template->append(
      'related_categories',
      get_cat_display_name( $page['category']['upper_names'] )
    );
}
else
{ // use only 1 sql query to get names for all related categories
  $ids = array();
  foreach ($related_categories as $category)
  {// add all uppercats to $ids
    $ids = array_merge($ids, explode(',', $category['uppercats']) );
  }
  $ids = array_unique($ids);
  $query = '
SELECT id, name, permalink
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',',$ids).')';
  $cat_map = hash_from_query($query, 'id');
  foreach ($related_categories as $category)
  {
    $cats = array();
    foreach ( explode(',', $category['uppercats']) as $id )
    {
      $cats[] = $cat_map[$id];
    }
    $template->append('related_categories', get_cat_display_name($cats) );
  }
}

// maybe someone wants a special display (call it before page_header so that
// they can add stylesheets)
$element_content = trigger_event(
  'render_element_content',
  '',
  $picture['current']
  );
$template->assign( 'ELEMENT_CONTENT', $element_content );

if (isset($picture['next'])
    and $picture['next']['src_image']->is_original()
    and strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome/') === false)
{
  $template->assign('U_PREFETCH', $picture['next']['derivatives'][pwg_get_session_var('picture_deriv', IMG_LARGE)]->get_url() );
}

$template->assign('U_CANONICAL', make_picture_url( array('image_id'=>$picture['current']['id'], 'image_file'=>$picture['current']['file']) ) );

// +-----------------------------------------------------------------------+
// |                               sub pages                               |
// +-----------------------------------------------------------------------+

include(PHPWG_ROOT_PATH.'include/picture_rate.inc.php');
if ($conf['activate_comments'])
{
  include(PHPWG_ROOT_PATH.'include/picture_comment.inc.php');
}
if ($metadata_showable and pwg_get_session_var('show_metadata') <> null )
{
  include(PHPWG_ROOT_PATH.'include/picture_metadata.inc.php');
}

// include menubar
$themeconf = $template->get_template_vars('themeconf');
if ($conf['picture_menu'] AND (!isset($themeconf['hide_menu_on']) OR !in_array('thePicturePage', $themeconf['hide_menu_on'])))
{
  if (!isset($page['start'])) $page['start'] = 0;
  include( PHPWG_ROOT_PATH.'include/menubar.inc.php');
  if (is_admin()) $template->assign('U_ADMIN', $url_admin); // overwrited by the menu
}

include(PHPWG_ROOT_PATH.'include/page_header.php');
trigger_action('loc_end_picture');
if ($page['slideshow'] and $conf['light_slideshow'])
{
  $template->pparse('slideshow');
}
else
{
  $template->pparse('picture');
}
//------------------------------------------------------------ log informations
pwg_log($picture['current']['id'], 'picture');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>