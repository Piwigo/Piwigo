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

$lang['Upgrade'] = 'Upgrade';
$lang['introduction message'] = 'Strona służy do wykonania aktualizacji bazy danych Piwigo do aktualnej wersji.
Asystent aktualizacji odgadł, że aktualnie używasz <strong>wersji %s</strong> (lub podobnej).';
$lang['Upgrade from %s to %s'] = 'Aktualizacja z wersji %s do %s';
$lang['Statistics'] = 'Statystyki';
$lang['total upgrade time'] = 'sumaryczny czas aktualizacji';
$lang['total SQL time'] = 'symaryczny czas SQL';
$lang['SQL queries'] = 'zapytań SQL';
$lang['Upgrade informations'] = 'Informacje o aktualizacji';
$lang['perform a maintenance check'] = 'Jeżeli napotkasz jakiś problem wykonaj weryfikację przez [Administracja>Specjalne>Maintenance].';
$lang['deactivated plugins'] = 'W ramach zabezpieczenia zostąły deaktywowane następujące wtyczki. Przed ich ponowną aktywacją musisz sprawdzić dostępność aktualizacji dla nich:';
$lang['upgrade login message'] = 'Tylko administrator może wykonać aktualizację: zaloguj się poniżej.';
$lang['You do not have access rights to run upgrade'] = 'Nie masz uprawnień do wykonania aktualizacji';
$lang['in include/mysql.inc.php, before ?>, insert:'] = 'W pliku <i>include/mysql.inc.php</i>, przed <b>?></b>, wstaw:';

// Upgrade informations from upgrade_1.3.1.php
$lang['all sub-categories of private categories become private'] = 'Wszystkie podkategorie kategorii prywatnych staną się prywatne';
$lang['user permissions and group permissions have been erased'] = 'Uprawnienia użytkowników oraz grup zostały usunięte';
$lang['only thumbnails prefix and webmaster mail saved'] = 'Z poprzedniej konfiguracji zostały zapisane tylko prefixy miniatur oraz adres email administratora.';

?>