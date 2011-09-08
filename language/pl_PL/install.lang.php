<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
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

$lang['Installation'] = 'Instalacja';
$lang['Basic configuration'] = 'Podstawowa konfiguracja';
$lang['Default gallery language'] = 'Domyślny język galerii';
$lang['Database configuration'] = 'Konfiguracja bazy danych';
$lang['Admin configuration'] = 'Konfiguracja administratora';
$lang['Start Install'] = 'Rozpoczęcie instalacji';
$lang['mail address must be like xxx@yyy.eee (example : jack@altern.org)'] = 'adres email musi być w postaci xxx@yyy.eee (np : jack@altern.org)';

$lang['Webmaster login'] = 'Logowanie Webmastera';
$lang['It will be shown to the visitors. It is necessary for website administration'] = 'To będzie wyświetlone dla odwiedzających i jest konieczne do celów administracyjnych ';

$lang['Connection to server succeed, but it was impossible to connect to database'] = 'Połączenie do serwera powiodło się, ale nie było możliwe połączenie do bazy danych';
$lang['Can\'t connect to server'] = 'Nie można połączyć się do serwera';

$lang['Host'] = 'MySQL host';
$lang['localhost, sql.multimania.com, toto.freesurf.fr'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['User'] = 'Użytkownik';
$lang['user login given by your host provider'] = 'login użytkownika dostarczona przez provider\'a';
$lang['Password'] = 'Hasło';
$lang['user password given by your host provider'] = 'hasło użytkownika dostarczona przez provider\'a';
$lang['Database name'] = 'Nazwa bazy danych';
$lang['also given by your host provider'] = 'także dostarczona przez provider\'a';
$lang['Database table prefix'] = 'Prefix tabel bazy danych';
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = 'tabele w bazie danych będą miały taki prefix (ułatwia to zarządzanie tabelami)';
$lang['enter a login for webmaster'] = 'wprowadź nazwę użytkownika posiadającego uprawnienia Webmaster';
$lang['webmaster login can\'t contain characters \' or "'] = 'login nie może zawierać następujących znaków \' lub "';
$lang['please enter your password again'] = 'wprowadź hasło jeszcze raz';
$lang['Webmaster password'] = 'Hasło użytkownika Webmaster';
$lang['Keep it confidential, it enables you to access administration panel'] = 'Zachowaj hasło, umożliwia ono dostęp do panelu administracyjnego';
$lang['Password [confirm]'] = 'Hasło [potwierdź]';
$lang['verification'] = 'weryfikacja';
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = 'Potrzebujesz pomocy ? Zadaj pytanie na <a href="%s">Forum Piwigo</a>.';
$lang['Webmaster mail address'] = 'Adres email Webmaster\'a';
$lang['Visitors will be able to contact site administrator with this mail'] = 'Z jego pomocą odwiedzający będą mogli się skontaktować z administratorem strony';

$lang['PHP 5 is required'] = 'PHP 5 jest wymagane';
$lang['It appears your webhost is currently running PHP %s.'] = 'Twój serwer aktualnie używa PHP w wersji %s.';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'Piwigo może spróbować przełączyć Twoją konfigurację do PHP 5 poprzez modyfikację pliku .htaccess.';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = 'Możesz również zmienić tę konfigurację sam, a następnie uruchomić ponownie Piwigo.';
$lang['Try to configure PHP 5'] = 'Spróbuj skonfigurować PHP 5';
$lang['Sorry!'] = 'Niestety!';
$lang['Piwigo was not able to configure PHP 5.'] = 'Piwigo nie mógł skonfigurować Twojego PHP 5.';
$lang['You may referer to your hosting provider\'s support and see how you could switch to PHP 5 by yourself.'] = 'Możesz skontaktować się z działem wsparcia Twojego providera aby dowiedzieć się jak włączyć PHP 5.';
$lang['Hope to see you back soon.'] = 'Do zobaczenia wkrótce.';
//For 2.1.0
$lang['Congratulations, Piwigo installation is completed'] = 'Gratulacje, instalacja Piwigo zakończona sukcesem';
$lang['An alternate solution is to copy the text in the box above and paste it into the file "local/config/database.inc.php" (Warning : database.inc.php must only contain what is in the textarea, no line return or space character)'] = 'Alternatywnym rozwiązaniem jest skopiować tekst z powyższego textbox i wkleić do pliku "local/config/database.inc.php" (Uwaga : database.inc.php musi zawierać to co jest w textbox i nie może zawierać znaków spacji oraz enter)';
$lang['Creation of config file local/config/database.inc.php failed.'] = 'Nie powiodło się stworzenie pliku konfiguracyjnego local/config/database.inc.php.';
$lang['Download the config file'] = 'Pobierz plik konfiguracyjny';
$lang['You can download the config file and upload it to local/config directory of your installation.'] = 'Możesz pobrać plik konfiguracyjny i wgrać go do katalogu lokalnego/z konfiguracją.';
?>