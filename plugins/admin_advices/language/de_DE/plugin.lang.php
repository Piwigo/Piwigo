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
$lang['An_advice_about'] = 'Ein neues Gutachten über';
$lang['contribute'] = 'wie Sie beitragen können'; 
$lang['navigation'] = 'Navigation';
$lang['Metadata'] = 'Metadaten';
$lang['current'] =  'Aktueller Wert: %s.';
$lang['TN-height&width'] = 'Thumbnail Höhe und Breite müssen gleich sein.';
$lang['Adv_case'][0] = array( /* **contribute */
 'Wenn Sie mit Ihrem eigenen Beitrag "Trick", danke für die Veröffentlichung',
 'auf den Forum von Piwigo (oder durch private Nachricht an einen der Entwickler),', 
 'und wir würden gerne um es innerhalb nächste Veröffentlichung.', );
$lang['Adv_case'][1] = array( /* newcat_default_status */
 'Versuchen $conf[\'newcat_default_status\'] = \'private\',',
 'Sie werden mehr Zeit haben zu beschreiben und überprüfen Sie Ihre Bilder.',
 'Zeit zu entscheiden zwischen privaten und öffentlichen Status.',
 'Wenn Sie privat wählen, Zeit zu vertreiben Genehmigung.',
 'Ihre neue Kategorie wird gut vorbereitet.', );
$lang['Adv_case'][2] = array( /* slideshow_period */
 'Dieser Wert könnte zu klein für niedrige Band-Verbindungen.',
 'Denken Sie an höheren Wert wie 4.', );
$lang['Adv_case'][3] = array( /* file_ext */
 'Sollte nie enthält Erweiterungen die ausgeführt werden können',
 'auf der Server-Seite wie *.php, *.php, *.asp,...', );
$lang['Adv_case'][4] = array( /* show_iptc_mapping */
 'Zeige die IPTC-Daten von Ihrem Bild:',
 ' 1 - Kopieren Sie eines Ihrer jpg Bilder (eine öffentliches) in ./tools/',
 ' 2 - Benennen Sie es als sample.jpg.',
 ' 3 - Führen Sie ./tools/metadata.php',
 ' 4 - Ergebnisse analysieren um festzustellen welche IPTC-Felder nützlich sein könnte für Ihre Besucher.',
 'Anfänger würde es vorziehen $conf[\'show_iptc\'] = false,',
 'Fortgeschrittene Benutzer würde kümmern $lang Werte und Auswirkungen auf die Vorlagen.', );
$lang['Adv_case'][5] = array( /* top_number */
 'Dieser Wert ist vielleicht zu hoch für niedrige Verbindungen hat, kann man 25-50 abhängig von Ihrem Thumbnail-Größen.', );
$lang['Adv_case'][6] = array( /* top_number */
 'Ein? Es könnte sein zu niedrig für Zufalls-Bilder hat kann man 5-10 abhängig von Ihrem Thumbnail-Größen.',  );
$lang['Adv_case'][7] = array( /* anti-flood_time */
 'Für normale Flow-Verarbeitung, Ihr Wert ist wahrscheinlich zu hoch. Angemessenen Wert ist 60 (Standard).' , );
$lang['Adv_case'][8] = array( /* calendar_datefield */
 'Autorisierten Werte sind ' ."'date_creation' oder 'date_available'" . ', Sonst können Sie unvorhersehbare Ergebnisse.' , );
$lang['Adv_case'][9] = array( /* calendar_datefield */
 "'date_creation'" . ' ist NICHT gefüllt von jedem aktiviert Verwendung von Metadaten-Mapping Felder aus.',
 'So aktivieren Metadaten Nutzung <strong>oder</strong> ändern bis zu $conf[\'calendar_datefield\']=\'date_available\'',
 'Aktivieren Sie Metadaten-Nutzung wie Sie wollen: ',
 '1 - $conf[\'use_iptc\'] = true, or $conf[\'use_exif\'] = true, jeder Weg wird korrekt.',
 '2 - Jeweils zu den einzelnen Änderungen:',
 '$conf[\'use_iptc_mapping\'] = array( ..., \'date_creation\'  => \'2#055\', ...',
 'oder/und:',
 '$conf[\'use_exif_mapping\'] = array(\'date_creation\' => \'DateTimeOriginal\', ...',
 '3 - Schließlich, eine neue Aufgabe liegt an Ihnen: Metadata-Synchronisierung.', );
$lang['Adv_case'][10] = array( /* newcat_default_visible */
 'Nicht sinnvoll, privaten Status ist besser, so dass Code $conf[\'newcat_default_visible\'] = true,', );
$lang['Adv_case'][11] = array( /* level_separator */
 'Probieren Sie etwas anderes wie $conf[\'level_separator\'] = \'+ \',',  );
$lang['Adv_case'][12] = array( /* paginate_pages_around */
 'Üblichen Bereich liegt zwischen 2 und 5. Mehr leichter, wählen Sie $conf[\'paginate_pages_around\'] = 2, ',
 'So bieten große Sprung, wählen Sie $conf[\'paginate_pages_around\'] = 7,', );
$lang['Adv_case'][13] = array( /* tn_width */
 'Sollte eine enge Wert auf Ihre Thumbnail-Breite. Üblichen Bereich liegt zwischen 96 und 150, über $conf[\'tn_width\'] = 128,', );
$lang['Adv_case'][14] = array( /* tn_height */
 'Sollte eine enge Wert auf Ihre Thumbnail-Höhe. Üblichen Bereich liegt zwischen 96 und 150, über $conf[\'tn_height\'] = 128,', );
$lang['Adv_case'][15] = array( /* tn_height */
 'Thumbnail Höhe und Breite müssen gleich sein.',
 'Wählen $conf[\'tn_height\'] = $conf[\'tn_width\'],',
 'oder $conf[\'tn_width\'] = $conf[\'tn_height\'],', );
$lang['Adv_case'][16] = array( /* show_version */
 'Aus Sicherheitsgründen, setzen Sie bitte $conf[\'show_version\'] = false,', );
$lang['Adv_case'][17] = array( /* show_thumbnail_caption */
 'Für eine leichtere Galerie müssen nur einen Blick bis zu $conf[\'show_thumbnail_caption\'] = false,', );
$lang['Adv_case'][18] = array( /* show_picture_name_on_title */
 'Für eine leichtere Galerie müssen nur einen Blick bis zu $conf[\'show_picture_name_on_title\'] = false,', );

$lang['Adv_case'][20] = array( /* allow_random_representative */
 'Verlassen $conf[\'allow_random_representative\'] = true, ',
 'aber analysieren, wenn Sie können verhindern, dass für Performance-Gründen.' , );
$lang['Adv_case'][21] = array( /* prefix_thumbnail */
 'Seien Sie vorsichtig, Ihre $conf[\'prefix_thumbnail\'] ist NICHT Standard.',
 'Ändern sich NICHT, es sei denn, Ihr Thumbnails sind NICHT sichtbar.',
 'Entfernten Standort übertragen verwenden Sie ein anderes Präfix aber create_listing_file.php muss geändert werden.',
 'Sie erhalten eine Warnmeldung während der Synchronisierung in diesem Fall.',
 'Versuchen Sie den gleichen Präfix durch alle Ihre Websites entweder lokal oder distants.',
 'Halten Sie diesen Parameter in Ihrer ./include/config_ <strong>local.inc.php</strong>',
 'Auf unserer Wiki Konfiguration Seite finden Sie weitere Informationen zum ./include/config_<strong>local.inc.php</strong>.', );
$lang['Adv_case'][22] = array( /* users_page */
 'Es sei denn, Sie haben ein niedriges Band-Anschluss besitzen, können erstellt $conf[\'users_page\'] auf einen höheren Wert, wenn Sie mehr als 20 Mitglieder.', );
$lang['Adv_case'][23] = array( /* mail_options */
 'Sollte falsch, nur wenige Webmaster haben um $conf[\'mail_options\'] = true, ',
 'Eine spezifische Beratung können Sie von einem fortgeschrittenen Benutzer auf unserem Forum in einigen Mailing-Fragen.', );
$lang['Adv_case'][24] = array( /* check_upgrade_feed */
 'Sollte falsch, nur PWG dev Team haben um $conf[\'check_upgrade_feed\'] = true, für Test-Zwecke.' , );
$lang['Adv_case'][25] = array( /* rate_items */
 'Ihr $conf[\'rate_items\'] hätte es 4 oder 5 Posten, nicht weniger', );
$lang['Adv_case'][26] = array( /* rate_items */
 'Ihr $conf[\'rate_items\'] hätte 5 oder 6 Posten, nicht mehr.',
 'Überprüfen Sie Ihre besten bewertet Bilder vor um einige Werte.',
 'Reduzieren Sie übermäßige Einschätzung des Hotels und ändern Sie Ihre $conf[\'rate_items\'].', );
$lang['Adv_case'][27] = array( /* show_iptc */
 'Könnte wahr sein, denken Sie etwa $conf[\'show_iptc\'] = false,',
 'Einige professionelle Fotografen wählen Sie falsche ihre Gründe dafür sind nicht wirklich professionell.' ,
 'NICHT zu verwechseln zwischen <strong>show</strong>_iptc und <strong>use</strong>_iptc (werfen Sie einen Blick auf Metadaten-Seite auf unserem Wiki).', );
$lang['Adv_case'][28] = array( /* use_iptc */
 'Dokumentare und professionellen Fotografen würde es stimmt, aber Anfänger sollten es als $conf[\'use_iptc\'] = false,',
 'Achten Sie darauf, der Hinweise auf Bereiche, in Metadaten-Synchronisierung.',
 'Genannten Bereichen wäre rewrited mit IPTC-Werte selbst in denjenigen sind NICHT leer.',
 'NICHT zu verwechseln zwischen <strong>show</strong>_iptc und <strong>use</strong>_iptc (werfen Sie einen Blick auf Metadaten-Seite auf unserem Wiki).', );
$lang['Adv_case'][29] = array( /* use_iptc */
 'Der Umgang mit IPTC:',
 '1 - Kopieren Sie eines Ihrer jpg Bilder (eine öffentliche eins) in ./tools/',
 '2 - Benennen Sie es als sample.jpg.',
 '3 - Start ./tools/metadata.php', 
 '4 - Ergebnisse analysieren, um festzustellen welche IPTC-Felder genutzt werden könnte um Datenbank-Felder.',
 'Anfänger würde es vorziehen $conf[\'use_iptc\'] = false,',
 'Fortgeschrittene Benutzer machen Dokumentation Anstrengungen vor dem Upload ihrer Bilder.',
 'IPTC-Felder müssen ausgefüllt werden beschrieben in $conf[\'use_iptc_mapping\']',
 'Auf jedem Fall, <strong>show</strong>_iptc_mapping und <strong>use</strong>_iptc_mapping muss völlig anders aus.', );
$lang['Adv_case'][30] = array( /* use_iptc_mapping */
 'Der Umgang mit IPTC:',
 'Achten Sie darauf, der Hinweise auf Bereiche, in Metadaten-Synchronisierung.',
 'Genannten Bereichen wäre rewrited mit IPTC-Werte selbst in denjenigen sind NICHT leer.',
 'Auf jedem Fall, <strong>show</strong>_iptc_mapping und <strong>use</strong>_iptc_mapping muss völlig anders aus.', );
$lang['Adv_case'][31] = array( /* show_exif */
 'Sollte wahr sein, einige Informationen von Ihrer Kamera angezeigt werden kann.',
 'Denken Sie über EXIF-Informationen könnten werden verschiedene nach Kamera-Modelle.',
 'Wenn Sie ändern Ihre Kamera diesen Bereichen könnte zum Teil anders.',
 'Viele professionelle Fotografen wählen Sie falsch, ihre Gründe dafür sind zum Schutz ihres Wissens.' ,
 'NICHT zu verwechseln zwischen <strong>show</strong>_exif und <strong>use</strong>_exif (werfen Sie einen Blick auf Metadaten-Seite auf unserem Wiki).', );
$lang['Adv_case'][32] = array( /* use_exif */
 'Dokumentare und professionellen Fotografen würde es stimmt, aber Anfänger sollten den Standardwert.',
 'Achten Sie darauf der Hinweise auf Bereiche in Metadaten-Synchronisierung.',
 'Genannten Bereichen wäre rewrited mit EXIF-Werte selbst in denjenigen sind NICHT leer.',
 'NICHT zu verwechseln zwischen <strong>show</strong>_exif und <strong>use</strong>_exif (werfen Sie einen Blick auf Metadaten-Seite auf unserem Wiki).', );
$lang['Adv_case'][33] = array( /* **navigation */
 'Sie können mit der Tastatur Pfeile zum Navigieren zwischen den Bildern.', );
$lang['Adv_case'][34] = array( /* compiled_template_cache_language */
 'Should be true, translation will be done at compilation time.',
 'If you are modifying a language (translators), you should consider to set it false.',
 'Setting it false, template @translate function are called at usage time.' );
$lang['Adv_case'][35] = array( /* template_compile_check */
 'Default is true, template changes are detected and the template is compiled.',
 'If you are not updating template any more for several days, ',
 'you should consider to set it false.',
 'Choosing false, template changes are not detected, this improves response time.',
 'Anyway Specials > Maintenance > Purge compiled templates, is recommended after this $conf change.' );
$lang['Adv_case'][19] = array( /* tags_default_display_mode */
 '\'cloud\' by default, importance of tags is shown with font size.',
 'You can change the tags page, $conf[\'tags_default_display_mode\'] = \'letters\'', );
?>