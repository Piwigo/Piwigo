<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
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
$lang['Basic configuration'] = 'Basis-Konfiguration';
$lang['Default gallery language'] = 'Standardsprache der Galerie';
$lang['Database configuration'] = 'Konfiguration der Datenbank';
$lang['Admin configuration'] = 'Konfiguration des Administrator-Kontos';
$lang['Start Install'] = 'Start der Installation';
$lang['mail address must be like xxx@yyy.eee (example : jack@altern.org)'] = 'Die E-Mail-Adresse muss in der Form xxx@yyy.eee (Beispiel: jack@altern.org)';

$lang['Webmaster login'] = 'Administrator';
$lang['It will be shown to the visitors. It is necessary for website administration'] = 'Benutzername des Administrators';

$lang['Connection to server succeed, but it was impossible to connect to database'] = 'Die Verbindung zum Server ist OK, aber nicht die Verbindung zu dieser Datenbank';
$lang['Can\'t connect to server'] = 'Es konnte keine Verbindung zum Datenbankserver aufgebaut werden';

$lang['Host'] = 'MySQL Host';
$lang['localhost, sql.multimania.com, toto.freesurf.fr'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['User'] = 'Benutzer';
$lang['user login given by your host provider'] = 'Benutzernamen für die MySQL Datenbank';
$lang['Password'] = 'Passwort';
$lang['user password given by your host provider'] = 'das von Ihrem Hosting-Provider';
$lang['Database name'] = 'Name der Datenbank';
$lang['also given by your host provider'] = 'Passwort für die MySQL Datenbank';
$lang['Database table prefix'] = 'Vorwahl Tabellen';
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = 'die Namen der Tabellen mit diesem Präfix (ermöglicht eine bessere Verwaltung der Datenbank)';
$lang['enter a login for webmaster'] = 'gib bitte einen Benutzernamen für den Webmaster an';
$lang['webmaster login can\'t contain characters \' or "'] = 'der Benutzername des Webmasters darf nicht die Zeichen \' und " enthalten';
$lang['please enter your password again'] = 'Bitte wählen Sie ein Passwort';
$lang['Webmaster password'] = 'Passwort';
$lang['Keep it confidential, it enables you to access administration panel'] = 'Administratorpasswort';
$lang['Password [confirm]'] = 'Passwort [Bestätigung]';
$lang['verification'] = 'Wiederholen Sie das eingegebene Passwort';
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = 'Brauchen Sie Hilfe? Stellen Sie Ihre Frage auf der <a href="%s"> Forum Piwigo </ a>.';
$lang['Webmaster mail address'] = 'Webmaster Mail-Adresse';
$lang['Visitors will be able to contact site administrator with this mail'] = 'Kontakt E-Mailadresse (nur für angemeldete Benutzer sichtbar)';

$lang['PHP 5 is required'] = 'PHP 5 ist erforderlich';
$lang['It appears your webhost is currently running PHP %s.'] = 'Warscheinlich läuft auf ihrem Webhost die PHP-Version %s.';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'Piwigo wird versuchen ihre Konfiguration auf PHP 5 zu schalten durch die Erstellung oder Änderung einer .htaccess-Datei.';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = 'Hinweis: Sie können Ihre Konfiguration manuel ändern und die Piwigo danach neu starten.';
$lang['Try to configure PHP 5'] = 'Versuche PHP 5 zu konfigurieren';
$lang['Sorry!'] = 'Entschuldigung!';
$lang['Piwigo was not able to configure PHP 5.'] = 'Piwigo ist nicht in der Lage PHP 5 zu konfigurieren.';
$lang["You may referer to your hosting provider's support and see how you could switch to PHP 5 by yourself."] = "Sie können Kontakt zu ihrem Hosting-Provider aufnehmen und um Unterstützung bitten zur Umstellung auf PHP 5.";
$lang['Hope to see you back soon.'] = 'Wir hoffen, Sie sind bald wieder zurück.';

$lang['Database type'] = 'Datenbanktyp';
$lang['The type of database your piwigo data will be store in'] = 'Der Typ der Datenbank, die Piwigo-Daten werden gespeichert in';
$lang['Congratulations, Piwigo installation is completed'] = 'Glückwunsch, Sie haben Piwigo erfolgreich installiert';

$lang['An alternate solution is to copy the text in the box above and paste it into the file "local/config/database.inc.php" (Warning : database.inc.php must only contain what is in the textarea, no line return or space character)'] = 'Eine andere Lösung ist, den Text im Kasten oben zu kopieren und ihn in die Datei "local/config/database.inc.php" einzufügen (Warnung: database.inc.php darf nur enthalten, was im Textbereich ist, keine Zeilenumbrüche (Enter) oder Leerzeichen)';
$lang['Creation of config file local/config/database.inc.php failed.'] = 'Die Erstellung der Datei local/config/database.inc.php ist fehlgeschlagen.';
$lang['Download the config file'] = 'Lade die Konfigurationsdatei herunter';
$lang['You can download the config file and upload it to local/config directory of your installation.'] = 'SIe können die Konfigurationsdatei herunterladen und in den Ordner local/config ihrer Installation hochladen.';
$lang['SQLite and PostgreSQL are currently in experimental state.'] = 'Die Unterstüztung von SQLite und PostgreSQL befindet sich noch in einem experimentellen Stadium.';
$lang['Learn more'] = 'Mehr Informationen';
?>
