<?php
/***************************************************************************
 *                           htmlfunctions.inc.php                         *
 *                            -------------------                          *
 *   application   : PhpWebGallery 1.3 <http://phpwebgallery.net>          *
 *   author        : Pierrick LE GALL <pierrick@z0rglub.com>               *
 *                                                                         *
 *   $Id$
 *                                                                         *
 ***************************************************************************

 ***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/

//include( PREFIX_INCLUDE.'./template/'.$user['template'].'/theme/conf.php' );
$user['lien_expanded']='./template/'.$user['template'].'/theme/expanded.gif';
$user['lien_collapsed']='./template/'.$user['template'].'/theme/collapsed.gif';
//include_once( PREFIX_INCLUDE.'./template/'.$user['template'].'/style.inc.php');

function get_icon( $date_comparaison )
{
  global $user, $conf;
  $difference = time() - $date_comparaison;
  $jours = 24*60*60;
  $output = '';
  if ( $difference < $user['long_period'] * $jours )
  {
    $icon_url = './template/'.$user['template'].'/theme/';
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
/*
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
*/

function get_frame_begin()
{
  global $user;
  $path = './template/'.$user['template'].'/theme/';
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
  $path = './template/'.$user['template'].'/theme/';
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

function initialize_template()
{
  global $vtp, $handle, $user, $lang;

 // $vtp->setGlobalVar( $handle, 'charset', $lang['charset'] );
  //$vtp->setGlobalVar( $handle, 'style', $user['style'] );
  $vtp->setGlobalVar( $handle, 'frame_start', get_frame_start() );
  $vtp->setGlobalVar( $handle, 'frame_begin', get_frame_begin() );
  $vtp->setGlobalVar( $handle, 'frame_end',   get_frame_end() );
 //$vtp->setVarF( $handle, 'header',
 //                './template/'.$user['template'].'/header.htm' );
  //$vtp->setVarF( $handle, 'footer',
   //              './template/'.$user['template'].'/footer.htm' );
}

function display_category( $category, $indent, $handle )
{
  global $user,$lang,$vtp;

  $vtp->addSession( $handle, 'category' );
  $vtp->setVar( $handle, 'category.indent', $indent );
  if ( $user['expand'] or $category['nb_sub_categories'] == 0 )
  {
    $vtp->addSession( $handle, 'bullet_wo_link' );
    $vtp->setVar( $handle, 'bullet_wo_link.bullet_url',
                  $user['lien_collapsed'] );
    $vtp->closeSession( $handle, 'bullet_wo_link' );
  }
  else
  {
    $vtp->addSession( $handle, 'bullet_w_link' );
    $url = './category.php';
	if (isset($page['cat']))
	{
	$url .='?cat='.$page['cat'];
    $url.= '&amp;expand='.$category['expand_string'];
	}
	else if ($category['expand_string']<>'')
	{
		$url.= '?expand='.$category['expand_string'];
	}
    $vtp->setVar( $handle, 'bullet_w_link.bullet_link', add_session_id($url) );
    if ( $category['expanded'] )
    {
      $vtp->setVar( $handle, 'bullet_w_link.bullet_url',
                    $user['lien_expanded'] );
    }
    else
    {
      $vtp->setVar( $handle, 'bullet_w_link.bullet_url',
                    $user['lien_collapsed'] );
    }
    $vtp->closeSession( $handle, 'bullet_w_link' );
  }

  $url = './category.php?cat='.$category['id'];
  $url.= '&amp;expand='.$category['expand_string'];
  $vtp->setVar( $handle, 'category.link_url', add_session_id( $url ) );

  $name = $category['name'];
  if ( $name == '' ) $name = str_replace( '_', ' ', $category['dir'] );
  $vtp->setVar( $handle, 'category.link_name', $name );

  if ( $category['id_uppercat'] == '' )
  {
    $vtp->setVar( $handle, 'category.name_style', 'font-weight:bold;' );
  }
  if ( $category['nb_sub_categories'] > 0 )
  {
    $vtp->addSession( $handle, 'subcat' );
    $vtp->setVar( $handle,'subcat.nb_subcats',$category['nb_sub_categories'] );
    $vtp->closeSession( $handle, 'subcat' );
  }
  $vtp->setVar( $handle, 'category.total_cat', $category['nb_images'] );
  $vtp->setVar( $handle, 'category.cat_icon',get_icon($category['date_last']));
  $vtp->closeSession( $handle, 'category' );

  // recursive call
  if ( $category['expanded'] )
  {
    foreach ( $category['subcats'] as $subcat ) {
      display_category( $subcat, $indent.str_repeat( '&nbsp', 2 ), $handle );
    }
  }
}
?>