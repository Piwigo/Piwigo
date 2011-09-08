<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// | Czech language localization                                           |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2009     Pavel Budka & Petr Jirsa    http://pbudka.co.cc |
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

$lang['Installation'] = 'Inštalácia';
$lang['Basic configuration'] = 'Základná konfigurácia';
$lang['Default gallery language'] = 'Základný jazyk galérie';
$lang['Database configuration'] = 'Databázová konfigurácia';
$lang['Admin configuration'] = 'Administrátorská konfigurácia';
$lang['Start Install'] = 'Spustiť inštaláciu';
$lang['mail address must be like xxx@yyy.eee (example : jack@altern.org)'] = 'e-mailová adresa musí mať formát xxx@yyy.eee (napríklad : kovac@zoznam.sk)';

$lang['Webmaster login'] = 'Používateľské meno správcu';
$lang['It will be shown to the visitors. It is necessary for website administration'] = 'Bude zobrazený návštevníkom. Je nutný pre administráciu aplikácie.';

$lang['Connection to server succeed, but it was impossible to connect to database'] = 'Spojenie na server sa podarilo, ale nebolo možné pripojiť databázu';
$lang['Can\'t connect to server'] = 'Nebolo možné sa pripojiť k serveru';

$lang['Host'] = 'MySQL server';
$lang['localhost, sql.multimania.com, toto.freesurf.fr'] = 'localhost, sql.multimania.com, toto.freesurf.sk';
$lang['User'] = 'Používateľ';
$lang['user login given by your host provider'] = 'používateľské meno, ktoré Vám pridelil prevádzkovateľ serveru';
$lang['Password'] = 'Heslo';
$lang['user password given by your host provider'] = 'heslo na tomto serveri';
$lang['Database name'] = 'Názov databázy';
$lang['also given by your host provider'] = 'ktorý na tomto servri';
$lang['Database table prefix'] = 'Predpona názvov databázových tabuliek';
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = 'názvy vytvorených databázových tabuliek budú označené predponou (pre ich jednoduchšiu správu)';
$lang['enter a login for webmaster'] = 'zadať používateľské meno webmastra';
$lang['webmaster login can\'t contain characters \' or "'] = 'používateľské meno správcu nemôže obsahovať znak \' alebo "';
$lang['please enter your password again'] = 'prosím zadať znovu heslo';
$lang['Webmaster password'] = 'Heslo webmastra';
$lang['Keep it confidential, it enables you to access administration panel'] = 'Heslo majte utajené, umožní Vám prístup do administrácie aplikácie';
$lang['Password [confirm]'] = 'Heslo [potvrdenie]';
$lang['verification'] = 'kontrola';
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = 'Potrebujete pomoc? Opýtajte sa na <a href="%s">Piwigo fóre</a>.';
$lang['Webmaster mail address'] = 'E-mail webmastra';
$lang['Visitors will be able to contact site administrator with this mail'] = 'Návštevníci môžu pomocou tohto e-mailu kontaktovať správcu';

// missing translations 2.1.0
$lang['PHP 5 is required'] = 'Je nutné PHP 5';
$lang['It appears your webhost is currently running PHP %s.'] = 'Na Vašom webhostingu je PHP %s.';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'Piwigo sa môže pokúsiť zmeniť Vaše nastavenie na PHP 5 tým, že vytvorí alebo zmení súbor .htaccess.';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = 'Prípadne môžete zmeniť Vašu konfiguráciu sami a potom reštartovať Piwigo.';
$lang['Try to configure PHP 5'] = 'Pokúste sa zmeniť Vaše nastavenie na PHP 5';
$lang['Sorry!'] = 'Bohužiaľ!';
$lang['Piwigo was not able to configure PHP 5.'] = 'Piwigo nebolo schopné zmeniť Vaše nastavenie na PHP 5.';
$lang['You may referer to your hosting provider\'s support and see how you could switch to PHP 5 by yourself.'] = 'Obráďte sa na poskytovateľa Vašeho webhostingu, aby ste zistili, ako môžete zmeniť Vaše nastavenie na PHP 5.';
$lang['Hope to see you back soon.'] = 'Veríme, že sa čoskoro uvidíme.';
$lang['Congratulations, Piwigo installation is completed'] = 'Gratulujeme, Piwigo inštalácia je ukončená';
$lang['An alternate solution is to copy the text in the box above and paste it into the file "local/config/database.inc.php" (Warning : database.inc.php must only contain what is in the textarea, no line return or space character)'] = 'Náhradným riešením je skopírovať text z boxu vyššie a vložiť ho do súboru "local/config/database.inc.php" (Upozornenie: súbor database.inc.php môže obsahovať len to, čo je v tomto boxe, žiadne znaky odriadkovania alebo medzery)';
$lang['Creation of config file local/config/database.inc.php failed.'] = 'Vytvorenie súboru local/config/database.inc.php sa nepodarilo.';
$lang['Download the config file'] = 'Stiahnuť konfiguračný súbor';
$lang['You can download the config file and upload it to local/config directory of your installation.'] = 'Môžete stiahnuť konfiguračný súbor a nahrať ho do adresára local/config Vašej inštalácie.';
?>