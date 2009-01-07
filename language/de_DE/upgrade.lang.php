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

$lang['Upgrade'] = 'Upgrade';
$lang['introduction message'] = 'Diese Seite schlägt vor, Ihre Datenbank auf die aktuelle Version upzugraden, die Ihre vorhandenen Daten enthält. 
Sie benutzen zur Zeit die Version <strong>release %s</strong> (oder kompatibel).';
$lang['Upgrade from %s to %s'] = 'Upgrade von der Version %s auf %s';
$lang['Statistics'] = 'Statistik';
$lang['total upgrade time'] = 'total upgrade time';
$lang['total SQL time'] = 'total SQL time';
$lang['SQL queries'] = 'SQL queries';
$lang['Upgrade informations'] = 'Upgrade Informationen';
$lang['perform a maintenance check'] = 'Führen Sie eine Datenbanküberprüfung durch in [Verwaltung>Erweiterte Einstellungen>Wartung] falls ein Problem auftritt.';
$lang['deactivated plugins'] = 'Als Vorsichtsmaßnahme wurden folgende Plugins deaktiviert. Prüfen Sie, ob ein Update verfügbar ist oder die Plugins kompatibel zur neuen Version sind, bevor Sie diese wieder aktivieren:';
$lang['upgrade login message'] = 'Nur Administratoren dürfen ein Upgrade durchführen. Bitte loggen Sie sich ein';
$lang['You do not have access rights to run upgrade'] = 'Sie haben nicht die erforderlichen Rechte ein Upgrade durchzuführen';
$lang['in include/mysql.inc.php, before ?>, insert:'] = 'In die Datei<i>include/mysql.inc.php</i>, vor <b>?></b>, bitte folgenden Text einfügen:';

// Upgrade informations from upgrade_1.3.1.php
$lang['all sub-categories of private categories become private'] = 'Alle Unterkategorien von privaten Kategorien bekommen den Status "private Kategorie"';
$lang['user permissions and group permissions have been erased'] = 'Alle Benutzer- und Gruppenberechtigungen/beschränkungen wurden entfernt';
$lang['only thumbnails prefix and webmaster mail saved'] = 'Es wurden nur die Thumbnail-Präfixe und die Mailadresse des Webmasters übernommen';

?>