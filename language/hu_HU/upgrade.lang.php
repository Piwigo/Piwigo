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

$lang['Upgrade'] = 'Frissítés';
$lang['introduction message'] = 'Ez az oldal felajánlja az adatbázis frissítést, hogy a Piwingó régi és új verziója megfeleljenek egymásnak.
A frissítő asszisztens megállapította, hogy a jelenlegi verzió: <strong> %s verzió</strong> (vagy azzal egyező).';
$lang['Upgrade from version %s to %s'] = 'Verziófrissítés %s verzióról %s verzióra';
$lang['Statistics'] = 'Statisztikák';
$lang['total upgrade time'] = 'teljes frissítési idő';
$lang['total SQL time'] = 'teljes SQL idő';
$lang['SQL queries'] = 'SQL lekérdezés';
$lang['Upgrade informations'] = 'Információk frissítése';
$lang['Perform a maintenance check in [Administration>Specials>Maintenance] if you encounter any problem.'] = 'Végezze el a karbantartás ellenőrzést [Adminisztráció>Speciális összetevők>Karbantartás] ha bármilyen problémával találkozna.';
$lang['As a precaution, following plugins have been deactivated. You must check for plugins upgrade before reactiving them:'] = 'Elővigyázatosságból a bővítmények ki vannak kapcsolva. Újraaktiválás előtt ellenőrizze, hogy a bövítmények kompattibilisek az új verzióval, ill. ellenőrizze, hogy elérhetők-e frissítések:';
$lang['Only administrator can run upgrade: please sign in below.'] = 'A frissítés csak rendszergazda jogosultsággal futtatható: kérjük jelentkezzen be.';
$lang['You do not have access rights to run upgrade'] = 'Önnek nincs jogosultsága a frissítés elvégzéséhez';
$lang['in include/mysql.inc.php, before ?>, insert:'] = 'In <i>include/mysql.inc.php</i>, before <b>?></b>, insert:';

// Upgrade informations from upgrade_1.3.1.php
$lang['All sub-categories of private categories become private'] = 'A magán kategóriák valamennyi alkategóriája magánkategóriává válik.';
$lang['User permissions and group permissions have been erased'] = 'A felhasználói és csoport jogosultságok törlésre kerültek';
$lang['Only thumbnails prefix and webmaster mail address have been saved from previous configuration'] = 'Csak a bélyegképek prefixe és a webmester email címe került mentésre az előző konfigurációból.';

?>