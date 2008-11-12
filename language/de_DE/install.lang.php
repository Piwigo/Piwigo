<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
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

$lang['Installation'] = 'Installation';
$lang['Initial_config'] = 'Base-Konfiguration';
$lang['Default_lang'] = 'Standardsprache der Galerie';
$lang['step1_title'] = 'Konfiguration der Datenbank';
$lang['step2_title'] = 'Konfiguration des Administrator-Kontos';
$lang['Start_Install'] = 'Start der Installation';
$lang['reg_err_mail_address'] = 'Die E-Mail-Adresse muss in der Form xxx@yyy.eee (Beispiel: jack@altern.org)';

$lang['install_webmaster'] = 'Administrator';
$lang['install_webmaster_info'] = 'Diese ID wird auf alle Ihre Besucher. Sie dient zur Verwaltung der Website.';

$lang['step1_confirmation'] = 'Die Parameter sind korrekt ausgefüllt';
$lang['step1_err_db'] = 'Die Verbindung zum Server ist OK, aber nicht die Verbindung zu dieser Datenbank';
$lang['step1_err_server'] = 'Es konnte keine Verbindung zum Server';

$lang['step1_host'] = 'MySQL Host';
$lang['step1_host_info'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['step1_user'] = 'Benutzer';
$lang['step1_user_info'] = 'Benutzernamen für Ihren Hosting-Provider';
$lang['step1_pass'] = 'Passwort';
$lang['step1_pass_info'] = 'das von Ihrem Hosting-Provider';
$lang['step1_database'] = 'Name der Datenbank';
$lang['step1_database_info'] = 'das von Ihrem Hosting-Provider';
$lang['step1_prefix'] = 'Vorwahl Tabellen';
$lang['step1_prefix_info'] = 'die Namen der Tabellen mit diesem Präfix (ermöglicht eine bessere Verwaltung der Datenbank)';
$lang['step2_err_login1'] = 'gib bitte einen Pseudonym für den Webmaster';
$lang['step2_err_login3'] = 'das Pseudonym des Webmasters darf nicht den Charakter \' und "';
$lang['step2_err_pass'] = 'Bitte geben Sie Ihr Passwort';
$lang['install_end_title'] = 'Installation abgeschlossen';
$lang['step2_pwd'] = 'Passwort';
$lang['step2_pwd_info'] = 'Sie bleiben vertraulich, es ermöglicht den Zugang zum Administration.';
$lang['step2_pwd_conf'] = 'Passwort [Bestätigung]';
$lang['step2_pwd_conf_info'] = 'Prüfung';
$lang['step1_err_copy'] = 'Kopieren Sie den Text in rosaen zwischen Bindestriche und fügen Sie ihn in die Datei "include / mysql.inc.php" (Warnung: mysql.inc.php müssen nur enthalten, was in rosa, keine Zeile zurück oder Leerzeichen)';
$lang['install_help'] = 'Brauchen Sie Hilfe? Stellen Sie Ihre Frage auf der <a href="%s"> Forum Piwigo </ a>.';
$lang['install_end_message'] = 'Die Konfiguration der Piwigo abgeschlossen ist, hier ist der nächste Schritt<br /><br />
* Gehen Sie auf die Identifizierung Seite: [ <a href="./identification.php">Identifizierung</a> ] und verwenden Sie die Login / Passwort für Webmaster<br />
* diesem Login ermöglicht Ihnen den Zugang zu den Verwaltungs-Panel und den Anweisungen, um Platz Bilder in Ihre Verzeichnisse.';
$lang['conf_mail_webmaster'] = 'Webmaster Mail-Adresse';
$lang['conf_mail_webmaster_info'] = 'Besucher können sich nicht Kontakt Site Administrator mit diesem E-Mail';
?>