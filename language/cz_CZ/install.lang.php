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
$lang['Basic configuration'] = 'Základní konfigurace';
$lang['Default gallery language'] = 'Základní jazyk galerie';
$lang['Database configuration'] = 'Databázová konfigurace';
$lang['Admin configuration'] = 'Administrátorská konfigurace';
$lang['Start Install'] = 'Spustit instalaci';
$lang['mail address must be like xxx@yyy.eee (example : jack@altern.org)'] = 'e-mailová adresa musí mít formát xxx@yyy.eee (například : novak@mail.cz)';

$lang['Webmaster login'] = 'Uživatelské jméno správce';
$lang['It will be shown to the visitors. It is necessary for website administration'] = 'Bude zobrazen návštěvníkům. Je nutný pro administraci aplikace.';

$lang['Parameters are correct'] = 'Parametry jsou v pořádku';
$lang['Connection to server succeed, but it was impossible to connect to database'] = 'Spojení na server se podařilo, ale nebylo možné připojit databázi';
$lang['Can\'t connect to server'] = 'Nebylo možné se připojit k serveru';
$lang['The next step of the installation is now possible'] = 'Nyní je možný další krok instalace';
$lang['next step'] = 'další krok';
$lang['Copy the text in pink between hyphens and paste it into the file "local/config/database.inc.php"(Warning : database.inc.php must only contain what is in pink, no line return or space character)'] = 'Zkopírujte růžový text mezi čárkami a vložte jej do souboru "include/mysql.inc.php"(Varování : mysql.inc.php musí obsahovat jen text, který je růžový, bez dalších odřádkování, nebo mezer)';

$lang['Host'] = 'MySQL server';
$lang['localhost, sql.multimania.com, toto.freesurf.fr'] = 'localhost, sql.multimania.com, toto.freesurf.cz';
$lang['User'] = 'Uživatel';
$lang['user login given by your host provider'] = 'uživatelské jméno, které Vám přidělil provozovatel serveru';
$lang['Password'] = 'Heslo';
$lang['user password given by your host provider'] = 'heslo na tomto serveru';
$lang['Database name'] = 'Jméno databáze';
$lang['also given by your host provider'] = 'které na tomto serveru';
$lang['Database table prefix'] = 'Předpona názvů databázových tabulek';
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = 'názvy vytvořených databázových tabulek budou tuto předponu (pro jejich snadnější správu)';
$lang['enter a login for webmaster'] = 'vložte uživatelské jméno správce';
$lang['webmaster login can\'t contain characters \' or "'] = 'uživatelské jméno správce nemůže obsahovat znak \' nebo "';
$lang['please enter your password again'] = 'prosím zadejte znovu heslo';
$lang['Installation finished'] = 'Instalace ukončena';
$lang['Webmaster password'] = 'Heslo správce';
$lang['Keep it confidential, it enables you to access administration panel'] = 'Heslo mějte utajeno, umožní Vám přístup do administrace aplikace';
$lang['Password [confirm]'] = 'Heslo [potvrzení]';
$lang['verification'] = 'kontrola';
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = 'Potřebujete pomoc ? Zeptejte se na <a href="%s">Piwigo fóru</a>.';
$lang['install_end_message'] = 'Konfigurace aplikace Piwigo je ukončena, tady jsou další kroky<br /><br />
* přejděte na stránku identifikace : [ <a href="identification.php">identifikace</a> ] a použijte uživatelské jméno a heslo správce<br />
* toto přihlášení Vám umožní přístup do administrace aplikacea k instrukcím jak umístit fotografie do adresářů';
$lang['Webmaster mail address'] = 'E-mail správce';
$lang['Visitors will be able to contact site administrator with this mail'] = 'Návštěvníci mohou pomocí tohoto e-mailu správce kontaktovat';
?>