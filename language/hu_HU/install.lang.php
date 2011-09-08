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



$lang['Installation'] = 'Telepítés';
$lang['Basic configuration'] = 'Alap konfiguráció';
$lang['Default gallery language'] = 'Galéria alapértelmezett nyelve';
$lang['Database configuration'] = 'Adatbázis konfiguráció';
$lang['Admin configuration'] = 'Rendszergazda fiókjának beállítása';
$lang['Start Install'] = 'Telepítés indítása';
$lang['mail address must be like xxx@yyy.eee (example : jack@altern.org)'] = 'E-mail formátuma: xxx@yyy.eee (pl.: kedvenc@nyuszi.hu)';
$lang['Webmaster login'] = 'Webmester';
$lang['It will be shown to the visitors. It is necessary for website administration'] = 'A látogatók látni fogják. Szükséges a weboldal adminisztrációjához';
$lang['Connection to server succeed, but it was impossible to connect to database'] = 'A kapcsolat a kiszolgálóval rendben, de nem sikerült csatlakozni az adatbázishoz';
$lang['Can\'t connect to server'] = 'Nem sikerült kapcsolódni a szerverhez';
$lang['Host'] = 'MySQL host';
$lang['localhost, sql.multimania.com, toto.freesurf.fr'] = 'localhost, sql, stb.';
$lang['User'] = 'Felhasználó név';
$lang['user login given by your host provider'] = 'a tárhelyszolgáltató által adott felhasználónév';
$lang['Password'] = 'Jelszó';
$lang['user password given by your host provider'] = 'a tárhelyen használt jelszó';
$lang['Database name'] = 'Adatbázis neve';
$lang['also given by your host provider'] = 'a szolgáltatótól kapott adatbázis név';
$lang['Database table prefix'] = 'Adatbázis tábla előtag';
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = 'az adatbázis táblák ezzel az előtaggal fognak kezdődni (lehetővé teszi a táblák jobb áttekinthetőségét)';
$lang['enter a login for webmaster'] = 'írja be a webmester bejelentkezési adatokat';
$lang['webmaster login can\'t contain characters \' or "'] = 'A webmester nevében nem használhatók a \' és " karakterek';
$lang['please enter your password again'] = 'kérjük, adja meg újra a jelszót';
$lang['Webmaster password'] = 'Webmester jelszó';
$lang['Keep it confidential, it enables you to access administration panel'] = 'Kezelje bizalmasan az adatokat, ezek lehetővé teszik a hozzáférést az adminisztrációs felülethez';
$lang['Password [confirm]'] = 'Jelszó [megerősítés]';
$lang['verification'] = 'jelszó egyezőségének ellenőrzése';
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = 'Segítségre van szüksége ? Kérdéseit itt teheti fel: <a href="%s">Piwigo üzenőfal</a>.';
$lang['Webmaster mail address'] = 'Webmester e-mail cím';
$lang['Visitors will be able to contact site administrator with this mail'] = 'A látogatók ezen az e-mail címen tudják felvenni a kapcsolatot az adminisztrátorral';
$lang['PHP 5 is required'] = 'PHP 5 szükséges';
$lang['It appears your webhost is currently running PHP %s.'] = 'Úgy tűnik, a tárhelyszolgáltatójánál jelenleg futó PHP %s.';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'Piwigo megpróbálhatja bekapcsolni a PHP 5-öt azáltal, hogy létrehoz vagy módosít egy .htaccess fájlt.';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = 'Figyelem: Ha meg tudja változtatni a PHP konfigurációt, indítsa újra a Piwigot.';
$lang['Try to configure PHP 5'] = 'Próbálja meg beállítani a PHP 5-öt';
$lang['Sorry!'] = 'Elnézést!';
$lang['Piwigo was not able to configure PHP 5.'] = 'Piwigo nem tudta beállítani a PHP 5-öt.';
$lang["You may referer to your hosting provider's support and see how you could switch to PHP 5 by yourself."] = "Lehet, hogy a tárhely szolgáltató támogatja a PHP 5-öt. A bekapcsoláshoz keresse meg őket.";
$lang['Hope to see you back soon.'] = 'Remélem később viszontlátjuk.';
$lang['Congratulations, Piwigo installation is completed'] = 'Gratulálunk, a Piwigo telepítése sikeresen befejeződött';
$lang['An alternate solution is to copy the text in the box above and paste it into the file "local/config/database.inc.php" (Warning : database.inc.php must only contain what is in the textarea, no line return or space character)'] = 'Egy lehetséges megoldás a szöveget az alul lévő dobozba másolni, és beilleszteni a "local/config/database.inc.php" fájlba. (Figyelem: database.inc.php csak azt tartalmazhatja amit a szövegterület, soremelés vagy üres karakterhely nélkül)';
$lang['Creation of config file local/config/database.inc.php failed.'] = 'A local/config/database.inc.php konfigurációs fájl létrehozása sikertelen.';
$lang['Download the config file'] = 'Konfigurációs fájl letöltése';
$lang['You can download the config file and upload it to local/config directory of your installation.'] = 'Letöltheti a konfigurációs fájlt és töltse fel a telepítésének local/config könyvtárába.';
$lang['Just another Piwigo gallery'] = 'Csak egy újabb Piwigo galéria';
$lang['Welcome to my photo gallery'] = 'Üdvözöljük a képgalériában';
?>