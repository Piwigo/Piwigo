<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * @package functions\html
 */


/**
 * Generates breadcrumb from categories list.
 * Categories string returned contains categories as given in the input
 * array $cat_informations. $cat_informations array must be an array
 * of array( id=>?, name=>?, permalink=>?). If url input parameter is null,
 * returns only the categories name without links.
 *
 * @param array $cat_informations
 * @param string|null $url
 * @return string
 */
function get_cat_display_name($cat_informations, $url='')
{
  global $conf;

  //$output = '<a href="'.get_absolute_root_url().$conf['home_page'].'">'.l10n('Home').'</a>';
  $output = '';
  $is_first=true;

  foreach ($cat_informations as $cat)
  {
    is_array($cat) or trigger_error(
        'get_cat_display_name wrong type for category ', E_USER_WARNING
      );

    $cat['name'] = trigger_change(
      'render_category_name',
      $cat['name'],
      'get_cat_display_name'
      );

    if ($is_first)
    {
      $is_first=false;
    }
    else
    {
      $output.= $conf['level_separator'];
    }

    if ( !isset($url) )
    {
      $output.= $cat['name'];
    }
    elseif ($url == '')
    {
      $output.= '<a href="'
            .make_index_url(
                array(
                  'category' => $cat,
                  )
              )
            .'">';
      $output.= $cat['name'].'</a>';
    }
    else
    {
      $output.= '<a href="'.PHPWG_ROOT_PATH.$url.$cat['id'].'">';
      $output.= $cat['name'].'</a>';
    }
  }
  return $output;
}

/**
 * Generates breadcrumb from categories list using a cache.
 * @see get_cat_display_name()
 *
 * @param string $uppercats
 * @param string|null $url
 * @param bool $single_link
 * @param string|null $link_class
 * @return string
 */
function get_cat_display_name_cache($uppercats,
                                    $url = '',
                                    $single_link = false,
                                    $link_class = null,
                                    $auth_key=null)
{
  global $cache, $conf;

  $add_url_params = array();
  if (isset($auth_key))
  {
    $add_url_params['auth'] = $auth_key;
  }

  if (!isset($cache['cat_names']))
  {
    $query = '
SELECT id, name, permalink
  FROM '.CATEGORIES_TABLE.'
;';
    $cache['cat_names'] = query2array($query, 'id');
  }

  $output = '';
  if ($single_link)
  {
    $single_url = add_url_params(get_root_url().$url.array_pop(explode(',', $uppercats)), $add_url_params);
    $output.= '<a href="'.$single_url.'"';
    if (isset($link_class))
    {
      $output.= ' class="'.$link_class.'"';
    }
    $output.= '>';
  }
  $is_first = true;
  foreach (explode(',', $uppercats) as $category_id)
  {
    $cat = $cache['cat_names'][$category_id];

    $cat['name'] = trigger_change(
      'render_category_name',
      $cat['name'],
      'get_cat_display_name_cache'
      );

    if ($is_first)
    {
      $is_first = false;
    }
    else
    {
      $output.= $conf['level_separator'];
    }

    if ( !isset($url) or $single_link )
    {
      $output.= $cat['name'];
    }
    elseif ($url == '')
    {
      $output.= '
<a href="'
      .add_url_params(
        make_index_url(
          array(
            'category' => $cat,
            )
          ),
        $add_url_params
        )
      .'">'.$cat['name'].'</a>';
    }
    else
    {
      $output.= '
<a href="'.PHPWG_ROOT_PATH.$url.$category_id.'">'.$cat['name'].'</a>';
    }
  }

  if ($single_link and isset($single_url))
  {
    $output.= '</a>';
  }

  return $output;
}

/**
 * Generates breadcrumb for a category.
 * @see get_cat_display_name()
 *
 * @param int $cat_id
 * @param string|null $url
 * @return string
 */
function get_cat_display_name_from_id($cat_id, $url = '')
{
  $cat_info = get_cat_info($cat_id);
  return get_cat_display_name($cat_info['upper_names'], $url);
}

/**
 * Apply basic markdown transformations to a text.
 * newlines becomes br tags
 * _word_ becomes underline
 * /word/ becomes italic
 * *word* becomes bolded
 * urls becomes a tags
 *
 * @param string $content
 * @return string
 */
function render_comment_content($content)
{
  $content = htmlspecialchars($content);
  $pattern = '/(https?:\/\/\S*)/';
  $replacement = '<a href="$1" rel="nofollow">$1</a>';
  $content = preg_replace($pattern, $replacement, $content);

  $content = nl2br($content);

  // replace _word_ by an underlined word
  $pattern = '/\b_(\S*)_\b/';
  $replacement = '<span style="text-decoration:underline;">$1</span>';
  $content = preg_replace($pattern, $replacement, $content);

  // replace *word* by a bolded word
  $pattern = '/\b\*(\S*)\*\b/';
  $replacement = '<span style="font-weight:bold;">$1</span>';
  $content = preg_replace($pattern, $replacement, $content);

  // replace /word/ by an italic word
  $pattern = "/\/(\S*)\/(\s)/";
  $replacement = '<span style="font-style:italic;">$1$2</span>';
  $content = preg_replace($pattern, $replacement, $content);

  // TODO : add a trigger

  return $content;
}


/**
 * Callback used for sorting by name.
 */
function name_compare($a, $b)
{
  return strcmp(strtolower($a['name']), strtolower($b['name']));
}

/**
 * Callback used for sorting by name (slug) with cache.
 */
function tag_alpha_compare($a, $b)
{
  global $cache;

  foreach (array($a, $b) as $tag)
  {
    if (!isset($cache[__FUNCTION__][ $tag['name'] ]))
    {
      $cache[__FUNCTION__][ $tag['name'] ] = pwg_transliterate($tag['name']);
    }
  }

  return strcmp($cache[__FUNCTION__][ $a['name'] ], $cache[__FUNCTION__][ $b['name'] ]);
}

/**
 * Exits the current script (or redirect to login page if not logged).
 */
function access_denied()
{
  global $user, $conf;

  $login_url =
      get_root_url().'identification.php?redirect='
      .urlencode(urlencode($_SERVER['REQUEST_URI']));

  if ( isset($user) and !is_a_guest() )
  {
    set_status_header(401);

    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
    echo '<div style="text-align:center;">'.l10n('You are not authorized to access the requested page').'<br>';
    echo '<a href="'.get_root_url().'identification.php">'.l10n('Identification').'</a>&nbsp;';
    echo '<a href="'.make_index_url().'">'.l10n('Home').'</a></div>';
    echo str_repeat( ' ', 512); //IE6 doesn't error output if below a size
    exit();
  }
  elseif (!$conf['guest_access'] and is_a_guest())
  {
    redirect_http($login_url);
  }
  else
  {
    redirect_html($login_url);
  }
}

/**
 * Exits the current script with 403 code.
 * @todo nice display if $template loaded
 *
 * @param string $msg
 * @param string|null $alternate_url redirect to this url
 */
function page_forbidden($msg, $alternate_url=null)
{
  set_status_header(403);
  if ($alternate_url==null)
    $alternate_url = make_index_url();
  redirect_html( $alternate_url,
    '<div style="text-align:left; margin-left:5em;margin-bottom:5em;">
<h1 style="text-align:left; font-size:36px;">'.l10n('Forbidden').'</h1><br>'
.$msg.'</div>',
    5 );
}

/**
 * Exits the current script with 400 code.
 * @todo nice display if $template loaded
 *
 * @param string $msg
 * @param string|null $alternate_url redirect to this url
 */
function bad_request($msg, $alternate_url=null)
{
  set_status_header(400);
  if ($alternate_url==null)
    $alternate_url = make_index_url();
  redirect_html( $alternate_url,
    '<div style="text-align:left; margin-left:5em;margin-bottom:5em;">
<h1 style="text-align:left; font-size:36px;">'.l10n('Bad request').'</h1><br>'
.$msg.'</div>',
    5 );
}

/**
 * Exits the current script with 404 code.
 * @todo nice display if $template loaded
 *
 * @param string $msg
 * @param string|null $alternate_url redirect to this url
 */
function page_not_found($msg, $alternate_url=null)
{
  set_status_header(404);
  if ($alternate_url==null)
    $alternate_url = make_index_url();
  redirect_html( $alternate_url,
    '<div style="text-align:left; margin-left:5em;margin-bottom:5em;">
<h1 style="text-align:left; font-size:36px;">'.l10n('Page not found').'</h1><br>'
.$msg.'</div>',
    5 );
}

/**
 * Exits the current script with 500 code.
 * @todo nice display if $template loaded
 *
 * @param string $msg
 * @param string|null $title
 * @param bool $show_trace
 */
function fatal_error($msg, $title=null, $show_trace=true)
{
  if (empty($title))
  {
    $title = l10n('Piwigo encountered a non recoverable error');
  }

  $btrace_msg = '';
  if ($show_trace and function_exists('debug_backtrace'))
  {
    $bt = debug_backtrace();
    for ($i=1; $i<count($bt); $i++)
    {
      $class = isset($bt[$i]['class']) ? (@$bt[$i]['class'].'::') : '';
      $btrace_msg .= "#$i\t".$class.@$bt[$i]['function'].' '.@$bt[$i]['file']."(".@$bt[$i]['line'].")\n";
    }
    $btrace_msg = trim($btrace_msg);
    $msg .= "\n";
  }

  $display = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<h1>$title</h1>
<pre style='font-size:larger;background:white;color:red;padding:1em;margin:0;clear:both;display:block;width:auto;height:auto;overflow:auto'>
<b>$msg</b>
$btrace_msg
</pre>\n";

  @set_status_header(500);
  echo $display.str_repeat( ' ', 300); //IE6 doesn't error output if below a size

  if ( function_exists('ini_set') )
  {// if possible turn off error display (we display it)
    ini_set('display_errors', false);
  }
  error_reporting( E_ALL );
  trigger_error( strip_tags($msg).$btrace_msg, E_USER_ERROR );
  die(0); // just in case
}

/**
 * Returns the breadcrumb to be displayed above thumbnails on tag page.
 *
 * @return string
 */
function get_tags_content_title()
{
  global $page;
  $title = '<a href="'.get_root_url().'tags.php" title="'.l10n('display available tags').'">'
    . l10n( count($page['tags']) > 1 ? 'Tags' : 'Tag' )
    . '</a> ';

  for ($i=0; $i<count($page['tags']); $i++)
  {
    $title.= $i>0 ? ' + ' : '';

    $title.=
      '<a href="'
      .make_index_url(
        array(
          'tags' => array( $page['tags'][$i] )
          )
        )
      .'" title="'
      .l10n('display photos linked to this tag')
      .'">'
      .trigger_change('render_tag_name', $page['tags'][$i]['name'], $page['tags'][$i])
      .'</a>';

    if (count($page['tags']) > 1)
    {
      $other_tags = $page['tags'];
      unset($other_tags[$i]);
      $remove_url = make_index_url(
        array(
          'tags' => $other_tags
          )
        );

      $title.=
        '<a id="TagsGroupRemoveTag" href="'.$remove_url.'" style="border:none;" title="'
        .l10n('remove this tag from the list')
        .'"><img src="'
          .get_root_url().get_themeconf('icon_dir').'/remove_s.png'
        .'" alt="x" style="vertical-align:bottom;" >'
        .'<span class="pwg-icon pwg-icon-close" ></span>'
        .'<i class="fas fa-plus" aria-hidden="true"></i>'
        .'</a>';
    }
  }
  return $title;
}

/**
 * Returns the breadcrumb to be displayed above thumbnails on combined categories page.
 *
 * @return string
 */
function get_combined_categories_content_title()
{
  global $page;

  $title = l10n('Albums').' ';

  $is_first = true;
  $all_categories = array_merge(array($page['category']), $page['combined_categories']);
  foreach ($all_categories as $idx => $category)
  {
    $title.= $is_first ? '' : ' + ';
    $is_first = false;

    $title.= get_cat_display_name(array($category));

    if (count($all_categories) > 1) // should be always the case
    {
      $other_cats = $all_categories;
      unset($other_cats[$idx]);

      $params = array(
        'category' => array_shift($other_cats),
        );

      if (count($other_cats) > 0)
      {
        $params['combined_categories'] = $other_cats;
      }
      $remove_url = make_index_url($params);

      $title.=
        '<a id="TagsGroupRemoveTag" href="'.$remove_url.'" style="border:none;" title="'
        .l10n('remove this tag from the list')
        .'"><img src="'
          .get_root_url().get_themeconf('icon_dir').'/remove_s.png'
        .'" alt="x" style="vertical-align:bottom;" >'
        .'<span class="pwg-icon pwg-icon-close" ></span>'
        .'</a>';
    }
  }

  return $title;
}

/**
 * Sets the http status header (200,401,...)
 * @param int $code
 * @param string $text for exotic http codes
 */
function set_status_header($code, $text='')
{
  if (empty($text))
  {
    switch ($code)
    {
      case 200: $text='OK';break;
      case 301: $text='Moved permanently';break;
      case 302: $text='Moved temporarily';break;
      case 304: $text='Not modified';break;
      case 400: $text='Bad request';break;
      case 401: $text='Authorization required';break;
      case 403: $text='Forbidden';break;
      case 404: $text='Not found';break;
      case 500: $text='Server error';break;
      case 501: $text='Not implemented';break;
      case 503: $text='Service unavailable';break;
    }
  }
  $protocol = $_SERVER["SERVER_PROTOCOL"];
  if ( ('HTTP/1.1' != $protocol) && ('HTTP/1.0' != $protocol) )
    $protocol = 'HTTP/1.0';

  header( "$protocol $code $text", true, $code );
  trigger_notify('set_status_header', $code, $text);
}

/**
 * Returns the category comment for rendering in html textual mode (subcatify)
 * This method is called by a trigger_notify()
 *
 * @param string $desc
 * @return string
 */
function render_category_literal_description($desc)
{
  !isset($desc) ? $desc = "" : false;
  return strip_tags($desc, '<span><p><a><br><b><i><small><big><strong><em>');
}

/**
 * Add known menubar blocks.
 * This method is called by a trigger_change()
 *
 * @param BlockManager[] $menu_ref_arr
 */
function register_default_menubar_blocks($menu_ref_arr)
{
  $menu = & $menu_ref_arr[0];
  if ($menu->get_id() != 'menubar')
    return;
  $menu->register_block( new RegisteredBlock( 'mbLinks', 'Links', 'piwigo'));
  $menu->register_block( new RegisteredBlock( 'mbCategories', 'Albums', 'piwigo'));
  $menu->register_block( new RegisteredBlock( 'mbTags', 'Related tags', 'piwigo'));
  $menu->register_block( new RegisteredBlock( 'mbSpecials', 'Specials', 'piwigo'));
  $menu->register_block( new RegisteredBlock( 'mbMenu', 'Menu', 'piwigo'));
  $menu->register_block( new RegisteredBlock( 'mbRelatedCategories', 'Related albums', 'piwigo') );

  // We hide the quick identification menu on the identification page. It
  // would be confusing.
  if (script_basename() != 'identification')
  {
    $menu->register_block( new RegisteredBlock( 'mbIdentification', 'Identification', 'piwigo') );
  }
}

/**
 * Returns display name for an element.
 * Returns 'name' if exists of name from 'file'.
 *
 * @param array $info at least file or name
 * @return string
 */
function render_element_name($info)
{
  if (!empty($info['name']))
  {
    return trigger_change('render_element_name', $info['name']);
  }
  return get_name_from_file($info['file']);
}

/**
 * Returns display description for an element.
 *
 * @param array $info at least comment
 * @param string $param used to identify the trigger
 * @return string
 */
function render_element_description($info, $param='')
{
  if (!empty($info['comment']))
  {
    return trigger_change('render_element_description', $info['comment'], $param);
  }
  return '';
}

/**
 * Add info to the title of the thumbnail based on photo properties.
 *
 * @param array $info hit, rating_score, nb_comments
 * @param string $title
 * @param string $comment
 * @return string
 */
function get_thumbnail_title($info, $title, $comment='')
{
  global $conf, $user;

  $details = array();

  if (!empty($info['hit']))
  {
    $details[] = $info['hit'].' '.strtolower(l10n('Visits'));
  }

  if ($conf['rate'] and !empty($info['rating_score']))
  {
    $details[] = strtolower(l10n('Rating score')).' '.$info['rating_score'];
  }

  if (isset($info['nb_comments']) and $info['nb_comments'] != 0)
  {
    $details[] = l10n_dec('%d comment', '%d comments', $info['nb_comments']);
  }

  if (count($details) > 0)
  {
    $title.= ' ('.implode(', ', $details).')';
  }

  if (!empty($comment))
  {
    $comment = strip_tags($comment);
    $title.= ' '.substr($comment, 0, 100).(strlen($comment) > 100 ? '...' : '');
  }

  $title = htmlspecialchars(strip_tags($title));
  $title = trigger_change('get_thumbnail_title', $title, $info);

  return $title;
}

/**
 * Event handler to protect src image urls.
 *
 * @param string $url
 * @param SrcImage $src_image
 * @return string
 */
function get_src_image_url_protection_handler($url, $src_image)
{
  return get_action_url($src_image->id, $src_image->is_original() ? 'e' : 'r', false);
}

/**
 * Event handler to protect element urls.
 *
 * @param string $url
 * @param array $infos id, path
 * @return string
 */
function get_element_url_protection_handler($url, $infos)
{
  global $conf;
  if ('images'==$conf['original_url_protection'])
  {// protect only images and not other file types (for example large movies that we don't want to send through our file proxy)
    $ext = get_extension($infos['path']);
    if (!in_array($ext, $conf['picture_ext']))
    {
      return $url;
    }
  }
  return get_action_url($infos['id'], 'e', false);
}

/**
 * Sends to the template all messages stored in $page and in the session.
 */
function flush_page_messages()
{
  global $template, $page;
  if ($template->get_template_vars('page_refresh') === null)
  {
    foreach (array('errors','infos','warnings', 'messages') as $mode)
    {
      if (isset($_SESSION['page_'.$mode]))
      {
        $page[$mode] = array_merge($page[$mode], $_SESSION['page_'.$mode]);
        unset($_SESSION['page_'.$mode]);
      }

      if (count($page[$mode]) != 0)
      {
        $template->assign($mode, $page[$mode]);
      }
    }
  }
}

?>
