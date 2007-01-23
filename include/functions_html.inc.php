<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
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

function get_icon($date, $is_child_date = false)
{
  global $page, $user, $lang;

  if (empty($date))
  {
    return '';
  }

  if (isset($page['get_icon_cache'][$date]))
  {
    if (! $page['get_icon_cache'][$date] )
      return '';
    return $page['get_icon_cache']['_icons_'][$is_child_date];
  }

  if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $date, $matches))
  {// date can be empty, no icon to display
    $page['get_icon_cache'][$date] = false;
    return '';
  }

  list($devnull, $year, $month, $day) = $matches;
  $unixtime = mktime( 0, 0, 0, $month, $day, $year );
  if ($unixtime === false  // PHP 5.1.0 and above
      or $unixtime === -1) // PHP prior to 5.1.0
  {
    $page['get_icon_cache'][$date] = false;
    return '';
  }

  $diff = time() - $unixtime;
  $day_in_seconds = 24*60*60;
  $page['get_icon_cache'][$date] = false;
  if ( $diff < $user['recent_period'] * $day_in_seconds )
  {
    if ( !isset($page['get_icon_cache']['_icons_'] ) )
    {
      $icons = array(false => 'recent', true => 'recent_by_child' );
      $title = $lang['recent_image'].'&nbsp;'.$user['recent_period']
          .'&nbsp;'.$lang['days'];
      foreach ($icons as $key => $icon)
      {
        $icon_url = get_themeconf('icon_dir').'/'.$icon.'.png';
        $size = getimagesize( PHPWG_ROOT_PATH.$icon_url );
        $icon_url = get_root_url().$icon_url;
        $output = '<img title="'.$title.'" src="'.$icon_url.'" class="icon" style="border:0;';
        $output.= 'height:'.$size[1].'px;width:'.$size[0].'px" alt="(!)" />';
        $page['get_icon_cache']['_icons_'][$key] = $output;
      }
    }
    $page['get_icon_cache'][$date] = true;
  }
  if (! $page['get_icon_cache'][$date] )
    return '';
  return $page['get_icon_cache']['_icons_'][$is_child_date];
}


function create_navigation_bar(
  $url, $nb_element, $start, $nb_element_page, $clean_url = false
  )
{
  global $lang, $conf;

  $pages_around = $conf['paginate_pages_around'];
  $start_str = $clean_url ? '/start-' :
    ( ( strstr($url, '?')===false ? '?':'&amp;') . 'start=' );

  $navbar = '';

  // current page detection
  if (!isset($start)
      or !is_numeric($start)
      or (is_numeric($start) and $start < 0))
  {
    $start = 0;
  }

  // navigation bar useful only if more than one page to display !
  if ($nb_element > $nb_element_page)
  {
    // current page and last page
    $cur_page = ceil($start / $nb_element_page) + 1;
    $maximum = ceil($nb_element / $nb_element_page);

    // link to first page ?
    if ($cur_page != 1)
    {
      $navbar.=
        '<a href="'.$url.'" rel="start">'
        .$lang['first_page']
        .'</a>';
    }
    else
    {
      $navbar.= $lang['first_page'];
    }
    $navbar.= ' | ';
    // link on previous page ?
    if ($start != 0)
    {
      $previous = $start - $nb_element_page;

      $navbar.=
        '<a href="'
        .$url.($previous > 0 ? $start_str.$previous : '')
        .'" rel="prev">'
        .$lang['previous_page']
        .'</a>';
    }
    else
    {
      $navbar.= $lang['previous_page'];
    }
    $navbar.= ' |';

    if ($cur_page > $pages_around + 1)
    {
      $navbar.= '&nbsp;<a href="'.$url.'">1</a>';

      if ($cur_page > $pages_around + 2)
      {
        $navbar.= ' ...';
      }
    }

    // inspired from punbb source code
    for ($i = $cur_page - $pages_around, $stop = $cur_page + $pages_around + 1;
         $i < $stop;
         $i++)
    {
      if ($i < 1 or $i > $maximum)
      {
        continue;
      }
      else if ($i != $cur_page)
      {
        $temp_start = ($i - 1) * $nb_element_page;

        $navbar.=
          '&nbsp;'
          .'<a href="'.$url
          .($temp_start > 0 ? $start_str.$temp_start : '')
          .'">'
          .$i
          .'</a>';
      }
      else
      {
        $navbar.=
          '&nbsp;'
          .'<span class="pageNumberSelected">'
          .$i
          .'</span>';
      }
    }

    if ($cur_page < ($maximum - $pages_around))
    {
      $temp_start = ($maximum - 1) * $nb_element_page;

      if ($cur_page < ($maximum - $pages_around - 1))
      {
        $navbar.= ' ...';
      }

      $navbar.= ' <a href="'.$url.$start_str.$temp_start.'">'.$maximum.'</a>';
    }

    $navbar.= ' | ';
    // link on next page ?
    if ($nb_element > $nb_element_page
        and $start + $nb_element_page < $nb_element)
    {
      $next = $start + $nb_element_page;

      $navbar.=
        '<a href="'.$url.$start_str.$next.'" rel="next">'
        .$lang['next_page']
        .'</a>';
    }
    else
    {
      $navbar.= $lang['next_page'];
    }

    $navbar.= ' | ';
    // link to last page ?
    if ($cur_page != $maximum)
    {
      $temp_start = ($maximum - 1) * $nb_element_page;

      $navbar.=
        '<a href="'.$url.$start_str.$temp_start.'" rel="last">'
        .$lang['last_page']
        .'</a>';
    }
    else
    {
      $navbar.= $lang['last_page'];
    }
  }
  return $navbar;
}

//
// Pick a language, any language ...
//
function language_select($default, $select_name = "language")
{
  $available_lang = get_languages();

  $lang_select = '<select name="' . $select_name . '">';
  foreach ($available_lang as $code => $displayname)
  {
    $selected = ( strtolower($default) == strtolower($code) ) ? ' selected="selected"' : '';
    $lang_select .= '<option value="' . $code . '"' . $selected . '>' . ucwords($displayname) . '</option>';
  }
  $lang_select .= '</select>';

  return $lang_select;
}

/**
 * returns the list of categories as a HTML string
 *
 * categories string returned contains categories as given in the input
 * array $cat_informations. $cat_informations array must be an association
 * of {category_id => category_name}. If url input parameter is null,
 * returns only the categories name without links.
 *
 * @param array cat_informations
 * @param string url
 * @param boolean replace_space
 * @return string
 */
function get_cat_display_name($cat_informations,
                              $url = '',
                              $replace_space = true)
{
  global $conf;

  $output = '';
  $is_first = true;
  foreach ($cat_informations as $id => $name)
  {
    if ($is_first)
    {
      $is_first = false;
    }
    else
    {
      $output.= $conf['level_separator'];
    }

    if ( !isset($url) )
    {
      $output.= $name;
    }
    elseif ($url == '')
    {
      $output.= '<a class=""';
      $output.= ' href="'
            .make_index_url(
                array(
                  'category'=>$id,
                  'cat_name'=>$name
                  )
              )
            .'">';
      $output.= $name.'</a>';
    }
    else
    {
      $output.= '<a class=""';
      $output.= ' href="'.PHPWG_ROOT_PATH.$url.$id.'">';
      $output.= $name.'</a>';
    }
  }
  if ($replace_space)
  {
    return replace_space($output);
  }
  else
  {
    return $output;
  }
}

/**
 * returns the list of categories as a HTML string, with cache of names
 *
 * categories string returned contains categories as given in the input
 * array $cat_informations. $uppercats is the list of category ids to
 * display in the right order. If url input parameter is empty, returns only
 * the categories name without links.
 *
 * @param string uppercats
 * @param string url
 * @param boolean replace_space
 * @return string
 */
function get_cat_display_name_cache($uppercats,
                                    $url = '',
                                    $replace_space = true)
{
  global $cat_names, $conf;

  if (!isset($cat_names))
  {
    $query = '
SELECT id,name
  FROM '.CATEGORIES_TABLE.'
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      $cat_names[$row['id']] = $row['name'];
    }
  }

  $output = '';
  $is_first = true;
  foreach (explode(',', $uppercats) as $category_id)
  {
    $name = $cat_names[$category_id];

    if ($is_first)
    {
      $is_first = false;
    }
    else
    {
      $output.= $conf['level_separator'];
    }

    if ( !isset($url) )
    {
      $output.= $name;
    }
    elseif ($url == '')
    {
      $output.= '
<a class=""
   href="'
      .make_index_url(
          array(
            'category'=>$category_id,
            'cat_name'=>$name
            )
        )
      .'">'.$name.'</a>';
    }
    else
    {
      $output.= '
<a class=""
   href="'.PHPWG_ROOT_PATH.$url.$category_id.'">'.$name.'</a>';
    }
  }
  if ($replace_space)
  {
    return replace_space($output);
  }
  else
  {
    return $output;
  }
}

/**
 * returns the HTML code for a category item in the menu (for the main page)
 *
 * HTML code generated uses logical list tags ul and each category is an
 * item li. The paramter given is the category informations as an array,
 * used keys are : id, name, nb_images, max_date_last, date_last
 * count_images, count_categories
 *
 * @param array categories
 * @return string
 */
function get_html_menu_category($categories)
{
  global $page, $lang;

  $ref_level = 0;
  $level = 0;
  $menu = '';

  // $page_cat value remains 0 for special sections
  $page_cat = 0;
  if (isset($page['category']))
  {
    $page_cat = $page['category'];
  }

  foreach ($categories as $category)
  {
    $level = substr_count($category['global_rank'], '.') + 1;
    if ($level > $ref_level)
    {
      $menu.= "\n<ul>";
    }
    else if ($level == $ref_level)
    {
      $menu.= "\n</li>";
    }
    else if ($level < $ref_level)
    {
      // we may have to close more than one level at the same time...
      $menu.= "\n</li>";
      $menu.= str_repeat("\n</ul></li>",($ref_level-$level));
    }
    $ref_level = $level;

    $menu.= "\n\n".'<li';
    if ($category['id'] == $page_cat)
    {
      $menu.= ' class="selected"';
    }
    $menu.= '>';

    $url = make_index_url(
            array(
              'category'=>$category['id'],
              'cat_name'=>$category['name']
              )
            );

    $menu.= "\n".'<a href="'.$url.'"';
    if ($page_cat != 0
        and $category['id'] == $page['cat_id_uppercat'])
    {
      $menu.= ' rel="up"';
    }
    $menu.= '>'.$category['name'].'</a>';

    // Count of category is main
    // if not picture on categorie, test on sub-categories
    if (($category['nb_images'] > 0) or ($category['count_images'] > 0))
    {
      $menu.= "\n".'<span class="';
      $menu.= ($category['nb_images'] > 0 ? "menuInfoCat"
                                          : "menuInfoCatByChild").'"';
      $menu.= ' title="';
      $menu.= ' '.get_display_images_count
                  (
                    $category['nb_images'],
                    $category['count_images'],
                    $category['count_categories'],
                    false
                  ).'">';
      $menu.= '['.($category['nb_images'] > 0 ? $category['nb_images']
                                              : $category['count_images']).']';
      $menu.= '</span>';
    }
    $child_date_last = @$category['max_date_last']> @$category['date_last'];
    $menu.= get_icon($category['max_date_last'], $child_date_last);
  }

  $menu.= str_repeat("\n</li></ul>",($level));

  return $menu;
}

/**
 * returns HTMLized comment contents retrieved from database
 *
 * newlines becomes br tags, _word_ becomes underline, /word/ becomes
 * italic, *word* becomes bolded
 *
 * @param string content
 * @return string
 */
function parse_comment_content($content)
{
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

  $content = '<div>'.$content.'</div>';
  return $content;
}

function get_cat_display_name_from_id($cat_id,
                                      $url = '',
                                      $replace_space = true)
{
  $cat_info = get_cat_info($cat_id);
  return get_cat_display_name($cat_info['name'], $url, $replace_space);
}

/**
 * Returns an HTML list of tags. It can be a multi select field or a list of
 * checkboxes.
 *
 * @param string HTML field name
 * @param array selected tag ids
 * @return array
 */
function get_html_tag_selection(
  $tags,
  $fieldname,
  $selecteds = array(),
  $forbidden_categories = null
  )
{
  global $conf;

  if (count ($tags) == 0 )
  {
    return '';
  }
  $output = '<ul class="tagSelection">';
  foreach ($tags as $tag)
  {
    $output.=
      '<li>'
      .'<label>'
      .'<input type="checkbox" name="'.$fieldname.'[]"'
      .' value="'.$tag['tag_id'].'"'
      ;

    if (in_array($tag['tag_id'], $selecteds))
    {
      $output.= ' checked="checked"';
    }

    $output.=
      ' />'
      .' '. $tag['name']
      .'</label>'
      .'</li>'
      ."\n"
      ;
  }
  $output.= '</ul>';

  return $output;
}

function name_compare($a, $b)
{
  return strcmp(strtolower($a['name']), strtolower($b['name']));
}

/**
 * exits the current script (either exit or redirect)
 */
function access_denied()
{
  global $user, $lang;

  $login_url =
      get_root_url().'identification.php?redirect='
      .urlencode(urlencode($_SERVER['REQUEST_URI']));

  if ( isset($user['is_the_guest']) and !$user['is_the_guest'] )
  {
    echo '<div style="text-align:center;">'.$lang['access_forbiden'].'<br />';
    echo '<a href="'.get_root_url().'identification.php">'.$lang['identification'].'</a>&nbsp;';
    echo '<a href="'.make_index_url().'">'.$lang['home'].'</a></div>';
    exit();
  }
  else
  {
    set_status_header(401);
    redirect_html($login_url);
  }
}

/**
 * exits the current script with 403 code
 * @param string msg a message to display
 * @param string alternate_url redirect to this url
 */
function page_forbidden($msg, $alternate_url=null)
{
  set_status_header(403);
  if ($alternate_url==null)
    $alternate_url = make_index_url();
  redirect_html( $alternate_url,
    '<div style="text-align:left; margin-left:5em;margin-bottom:5em;">
<h1 style="text-align:left; font-size:36px;">Forbidden</h1><br/>'
.$msg.'</div>',
    5 );
}

/**
 * exits the current script with 404 code when a page cannot be found
 * @param string msg a message to display
 * @param string alternate_url redirect to this url
 */
function page_not_found($msg, $alternate_url=null)
{
  set_status_header(404);
  if ($alternate_url==null)
    $alternate_url = make_index_url();
  redirect_html( $alternate_url,
    '<div style="text-align:left; margin-left:5em;margin-bottom:5em;">
<h1 style="text-align:left; font-size:36px;">Page not found</h1><br/>'
.$msg.'</div>',
    5 );
}

/* returns the title to be displayed above thumbnails on tag page
 */
function get_tags_content_title()
{
  global $page;
  $title = count($page['tags']) > 1 ? l10n('Tags') : l10n('Tag');
  $title.= ' ';

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
      .l10n('See pictures linked to this tag only')
      .'">'
      .$page['tags'][$i]['name']
      .'</a>';

    if ( count($page['tags'])>2 )
    {
      $other_tags = $page['tags'];
      unset ( $other_tags[$i] );
      $title.=
        '<a href="'
        .make_index_url(
          array(
            'tags' => $other_tags
            )
          )
        .'" style="border:none;" title="'
        .l10n('remove this tag')
        .'"><img src="'
        .get_root_url().get_themeconf('icon_dir').'/remove_s.png'
        .'" alt="x" style="vertical-align:bottom;" class="button"/>'
        .'</a>';
    }

  }
  return $title;
}

/**
  Sets the http status header (200,401,...)
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
    }
  }
  header("HTTP/1.1 $code $text");
  header("Status: $code $text");
  trigger_action('set_status_header', $code, $text);
}
?>
