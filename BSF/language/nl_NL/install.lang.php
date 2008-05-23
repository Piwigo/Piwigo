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

$lang['install_message'] = 'Bericht';
$lang['Initial_config'] = 'Basis configuratie';
$lang['Default_lang'] = 'Standaard gallery taal';
$lang['step1_title'] = 'Database configuratie';
$lang['step2_title'] = 'Admin configuratie';
$lang['Start_Install'] = 'Start Installatie';
$lang['reg_err_mail_address'] = 'E-mail adres moet lijken op xxx@yyy.eee (voorbeeld : jack@altern.org)';

$lang['install_webmaster'] = 'Webmaster login';
$lang['install_webmaster_info'] = 'Het word getoond aan de bezoekers. Het is ook noodzakelijk voor de administratie van de website';

$lang['step1_confirmation'] = 'Parameters zijn correct';
$lang['step1_err_db'] = 'De verbinding met de server is geslaagd, maar het is niet mogelijk om verbinding te krijgen met de database';
$lang['step1_err_server'] = 'Geen verbinding met de server';
$lang['step1_err_copy_2'] = 'Het is nu mogelijk om verder te gaan met de volgende stap van de installatie';
$lang['step1_err_copy_next'] = 'volgende stap';
$lang['step1_err_copy'] = 'Kopieer de tekst tussen de lijnen en plak deze in het bestand "include/mysql.inc.php"(Waarschuwing: mysql.inc.php mag alleen het blauwe gedeelte bevatten, geen return of extra spatie). Dit moet alleen wanneer dit bestand geen schrijfrechten';

$lang['step1_host'] = 'MySQL host';
$lang['step1_host_info'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['step1_user'] = 'Gebruiker';
$lang['step1_user_info'] = 'De gebruikersnaam welke door uw provider is gegeven';
$lang['step1_pass'] = 'Wachtwoord';
$lang['step1_pass_info'] = 'De gebruikersnaam welke door uw provider is gegeven';
$lang['step1_database'] = 'Database naam';
$lang['step1_database_info'] = 'Ook deze is door uw provider gegeven';
$lang['step1_prefix'] = 'Database tabel voorvoegsel';
$lang['step1_prefix_info'] = 'Tabellen in de database worden voorzien van dit voorvoegsel (dit maakt een beter beheer van de database mogelijk) ook wel prefix genoemd';
$lang['step2_err_login1'] = 'Geef een gebruikersnaam voor de beheerder';
$lang['step2_err_login3'] = 'De gebruikersnaam mag geen \' of " bevatten';
$lang['step2_err_pass'] = 'Vul a.u.b. nogmaals uw wachtwoord in';
$lang['install_end_title'] = 'Installatie voltooid';
$lang['step2_pwd'] = 'Webmaster wachtwoord';
$lang['step2_pwd_info'] = 'Hou dit vertrouwlijk, dit geeft toegang tot de beheermodule';
$lang['step2_pwd_conf'] = 'Wachtwoord [bevestigen]';
$lang['step2_pwd_conf_info'] = 'verificatie';
$lang['install_help'] = 'Hulp nodig ? stel een vraag op het <a href="%s" target="_blank">Piwigo forum</a>.';
$lang['install_end_message'] = 'Het installeren van Piwigo is klaar, de volgende stap is<br /><br />het verwijderen van "install.php" dit is om de veiligheid te waarborgen<br />
Vervolg de instructies nadat "install.php" is verwijderd:<ul><li>Ga naar de Indentificatie pagina: [ <a href="identification.php">Indentificatie</a> ] gebruik hiervoor het eerder opgegeven gebruikersnaam met wachtwoord</li>
<li>Deze gebruikersnaam geeft u toegang tot de beheermenu zodat u afbeeldingen op uw website kan plaatsen</li></ul>';

$lang['conf_mail_webmaster'] = 'Webmaster email adres';
$lang['conf_mail_webmaster_info'] = 'Het is mogelijk dat bezoekers contact opnemen met de beheerder middels e-mail';
?>