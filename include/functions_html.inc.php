<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
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

function get_icon( $date )
{
  global $user, $conf, $lang;

  if (!preg_match('/\d{4}-\d{2}-\d{2}/', $date))
  {
    return '';
  }

  list( $year,$month,$day ) = explode( '-', $date );
  $unixtime = mktime( 0, 0, 0, $month, $day, $year );
  
  $diff = time() - $unixtime;
  $day_in_seconds = 24*60*60;
  $output = '';
  $title = $lang['recent_image'].'&nbsp;';
  if ( $diff < $user['recent_period'] * $day_in_seconds )
  {
    $icon_url = './template/'.$user['template'].'/theme/';
    $icon_url.= 'recent.png';
    $title .= $user['recent_period'];
    $title .=  '&nbsp;'.$lang['days'];
    $size = getimagesize( $icon_url );
    $output = '<img title="'.$title.'" src="'.$icon_url.'" class="icon" style="border:0;';
    $output.= 'height:'.$size[1].'px;width:'.$size[0].'px" alt="(!)" />';
  }
  return $output;
}

function create_navigation_bar($url, $nb_element, $start,
                               $nb_element_page, $link_class)
{
  global $lang, $conf;

  $pages_around = $conf['paginate_pages_around'];
  
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
      $navbar.= '<a href="';
      $navbar.= add_session_id($url.'&amp;start=0');
      $navbar.= '" class="'.$link_class.'">'.$lang['first_page'];
      $navbar.= '</a>';
    }
    else
    {
      $navbar.= $lang['first_page'];
    }
    $navbar.= ' | ';
    // link on previous page ?
    if ( $start != 0 )
    {
      $previous = $start - $nb_element_page;
      $navbar.= '<a href="';
      $navbar.= add_session_id( $url.'&amp;start='.$previous );
      $navbar.= '" class="'.$link_class.'">'.$lang['previous_page'];
      $navbar.= '</a>';
    }
    else
    {
      $navbar.= $lang['previous_page'];
    }
    $navbar.= ' | ';

    if ($cur_page > $pages_around + 1)
    {
      $navbar.= '&nbsp;<a href="';
      $navbar.= add_session_id($url.'&amp;start=0');
      $navbar.= '" class="'.$link_class.'">1</a>';
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
        $navbar.= '&nbsp;<a href="';
        $navbar.= add_session_id($url.'&amp;start='.$temp_start);
        $navbar.= '" class="'.$link_class.'">'.$i.'</a>';
      }
      else
      {
        $navbar.= '&nbsp;<span class="pageNumberSelected">';
        $navbar.= $i.'</span>';
      }
    }

    if ($cur_page < ($maximum - $pages_around))
    {
      $temp_start = ($maximum - 1) * $nb_element_page;
      if ($cur_page < ($maximum - $pages_around - 1))
      {
        $navbar.= ' ...';
      }
      $navbar.= ' <a href="';
      $navbar.= add_session_id($url.'&amp;start='.$temp_start);
      $navbar.= '" class="'.$link_class.'">'.$maximum.'</a>';
    }
    
    $navbar.= ' | ';
    // link on next page ?
    if ( $nb_element > $nb_element_page
         && $start + $nb_element_page < $nb_element )
    {
      $next = $start + $nb_element_page;
      $navbar.= '<a href="';
      $navbar.= add_session_id( $url.'&amp;start='.$next );
      $navbar.= '" class="'.$link_class.'">'.$lang['next_page'].'</a>';
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
      $navbar.= '<a href="';
      $navbar.= add_session_id($url.'&amp;start='.$temp_start);
      $navbar.= '" class="'.$link_class.'">'.$lang['last_page'];
      $navbar.= '</a>';
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
 * of {category_id => category_name}. If url input parameter is empty,
 * returns only the categories name without links.
 *
 * @param array cat_informations
 * @param string url
 * @param boolean replace_space
 * @return string
 */
function get_cat_display_name($cat_informations,
                              $url = 'category.php?cat=',
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

    if ($url == '')
    {
      $output.= $name;
    }
    else
    {
      $output.= '<a class=""';
      $output.= ' href="'.add_session_id(PHPWG_ROOT_PATH.$url.$id).'">';
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
                                    $url = 'category.php?cat=',
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

    if ($url == '')
    {
      $output.= $name;
    }
    else
    {
      $output.= '
<a class=""
   href="'.add_session_id(PHPWG_ROOT_PATH.$url.$category_id).'">'.$name.'</a>';
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
 * returns the HTML code for a category item in the menu (for category.php)
 *
 * HTML code generated uses logical list tags ul and each category is an
 * item li. The paramter given is the category informations as an array,
 * used keys are : id, name, nb_images, date_last
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
    if (isset($page['cat'])
        and is_numeric($page['cat'])
        and $category['id'] == $page['cat'])
    {
      $menu.= ' class="selected"';
    }
    $menu.= '>';
  
    $url = add_session_id(PHPWG_ROOT_PATH.'category.php?cat='.$category['id']);
    $menu.= "\n".'<a href="'.$url.'">'.$category['name'].'</a>';

    if ($category['nb_images'] > 0)
    {
      $menu.= "\n".'<span class="menuInfoCat"';
      $menu.= ' title="'.$category['nb_images'];
      $menu.= ' '.$lang['images_available'].'">';
      $menu.= '['.$category['nb_images'].']';
      $menu.= '</span>';
      $menu.= get_icon($category['date_last']);
    }
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
  $content = nl2br($content);
  
  // replace _word_ by an underlined word
  $pattern = '/_([^\s]*)_/';
  $replacement = '<span style="text-decoration:underline;">\1</span>';
  $content = preg_replace($pattern, $replacement, $content);
  
  // replace *word* by a bolded word
  $pattern = '/\*([^\s]*)\*/';
  $replacement = '<span style="font-weight:bold;">\1</span>';
  $content = preg_replace($pattern, $replacement, $content);
  
  // replace /word/ by an italic word
  $pattern = '/\/([^\s]*)\//';
  $replacement = '<span style="font-style:italic;">\1</span>';
  $content = preg_replace($pattern, $replacement, $content);

  return $content;
}

function get_cat_display_name_from_id($cat_id,
                                      $url = 'category.php?cat=',
                                      $replace_space = true)
{
  $cat_info = get_cat_info($cat_id);
  return get_cat_display_name($cat_info['name'], $url, $replace_space);
}
?>
