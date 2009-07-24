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
$lang['An_advice_about'] = 'Un\'nuovo consiglio about';
$lang['contribute'] = 'come contribuire'; 
$lang['navigation'] = 'navigazione';
$lang['Metadata'] = 'Meta-dati';
$lang['current'] =  'Valore attuale : %s.';
$lang['TN-height&width'] = 'Altezza e lunghezza delle miniature dovrebbero essere uguali.';
$lang['Adv_case'][0] = array( /* **contribute */
 'Se volete contribuire con i vostri propri "trucchi", pubblicateli pure',
 'sul forum di Piwigo (o inviateli per posta agli sviluppatori),',
 'e saremmo felici d\'aggiungerli nella la prossima versione.',);
$lang['Adv_case'][1] = array( /* newcat_default_status */
 'Trovate $conf[\'newcat_default_status\'] = \'private\',',
 'Avrete più tempo per descrivere e controllare le vostre immagini.',
 'Del tempo per decidere tra lo stato privato e pubblico. ',
 'Se scegliete di rimanere "privato", passerete direttamente all\'attribuzione delle autorizzazioni.',
 'Le vostre nuove categorie saranno pronte più facilmente.', );
$lang['Adv_case'][2] = array( /* slideshow_period */
 'Questo valore potrebbe essere troppo piccolo per le connessioni a bassa velocità.',
 'Pesate ad un valore più alto come 4.', );
$lang['Adv_case'][3] = array( /* file_ext */
 'Non dovrebbe mai contiene delle estensioni che possono essere eseguite',
 'sul server come *.php, *.PHP, *.asp, ...', );
$lang['Adv_case'][4] = array( /* show_iptc_mapping */
 'Come gestire i IPTC :',
 ' 1 - Copiate un\'immagine jpg (pubblica) in ./tools/',
 ' 2 - Rinominatela in sample.jpg.',
 ' 3 - Eseguite ./tools/metadata.php',
 ' 4 - Analizzate il risultato per identificare quali campi ',
 'I principianti lasceranno $conf[\'show_iptc\'] = false,',
 'Gli utenti più sperimentati si occuperanno dei valori della tabella $lang e degli impatti sui templates.', );
$lang['Adv_case'][5] = array( /* top_number */
 'Questo valore è forse troppo alto per i collegamenti a bassa velocità.',
 'Pensate ad un valore tra 25-50 in funzione delle dimenzioni delle miniature.', );
$lang['Adv_case'][6] = array( /* top_number */
 'Una sola? Almeno per le "immagini a caso", pensate ad un valore tra 5-10 circa in funzione delle dimenzioni delle miniature.',  );
$lang['Adv_case'][7] = array( /* anti-flood_time */
 'Per uno scorrimento normale, il vostro valore è probabilmente troppo alto. Un buon\' valore sarebbe 60 (valore predefinito).', );
$lang['Adv_case'][8] = array( /* calendar_datefield */
 'I valori autorizzati sono ' . "'date_creation' o 'date_available'" . ', tutt\'altro valore può dare risultato inaspettato.' , );
$lang['Adv_case'][9] = array( /* calendar_datefield */
 "'date_creation'" . ' é vuoto. Nessun campo dei metadati (use_) n\'attualizza la base.',
 'O attivate l\'uso dei metatati activate <strong>o</strong> cambiate a $conf[\'calendar_datefield\'] = \'date_available\'',
 'Attivate l\'uso dei metadati semplicemente con :',
 '1 - $conf[\'use_iptc\'] = true, o $conf[\'use_exif\'] = true; le due sonuzioni possono essere usate.',
 '2 - Respettivamente eseguire le modifiche:',
 '$conf[\'use_iptc_mapping\'] = array( ..., \'date_creation\' => \'2#055\', ...',
 'o/e:',
 '$conf[\'use_exif_mapping\'] = array(\'date_creation\' => \'DateTimeOriginal\', ...',
 '3 - In fine una nuova operazione é da eseguire : la sincronizzazione dei metadati synchronization.', );
$lang['Adv_case'][10] = array( /* newcat_default_visible */
 'E un errore, lo stato "private" é più semplice, allora scegliete $conf[\'newcat_default_visible\'] = true,', );
$lang['Adv_case'][11] = array( /* level_separator */
 'Potete sempre provare un altro separatore come : $conf[\'level_separator\'] = \'+ \',',  );
$lang['Adv_case'][12] = array( /* paginate_pages_around */
 'I valore di default si trovano tra 2 e 5. Per un sito con un interfacia "leggera" scegliere : ',
 '$conf[\'paginate_pages_around\'] = 2;',
 'Al fine d\'offrire più accessi diretti, scegliere : $conf[\'paginate_pages_around\'] = 7,', );
$lang['Adv_case'][13] = array( /* tn_width */
 'Deve essere un valore vicino alla larghezza delle vostre miniature. I valori di default sono tra 96 e 150, come $conf[\'tn_width\'] = 128,', );
$lang['Adv_case'][14] = array( /* tn_height */
 'Deve essere un valore vicino al altezza delle vostre miniature. I valori di default sono tra 96 e 150, come $conf[\'tn_height\'] = 128,', );
$lang['Adv_case'][15] = array( /* tn_height */
 'L\'altezza e la lunghezza delle miniature dovrebbero essere uguali.',
 'Provate $conf[\'tn_height\'] = $conf[\'tn_width\'],',
 'o $conf[\'tn_width\'] = $conf[\'tn_height\'],', );
$lang['Adv_case'][16] = array( /* show_version */
 'Per raggioni di sicurezza scegliete piuttosto $conf[\'show_version\'] = false,', );
$lang['Adv_case'][17] = array( /* show_thumbnail_caption */
 'Per una galleria meno "carica", provate con $conf[\'show_thumbnail_caption\'] = false,', );
$lang['Adv_case'][18] = array( /* show_picture_name_on_title */
 'Per una galleria meno "carica", provate con $conf[\'show_picture_name_on_title\'] = false,', );
$lang['Adv_case'][19] = array( /* tags_default_display_mode */
 'Di default impostato a \'cloud\' (nuvola), più un "tag" è utilizzato più sarà scritto in grande.',
 'Potete modificare la visualizzazione dei tags, $conf[\'tags_default_display_mode\'] = \'letters\'', );
$lang['Adv_case'][20] = array( /* allow_random_representative */
 'Lasciate pure $conf[\'allow_random_representative\'] = true, ',
 'ma provate a vedere come evitarlo per raggioni di performance.' , );
$lang['Adv_case'][21] = array( /* prefix_thumbnail */
 'Attenzione, il vostro $conf[\'prefix_thumbnail\'] non è di default.',
 'Non dovete cambiarlo eccetto se le vostre miniature non sono visibili',
 'Il sito distante potrebbe usare un prefisso diverso, il create_listing_file.php dovrà essere modificato',
 'In questo caso, dovreste avere un messaggio d\'avvertimento durante la sincronizzazione',
 'Provate a mantenere lo stesso prefisso per tutti i siti sia locali che distanti',
 'Tenere questo parametro nel vostro ./include/config_<strong>locale.inc.php</strong>',
 'Vedere la pagina di configurazione nel WIKI per maggiore informazioni su ./include/config_<strong>locale.inc.php</strong>. ',);
$lang['Adv_case'][22] = array( /* users_page */
 'Se avete una conessione a banda larga potete aumentare $conf[\'users_page\'] sopra tutto se avete più di 20 utenti registrati.', );
$lang['Adv_case'][23] = array( /* mail_options */
 'Dovrebbe essere a "false", solo qualche webmaster dovranno impostare $conf[\'mail_options\'] = true, ',
 'Un utente esperto del nostro forum ha consigliato questo valore per coloro che hanno avuto problemi con l\'Email.', );
$lang['Adv_case'][24] = array( /* check_upgrade_feed */
 'Dovrebbe essere a "false". Solo i sviluppatori della Team di PWG impostono $conf[\'check_upgrade_feed\'] = true, per i loro test.' , );
$lang['Adv_case'][25] = array( /* rate_items */
 'Il vostro $conf[\'rate_items\'] dovrebbe avere 4 o 5 elementi ma non meno.', );
$lang['Adv_case'][26] = array( /* rate_items */
 'Il vostro $conf[\'rate_items\'] dovrebbe avere 4 o 5 elementi ma non di più.',
 'Verificate le foto le più votate prima di togliere certi valori.',
 'Ridurre certi valori eccessivi e modificate il vostro $conf[\'rate_items\'].', );
$lang['Adv_case'][27] = array( /* show_iptc */
 'Puo essere a "true", scegliete piuttosto $conf[\'show_iptc\'] = false,',
 'Come certi fotografi professioali scegliete false anche se le loro raggioni non sono per forsa professionali.' ,
 'Non confondere <strong>show</strong>_iptc e <strong>use</strong>_iptc (consultate le pagine sui metadati nel WIKI).', );
$lang['Adv_case'][28] = array( /* use_iptc */
 'I documentaristi e fotografi professionnali lo regolerebbero a "true", ma i principianti dovrebbero lasciarlo a $conf[\'use_iptc\'] = false,',
 'Attenti ai campi mensionati durante la sincronizzazione dei metadati.',
 'I campi menzionati potrebbero essere sovrascritti con i valori dei campi IPTC anche se non sono vuoti',
 'Non confondere <strong>show</strong>_iptc e <strong>use</strong>_iptc (consultate le pagine sui metadati nel WIKI).', );
$lang['Adv_case'][29] = array( /* use_iptc */
 'Come gestire i IPTC:',
 '1 - Copiate un\'immagine jpg (pubblica) in ./tools/',
 '2 - Rinominatela in sample.jpg.',
 '3 - Eseguite ./tools/metadata.php',
 '4 - Analizzate il risultato per identificare quali campi IPTC potrebbero completale la vostra base dati',
 'I principianti lasceranno $conf[\'show_iptc\'] = false,',
 'Gli utenti più sperimentati documenteranno le loro foto prima di trasferirle sul sito.',
 'I campi IPTC devono essere elencati in $conf[\'use_iptc_mapping\']',
 'In tutti i casi, <strong>show</strong>_iptc_mapping e <strong>use</strong>_iptc_mapping saranno totalmente diversi.', );
$lang['Adv_case'][30] = array( /* use_iptc_mapping */
 'Come gestire i IPTC:',
 'Attenti ai campi mensionati durante la sincronizzazione dei metadati.',
 'I campi menzionati potrebbero essere sovrascritti con i valori dei campi IPTC anche se non sono vuoti',
 'In tutti i casi, <strong>show</strong>_iptc_mapping e <strong>use</strong>_iptc_mapping saranno totalmente diversi.', );
$lang['Adv_case'][31] = array( /* show_exif */
 'Dovrebbe essere a "true", certe informazioni della vostra macchina fotografica potrebbero essere visualizzate.',
 'Le informazioni EXIF possono essere diverse a seconda del modello della macchina fotografica.',
 'Se cambiate macchina fotografica, le informazioni visualizzate potrebbero cambiare.',
 'Parecchi fotografi professionali sceglieranno "false", per proteggere il loro lavoro.' ,
 'Non confondere <strong>show</strong>_iptc e <strong>use</strong>_iptc (consultate le pagine sui metadati nel WIKI).', );
$lang['Adv_case'][32] = array( /* use_exif */
 'I documentaristi e fotografi professionnali lo regolerebbero a "true", ma i principianti dovrebbero lasciare il valore di default.',
 'Take care of mentionned fields in metadata synchronization.',
 'I campi menzionati potrebbero essere sovrascritti con i valori dei campi EXIF anche se non sono vuoti',
 'Non confondere <strong>show</strong>_exif e <strong>use</strong>_exif (consultate le pagine sui metadati nel WIKI).', );
$lang['Adv_case'][33] = array( /* **navigation */
 'Potrete usare le frecce della tastiera per navigare tra le foto.', );
$lang['Adv_case'][34] = array( /* compiled_template_cache_language */
 'Doverbbe essere a "true", la traduzione sarà eseguta al momento della compilazione.',
 'Se modificate i files lingua (traduzioni), dovreste impostare il parametro a "false".',
 'Impostato a "false", i "@translate" dei templates saranno trattati ad ogni uso.' );
$lang['Adv_case'][35] = array( /* template_compile_check */
 'Di default a "true", ogni modifica sui templates è rilevata e il template modificato è compilato.',
 'Se non modificate i vostri templates per qualche giorno, ',
 'dovreste penasre ad impostarlo a "false".',
 'Scelgliendo "false", le modifiche dei templates non saranno più rilevate, ciò amegliora i tempi di risposta.',
 'Comunque Speciale > Manutezione > Spurgare i templates compilati, è consigliato dopo una modifica di $conf.' );
?>