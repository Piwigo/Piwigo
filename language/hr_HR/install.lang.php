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

$lang['Installation'] = 'Ugradba';
$lang['Initial_config'] = 'Osnovna postava';
$lang['Default_lang'] = 'Pretpostavljeni jezik galerije';
$lang['step1_title'] = 'Postava baze podataka';
$lang['step2_title'] = 'Upravna postava';
$lang['Start_Install'] = 'Pokreni ugradbu';
$lang['reg_err_mail_address'] = 'e-mail adresa mora biti kao xxx@yyy.eee (example : jere@stranica.org)';

$lang['install_webmaster'] = 'Webmaster-ova prijava';
$lang['install_webmaster_info'] = 'Biti će prikazano posjetiteljima. Potrebno je za upravljanje web mjestom';

$lang['step1_confirmation'] = 'Postavke su ispravne';
$lang['step1_err_db'] = 'Povezivanje sa poslužiteljem uspješno, ali je nemoguće povezivanje sa bazom podataka';
$lang['step1_err_server'] = 'Ne mogu se povezati sa poslužiteljem';
$lang['step1_err_copy_2'] = 'Slijedeći korak ugradbe je omogućen';
$lang['step1_err_copy_next'] = 'slijedeći korak';
$lang['step1_err_copy'] = 'Kopirajte ružičasti tekst između crtica i prebacite ga u "include/mysql.inc.php"(Pozor : mysql.inc.php mora sadržavati samo ružičasti tekst, bez znakova novog reda ili razmaka)';

$lang['step1_host'] = 'MySQL poslužitelj';
$lang['step1_host_info'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['step1_user'] = 'Korisnik';
$lang['step1_user_info'] = 'korisničko ime dobiveno od davatelja usluge smještaja';
$lang['step1_pass'] = 'Zaporka';
$lang['step1_pass_info'] = 'korisnička zaporka dobivena od davatelja usluge smještaja';
$lang['step1_database'] = 'Naziv baze podataka';
$lang['step1_database_info'] = 'također dobivena od davatelja usluge smještaja';
$lang['step1_prefix'] = 'Predznak tabela baze podataka';
$lang['step1_prefix_info'] = 'nazivi tabela baze podataka će biti predznačeni sa time (omogućava vam bolje upravljanje tabelama)';
$lang['step2_err_login1'] = 'upišite korisničko ime za webmaster-a';
$lang['step2_err_login3'] = 'webmaster-ovo korisničko ime ne može sadržavati znakove \' ili "';
$lang['step2_err_pass'] = 'molimo upišite zaporku ponovo';
$lang['install_end_title'] = 'Ugradba završena';
$lang['step2_pwd'] = 'Webmaster-ova zaporka';
$lang['step2_pwd_info'] = 'Čuvajte je na sigurnom mjestu, ona omogućava pristup upravnoj ploči';
$lang['step2_pwd_conf'] = 'Zaporka [potvrdi]';
$lang['step2_pwd_conf_info'] = 'ovjera';
$lang['install_help'] = 'Trebate pomoć? Pitajte na <a href="%s">Piwigo message board</a>.';
$lang['install_end_message'] = 'Postava Piwigo-a je završena, slijedeći korak je<br /><br />
* idite na prijavnicu i koristite korisničko ime/zaporku danu za webmaster-a<br />
* ova prijava će vam omogućiti pristup upravnoj ploči i uputama za pohranu slika u mape';
$lang['conf_mail_webmaster'] = 'Webmaster-ova e-mail adresa';
$lang['conf_mail_webmaster_info'] = 'Posjetitelji će moći pisati upravitelju galerije preko ove adrese';
?>