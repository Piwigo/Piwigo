<?php
/***************************************************************************
 *                 thumbnail.php is a part of PhpWebGallery                *
 *                            -------------------                          *
 *   last update          : Thursday, July 25, 2002                        *
 *   email                : pierrick@z0rglub.com                           *
 *                                                                         *
 ***************************************************************************/

/***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/
include_once( "./include/isadmin.inc.php" );
	
function get_subdirs( $rep )
{
  $sub_rep = array();
  $i = 0;
  if ( $opendir = opendir ( $rep ) )
  {
    while ( $file = readdir ( $opendir ) )
    {
      if ( $file != "thumbnail" && $file != "."
           && $file != ".." && is_dir ( $rep.$file ) )
      {
        $sub_rep[$i++] = $file;
      }
    }
  }
  return $sub_rep;
}

/*
	$tab_ext = array ( 'jpg', 'JPG','png','PNG' );
	$tab_tn_ext = array ( 'jpg', 'JPG','png','PNG', 'gif', 'GIF' );
*/
function get_images_without_thumbnail( $dir )
{
  $i = 0;
  if ( $opendir = opendir ( $dir ) )
  {
    while ( $file = readdir ( $opendir ) )
    {
      $lien_image = $dir."/".$file;
      if ( is_image( $lien_image, true ) )
      {
        if ( !TN_exist( $dir, $file ) )
        {
          $taille_image = getimagesize( $lien_image );
          $size = floor ( filesize( $lien_image ) / 1024 ). " KB";
          $images[$i++] = array( 	'name' => $file,
                                        'width' => $taille_image[0],
                                        'height' => $taille_image[1],
                                        'size' => $size
            );
        }
      }
    }
  }
  return $images;
}
	
function scandir( $DIR, $width, $height )
{
  global $HTTP_POST_VARS, $conf, $output;
  $compteur = 0;
  $temps = array();
  if ( $ODIR = opendir( $DIR ) )
  {
    while ( $FILE = readdir ( $ODIR ) )
    {
      $TMP = $DIR."/".$FILE;
      if ( is_image ( $TMP, true ) )
      {
        if ( $compteur < $HTTP_POST_VARS['n'] && !TN_exist( $DIR, $FILE ) )
        {
          $t1 = explode( " ", microtime() );
          $t2 = explode( ".", $t1[0] );
          $t2 = $t1[1].".".$t2[1];
          $info = RatioResizeImg( $FILE, $width, $height, $DIR."/", "jpg" );
          $t3 = explode( " ", microtime() );
          $t4 = explode( ".", $t3[0] );
          $t4 = $t3[1].".".$t4[1];
          $info['temps'] = ( $t4 - $t2 ) * 1000;
          $temps[$compteur++] = $info;
          //$output.= " (".number_format( $temps[$compteur-1], 2, '.', ' ')." ms)<br />";;
        }
      }
    }
  }
  return $temps;
}
	
function RatioResizeImg( $image, $newWidth, $newHeight, $path, $extension)
{
  global $conf, $HTTP_POST_VARS;
  // chemin complet de l'image :
  $chemin = $path.$image;
  // détéction du type de l'image
  eregi( "(...)$", $chemin, $regs);
  $type = $regs[1];
  switch( $type )
  {
  case "jpg": $srcImage = @imagecreatefromjpeg( $chemin ); break; 
  case "JPG": $srcImage = @imagecreatefromjpeg( $chemin ); break; 
  case "png": $srcImage = @imagecreatefrompng( $chemin ); break; 
  case "PNG": $srcImage = @imagecreatefrompng( $chemin ); break; 
  default : unset( $type ); break;
  }
		
  if( $srcImage )
  {
    // hauteurs/largeurs
    $srcWidth = imagesx( $srcImage ); 
    $srcHeight = imagesy( $srcImage ); 
    $ratioWidth = $srcWidth/$newWidth;
    $ratioHeight = $srcHeight/$newHeight;
			
    // taille maximale dépassée ?
    if (($ratioWidth > 1) || ($ratioHeight > 1))
    {
      if( $ratioWidth < $ratioHeight)
      { 
        $destWidth = $srcWidth/$ratioHeight;
        $destHeight = $newHeight; 
      }
      else
      { 
        $destWidth = $newWidth; 
        $destHeight = $srcHeight/$ratioWidth;
      }
    }
    else
    {
      $destWidth = $srcWidth;
      $destHeight = $srcHeight;
    }
    // selon votre version de GD installée sur le serveur hébergeur
    if ( $HTTP_POST_VARS['gd'] == 2 )
    {
      // Partie 1 : GD 2.0 ou supérieur, résultat très bons
      $destImage = imagecreatetruecolor( $destWidth, $destHeight); 
      imagecopyresampled( $destImage, $srcImage, 0, 0, 0, 0, $destWidth,$destHeight,$srcWidth,$srcHeight );
    }
    else
    {
      // Partie 2 : GD inférieur à 2, résultat très moyens
      $destImage = imagecreate( $destWidth, $destHeight);
      imagecopyresized( $destImage, $srcImage, 0, 0, 0, 0, $destWidth,$destHeight,$srcWidth,$srcHeight );
    }
			
			
    if( !is_dir( $path."thumbnail" ) )
    {
      umask(0000);
      mkdir( $path."thumbnail", 0777 );
    }
    $dest_file  = $path."thumbnail/".$conf['prefixe_thumbnail'].substr ( $image, 0, strrpos ( $image, ".") ).".".$extension;
			
    // création et sauvegarde de l'image finale
    imagejpeg($destImage, $dest_file);
    // libère la mémoire
    imagedestroy( $srcImage );
    imagedestroy( $destImage );
			
    // renvoit l'URL de l'image
    //return $dest_file;
    $taille_image = getimagesize( $chemin );
    $size = number_format( floor ( filesize( $chemin ) / 1024 ), 0, '', ' ')." KB";
    $tn_taille_image = getimagesize( $dest_file );
    $tn_size = number_format( floor ( filesize( $dest_file ) ), 0, '', ' ')." octets";
    $info = array( 	'name' => $image,
                        'width' => $taille_image[0],
                        'height' => $taille_image[1],
                        'size' => $size,
                        'tn_name' => $conf['prefixe_thumbnail'].substr ( $image, 0, strrpos ( $image, ".") ).".".$extension,
                        'tn_width' => $tn_taille_image[0],
                        'tn_height' => $tn_taille_image[1],
                        'tn_size' => $tn_size
      );
    return $info;
  }
  // erreur
  else
  {
    echo $lang['tn_no_support']." ";
    if ($type)
    {
      echo $lang['tn_format']." $type";
    }
    else
    {
      echo $lang['tn_thisformat'];
    }
    exit();
  }
}
	
function array_max( $array )
{
  $max = 0;
  for ( $i = 0; $i < sizeof( $array ); $i++ )
  {
    if ( $array[$i] > $max )
    {
      $max = $array[$i];
    }
  }
  return $max;
}
	
function array_min( $array )
{
  $min = 99999999999999;
  for ( $i = 0; $i < sizeof( $array ); $i++ )
  {
    if ( $array[$i] < $min )
    {
      $min = $array[$i];
    }
  }
  return $min;
}
	
function array_moy( $array )
{
  return array_sum( $array ) / sizeof( $array );
}
	
// get_dirs retourne un tableau contenant tous les sous-répertoires d'un répertoire
function get_displayed_dirs( $rep, $indent )
{
  global $conf,$lang;
		
  $sub_rep = array();
  $i = 0;
  $dirs = "";
  if ( $opendir = opendir ( $rep ) )
  {
    while ( $file = readdir ( $opendir ) )
    {
      if ( $file != "." && $file != ".." && is_dir ( $rep."/".$file ) && $file != "thumbnail" )
      {
        $sub_rep[$i++] = $file;
      }
    }
  }
  // write of the dirs
  for ( $i = 0; $i < sizeof( $sub_rep ); $i++ )
  {
    $images = get_images_without_thumbnail( $rep."/".$sub_rep[$i] );
    $nb_picture_without_TN = sizeof( $images );
    $dirs.= $indent;
    if ( $nb_picture_without_TN > 0 )
    {
      $dirs.= "<a href=\"".add_session_id_to_url( "./admin.php?page=thumbnail&amp;dir=".$rep."/".$sub_rep[$i] )."\">";
    }
    $dirs.= "<img src=\"".$conf['lien_puce']."\" style=\"border:none;\" alt=\"&gt;\"/>".$sub_rep[$i];
    if ( $nb_picture_without_TN > 0 )
    {
      $dirs.= "</a>";
    }
    if ( $nb_picture_without_TN > 0 )
    {
      $dirs.= " [ $nb_picture_without_TN ".$lang['tn_dirs_alone']." ]";
    }
    $dirs.= "<br />";
    $dirs.= get_displayed_dirs( $rep."/".$sub_rep[$i], $indent."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" );
  }
  return $dirs;		
}

$output = "";
	
if ( isset( $HTTP_GET_VARS['dir'] ) )
{
  //---------------vérification de la présence d'images sans thumbnail
  $images = get_images_without_thumbnail( $HTTP_GET_VARS['dir'] );
  if ( sizeof( $images ) == 0 )
  {
    $output.= "<div style=\"text-align:center;font-weight:bold;margin:10px;\"> [ 0 ".$lang['tn_dirs_alone']." ]</div>";
  }
  else if ( isset( $HTTP_POST_VARS['submit'] ) )
  {
    //----------------------------------------vérification des variables
    $nb_erreur = 0;
    $erreur = "";
    if ( !ereg( "^[0-9]{2,3}$", $HTTP_POST_VARS['width'] ) || $HTTP_POST_VARS['width'] < 10 )
    {
      $nb_erreur++;
      $erreur.= "<li>".$lang['tn_err_width']." 10</li>";
    }
    if ( !ereg( "^[0-9]{2,3}$", $HTTP_POST_VARS['height'] ) || $HTTP_POST_VARS['height'] < 10 )
    {
      $nb_erreur++;
      $erreur.= "<li>".$lang['tn_err_height']." 10</li>";
    }
    if ( !isset( $HTTP_POST_VARS['gd'] ) )
    {
      $nb_erreur++;
      $erreur.= "<li>".$lang['tn_err_GD']."</li>";
    }
			
    //---------------------------------------------listing des résultats
    if ( $nb_erreur == 0 )
    {
      $style = "class=\"row2\" style=\"text-align:center;font-weight:bold;";
      $output.= "
	<table style=\"width:100%;\">
		<tr>
			<th colspan=\"10\">".$lang['tn_results_title']."</th>
		</tr>
		<tr>
			<td ".$style."\">&nbsp;</td>
			<td ".$style."\">".$lang['tn_picture']."</td>
			<td ".$style."\">".$lang['tn_filesize']."</td>
			<td ".$style."\">".$lang['tn_width']."</td>
			<td ".$style."\">".$lang['tn_height']."</td>
			<td ".$style."background-color:#D3DCE3;\">".$lang['tn_results_gen_time']."</td>
			<td ".$style."\">".$lang['thumbnail']."</td>
			<td ".$style."\">".$lang['tn_filesize']."</td>
			<td ".$style."\">".$lang['tn_width']."</td>
			<td ".$style."\">".$lang['tn_height']."</td>
		</tr>";
      $tab_infos = scandir( $HTTP_GET_VARS['dir'], $HTTP_POST_VARS['width'], $HTTP_POST_VARS['height'] );
      for ( $i = 0; $i < sizeof ( $tab_infos ); $i++ )
      {
        $temps[$i] = $tab_infos[$i]['temps'];
      }
      $max = array_max( $temps );
      $min = array_min( $temps );
      for ( $i = 0; $i < sizeof ( $tab_infos ); $i++ )
      {
        $temps[$i] = $tab_infos[$i]['temps'];
        $num = $i + 1;
        $class = "";
        if ( $i%2 == 1 )
        {
          $class = "class=\"row2\"";
        }
        $output.= "
		<tr>
			<td class=\"row2\">$num</td>
			<td $class>".$tab_infos[$i]['name']."</td>
			<td $class style=\"text-align:right;\">".$tab_infos[$i]['size']."</td>
			<td $class style=\"text-align:right;\">".$tab_infos[$i]['width']."</td>
			<td $class style=\"text-align:right;\">".$tab_infos[$i]['height']."</td>
			<th><div style=\"text-align:right;margin-right:5px;";
        if ( $tab_infos[$i]['temps'] == $max )
        {
          $output.= "color:red;";
        }
        if ( $tab_infos[$i]['temps'] == $min )
        {
          $output.= "color:green;";
        }
        $output.= "\">".number_format( $tab_infos[$i]['temps'], 2, '.', ' ')." ms</div></th>
			<td $class>".$tab_infos[$i]['tn_name']."</td>
			<td $class style=\"text-align:right;\">".$tab_infos[$i]['tn_size']."</td>
			<td $class style=\"text-align:right;\">".$tab_infos[$i]['tn_width']."</td>
			<td $class style=\"text-align:right;\">".$tab_infos[$i]['tn_height']."</td>
		</tr>";
      }
      $output.= "
		<tr>
			<td colspan=\"10\">&nbsp;</td>
		</tr>
	</table>
	<table style=\"margin:auto;border:1px solid black;\">
		<tr>
			<td colspan=\"2\" style=\"text-align:center;font-weight:bold;\" class=\"row2\">".$lang['tn_stats']."</td>
		</tr>
		<tr>
			<td>".$lang['tn_stats_nb']." : </td>
			<td style=\"text-align:center;\">".sizeof( $temps )."</td>
		</tr>
		<tr>
			<td>".$lang['tn_stats_total']." : </td>
			<td style=\"text-align:right;\">".number_format( array_sum( $temps ), 2, '.', ' ')." ms</td>
		</tr>
		<tr>
			<td>".$lang['tn_stats_max']." : </td>
			<td style=\"text-align:right;\">".number_format( $max, 2, '.', ' ')." ms</td>
		</tr>
		<tr>
			<td>".$lang['tn_stats_min']." : </td>
			<td style=\"text-align:right;\">".number_format( $min, 2, '.', ' ')." ms</td>
		</tr>
		<tr>
			<td>".$lang['tn_stats_mean']." : </td>
			<td style=\"text-align:right;\">".number_format( array_moy( $temps ), 2, '.', ' ')." ms</td>
		</tr>
	</table>
	<table>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</table>";
    }
    else
    {
      $output.= "
	<div class=\"erreur\" style=\"margin-top:10px;\">".$lang['tn_err']."</div>
	<div class=\"erreur\" style=\"text-align:left;margin-left:20px;\">
		<ul>
			$erreur
		</ul>
	</div>";
    }
  }
  //-------------------------------------paramètres de miniaturisation
  if ( sizeof( $images ) != 0 )
  {
    $output.= "
	<style>
		div.key
		{
			margin-left : 10px;
		}
		td.choice
		{
			text-align : center;
		}
	</style>";
    $output.= "
	<form method=\"post\" action=\"".add_session_id_to_url( "./admin.php?page=thumbnail&amp;dir=".$HTTP_GET_VARS['dir'] )."\">
	<table style=\"width:100%;\">
		<tr>
			<th colspan=\"3\">".$lang['tn_params_title']."</th>
		</tr>";
    $output.= "
		<tr>
			<td colspan=\"3\">&nbsp;</td>
		</tr>";
    $output.= "
		<tr>
			<td><div class=\"key\">".$lang['tn_params_GD']."</div></td>
			<td class=\"choice\">
				<input type=\"radio\" name=\"gd\" value=\"2\"/ checked=\"checked\">2.x
				<input type=\"radio\" name=\"gd\" value=\"1\"";
    if ( $HTTP_POST_VARS['gd'] == 1 )
    {
      $output.= " checked=\"checked\"";
    }
    $output.= "/>1.x
			</td>
			<td style=\"width:50%;\" class=\"row2\">".$lang['tn_params_GD_info']."</td>
		</tr>
		<tr>
			<td><div class=\"key\">".$lang['tn_width']."</div></td>
			<td class=\"choice\"><input type=\"text\" name=\"width\" value=\"";
    if ( isset( $HTTP_POST_VARS['width'] ) )
    {
      $output.= $HTTP_POST_VARS['width'];
    }
    else
    {
      $output.= "128";
    }
    $output.="\"/></td>
			<td class=\"row2\">".$lang['tn_params_width_info']."</td>
		</tr>
		<tr>
			<td><div class=\"key\">".$lang['tn_height']."</div></td>
			<td class=\"choice\"><input type=\"text\" name=\"height\" value=\"";
    if ( isset( $HTTP_POST_VARS['height'] ) )
    {
      $output.= $HTTP_POST_VARS['height'];
    }
    else
    {
      $output.= "96";
    }
    $output.="\"/></td>
			<td class=\"row2\">".$lang['tn_params_height_info']."</td>
		</tr>
		<tr>
			<td><div class=\"key\">".$lang['tn_params_create']."</div></td>
			<td class=\"choice\">
				<select name=\"n\">
					<option>5</option>
					<option>10</option>
					<option>20</option>
					<option>40</option>
				</select>
			</td>
			<td class=\"row2\">".$lang['tn_params_create_info']."</td>
		</tr>
		<tr>
			<td><div class=\"key\">".$lang['tn_params_format']."</div></td>
			<td class=\"choice\"><span style=\"font-weight:bold;\">jpeg</span></td>
			<td class=\"row2\">".$lang['tn_params_format_info']."</td>
		</tr>
		<tr>
			<td colspan=\"3\">&nbsp;</td>
		</tr>
		<tr>
			<td colspan=\"3\" style=\"text-align:center;\">
				<input type=\"submit\" name=\"submit\" value=\"".$lang['submit']."\"/>
			</td>
		</tr>";
    $output.= "
	</table>
	</form>";
    //-----------------------------------liste des images sans miniature
    $images = get_images_without_thumbnail( $HTTP_GET_VARS['dir'] );
    $style = "class=\"row2\" style=\"text-align:center;font-weight:bold;";
    $output.= "
	<table style=\"width:100%;\">
		<tr>
			<th colspan=\"5\"><span style=\"color:#006699;\">".sizeof( $images )."</span> ".$lang['tn_alone_title']."</th>
		</tr>
		<tr>
			<td ".$style."\">&nbsp;</td>
			<td ".$style."width:50%;\">".$lang['tn_picture']."</td>
			<td ".$style."width:17%;\">".$lang['tn_filesize']."</td>
			<td ".$style."width:17%;\">".$lang['tn_width']."</td>
			<td ".$style."width:16%;\">".$lang['tn_height']."</td>
		</tr>";
    for ( $i = 0; $i < sizeof( $images ); $i++ )
    {
      $num = $i + 1;
      $class = "";
      if ( $i%2 == 1 )
      {
        $class = " class=\"row2\"";
      }
      $output.= "
		<tr>
			<td class=\"row2\">".$num."</td>
			<td $class><div style=\"margin-left:10px;\">".$images[$i]['name']."</div></td>
			<td $class><div style=\"margin-left:10px;\">".$images[$i]['size']."</div></td>
			<td $class><div style=\"margin-left:10px;\">".$images[$i]['width']."</div></td>
			<td $class><div style=\"margin-left:10px;\">".$images[$i]['height']."</div></td>
		</tr>";
    }
    $output.= "
	</table>";
  }
}
//-----------------------------------liste des répertoires
//-------------------------si aucun répertoire selectionné
else
{
  $output = "
		<table style=\"width:100%;\">
			<tr>
				<th>".$lang['tn_dirs_title']."</th>
			</tr>";
  $output.= "
			<tr>
				<td>
					<div class=\"retrait\">
						<img src=\"".$conf['lien_puce']."\" alt=\"\"/>galleries";
  $output.= "<br />";
  $output.= get_displayed_dirs( "../galleries", "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" );
  $output.= "
					</div>
				</td>
			</tr>
		</table>";
}
echo $output;
?>