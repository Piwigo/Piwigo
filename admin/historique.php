<?php
/***************************************************************************
 *                 historique.php is a part of PhpWebGallery               *
 *                            -------------------                          *
 *   last update          : Monday, July 15, 2002                          *
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
	
	if ( $HTTP_GET_VARS['empty'] == 1 )
	{
		mysql_query( "delete from PREFIX_TABLE"."history;" );
	}
	define (NB_JOUR_HISTO,"7");
	$tMois  = array("janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre");
	$tJours = array("dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi");
	
	// on affiche les visites pour les 48 dernières heures
	// il faut trouver le unix date de la veille à 00h00 :
	// time (); nous donne le nombre de secondes actuelle
	$date_ref = time() - (7*24*60*60);
	$result = mysql_query( "select date,login,IP,categorie,page,titre,commentaire from PREFIX_TABLE"."history where date > '$date_ref' order by date desc;");
	echo"<div style=\"text-align:center;\"><a href=\"".add_session_id_to_url( "./admin.php?page=historique&amp;empty=1" )."\">empty / vider</a></div>";
	echo"<div style=\"color:green;text-align:center;margin:10px\">";
	// affichage de la date du jour
	echo $tJours[date("w")] ." "; 
	echo date("j").(date("j") == 1 ? "er " : " "); 
	echo $tMois[date("n")-1]." ".date("Y")." ";
	echo " à ".date("G")."h".date("i");
	echo"</div>";
?>
		<table width='100%'>
				<tr>
					<th width='1%'>date</th>
					<th>login</th>
					<th>IP</th>
					<th>page</th>
					<th>categorie</th>
					<th>image</th>
				</tr>
				<tr>
						<td colspan=7 height=5><div class='style1'></div></td>
				</tr>
			<?
				$fin = time();
				$debut = mktime ( 23,59,59,date("n"),date("j")-1,date("Y") );
				for ( $i = 0; $i < NB_JOUR_HISTO; $i++ )
				{
					// 1. affichage du nom du jour
					echo"	<tr>
									<td><nobr>";
					echo"<img src=\"".$conf['repertoire_image']."moins.gif\">&nbsp;&nbsp;<b>";
					echo $tJours[date("w",$fin)] ." "; 
					echo date("j",$fin).(date("j",$fin) == 1 ? "er " : " "); 
					echo $tMois[date("n",$fin)-1]." ".date("Y",$fin)."</b>";
					echo"		</nobr></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>";
					// 2. affichage de tous les évènements pour le jour donné
					// entre la veille à 23h59m59s et le jour même 23h59m59s
					$result = mysql_query("select date,login,IP,categorie,page,titre,commentaire from PREFIX_TABLE"."history where date > '$debut' and date < '$fin' order by date desc;");
					$fin = $debut;
					// on recule le début d'une journée complète
					$debut = $debut - 24*60*60;
					while($row=mysql_fetch_array($result))
					{
						$date = date("G\hi s", $row[date]);
						$date = str_replace ( " ","min ", $date );
						$date .= " sec";
						// on réduit la taille du commentaire à ses premiers caractères
						$affichage_commentaire = "";
						if($row[commentaire] != '')
						{
							$affichage_commentaire = substr($row[commentaire],0,10);
							$affichage_commentaire .= "...";
						}
						echo"	<tr>
										<td>&nbsp;|-&nbsp;&nbsp;$date</td>
										<td>$row[login]</td>
										<td>$row[IP]</td>
										<td>$row[page]</td>
										<td>$row[categorie]</td>
										<td>$row[titre]</td>
									</tr>";
					}
				}			
	echo"	</table>
			</center>";
?>