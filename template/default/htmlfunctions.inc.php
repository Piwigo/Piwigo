<?php
/***************************************************************************
 *            htmlfunctions.inc.php is a part of PhpWebGallery             *
 *                            -------------------                          *
 *   last update          : Wednesday, 25 December 2002                    *
 *   email                : pierrick@z0rglub.com                           *
 *                                                                         *
 ***************************************************************************

 ***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/

function get_icon( $date_comparaison )
{
  global $user, $conf;
  $difference = time() - $date_comparaison;
  $jours = 24*60*60;
  $output = '';
  if ( $difference < $user['long_period'] * $jours )
  {
    $icon_url = './theme/'.$user['theme'].'/';
    if ( $difference < $user['short_period'] * $jours )
    {
      $icon_url.= 'new_short.gif';
    }
    else
    {
      $icon_url.= 'new_long.gif';
    }
    $size = getimagesize( $icon_url );
    $output = '<img src="'.$icon_url.'" style="border:0;';
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
  return '<table style="width:';
}

function get_frame_begin()
{
  return ';">
            <tr>
              <td style="border:1px dashed gray;width:100%;padding:5px;background-color:white;">';
}

function get_frame_end()
{
  return '
              </td>
            </tr>   
          </table>';
}
/*
function get_frame_begin()
{
  global $user;
  $path = './theme/'.$user['theme'].'/';
  $size_01 = getimagesize( $path.'01.gif' );
  $size_02 = getimagesize( $path.'02.gif' );
  $size_03 = getimagesize( $path.'03.gif' );
  return ';">
            <tr>
              <td><img src="'.$path.'01.gif" style="width:'.$size_01[0].'px;display:box;" alt="" /></td>
              <td><img src="'.$path.'02.gif" style="display:box;width:100%;height:'.$size_02[1].'px;" alt="" /></td>
              <td><img src="'.$path.'03.gif" style="display:box;width:'.$size_03[0].'px;" alt="" /></td>
            </tr>
            <tr>
              <td style="background:url('.$path.'04.gif);"></td>
              <td style="background:url('.$path.'05.gif);width:100%;">';
}
        
function get_frame_end()
{
  global $user;
  $path = './theme/'.$user['theme'].'/';
  $size_08 = getimagesize( $path.'08.gif' );
  return '
              </td>
              <td style="background:url('.$path.'06.gif);"></td>
            </tr>
            <tr>
              <td><img src="'.$path.'07.gif" alt="" /></td>
              <td><img src="'.$path.'08.gif" style="width:100%;height:'.$size_08[1].'px;" alt="" /></td>
              <td><img src="'.$path.'09.gif" alt="" /></td>
            </tr>   
          </table>';
}
*/
function initialize_template()
{
  global $vtp, $handle, $user;

  $vtp->setGlobalVar( $handle, 'style', $user['style'] );
  $vtp->setGlobalVar( $handle, 'frame_start', get_frame_start() );
  $vtp->setGlobalVar( $handle, 'frame_begin', get_frame_begin() );
  $vtp->setGlobalVar( $handle, 'frame_end',   get_frame_end() );
  $vtp->setVarF( $handle, 'header',
                 './template/'.$user['template'].'/header.htm' );
  $vtp->setVarF( $handle, 'footer',
                 './template/'.$user['template'].'/footer.htm' );
}
?>