<?php
// +-----------------------------------------------------------------------+
// |                         htmlfunctions.inc.php                         |
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

function get_icon( $date_comparaison )
{
  global $user, $conf, $lang;
  $difference = time() - $date_comparaison;
  $jours = 24*60*60;
  $output = '';
  $title = $lang['recent_image'].'&nbsp;';
  if ( $difference < $user['long_period'] * $jours )
  {
    $icon_url = './template/'.$user['template'].'/theme/';
    if ( $difference < $user['short_period'] * $jours )
    {
      $icon_url.= 'new_short.gif';
    $title .= $user['short_period'];
    }
    else
    {
      $icon_url.= 'new_long.gif';
    $title .= $user['long_period'];
    }
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
        $navigation_bar.= ' <span style="font-weight:bold;">'.$i.'</span> ';
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
  global $lang_info;
  $dir = opendir(PHPWG_ROOT_PATH . 'language');
  $available_lang= array();

  while ( $file = readdir($dir) )
  {
    if (is_dir ( realpath(PHPWG_ROOT_PATH.'language/'.$file) ) 
	  && !is_link(realpath(PHPWG_ROOT_PATH  . 'language/' . $file))
	  && isset($lang_info['language'][$file]))
    {
      $available_lang[$file] = $lang_info['language'][$file];
    }
  }
  closedir($dir);
  @asort($available_lang);
  @reset($available_lang);

  $lang_select = '<select name="' . $select_name . '" onchange="this.form.submit()">';
  while ( list($code, $displayname) = @each($available_lang) )
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
  $dir = opendir(PHPWG_ROOT_PATH . 'template');
  $style_select = '<select name="' . $select_name . '">';
  while ( $file = readdir($dir) )
  {
    if (is_dir ( realpath(PHPWG_ROOT_PATH.'template/'.$file) ) 
	  && !is_link(realpath(PHPWG_ROOT_PATH  . 'template/' . $file))
	  && !strstr($file,'.'))
    {
      $selected = ( $file == $default_style ) ? ' selected="selected"' : '';
	  $style_select .= '<option value="' . $file . '"' . $selected . '>' . $file . '</option>';
    }
  }
  closedir($dir);
  return $style_select;
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
?>
