<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// | Czech language localization                                           |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2009     Pavel Budka & Petr Jirsa    http://pbudka.co.cc |
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

$lang['Installation'] = 'Instalace';
$lang['Initial_config'] = 'Základní konfigurace';
$lang['Default_lang'] = 'Základní jazyk galerie';
$lang['step1_title'] = 'Databázová konfigurace';
$lang['step2_title'] = 'Administrátorská konfigurace';
$lang['Start_Install'] = 'Spustit instalaci';
$lang['reg_err_mail_address'] = 'e-mailová adresa musí mít formát xxx@yyy.eee (například : novak@mail.cz)';

$lang['install_webmaster'] = 'Uživatelské jméno správce';
$lang['install_webmaster_info'] = 'Bude zobrazen návštěvníkům. Je nutný pro administraci aplikace.';

$lang['step1_confirmation'] = 'Parametry jsou v pořádku';
$lang['step1_err_db'] = 'Spojení na server se podařilo, ale nebylo možné připojit databázi';
$lang['step1_err_server'] = 'Nebylo možné se připojit k serveru';
$lang['step1_err_copy_2'] = 'Nyní je možný další krok instalace';
$lang['step1_err_copy_next'] = 'další krok';
$lang['step1_err_copy'] = 'Zkopírujte růžový text mezi čárkami a vložte jej do souboru "include/mysql.inc.php"(Varování : mysql.inc.php musí obsahovat jen text, který je růžový, bez dalších odřádkování, nebo mezer)';

$lang['step1_host'] = 'MySQL server';
$lang['step1_host_info'] = 'localhost, sql.multimania.com, toto.freesurf.cz';
$lang['step1_user'] = 'Uživatel';
$lang['step1_user_info'] = 'uživatelské jméno, které Vám přidělil provozovatel serveru';
$lang['step1_pass'] = 'Heslo';
$lang['step1_pass_info'] = 'heslo na tomto serveru';
$lang['step1_database'] = 'Jméno databáze';
$lang['step1_database_info'] = 'které na tomto serveru';
$lang['step1_prefix'] = 'Předpona názvů databázových tabulek';
$lang['step1_prefix_info'] = 'názvy vytvořených databázových tabulek budou tuto předponu (pro jejich snadnější správu)';
$lang['step2_err_login1'] = 'vložte uživatelské jméno správce';
$lang['step2_err_login3'] = 'uživatelské jméno správce nemůže obsahovat znak \' nebo "';
$lang['step2_err_pass'] = 'prosím zadejte znovu heslo';
$lang['install_end_title'] = 'Instalace ukončena';
$lang['step2_pwd'] = 'Heslo správce';
$lang['step2_pwd_info'] = 'Heslo mějte utajeno, umožní Vám přístup do administrace aplikace';
$lang['step2_pwd_conf'] = 'Heslo [potvrzení]';
$lang['step2_pwd_conf_info'] = 'kontrola';
$lang['install_help'] = 'Potřebujete pomoc ? Zeptejte se na <a href="%s">Piwigo fóru</a>.';
$lang['install_end_message'] = 'Konfigurace aplikace Piwigo je ukončena, tady jsou další kroky<br /><br />
* přejděte na stránku identifikace : [ <a href="identification.php">identifikace</a> ] a použijte uživatelské jméno a heslo správce<br />
* toto přihlášení Vám umožní přístup do administrace aplikacea k instrukcím jak umístit fotografie do adresářů';
$lang['conf_mail_webmaster'] = 'E-mail správce';
$lang['conf_mail_webmaster_info'] = 'Návštěvníci mohou pomocí tohoto e-mailu správce kontaktovat';
?>