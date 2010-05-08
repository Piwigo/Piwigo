<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
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

$lang['Upgrade'] = 'Aktualizacja';
$lang['Upgrade from version %s to %s'] = 'Aktualizacja z wersji %s do %s';
$lang['Statistics'] = 'Statystyki';
$lang['total upgrade time'] = 'sumaryczny czas aktualizacji';
$lang['total SQL time'] = 'symaryczny czas SQL';
$lang['SQL queries'] = 'zapytań SQL';
$lang['Upgrade informations'] = 'Informacje o aktualizacji';
$lang['Perform a maintenance check in [Administration>Specials>Maintenance] if you encounter any problem.'] = 'Jeżeli napotkasz jakiś problem wykonaj weryfikację przez [Administracja>Specjalne>Maintenance].';
$lang['Only administrator can run upgrade: please sign in below.'] = 'Tylko administrator może wykonać aktualizację: zaloguj się poniżej.';
$lang['You do not have access rights to run upgrade'] = 'Nie masz uprawnień do wykonania aktualizacji';

// Upgrade informations from upgrade_1.3.1.php
$lang['All sub-categories of private categories become private'] = 'Wszystkie podkategorie kategorii prywatnych staną się prywatne';
$lang['User permissions and group permissions have been erased'] = 'Uprawnienia użytkowników oraz grup zostały usunięte';
$lang['Only thumbnails prefix and webmaster mail address have been saved from previous configuration'] = 'Z poprzedniej konfiguracji zostały zapisane tylko prefixy miniatur oraz adres email administratora.';
//For version 2.1.0
$lang['This page proposes to upgrade your database corresponding to your old version of Piwigo to the current version. The upgrade assistant thinks you are currently running a <strong>release %s</strong> (or equivalent).'] = 'Ta strona proponuje aktualizację Twojej bazy danych Piwigo do nowej wersji. Asystent aktualizacji myśli, że aktualnie uzywasz <strong>wersji %s</strong> (lub równoważnej).';
$lang['As a precaution, following plugins have been deactivated. You must check for plugins upgrade before reactiving them:'] = 'Jako zabezpieczenie, nastepujące wtyczki zostały zdeaktywowane. Przed ich aktywacją musisz sprawdzić czy nie ma nowszych wersji:';
?>