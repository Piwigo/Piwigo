<?php
// +-----------------------------------------------------------------------+
// |                         functions_html.inc.php                        |
// +-----------------------------------------------------------------------+
// | application   : PhpWebGallery <http://phpwebgallery.net>              |
// | branch        : BSF (Best So Far)                                     |
// +-----------------------------------------------------------------------+
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
    $icon_url.= 'recent.gif';
    $title .= $user['recent_period'];
    $title .=  '&nbsp;'.$lang['days'];
    $size = getimagesize( $icon_url );
    $output = '<img title="'.$title.'" src="'.$icon_url.'" style="border:0;';
    $output.= 'height:'.$size[1].'px;width:'.$size[0].'px" alt="" />';
  }
  return $output;
}

function create_navigation_bar( $url, $nb_element, $start,
                                $nb_element_page, $link_class )
{
  global $lang;
  $navigation_bar = "";
  // 0. détection de la page en cours
  if( !isset( $start )
      || !is_numeric( $start )
      || ( is_numeric( $start ) && $start < 0 ) )
  {
    $start = 0;
  }
  // on n'affiche la bare de navigation que si on plus de 1 page
  if ( $nb_element > $nb_element_page )
  {
    // 1.une page précédente ?
    if ( $start != 0 )
    {
      $previous = $start - $nb_element_page;
      $navigation_bar.= '<a href="';
      $navigation_bar.= add_session_id( $url.'&amp;start='.$previous );
      $navigation_bar.= '" class="'.$link_class.'">'.$lang['previous_page'];
      $navigation_bar.= '</a>';
      $navigation_bar.= ' | ';
    }
    // 2.liste des numéros de page
    $maximum = ceil ( $nb_element / $nb_element_page );
    for ( $i = 1; $i <= $maximum; $i++ )
    {
      $temp_start = ( $i - 1 ) * $nb_element_page;
      if ( $temp_start == $start )
      {
        $navigation_bar.= ' <span class="pageNumberSelected">'.$i.'</span> ';
      }
      else
      {
        $navigation_bar.= ' <a href="';
        $navigation_bar.= add_session_id( $url.'&amp;start='.$temp_start );
        $navigation_bar.= '" class="'.$link_class.'">'.$i.'</a> ';
      }
    }
    // 3.une page suivante ?
    if ( $nb_element > $nb_element_page
         && $start + $nb_element_page < $nb_element )
    {
      $next = $start + $nb_element_page;
      $navigation_bar.= ' | <a href="';
      $navigation_bar.= add_session_id( $url.'&amp;start='.$next );
      $navigation_bar.= '" class="'.$link_class.'">'.$lang['next_page'].'</a>';
    }
  }
  return $navigation_bar;
}

//
// Pick a language, any language ...
//
function language_select($default, $select_name = "language")
{
  $available_lang = get_languages();

  $lang_select = '<select name="' . $select_name . '" onchange="this.form.submit()">';
  foreach ($available_lang as $code => $displayname)
  {
    $selected = ( strtolower($default) == strtolower($code) ) ? ' selected="selected"' : '';
    $lang_select .= '<option value="' . $code . '"' . $selected . '>' . ucwords($displayname) . '</option>';
  }
  $lang_select .= '</select>';

  return $lang_select;
}

//
// Pick a template/theme combo, 
//
function style_select($default_style, $select_name = "style")
{
  $templates = get_templates();

  $style_selected = '<select name="' . $select_name . '" >';
  foreach ($templates as $template)
  {
    $selected = ( $template == $default_style ) ? ' selected="selected"' : '';
    $style_selected.= '<option value="'.$template.'"'.$selected.'>';
    $style_selected.= $template.'</option>';
  }
  $style_selected .= '</select>';
  return $style_selected;
}

// The function get_cat_display_name returns a string containing the list
// of upper categories to the root category from the lowest category shown
// example : "anniversaires - fete mere 2002 - animaux - erika"
// You can give this parameters :
//   - $style : the style of the span tag for the lowest category,
//     "font-style:italic;" for example
function get_cat_display_name( $cat_informations, $separator, 
  $url = 'category.php?cat=', $replace_space = true)
{
  $output = '';
  $i=0;
  while ( list ($id, $name) = each($cat_informations)) 
  {
    if ( $i )  $output.= $separator;
	$i++;
	if (empty($style) && empty($url) || ($i == count( $cat_informations))) 
	  $output.= $name;
    elseif (!empty($url))
      $output.= '<a class="" href="'.add_session_id(PHPWG_ROOT_PATH.$url.$id).'">'.$name."</a>";
	else
      $output.= '<span style="'.$style.'">'.$name.'</span>';
  }
  if ( $replace_space ) return replace_space( $output );
  else                  return $output;
}

/**
 * returns the HTML code for a category item in the menu (for category.php)
 *
 * HTML code generated uses logical list tags ul and each category is an
 * item li. The paramter given is the category informations as an array,
 * used keys are : id, name, dir, nb_images, date_last, subcats (sub-array)
 *
 * @param array category
 * @return string
 */
function get_html_menu_category($category)
{
  global $page, $lang;

  $menu = '

           <li>';
  
  $url = add_session_id(PHPWG_ROOT_PATH.'category.php?cat='.$category['id']);

  $class = '';
  if (isset($page['cat'])
      and is_numeric($page['cat'])
      and $category['id'] == $page['cat'])
  {
    $class = 'menuCategorySelected';
  }
  else
  {
    $class = 'menuCategoryNotSelected';
  }
  
  $name = $category['name'];
  if (empty($name))
  {
    $name = str_replace('_', ' ', $category['dir']);
  }

  $menu.= '
           <a href="'.$url.'"
              title="'.$lang['hint_category'].'"
              class="'.$class.'">
             '.$name.'
           </a>';

  if ($category['nb_images'] > 0)
  {
    $menu.= '
             <span class="menuInfoCat"
                   title="'.$category['nb_images'].'
                          '.$lang['images_available'].'">
             ['.$category['nb_images'].']
             </span>
             '.get_icon($category['date_last']);
  }
  
  // recursive call
  if ($category['expanded'] and count($category['subcats']) > 0)
  {
    $menu.= '
             <ul class="menu">';
    foreach ($category['subcats'] as $subcat)
    {
      $menu.= get_html_menu_category($subcat);
    }
    $menu.= '
             </ul>';
  }

  $menu.= '</li>';

  return $menu;
}
?>
