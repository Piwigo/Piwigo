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

function get_frame_start()
{
  return '<table style="padding:0px;border-collapse:collapse; width:';
}

function get_frame_begin()
{
  global $user;
  $path = './template/'.$user['template'].'/theme/';
  $size_01 = getimagesize( $path.'01.gif' );
  $size_02 = getimagesize( $path.'02.gif' );
  $size_03 = getimagesize( $path.'03.gif' );
  return ';">
            <tr>
              <td><img src="'.$path.'01.gif" style="margin:auto;width:'.$size_01[0].'px;display:box;" alt="" /></td>
              <td><img src="'.$path.'02.gif" style="margin:auto;display:box;width:100%;height:'.$size_02[1].'px;" alt="" /></td>
              <td><img src="'.$path.'03.gif" style="margin:auto;display:box;width:'.$size_03[0].'px;" alt="" /></td>
            </tr>
            <tr>
              <td style="margin:autox;background:url('.$path.'04.gif);"></td>
              <td style="margin:auto;background:url('.$path.'05.gif);width:100%;">';
}
        
function get_frame_end()
{
  global $user;
  $path = './template/'.$user['template'].'/theme/';
  $size_08 = getimagesize( $path.'08.gif' );
  return '
              </td>
              <td style="margin:auto;background:url('.$path.'06.gif);"></td>
            </tr>
            <tr >
              <td><img src="'.$path.'07.gif" style="margin:auto;" alt="" /></td>
              <td><img src="'.$path.'08.gif" style="margin:auto;width:100%;height:'.$size_08[1].'px;" alt="" /></td>
              <td><img src="'.$path.'09.gif" style="margin:auto;" alt="" /></td>
            </tr>   
          </table>';
}

function initialize_template()
{
  global $template, $user, $lang;
  
  $template->assign_vars(array(
	'T_START' => get_frame_start(),
	'T_BEGIN' => get_frame_begin(),
	'T_END' =>  get_frame_end()
	)
	);
}

function make_jumpbox($value, $selected, $usekeys=false)
{
  $boxstring = '';
  $nb = sizeof( $value);
  $keys = ($usekeys?array_keys($value):$value);
  $value = ($usekeys?array_values($value):$value);
  for ( $i = 0; $i < $nb; $i++ )
  {
    $boxstring .= '<option value="'.$keys[$i].'"';
    if ($selected == $keys[$i]) $boxstring .=' selected="selected"';
    $boxstring .='>'.$value[$i].'</option>';
  }
  return $boxstring;
}

function make_radio($name, $value, $selected, $usekeys=false)
{
  $boxstring = '';
  $nb = sizeof( $value);
  $keys = ($usekeys?array_keys($value):$value);
  $value = ($usekeys?array_values($value):$value);
  for ( $i = 0; $i < $nb; $i++ )
  {
    $boxstring .= '<input type="radio" name="'.$name.'" value="'.$keys[$i].'"';
    if ($selected == $keys[$i]) $boxstring .=' checked';
    $boxstring .='/>'.$value[$i];
  }
  return $boxstring;
}
?>
