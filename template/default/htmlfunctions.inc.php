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

$user['lien_expanded']='./template/'.$user['template'].'/theme/expanded.gif';
$user['lien_collapsed']='./template/'.$user['template'].'/theme/collapsed.gif';

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
  global $template, $user, $lang;
  
  $template->assign_vars(array(
	'T_START' => get_frame_start(),
	'T_BEGIN' => get_frame_begin(),
	'T_END' =>  get_frame_end()
	)
	);


  global $vtp, $handle;
  if (isset($handle))
  {
  $vtp->setGlobalVar( $handle, 'frame_start', get_frame_start() );
  $vtp->setGlobalVar( $handle, 'frame_begin', get_frame_begin() );
  $vtp->setGlobalVar( $handle, 'frame_end',   get_frame_end() );
  }
}

function display_category( $category, $indent )
{
  global $user,$lang,$template, $vtp, $handle;
  
  $style='';
  $url = './category.php?cat='.$category['id'];
  $url.= '&amp;expand='.$category['expand_string'];
  $name = $category['name'];
  if ( $name == '' ) $name = str_replace( '_', ' ', $category['dir'] );
  if ( $category['id_uppercat'] == '' )
  {
    $style = 'font-weight:bold;';
  }
  
  $template->assign_block_vars('category', array(
    'LINK_NAME' => $name,
	'INDENT' => $indent,
	'NB_SUBCATS'=>$category['nb_sub_categories'],
	'TOTAL_CAT'=>$category['nb_images'],
	'CAT_ICON'=>get_icon($category['date_last']),
	
	'T_NAME'=>$style,
    'U_LINK' => add_session_id($url)));

  if ( $user['expand'] or $category['nb_sub_categories'] == 0 )
  {
  	$template->assign_block_vars('category.bulletnolink', array('BULLET_IMAGE' =>  $user['lien_collapsed']));
  }
  else
  {
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
	
	if ( $category['expanded'] )
    {
      $img=$user['lien_expanded'];
    }
    else
    {
      $img=$user['lien_collapsed'];
    }
	
    $template->assign_block_vars('category.bulletlink', array(
      'BULLET_IMAGE' =>  $img,
	  'U_BULLET_LINK'=>  add_session_id($url)		
	));
  }

  // recursive call
  if ( $category['expanded'] )
  {
    foreach ( $category['subcats'] as $subcat ) {
	  $template->assign_block_vars('category.subcat', array());
      display_category( $subcat, $indent.str_repeat( '&nbsp', 2 ));
    }
  }
}
?>
