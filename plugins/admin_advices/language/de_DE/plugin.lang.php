<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
// +-----------------------------------------------------------------------+
// | This program is free software, you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY, without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program, if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

// --------- Starting below: New or revised $lang ---- from Butterfly (1.8)
$lang['An_advice_about'] = 'Ein neuer Tipp für';
$lang['contribute'] = 'wie Sie beitragen können'; 
$lang['navigation'] = 'Navigation';
$lang['Metadata'] = 'Metadaten';
$lang['current'] =  'Aktueller Wert: %s.';
$lang['TN-height&width'] = 'Thumbnailhöhe und Breite müssen gleich sein.';
$lang['Adv_case'][0] = array( /* **contribute */
 'Wenn Sie mit Ihrem eigenen "Tipp" beitragen wollen, veröffentlichen Sie ihn bitte',
 'im Forum von Piwigo (oder durch eine private Nachricht an einen der Entwickler),', 
 'und wir werden ihn in der nächsten Version berücksichtigen.', );
$lang['Adv_case'][1] = array( /* newcat_default_status */
 'Versuchen Sie $conf[\'newcat_default_status\'] = \'private\', zu setzen',
 'Sie haben dann mehr Zeit, ihren neuen Bildern Beschreibungen zuzufügen und sie zu überprüfen.',
 'Zeit, zu entscheiden zwischen privaten und öffentlichen Status.',
 'Wenn Sie privat wählen, kann die Kategorie erst einmal niemand einsehen bis Sie sie endgültig freigeben.',
 'Ihre neue Kategorie wird gut vorbereitet.', );
$lang['Adv_case'][2] = array( /* slideshow_period */
 'Dieser Wert könnte zu klein für Schmalbandverbindungen.',
 'Denken Sie an einen höheren Wert wie z.B. 4.', );
$lang['Adv_case'][3] = array( /* file_ext */
 'Sollte nie Erweiterungen von ausführbaren Dateien enthalten',
 'auf der Server-Seite wie *.php, *.php, *.asp,...', );
$lang['Adv_case'][4] = array( /* show_iptc_mapping */
 'Zeige die IPTC-Daten von Ihrem Bild:',
 ' 1 - Kopieren Sie eines Ihrer jpg Bilder (ein öffentliches) in den Ordner ./tools/',
 ' 2 - Benennen Sie es um in sample.jpg.',
 ' 3 - Führen Sie ./tools/metadata.php aus',
 ' 4 - Ergebnisse analysieren um festzustellen welche IPTC-Felder nützlich sein könnte für Ihre Besucher.',
 'Anfängern empfehlen wir $conf[\'show_iptc\'] = false zu setzen,',
 'Fortgeschrittene Benutzer können die Werte von $lang analysieren und deren Auswirkungen auf die Vorlagen.', );
$lang['Adv_case'][5] = array( /* top_number */
 'Dieser Wert ist vielleicht zu hoch für Schmalbandverbindungen, 25-50 abhängig von der Größe der Thumbnails dürften gut sein.', );
$lang['Adv_case'][6] = array( /* top_number */
 'Eins? Es könnte sein zu niedrig für Zufalls-Bilder, 5-10 abhängig von der Größe der Thumbnails dürften gut sein.',  );
$lang['Adv_case'][7] = array( /* anti-flood_time */
 'Für normale Flow-Verarbeitung ist Ihr Wert wahrscheinlich zu hoch. Ein angemessenen Wert ist 60 (Standard).' , );
$lang['Adv_case'][8] = array( /* calendar_datefield */
 'Erlaubte Werte sind ' ."'date_creation' oder 'date_available'" . ', ansonsten bekommen Sie unvorhersehbare Ergebnisse.' , );
$lang['Adv_case'][9] = array( /* calendar_datefield */
 "'date_creation'" . ' ist NICHT nutzbar, da keine Metadaten verarbeitet werden.',
 'So aktivieren Sie die Metadatennutzung <strong>oder</strong> ändern Sie $conf[\'calendar_datefield\']=\'date_available\'',
 'Aktivieren Sie Metadaten-Nutzung wenn Sie wollen: ',
 '1 - $conf[\'use_iptc\'] = true, oder $conf[\'use_exif\'] = true, alle beiden Möglichkeiten werden unterstützt.',
 '2 - Jeweils zu den einzelnen Änderungen:',
 '$conf[\'use_iptc_mapping\'] = array( ..., \'date_creation\'  => \'2#055\', ...',
 'oder/und:',
 '$conf[\'use_exif_mapping\'] = array(\'date_creation\' => \'DateTimeOriginal\', ...',
 '3 - Schließlich liegt eine neue Aufgabe vor Ihnen: Metadata-Synchronisierung.', );
$lang['Adv_case'][10] = array( /* newcat_default_visible */
 'Nicht sinnvoll, Voreinstellung auf privaten Status ist besser, ändern Sie den Code  $conf[\'newcat_default_visible\'] = true,', );
$lang['Adv_case'][11] = array( /* level_separator */
 'Probieren Sie etwas anderes wie z.B. $conf[\'level_separator\'] = \'+ \',',  );
$lang['Adv_case'][12] = array( /* paginate_pages_around */
 'Der übliche Bereich liegt zwischen 2 und 5. Mehr ist leichter, wählen Sie $conf[\'paginate_pages_around\'] = 2, ',
 'So bieten Sie große Sprünge an, ändern Sie $conf[\'paginate_pages_around\'] = 7,', );
$lang['Adv_case'][13] = array( /* tn_width */
 'Stellt die Breite ihrer zu erstellenden Thumbnails ein. Der üblichen Bereich liegt zwischen 96 und 150 und wird so eingestellt: $conf[\'tn_width\'] = 128,', );
$lang['Adv_case'][14] = array( /* tn_height */
 'Stellt die Höhe ihrer zu erstellenden Thumbnails ein. Der üblichen Bereich liegt zwischen 96 und 150 und wird so eingestellt: $conf[\'tn_height\'] = 128,', );
$lang['Adv_case'][15] = array( /* tn_height */
 'Thumbnail Höhe und Breite müssen gleich sein.',
 'Wählen $conf[\'tn_height\'] = $conf[\'tn_width\'],',
 'oder $conf[\'tn_width\'] = $conf[\'tn_height\'],', );
$lang['Adv_case'][16] = array( /* show_version */
 'Aus Sicherheitsgründen, setzen Sie bitte $conf[\'show_version\'] = false,', );
$lang['Adv_case'][17] = array( /* show_thumbnail_caption */
 'Für eine leichtere Galerie werfen Sie einen Blick auf $conf[\'show_thumbnail_caption\'] = false,', );
$lang['Adv_case'][18] = array( /* show_picture_name_on_title */
 'Für eine leichtere Galerie werfen Sie einen Blick auf $conf[\'show_picture_name_on_title\'] = false,', );
$lang['Adv_case'][19] = array( /* tags_default_display_mode */
 '\'cloud\' standartmäßig wird die Bedeutung der Tags über die Schriftgröße angezeigt.',
 'Sie können die Tag-Seite so ändern, $conf[\'tags_default_display_mode\'] = \'letters\'', );
 $lang['Adv_case'][20] = array( /* allow_random_representative */
 'Lassen Sie den Wert stehen $conf[\'allow_random_representative\'] = true, ',
 'aber analysieren sie die Performance.' , );
$lang['Adv_case'][21] = array( /* prefix_thumbnail */
 'Seien Sie vorsichtig, Ihr Wert $conf[\'prefix_thumbnail\'] ist NICHT Standard.',
 'Ändern Sie ihn NICHT, es sei denn, Ihr Thumbnails sind NICHT sichtbar.',
 'Wenn auf entfernten Standorten ein anderes Präfix werwendet wird, ändern Sie es in der jeweiligen Datei create_listing_file.php.',
 'Sie erhalten eine Warnmeldung während der Synchronisierung in diesem Fall.',
 'Versuchen Sie den gleichen Präfix in all ihren lokalen wie entfernten Websites zu verwenden.',
 'Halten Sie diesen Parameter in Ihrer ./include/config_ <strong>local.inc.php</strong>',
 'Auf unserer Wiki Seite zur Konfiguration finden Sie weitere Informationen zum Thema  ./include/config_<strong>local.inc.php</strong>.', );
$lang['Adv_case'][22] = array( /* users_page */
 'Es sei denn, Sie haben einen Schmalbandanschluss, können Sie den Wert in $conf[\'users_page\'] auf einen höheren Werteinstellen. Zeigt aber nur Auswirkungen, wenn Sie mehr als 20 Mitglieder in der Galerie haben.', );
$lang['Adv_case'][23] = array( /* mail_options */
 'Sollte false sein, nur wenige Webmaster haben  einen Grund diese Option auf $conf[\'mail_options\'] = true zu ändern, ',
 'Für eine spezifische Beratung in diesem Fall besuchen Sie unser Forum. Ein fortgeschrittener User wird ihnen helfen', );
$lang['Adv_case'][24] = array( /* check_upgrade_feed */
 'Sollte false bleiben, ist nur für Entwickler interessant diesen Wert auf $conf[\'check_upgrade_feed\'] = true, für Test-Zwecke zu ändern.' , );
$lang['Adv_case'][25] = array( /* rate_items */
 'Ihr $conf[\'rate_items\'] sollte 4 oder 5 Posten haben, nicht weniger', );
$lang['Adv_case'][26] = array( /* rate_items */
 'Ihr $conf[\'rate_items\'] sollte 5 oder 6 Posten haben, nicht mehr.',
 'Überprüfen Sie Ihre am besten bewerteten Bilder bevor Sie Änderungen vornehmen.',
 'Reduzieren Sie übermäßige Bewertungen und ändern Sie Ihre $conf[\'rate_items\'].', );
$lang['Adv_case'][27] = array( /* show_iptc */
 'Kann auf true gesetzt werden, denken Sie über eine Änderung in $conf[\'show_iptc\'] = false nach,',
 'Einige professionelle Fotografen wählen false, ihre Gründe dafür sind nicht wirklich professionell.' ,
 'Bitte NICHT <strong>show</strong>_iptc und <strong>use</strong>_iptc verwechseln (werfen Sie einen Blick auf Metadaten-Seite auf unserem Wiki).', );
$lang['Adv_case'][28] = array( /* use_iptc */
 'Dokumentare und professionellen Fotografen würde es auf true setzen, aber Anfänger sollten es auf $conf[\'use_iptc\'] = false belassen,',
 'Achten Sie auf die Felder die in der Metadaten-Synchronisierung erwähnt werden.',
 'Genannte Bereichen werden neu beschrieben mit den IPTC-Werten selbst diejenigen, die NICHT leer sind.',
 'NICHT zu verwechseln mit <strong>show</strong>_iptc und <strong>use</strong>_iptc (werfen Sie einen Blick auf Metadaten-Seite auf unserem Wiki).', );
$lang['Adv_case'][29] = array( /* use_iptc */
 'Der Umgang mit IPTC:',
 '1 - Kopieren Sie eines Ihrer jpg Bilder (ein öffentliches) in ./tools/',
 '2 - Benennen Sie es um in sample.jpg.',
 '3 - Starten Sie ./tools/metadata.php', 
 '4 - Ergebnisse analysieren, um festzustellen welche IPTC-Felder genutzt werden können um sie in der Datenbank zu überschreiben.',
 'Anfängern empfehlen wir $conf[\'show_iptc\'] = false zu setzen,',
 'Fortgeschrittene Benutzer analysieren die Bildinformationen vor dem Upload.',
 'IPTC-Felder werden beschrieben in $conf[\'use_iptc_mapping\']',
 'Auf jedem Fall, <strong>show</strong>_iptc_mapping und <strong>use</strong>_iptc_mapping müssen völlig verschieden sein.', );
 $lang['Adv_case'][30] = array( /* use_iptc_mapping */
 'Der Umgang mit IPTC:',
 'Achten Sie auf Hinweise in den Berichten der Metadaten-Synchronisierung.',
 'Genannte Bereichen werden neu beschrieben mit den IPTC-Werten selbst diejenigen, die NICHT leer sind.',
 'Auf jedem Fall, <strong>show</strong>_iptc_mapping und <strong>use</strong>_iptc_mapping müssen völlig verschieden sein.', );
$lang['Adv_case'][31] = array( /* show_exif */
 'Sollte wahr sein, einige Informationen von Ihrer Kamera angezeigt werden kann.',
 'Denken Sie über EXIF-Informationen könnten werden verschiedene nach Kamera-Modelle.',
 'Wenn Sie ändern Ihre Kamera diesen Bereichen könnte zum Teil anders.',
 'Viele professionelle Fotografen wählen Sie falsch, ihre Gründe dafür sind zum Schutz ihres Wissens.' ,
 'NICHT zu verwechseln zwischen <strong>show</strong>_exif und <strong>use</strong>_exif (werfen Sie einen Blick auf Metadaten-Seite auf unserem Wiki).', );
$lang['Adv_case'][32] = array( /* use_exif */
 'Dokumentare und professionellen Fotografen würde es auf true setzen, aber Anfänger sollten es auf dem Standartwert belassen.',
 'Achten Sie auf Hinweise nach der Metadaten-Synchronisierung.',
 'Genannte Bereichen werden neu beschrieben mit den EXIF-Werten selbst diejenigen, die NICHT leer sind.',
 'Verwechseln Sie bitte nicht <strong>show</strong>_exif mit <strong>use</strong>_exif (werfen Sie einen Blick auf Metadaten-Seite auf unserem Wiki).', );
$lang['Adv_case'][33] = array( /* **navigation */
 'Sie können mit den Pfeil-Tasten ihrer Tastatur zwischen den Bildern navigieren.', );
$lang['Adv_case'][34] = array( /* compiled_template_cache_language */
 'Sollte auf true belassen werden.',
 'Wenn Sie selbst eine Übersetzung ändern können Sie den Wert auf false ändern.',
 'Wenn der Wert false eingetragen ist, wird template @translate Function wird so lang aufgerufen.' );
$lang['Adv_case'][35] = array( /* template_compile_check */
 'Standart ist true, Änderungen am Template werden erkannt und das Template compiliert.',
 'Wenn sie das Template mehrere Tage nicht bearbeiten, ',
 'setzen Sie den Wert bitte auf false.',
 'Wenn der Wert auf false steht, wird  nicht nach Änderungen an den Templates gesucht. Dies verbessert die Antwortzeiten.',
 'Erweiterte Verwaltung > Erweiterte EinstellungenMaintenance > Löschen der kompilierten Templates, wird empfohlen nach einer Änderung von $conf change.' );
?>