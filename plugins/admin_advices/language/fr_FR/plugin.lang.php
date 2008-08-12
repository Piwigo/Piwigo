<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
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
$lang['An_advice_about'] = 'Un nouveau conseil à propos de ';
$lang['contribute'] = 'comment contribuer'; 
$lang['navigation'] = 'navigation';
$lang['Metadata'] = 'Méta-données';
$lang['current'] =  'Valeur actuelle : %s.';
$lang['TN-height&width'] = 'Hauteur et largeur de miniature devrait être égales.';
$lang['Adv_case'][0] = array( /* **contribute */
 'Si vous souhaitez contribuer avec votre propre «astuce», merci de la publier',
 'sur les Forums de Piwigo (ou par message privé à l\'un des développeurs),', 
 'et nous serons heureux de l\'ajouter dès la publication suivante.', );
$lang['Adv_case'][1] = array( /* newcat_default_status */
 'Essayez $conf[\'newcat_default_status\'] = \'private\';',
 'Vous aurez plus de temps pour décrire et contrôler vos images. ',
 'Du temps pour vous décider entre un statut privé ou public.',
 'Si vous choisissez de rester privé, vous passerez directement à l\'attribution des autorisations. ',
 'Vos nouvelles catégories seront préparées plus facilement.', );
$lang['Adv_case'][2] = array( /* slideshow_period */
 'Ce délai pourrait être trop court pour les connexions en bas débit. ',
 'Pensez à une valeur supérieure comme 4.', );
$lang['Adv_case'][3] = array( /* file_ext */
 'Ne devrait jamais contenir des extensions pouvant être exécutées', 
 'sur le serveur comme *.php, *.PHP, *.asp, ...', );
$lang['Adv_case'][4] = array( /* show_iptc_mapping */
 'Comment gérer les IPTC:',
 ' 1 - Copiez une image jpg (publique) dans ./tools/',
 ' 2 - Renommez celle-ci en sample.jpg.',
 ' 3 - Lancez ./tools/metadata.php',
 ' 4 - Analysez les résultats pour déterminer quels champs ',
 'Les débutants laisseront $conf[\'show_iptc\'] = false;',
 'Les utilisateurs avancés penseront aux valeurs du tableau $lang; voire même à l\'impact possible sur les templates.', );
$lang['Adv_case'][5] = array( /* top_number */
 'Cette valeur pourrait être trop grande pour des connexions bas débit.', 
 'Pensez à une valeur située entre 25-50 en fonction de la taille de vos minitures.', );
$lang['Adv_case'][6] = array( /* top_number */
 'Une seule? Au moins pour les images aléatoires, pensez autour de 5-10 selon la tailles de vos miniatures.', );
$lang['Adv_case'][7] = array( /* anti-flood_time */
 'Pour un traitement fluide, votre valeur est sans doute trop grande. Une valeur raisonnable serait 60 (valeur par défaut).', );
$lang['Adv_case'][8] = array( /* calendar_datefield */
 'Les valeurs admises sont ' . "'date_creation' ou 'date_available'" . ', toute autre valeur peut aboutir à des résultats imprévisibles.', );
 $lang['Adv_case'][9] = array( /* calendar_datefield */
 "La 'date_creation'" . ' n\'est pas renseignée. Aucun champ des méta-données (use_) n\'actualise la base.',
 'Soit vous activez l\'usage des méta-données <strong>ou</strong> changez pour $conf[\'calendar_datefield\'] = \'date_available\'',
 'Activez l\'usage des méta-données simplement par: ',
 '1 - $conf[\'use_iptc\'] = true; ou $conf[\'use_exif\'] = true; au choix, les 2 sont valables.',
 '2 - Respectivement à chacune faire la modif:',
 '$conf[\'use_iptc_mapping\'] = array( ..., \'date_creation\' => \'2#055\', ...',
 'et/ou:',
 '$conf[\'use_exif_mapping\'] = array(\'date_creation\' => \'DateTimeOriginal\', ...',
 '3 - Enfin une nouvelle tache vous est destinée: la synchronisation des méta-données.', );
$lang['Adv_case'][10] = array( /* newcat_default_visible */
 'C\'est une erreur, un statut "private" est plus simple, alors choisissez $conf[\'newcat_default_visible\'] = true;', );
$lang['Adv_case'][11] = array( /* level_separator */
 'Vous pouvez toujours essayer un autre séparateur comme : $conf[\'level_separator\'] = \'+ \';', );
$lang['Adv_case'][12] = array( /* paginate_pages_around */
 'Les valeurs habituelles se situent entre 2 et 5. Pour un site avec une interface légère, on choisira : ',
 '$conf[\'paginate_pages_around\'] = 2;',
 'Afin de proposer plus d\'accès directs, on choisira : $conf[\'paginate_pages_around\'] = 7;', );
$lang['Adv_case'][13] = array( /* tn_width */
 'Doit être une valeur proche de la largeur de vos miniatures. Les valeurs habituelles se situent entre 96 et 150, comme $conf[\'tn_width\'] = 128;', );
$lang['Adv_case'][14] = array( /* tn_height */
 'Doit être une valeur proche de la hauteur de vos miniatures. Les valeurs habituelles se situent entre 96 et 150, comme $conf[\'tn_height\'] = 128;', );
$lang['Adv_case'][15] = array( /* tn_height */
 'Largeur et hauteur de miniature devraient être égales.',
 'Essayez $conf[\'tn_height\'] = $conf[\'tn_width\'],',
 'ou $conf[\'tn_width\'] = $conf[\'tn_height\'],', );
$lang['Adv_case'][16] = array( /* show_version */
 'Pour des raisons de sécurité de votre galerie, préférez $conf[\'show_version\'] = false;', );
$lang['Adv_case'][17] = array( /* show_thumbnail_caption */
 'Pour une galerie moins chargée, faites le test de $conf[\'show_thumbnail_caption\'] = false,', );
$lang['Adv_case'][18] = array( /* show_picture_name_on_title */
 'Pour une galerie moins chargée, faites le test de $conf[\'show_picture_name_on_title\'] = false,', );
$lang['Adv_case'][19] = array( /* subcatify */
 'Si aucune de vos catégories ne possède de description alors essayez $conf[\'subcatify\'] = false;', );
$lang['Adv_case'][20] = array( /* allow_random_representative */
 'Laissez $conf[\'allow_random_representative\'] = true, ',
 'mais étudiez comment vous pourriez l\'éviter pour des raisons de performance.' , );
$lang['Adv_case'][21] = array( /* prefix_thumbnail */
 'Attention, votre $conf[\'prefix_thumbnail\']  n\'est pas standard.',
 'Ne pas changer votre préfixe sauf si vos miniatures ont un problème d\'affichage.',
 'Un site distant peut avoir un préfixe différent, le create_listing_file.php devra être modifié.',
 'Vous devriez avoir un message d\'avertissement pendant la synchronisation dans ce cas.',
 'Essayez de garder le même préfixe de miniatures pour les sites locaux ou distants.',
 'Conservez ce paramètre dans votre ./include/config_<strong>local.inc.php</strong>.',
 'Voir la page sur la configuration dans le Wiki pour plus d\'informations à propos de ./include/config_<strong>local.inc.php</strong>.', );
$lang['Adv_case'][22] = array( /* users_page */
 'A moins d\'avoir une connexion bas débit, vous pouvez augmenter largement $conf[\'users_page\'] surtout si vous avez plus de 20 membres.', );
$lang['Adv_case'][23] = array( /* mail_options */
 'Devrait être à false, seulement quelques webmasters devront indiquer $conf[\'mail_options\'] = true;',
 'Un utilisateur avancé de notre forum les aura conseillé dans un seul cas de problème d\'email.', );
$lang['Adv_case'][24] = array( /* check_upgrade_feed */
 'Devrait être à false, seuls les membres de l\'équipe Piwigo codent $conf[\'check_upgrade_feed\'] = true; pour leurs tests.', );
$lang['Adv_case'][25] = array( /* rate_items */
 'Votre $conf[\'rate_items\'] devrait avoir 4 ou 5 éléments mais pas moins.', );
$lang['Adv_case'][26] = array( /* rate_items */
 'Votre $conf[\'rate_items\'] devrait avoir 4 ou 5 éléments mais pas plus.',
 'Contrôlez vos images les mieux notées avant de retirer certaines valeurs.',
 'Réduire les valeurs excessives et modifiez votre $conf[\'rate_items\'].', );
$lang['Adv_case'][27] = array( /* show_iptc */
 'Peut être effectivement à true, éventuellement choisissez $conf[\'show_iptc\'] = false,',
 'Comme quelques photographes professionnels choisissez false bien que leurs raisons ne soient guère professionnelles.',
 'Ne confondez pas <strong>show</strong>_iptc et <strong>use</strong>_iptc (consultez les pages sur les métadonnées de notre wiki).', );
$lang['Adv_case'][28] = array( /* use_iptc */
 'Les documentalistes et photographes professionnels préfèreront la valeur true, mais les débutants devraient laisser $conf[\'use_iptc\'] = false,',
 'Faire attention aux champs mentionnés dans la synchronisation des métadonnées.',
 'Les champs indiqués pourront être écrasés par des valeurs de champs IPTC quand bien même ces champs ne seraient pas vides.',
 'Ne confondez pas <strong>show</strong>_iptc et <strong>use</strong>_iptc (consultez les pages sur les métadonnées de notre wiki).', );
$lang['Adv_case'][29] = array( /* use_iptc */
 'Comment gérer les IPTC:',
 '1 - Copiez une image jpg (publique) dans ./tools/',
 '2 - Renommez celle-ci en sample.jpg.',
 '3 - Lancez ./tools/metadata.php',
 '4 - Analysez les résultats pour déterminer quels champs IPTC pourraient compléter votre base de données.',
 'Les débutants laisseront $conf[\'use_iptc\'] = false,',
 'Les utilisateurs avancés feront des efforts de documentation avant de transférer leurs images.',
 'Les champs IPTC doivent être décrits par $conf[\'use_iptc_mapping\']',
 'Dans tous les cas, <strong>show</strong>_iptc_mapping et <strong>use</strong>_iptc_mapping seront totalement différents.', );
$lang['Adv_case'][30] = array( /* use_iptc_mapping */
 'Comment gérer les IPTC:',
 'Faites attention aux champs mentionnés dans la synchronisation des métadonnées.',
 'Les champs indiqués pourront être écrasés par des valeurs de champs IPTC quand bien même ces champs ne seraient pas vides.',
 'Dans tous les cas, <strong>show</strong>_iptc_mapping et <strong>use</strong>_iptc_mapping seront totalement différents.', );
$lang['Adv_case'][31] = array( /* show_exif */
 'Devrait être à true, certaines informations propres à votre appareil pourront être affichées.',
 'Pensez au fait que les informations EXIF peuvent être différentes suivant les modèles d\'appareil.',
 'Si vous changez votre appareil ces champs pourraient en partie differents.',
 'Beaucoup de photographes professionnels choissent false, ceci afin de protéger leur savoir-faire.' ,
 'Ne confondez pas <strong>show</strong>_exif et <strong>use</strong>_exif (consultez les pages sur les métadonnées de notre wiki).', );
$lang['Adv_case'][32] = array( /* use_exif */
'Les documentalistes et photographes professionnels préfèreront la valeur true, mais les débutants laisseront la valeur par défaut.',
 'Take care of mentionned fields in metadata synchronization.',
 'Les champs indiqués pourront être écrasés par des valeurs de champs EXIF quand bien même ces champs ne seraient pas vides.',
 'Ne confondez pas <strong>show</strong>_exif et <strong>use</strong>_exif (consultez les pages sur les métadonnées de notre wiki).', );
$lang['Adv_case'][33] = array( /* **navigation */
 'Vous pouvez utiliser les flèches du clavier pour naviguer entre les images.', );
?>