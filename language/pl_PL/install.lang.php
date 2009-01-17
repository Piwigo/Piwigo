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

$lang['Installation'] = 'Instalacja';
$lang['Initial_config'] = 'Podstawowa konfiguracja';
$lang['Default_lang'] = 'Domyślny język galerii';
$lang['step1_title'] = 'Konfiguracja bazy danych';
$lang['step2_title'] = 'Konfiguracja administratora';
$lang['Start_Install'] = 'Rozpoczęcie instalacji';
$lang['reg_err_mail_address'] = 'adres email musi być w postaci xxx@yyy.eee (np : jack@altern.org)';

$lang['install_webmaster'] = 'Logowanie Webmastera';
$lang['install_webmaster_info'] = 'To będize wyświetlone dla odwiedzających i jest konieczne do celów administracyjnych ';

$lang['step1_confirmation'] = 'Parametry są poprawne';
$lang['step1_err_db'] = 'Połączenie do serwera powiodło się, ale nie było możliwe połączenie do bazy danych';
$lang['step1_err_server'] = 'Nie można połączyć sie do serwera';
$lang['step1_err_copy_2'] = 'Teraz mozliwy jest następny krok instalacji';
$lang['step1_err_copy_next'] = 'następny krok';
$lang['step1_err_copy'] = 'Skopiuj tekst zaznaczony na różowo pomiędzy cudzysłowiami i wklej do pliku "include/mysql.inc.php"(Uwaga : mysql.inc.php musi zawierać tylko to co jest na różowo bez żadnych znaków końca linii czy spacji)';

$lang['step1_host'] = 'MySQL host';
$lang['step1_host_info'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['step1_user'] = 'Uzytkownik';
$lang['step1_user_info'] = 'login użytkownika dostarczona przez provider\'a';
$lang['step1_pass'] = 'Hasło';
$lang['step1_pass_info'] = 'hasło użytkownika dostarczona przez provider\'a';
$lang['step1_database'] = 'NAzwa bazy danych';
$lang['step1_database_info'] = 'także dostarczona przez provider\'a';
$lang['step1_prefix'] = 'Prefix tabel bazy danych';
$lang['step1_prefix_info'] = 'tabele w bazie dnaych będą miały taki prefix (ułatwia to zarządzanie tabelami)';
$lang['step2_err_login1'] = 'wprowadź nazwę użytkownika posiadającego uprawnienia Webmaster';
$lang['step2_err_login3'] = 'login nie może zawierać nastepujących znaków \' lub "';
$lang['step2_err_pass'] = 'wprowadź hasło jeszcze raz';
$lang['install_end_title'] = 'Instalacja zakończona';
$lang['step2_pwd'] = 'Hasło użytkownika Webmaster';
$lang['step2_pwd_info'] = 'Zachowaj hasło, umożliwia ono dostep do panelu administracyjnego';
$lang['step2_pwd_conf'] = 'Hasło [potwierdź]';
$lang['step2_pwd_conf_info'] = 'weryfikacja';
$lang['install_help'] = 'Potrzebujesz pomocy ? Zadaj pytanie na <a href="%s">Forum Piwigo</a>.';
$lang['install_end_message'] = 'Konfiguracja Piwigo została zakończona, następny krok to<br /><br />
* przejdź do strony logowania : [ <a href="identification.php">logowanie</a> ] i wprowadź użytkownika/hasło będącego webmaster\'em<br />
* logowanie to umożliwi Ci dostęp do panelu administracyjnego oraz instrukcji jak umieszczaćzdjęcia w katalogach';
$lang['conf_mail_webmaster'] = 'Adres email Webmaster\'a';
$lang['conf_mail_webmaster_info'] = 'Z jego pomocą odwiedzający będą mogli się skontaktować z administratorem strony';
?>