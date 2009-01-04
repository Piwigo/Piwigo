<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
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

$lang['Upgrade'] = 'Mise à jour';
$lang['introduction message'] = 'Cette page vous propose de mettre à jour la base de donnée correspondante à votre ancienne version de piwigo vers la nouvelle version.
L\'assistant de mise à jour pense que la version actuelle est une <strong>version %s</strong> (ou équivalente).';
$lang['Upgrade from %s to %s'] = 'Mise à jour de la version %s à %s';
$lang['Statistics'] = 'Statistiques';
$lang['total upgrade time'] = 'temps total de la mise à jour';
$lang['total SQL time'] = 'temps total des requêtes SQL';
$lang['SQL queries'] = 'nombre de requêtes SQL';
$lang['Upgrade informations'] = 'Informations sur la mise à jour';
$lang['perform a maintenance check'] = 'Veuillez effectuer une maintenance dans [Administration>Spéciales>Maintenance] si vous rencontrez des problèmes.';
$lang['deactivated plugins'] = 'Par précaution, les plugins suivants ont été désactivés. Vérifiez s\'il existe des mises à jour avant de les réactiver:';
$lang['upgrade login message'] = 'Seul un administrateur peut lancer la mise à jour: veuillez vous identifier ci-dessous.';
$lang['You do not have access rights to run upgrade'] = 'Vous n\'avez pas les droits necessaires pour lancer la mise à jour.';
$lang['in include/mysql.inc.php, before ?>, insert:'] = 'Dans le fichier <i>include/mysql.inc.php</i>, avant <b>?></b>, insérez:';

// Upgrade informations from upgrade_1.3.1.php
$lang['all sub-categories of private categories become private'] = 'Toutes les sous-catégories de catégories privées deviennent privées';
$lang['user permissions and group permissions have been erased'] = 'Les permissions des utilisateurs et des groupes ont été effacées';
$lang['only thumbnails prefix and webmaster mail saved'] = 'Seuls le préfixe des miniatures et l\'adresse email du webmestre ont étés sauvegardés de la configuration précédente';

?>