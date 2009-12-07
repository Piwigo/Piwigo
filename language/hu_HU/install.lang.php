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

$lang['Installation'] = 'Telepítés';
$lang['Initial_config'] = 'Alap konfiguráció';
$lang['Default_lang'] = 'Galéria alapértelmezett nyelve';
$lang['step1_title'] = 'Adatbázis konfiguráció';
$lang['step2_title'] = 'Rendszergazda fiókjának beállítása';
$lang['Start_Install'] = 'Telepítés indítása';
$lang['reg_err_mail_address'] = 'E-mail formátuma: xxx@yyy.eee (pl.: kedvenc@nyuszi.hu)';

$lang['install_webmaster'] = 'Webmester';
$lang['install_webmaster_info'] = 'A látogatók látni fogják. Szükséges a weboldal adminisztrációjához';

$lang['step1_confirmation'] = 'Adatok rendben';
$lang['step1_err_db'] = 'A kapcsolat a kiszolválóval rendben, de nem sikerült csatlakozni az adatbázishoz';
$lang['step1_err_server'] = 'Nem sikerült kapcsolódni a szerverhez';
$lang['step1_err_copy_2'] = 'A következő lépés, most már indulhat a telepítés';
$lang['step1_err_copy_next'] = 'következő lépés';
$lang['step1_err_copy'] = 'Másolja ki a rózsaszín kötőjelek közötti szöveget, majd illessze be az "include/mysql.inc.php" fájlba (Figyelem! Csak a rózsaszín szövegrészt tartalmazza! Sortörések és üres karakterek nélkül!)';

$lang['step1_host'] = 'MySQL host';
$lang['step1_host_info'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['step1_user'] = 'Felhasználó név';
$lang['step1_user_info'] = 'a tárhelyszolgáltató által adott felhasználónév';
$lang['step1_pass'] = 'Jelszó';
$lang['step1_pass_info'] = 'a tárhelyen használt jelszó';
$lang['step1_database'] = 'Adatbázis neve';
$lang['step1_database_info'] = 'a szolgáltatótól kapott adatbázis név';
$lang['step1_prefix'] = 'Adatbázis tábla előtag';
$lang['step1_prefix_info'] = 'az adatbázis táblák ezzel az előtaggal fognak kezdődni (lehetővé teszi a táblák jobb áttekinthetőségét)';
$lang['step2_err_login1'] = 'írja be a webmester bejelentkezési adatokat';
$lang['step2_err_login3'] = 'A webmester nevében nem használhatók a \' és " karakterek';
$lang['step2_err_pass'] = 'kérjük, adja meg újra a jelszót';
$lang['install_end_title'] = 'Telepítés kész';
$lang['step2_pwd'] = 'Webmester jelszó';
$lang['step2_pwd_info'] = 'Kezelje bizalmasan az adatokat, ezek lehetővé teszik a hozzáférést az adminisztrációs felülethez';
$lang['step2_pwd_conf'] = 'Jelszó [megerősítés]';
$lang['step2_pwd_conf_info'] = 'jelszó egyezőségének ellenőrzése';
$lang['install_help'] = 'Segítségre van szüksége ? Kérdéseit itt teheti fel: <a href="%s">Piwigo üzenőfal</a>.';
$lang['install_end_message'] = 'A Piwigo konfigurálása befejeződött, jöhet a következő lépés<br /><br />
* menjen a Főoldalra és használja a webmester felhasználónév/jelszó párost.<br />
* a felhasználónév/jelszó segítségével eléri az adminisztrációs felületet, valamint lehetővé válik a felhasználók és képek kezelése';
$lang['conf_mail_webmaster'] = 'Webmester email cím';
$lang['conf_mail_webmaster_info'] = 'A látogatók ezen az email címen tudják felvenni a kapcsolatot az adminisztrátorral';

$lang['PHP 5 is required'] = 'PHP 5 szükséges';
$lang['It appears your webhost is currently running PHP %s.'] = 'Úgy tűnik, a tárhelyszolgáltatójánál jelenleg futó PHP %s.';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'Piwigo megpróbálhatja bekapcsolni a PHP 5-öt azáltal, hogy létrehoz vagy módosít egy .htaccess fájlt.';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = 'Figyelem: Ha meg tudja változtatni a PHP konfigurációt, indítsa újra a Piwigot.';
$lang['Try to configure PHP 5'] = 'Próbálja meg beállítani a PHP 5-öt';
$lang['Sorry!'] = 'Elnézést!';
$lang['Piwigo was not able to configure PHP 5.'] = 'Piwigo nem tudta beállítani a PHP 5-öt.';
$lang["You may referer to your hosting provider's support and see how you could switch to PHP 5 by yourself."] = "Lehet, hogy a tárhely szolgáltató támogatja a PHP 5-öt. A bekapcsoláshoz keresse meg őket.";
$lang['Hope to see you back soon.'] = 'Remélem később viszontlátjuk.';
?>