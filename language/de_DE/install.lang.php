<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
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
$lang['Admin configuration'] = 'Konfiguration des Administrator-Accounts';
$lang['Start Install'] = 'Start der Installation';
$lang['mail address must be like xxx@yyy.eee (example : jack@altern.org)'] = 'Die E-Mail-Adresse muss dem Muster xxx@yyy.eee (Beispiel: jack@altern.org) entsprechen.';

$lang['Webmaster login'] = 'Webmaster-Login';
$lang['It will be shown to the visitors. It is necessary for website administration'] = 'Benutzername des Administrators';

$lang['Connection to server succeed, but it was impossible to connect to database'] = 'Eine Verbindung zum Server konnte hergestellt werden, nicht aber zur Datenbank.';
$lang['Can\'t connect to server'] = 'Es konnte keine Verbindung zum Datenbankserver aufgebaut werden.';

$lang['Host'] = 'MySQL-Host';
$lang['localhost, sql.multimania.com, toto.freesurf.fr'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['User'] = 'Benutzer';
$lang['user login given by your host provider'] = 'Benutzername Ihrer MySQL-Datenbank (wie von Ihrem Hosting-Provider angegeben)';
$lang['Password'] = 'Passwort';
$lang['user password given by your host provider'] = 'Passwort Ihrer MySQL-Datenbank (wie von Ihrem Hosting-Provider angegeben)';
$lang['Database name'] = 'Name der Datenbank';
$lang['also given by your host provider'] = 'ebenso von Ihrem Host-Provider bereitgestellt';
$lang['Database table prefix'] = 'Präfix der Datenbanktabellen';
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = 'wird den Namen der Datenbanktabellen vorangestellt, damit Sie Ihre Tabellen einfacher verwalten können';
$lang['enter a login for webmaster'] = 'Geben Sie einen Benutzernamen für den Webmaster an';
$lang['webmaster login can\'t contain characters \' or "'] = 'Der Benutzername des Webmasters darf nicht die Zeichen \' und " enthalten';
$lang['please enter your password again'] = 'Bitte geben Sie Ihr Passwort erneut ein';
$lang['Webmaster password'] = 'Webmaster-Passwort';
$lang['Keep it confidential, it enables you to access administration panel'] = 'Administrator-Passwort';
$lang['Password [confirm]'] = 'Passwort [Bestätigung]';
$lang['verification'] = 'Wiederholen Sie das eingegebene Passwort';
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = 'Brauchen Sie Hilfe? Stellen Sie Ihre Fragen im <a href="%s"> Piwigo-Forum</ a>.';
$lang['Webmaster mail address'] = 'Webmaster-E-Mail-Adresse';
$lang['Visitors will be able to contact site administrator with this mail'] = 'Kontakt-E-Mail-Adresse (nur für angemeldete Benutzer sichtbar)';

$lang['PHP 5 is required'] = 'PHP5 ist erforderlich';
$lang['It appears your webhost is currently running PHP %s.'] = 'Warscheinlich läuft auf Ihrem Server die PHP-Version %s.';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'Piwigo wird versuchen, Ihre Konfiguration auf PHP5 umzustellen. Zu diesem Zweck wird eine .htaccess-Datei erstellt oder geändert.';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = 'Hinweis: Sie können Ihre Konfiguration selbst ändern. Starten Sie Piwigo danach neu.';
$lang['Try to configure PHP 5'] = 'Versuche PHP5 zu konfigurieren';
$lang['Sorry!'] = 'Sorry!';
$lang['Piwigo was not able to configure PHP 5.'] = 'Piwigo ist nicht in der Lage PHP5 zu konfigurieren.';
$lang["You may referer to your hosting provider's support and see how you could switch to PHP 5 by yourself."] = "Sie können Kontakt zu ihrem Hosting-Provider aufnehmen und um Unterstützung bitten zur Umstellung auf PHP 5.";
$lang['Hope to see you back soon.'] = 'Hoffentlich sehen wir uns bald wieder!';

$lang['Congratulations, Piwigo installation is completed'] = 'Glückwunsch! Sie haben Piwigo erfolgreich installiert.';

$lang['An alternate solution is to copy the text in the box above and paste it into the file "local/config/database.inc.php" (Warning : database.inc.php must only contain what is in the textarea, no line return or space character)'] = 'Sie können auch den Text in der Box unten kopieren und in die Datei "local/config/database.inc.php" einfügen. (Warnung: database.inc.php darf nur den reinen Text ohne Zeilenumbrüche und Leerzeichen enthalten.)';
$lang['Creation of config file local/config/database.inc.php failed.'] = 'Die Erstellung der Datei local/config/database.inc.php ist fehlgeschlagen.';
$lang['Download the config file'] = 'Lade die Konfigurationsdatei herunter';
$lang['You can download the config file and upload it to local/config directory of your installation.'] = 'Sie können die Konfigurationsdatei herunterladen und in das Verzeichnis local/config ihrer Installation hochladen.';
$lang['Just another Piwigo gallery'] = 'Meine Piwigo-Fotogalerie';
$lang['Welcome to my photo gallery'] = 'Willkommen!';
$lang['Don\'t hesitate to consult our forums for any help : %s'] = 'Besuchen Sie unser Forum, wenn Sie Hilfe benötigen: %s';
$lang['Welcome to your new installation of Piwigo!'] = 'Willkommen zu Ihrer neuen Piwigo-Installation!';
$lang['You may referer to your hosting provider\'s support and see how you could switch to PHP 5 by yourself.'] = 'Sie können Kontakt mit Ihrem Provider aufnehmen und um Unterstützung bei der Umstellung auf PHP5 bitten.';
?>