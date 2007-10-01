<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | file          : $Id$
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

$lang['install_message'] = 'Message';
$lang['Initial_config'] = 'Configuration de Base';
$lang['Default_lang'] = 'Langue par défaut de la galerie';
$lang['step1_title'] = 'Configuration de la Base de données';
$lang['step2_title'] = 'Configuration du compte Administrateur';
$lang['Start_Install'] = 'Démarrer l\'installation';
$lang['reg_err_mail_address'] = 'L\'adresse mail doit être de la forme xxx@yyy.eee (exemple : jack@altern.org)';

$lang['install_webmaster'] = 'Administrateur';
$lang['install_webmaster_info'] = 'Cet identifiant apparaîtra à tous vos visiteurs. Il vous sert pour administrer le site';

$lang['step1_confirmation'] = 'Les paramètres rentrés sont corrects';
$lang['step1_err_db'] = 'La connexion au serveur est OK, mais impossible de se connecter à cette base de données';
$lang['step1_err_server'] = 'Impossible de se connecter au serveur';

$lang['step1_host'] = 'Hôte MySQL';
$lang['step1_host_info'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['step1_user'] = 'Utilisateur';
$lang['step1_user_info'] = 'nom d\'utilisateur pour votre hébergeur';
$lang['step1_pass'] = 'Mot de passe';
$lang['step1_pass_info'] = 'celui fourni par votre hébergeur';
$lang['step1_database'] = 'Nom de la base';
$lang['step1_database_info'] = 'celui fourni par votre hébergeur';
$lang['step1_prefix'] = 'Préfixe des noms de table';
$lang['step1_prefix_info'] = 'le nom des tables apparaîtra avec ce préfixe (permet de mieux gérer sa base de données)';
$lang['step2_err_login1'] = 'veuillez rentrer un pseudo pour le webmaster';
$lang['step2_err_login3'] = 'le pseudo du webmaster ne doit pas comporter les caractère " et \'';
$lang['step2_err_pass'] = 'veuillez retaper votre mot de passe';
$lang['install_end_title'] = 'Installation terminée';
$lang['step2_pwd'] = 'Mot de passe';
$lang['step2_pwd_info'] = 'Il doit rester confidentiel, il permet d\'accéder au panneau d\'administration.';
$lang['step2_pwd_conf'] = 'Mot de passe [ Confirmer ]';
$lang['step2_pwd_conf_info'] = 'Vérification';
$lang['step1_err_copy'] = 'Copiez le texte en bleu entre les tirets et collez-le dans le fichier mysql.inc.php qui se trouve dans le répertoire "include" à la base de l\'endroit où vous avez installé PhpWebGallery (le fichier mysql.inc.php ne doit comporter QUE ce qui est en bleu entre les tirets, aucun retour à la ligne ou espace n\'est autorisé)';
$lang['install_help'] = 'Besoin d\'aide ? Posez votre question sur le <a href="%s">forum de PhpWebGallery</a>.';
$lang['install_end_message'] = 'La configuration de l\'application s\'est correctement déroulée, place à la prochaine étape<br /><br />
Par mesure de sécurité, merci de supprimer le fichier "install.php"<br />
Un fois ce fichier supprimé, veuillez suivre ces indications :
<ul>
<li>allez sur la page d\'identification : [ <a href="./identification.php">identification</a> ] et connectez-vous avec le pseudo donné pour le webmaster</li>
<li>celui-ci vous permet d\'accéder à la partie administration et aux instructions pour placer les images dans les répertoires.</li>
</ul>';
$lang['conf_mail_webmaster'] = 'Adresse e-mail de l\'Administrateur';
$lang['conf_mail_webmaster_info'] = 'Les visiteurs pourront vous contacter par ce mail';
?>