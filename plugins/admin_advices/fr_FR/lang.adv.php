<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
//$lang_info['language_name'] = 'Français';
//$lang_info['country'] = 'France';
//$lang_info['charset'] = 'iso-8859-1';
//$lang_info['direction'] = 'ltr';
//$lang_info['code'] = 'fr';
global $lang;
$lang['An_advice_about'] = 'Un nouveau conseil à propos de ';
$lang['Metadata'] = 'Méta-données';

foreach ($conf as $key => $value)
{
  if ( is_string($value) )
  {
    $bool = ($value == 'false') ? false : $value;
    $bool = ($value == 'true') ? true : $bool;
    $conf[$key] = $bool;
  }
}

//
//               Don't forget to update range for new advices
//
$cases = range(1,34);
srand ((double) microtime() * 10000000);
shuffle($cases);

$cond = false;
foreach ($cases as $id_adv)
{
  if ($cond) break;
  $adv = array();
  switch ($id_adv) {
    Case 1 :
      $adv[] = 'Valeur actuelle : public. ';
      $adv[] = 'Essayez $conf[\'newcat_default_status\'] = \'private\';';
      $adv[] = 'Vous aurez plus de temps pour décrire et contrôler vos images. '
             . 'Du temps pour vous décider entre un statut privé ou public.';
      $adv[] = 'Si vous choisissez de rester privé, vous passerez directement '
             . 'à l\'attribution des autorisations. <br />'
             . 'Vos nouvelles catégories seront préparées plus facilement.';
      $cond = ($conf['newcat_default_status'] !== 'public');
      $confk = 'newcat_default_status';
      break;

    Case 2 :
      $adv[] = 'Valeur actuelle : ' . (string) $conf['slideshow_period'] . '.';
      $adv[] = 'Ce délai pourrait être trop petit pour les connexions '
             . 'en bas débit.';
      $adv[] = 'Pensez à une valeur supérieure comme 4.';
      $cond = ( $conf['slideshow_period'] < 4 );
      $confk = 'slideshow_period';
      break;

    Case 3 :
      $adv[] = 'Valeur actuelle : ' . implode(', ', $conf['file_ext']) . '. ';
      $adv[] = 'Ne devrait jamais contenir des extensions pouvant être ';
      $adv[] = 'exécutées sur le serveur comme *.php, *.PHP, *.asp, ...';
      $cond = ( in_array('php',$conf['file_ext']) );
      $confk = 'file_ext';
      break;

    Case 4 :
      $adv[] = 'Comment gérer les IPTC:';
      $adv[] = ' 1 - Copiez une image jpg (publique) dans ./tools/<br />'
             . ' 2 - Renommez celle-ci en sample.jpg.<br />'
             . ' 3 - Lancez ./tools/metadata.php<br />'
             . ' 4 - Analysez les résultats pour déterminer quels champs '
             . 'IPTC pourraient intéresser vos visiteurs.';
      $adv[] = 'Les débutants laisseront $conf[\'show_iptc\'] = false;';
      $adv[] = 'Les utilisateurs avancés penseront aux valeurs du tableau '
             . '$lang; voire même à l\'impact possible sur les templates.';
      $cond = true;
      $confk = 'show_iptc_mapping';
      break;

     Case 5 :
      $adv[] = 'Valeur actuelle : ' . (string) $conf['top_number'] . '.';
      $adv[] = 'Cette valeur pourrait être trop grande pour des connexions '
             . 'bas débit.<br /> Pensez à une valeur située entre 25-50 '
             . 'en fonction de la taille de vos minitures.';
      $cond = ( $conf['top_number'] > 50 );
      $confk = 'top_number';
      break;

     Case 6 :
      $adv[] = 'Valeur actuelle : ' . (string) $conf['top_number'] . '.';
      $adv[] = 'Une seule? Au moins pour les images aléatoires, pensez '
             . 'autour de 5-10 selon la tailles de vos miniatures.';
      $cond = ( $conf['top_number'] < 2 ) ? true : false;
      $confk = 'top_number';
      break;

     Case 7 :
      $adv[] = 'Valeur actuelle : ' . (string) $conf['anti-flood_time'] . '.';
      $adv[] = 'Pour un traitement fluide, votre valeur est sans doute trop '
             . 'grande. Une valeur raisonnable serait 60 (valeur par défaut).' ;
      $cond = ( $conf['anti-flood_time'] > 100 ) ? true : false;
      $confk = 'anti-flood_time';
      break;

     Case 8 :
      $adv[] = 'Valeur actuelle : ' . (string) $conf['calendar_datefield'] .'.';
      $adv[] = 'Les valeurs admises sont '
             . "'date_creation' ou 'date_available'" . ', toute autre valeur'
             . 'peut aboutir à des résultats imprévisibles.' ;
      $cond = ( !in_array($conf['calendar_datefield'],
        array('date_creation','date_available')) );
      $confk = 'calendar_datefield';
      break;

     Case 9 :
      // If (iptc or exif) are used and date_creation is updated
      // Then it's Ok, you can use date_creation by default for calendar
      // else ... Advise
      $adv[] = 'Valeur actuelle : ' . (string) $conf['calendar_datefield'] .'.';
      $adv[] = "La 'date_creation'" . ' n\'est pas renseignée. Aucun champ '
             . 'des méta-données (use_) n\'actualise la base.';
      $adv[] = 'Soit vous activez l\'usage des méta-données <strong>ou'
             . '</strong> changez pour '
             . '$conf[\'calendar_datefield\'] = \'date_available\'';
      $adv[] = 'Activez l\'usage des méta-données simplement par: <br />'
             . '1 - $conf[\'use_iptc\'] = true; ou $conf[\'use_exif\'] = true; '
             . 'au choix, les 2 sont valables.<br />'
             . '2 - Respectivement à chacune faire la modif:<br />'
             . '$conf[\'use_iptc_mapping\'] = array( ..., \'date_creation\' '
             . '=> \'2#055\', ...<br />'
             . 'et/ou:<br />'
             . '$conf[\'use_exif_mapping\'] = array(\'date_creation\' '
             . '=> \'DateTimeOriginal\', ...<br />'
             . '3 - Enfin une nouvelle tache vous est destinée: '
             . 'la synchronisation des méta-données.' ;
      $cond2 = ( $conf['use_exif'] and
                isset($conf['use_exif_mapping']['date_creation']) );
      $cond3 = ( $conf['use_iptc'] and
                isset($conf['use_iptc_mapping']['date_creation']) );
      $cond = ( $conf['calendar_datefield'] == 'date_creation' );
      $cond = ( ($cond2 or $cond3) and $cond ) ? false : true;
      $confk = 'calendar_datefield';
      break;

     Case 10 :
      $adv[] = 'Valeur actuelle : false.';
      $adv[] = 'C\'est une erreur, un statut "private" est plus simple, '
             . 'alors choisissez $conf[\'newcat_default_visible\'] = true;' ;
      $cond = !$conf['newcat_default_visible'];
      $confk = 'newcat_default_visible';
      break;

     Case 11 :
      $adv[] = 'Valeur actuelle : ' . (string) $conf['level_separator'] . '.';
      $adv[] = 'Vous pouvez toujours essayer un autre séparateur commme :'
             . '<br />$conf[\'level_separator\'] = \'+ \';';
      $cond = ( $conf['level_separator'] == ' / ' );
      $confk = 'level_separator';
      break;

     Case 12 :
      $adv[] = 'Valeur actuelle : ' . (string) $conf['paginate_pages_around']
             . '.';
      $adv[] = 'Les valeurs habituelles se situent entre 2 et 5.'
             . 'Pour un site avec une interface légère, on choisira : <br />'
             . '$conf[\'paginate_pages_around\'] = 2; <br />'
             . 'Afin de proposer plus d\'accès directs, on choisira : <br />'
             . '$conf[\'paginate_pages_around\'] = 7;';
      $cond = (($conf['paginate_pages_around'] < 2)
            or ($conf['paginate_pages_around'] > 12));
      $confk = 'paginate_pages_around';
      break;

     Case 13 :
      $adv[] = 'Valeur actuelle : ' . (string) $conf['tn_width'] . '.';
      $adv[] = 'Doit être une valeur proche de la largeur de vos miniatures.';
      $adv[] = 'Les valeurs habituelles se situent entre 96 et 150, '
             . 'comme $conf[\'tn_width\'] = 128;';
      $cond = (($conf['tn_width'] < 66)
            or ($conf['tn_width'] > 180));
      $confk = 'tn_width';
      break;

     Case 14 :
      $adv[] = 'Valeur actuelle : ' . (string) $conf['tn_height'] . '.';
      $adv[] = 'Doit être une valeur proche de la hauteur de vos miniatures.';
      $adv[] = 'Les valeurs habituelles se situent entre 96 et 150, '
             . 'comme $conf[\'tn_height\'] = 128;';
      $cond = (($conf['tn_height'] < 66)
            or ($conf['tn_height'] > 180));
      $confk = 'tn_height';
      break;

     Case 15 :
      $adv[] = 'Il n\'y a aucune raison pour que la largeur maximale soit '
             . 'différente de la hauteur maximale. Pourquoi les ajouts en '
             . 'portrait afficheraient des miniatures dans une résolution '
             . 'différente de celle des miniatures en paysage?';
      $adv[] = 'Essayez $conf[\'tn_height\'] = ' . (string) $conf['tn_width']
             . ';<br />'
             . 'ou $conf[\'tn_width\'] = ' . (string) $conf['tn_height'] . ';';
      $cond = ( $conf['tn_height'] !== $conf['tn_width'] );
      $confk = 'tn_height';
      break;

     Case 16 :
      $adv[] = 'Valeur actuelle : true.';
      $adv[] = 'Pour des raisons de sécurité de votre galerie, préférez '
             . '$conf[\'show_version\'] = false;';
      $cond = $conf['show_version'];
      $confk = 'show_version';
      break;

     Case 17 :
      $adv[] = 'Valeur actuelle : true.';
      $adv[] = 'Pour une galerie moins chargée, faites le test de '
             . '$conf[\'show_thumbnail_caption\'] = false;';
      $cond = $conf['show_thumbnail_caption'];
      $confk = 'show_thumbnail_caption';
      break;

     Case 18 :
      $adv[] = 'Valeur actuelle : true.';
      $adv[] = 'Pour une galerie moins chargée, faites le test de '
             . '$conf[\'show_picture_name_on_title\'] = false;';
      $cond = $conf['show_picture_name_on_title'];
      $confk = 'show_picture_name_on_title';
      break;

     Case 19 :
      $adv[] = 'Valeur actuelle : true.';
      $adv[] = 'Aucune de vos catégories ne possède de descriptions alors '
             . 'essayez $conf[\'subcatify\'] = false;';
      $cond = $conf['subcatify'];
      $confk = 'subcatify';
      break;

     Case 20 :
      $adv[] = 'Valeur actuelle : true.';
      $adv[] = 'Laissez $conf[\'allow_random_representative\'] = true; <br />'
             . 'mais étudiez comment vous pouvez l\'éviter pour des raisons '
             . 'de performance.' ;
      $cond = $conf['allow_random_representative'];
      $confk = 'allow_random_representative';
      break;

     Case 21 :
      $adv[] = 'Valeur actuelle : ' . (string) $conf['prefix_thumbnail'] . '.';
      $adv[] = 'Attention, votre $conf[\'prefix_thumbnail\'] n\'est pas '
             . 'standard.';
      $adv[] = 'Ne pas changer votre préfixe sauf si vos miniatures ont un '
             . 'problème d\'affichage.';
      $adv[] = 'Un site distant peut avoir un préfixe différent, le '
             . 'create_listing_file.php devra être modifié.<br />'
             . 'Vous devriez avoir un message d\'avertissement pendant la '
             . 'synchronisation dans ce cas.';
      $adv[] = 'Essayez de garder le même préfixe de miniatures pour les sites '
             . 'locaux ou distants.';
      $adv[] = 'Conservez ce paramètre dans votre ./include/config_'
             . '<strong>local.inc.php</strong>. <br />'
             . 'Voir la page sur la configuration dans le Wiki pour plus '
             . 'd\'informations à propos de '
             . './include/config_<strong>local.inc.php</strong>.';
      $cond = ( $conf['prefix_thumbnail'] !== 'TN-' );
      $confk = 'prefix_thumbnail';
      break;

     Case 22 :
      $adv[] = 'Valeur actuelle : ' . (string) $conf['users_page'] . '.';
      $adv[] = 'A moins d\'avoir une connexion bas débit, vous pouvez '
             . 'augmenter largement $conf[\'users_page\'] '
             . 'surtout si vous avez plus de 20 membres.';
      $cond = ( $conf['users_page'] < 21 );
      $confk = 'users_page';
      break;

     Case 23 :
      $adv[] = 'Valeur actuelle : true.';
      $adv[] = 'Devrait être à false, seulement quelques webmasters devront '
             . 'indiquer $conf[\'mail_options\'] = true; <br />'
             . 'Un utilisateur avancé de notre forum les aura conseillé '
             . 'dans un seul cas de problème d\'email.' ;
      $cond = $conf['mail_options'];
      $confk = 'mail_options';
      break;

     Case 24 :
      $adv[] = 'Valeur actuelle : true.';
      $adv[] = 'Devrait être à false, seuls les membres de l\'équipe PWG '
             . 'codent $conf[\'check_upgrade_feed\'] = true; pour leurs tests.';
      $cond = $conf['check_upgrade_feed'];
      $confk = 'check_upgrade_feed';
      break;

     Case 25 :
      $adv[] = '$conf[\'rate_items\'] dispose de ' . count($conf['rate_items'])
             . 'éléments.';
      $adv[] = 'Votre $conf[\'rate_items\'] devrait avoir 4 ou 5 éléments '
             . 'mais pas moins.';
      $cond = ( count($conf['rate_items']) < 4 );
      $confk = 'rate_items';
      break;

     Case 26 :
      $adv[] = '$conf[\'rate_items\'] has ' . count($conf['rate_items'])
             . 'items.';
      $adv[] = 'Votre $conf[\'rate_items\'] devrait avoir 4 ou 5 éléments '
             . 'mais pas plus.';
      $adv[] = 'Contrôlez vos images les mieux notées avant de retirer '
             . ' certaines valeurs.'
             . '<br />Réduire les valeurs excessives et modifiez votre '
             . '$conf[\'rate_items\'].';
      $cond = ( count($conf['rate_items']) > 6 );
      $confk = 'rate_items';
      break;

     Case 27 :
      $adv[] = 'Valeur actuelle : true.';
      $adv[] = 'Peut être effectivement à true, éventuellement choisissez '
             . '$conf[\'show_iptc\'] = false;'
             . '<br />Comme quelques photographes professionnels choisissez '
             . 'false bien que leurs raisons ne soient guère professionnelles.';
      $adv[] = 'Ne confondez pas <strong>show</strong>_iptc et '
             . '<strong>use</strong>_iptc (consultez la pages de métadonnées '
             . 'sur notre wiki).';
      $cond = $conf['show_iptc'];
      $confk = 'show_iptc';
      break;

     Case 28 :
      $adv[] = 'Valeur actuelle : true.';
      $adv[] = 'Les documentalistes et photographes professionnels choisiront '
             . 'cette valeur true, mais les débutants devraient laisser '
             . '$conf[\'use_iptc\'] = false;';
      $adv[] = 'Faire attention aux champs mentionnés dans la synchronisation '
             . 'des métadonnées.<br />Les champs indiqués pourront être '
             . 'écrasés par des valeurs de champs IPTC quand bien même ces '
             . 'champs ne seraient pas vides.';
      $adv[] = 'Ne confondez pas <strong>show</strong>_iptc et '
             . '<strong>use</strong>_iptc (consultez la pages de métadonnées '
             . 'sur notre wiki).';
      $cond = $conf['use_iptc'];
      $confk = 'use_iptc';
      break;

     Case 29 :
      $adv[] = 'Comment gérer les IPTC:';
      $adv[] = ' 1 - Copiez une image jpg (publique) dans ./tools/<br />'
             . ' 2 - Renommez celle-ci en sample.jpg.<br />'
             . ' 3 - Lancez ./tools/metadata.php<br />'
             . ' 4 - Analysez les résultats pour déterminer quels champs '
             . 'IPTC pourraient intéresser vos visiteurs.';
      $adv[] = 'Les débutants laisseront $conf[\'use_iptc\'] = false;';
      $adv[] = 'Les utilisateurs avancés feront des efforts de documentation '
             . 'avant de transférer leurs images.<br />'
             . 'Les champs IPTC doivent être décrits par '
             . '$conf[\'use_iptc_mapping\']';
      $adv[] = 'Dans tous les cas, <strong>show</strong>_iptc_mapping et '
             . '<strong>use</strong>_iptc_mapping seront '
             . 'totalement différents.';
      $cond = true;
      $confk = 'use_iptc';
      break;

     Case 30 :
      $adv[] = 'Comment gérer les IPTC:';
      $adv[] = ' 1 - Copiez une image jpg (publique) dans ./tools/<br />'
             . ' 2 - Renommez celle-ci en sample.jpg.<br />'
             . ' 3 - Lancez ./tools/metadata.php<br />'
             . ' 4 - Analysez les résultats pour déterminer quels champs '
             . 'IPTC pourraient intéresser vos visiteurs.';
      $adv[] = 'Les débutants laisseront $conf[\'use_iptc\'] = false;';
      $adv[] = 'Les utilisateurs avancés feront des efforts de documentation '
             . 'avant de transférer leurs images.<br />'
             . 'Les champs IPTC doivent être décrits par '
             . '$conf[\'use_iptc_mapping\']';
      $adv[] = 'Faire attention aux champs mentionnés dans la synchronisation '
             . 'des métadonnées.<br />Les champs indiqués pourront être '
             . 'écrasés par des valeurs de champs IPTC quand bien même ces '
             . 'champs ne seraient pas vides.';
      $adv[] = 'Dans tous les cas, <strong>show</strong>_iptc_mapping et '
             . '<strong>use</strong>_iptc_mapping seront '
             . 'totalement différents.';
      $cond = true;
      $confk = 'use_iptc_mapping';
      break;

     Case 31 :
      $adv[] = 'Valeur actuelle : '
             . ( ( $conf['show_exif'] ) ? 'true':'false' ) . '.';
      $adv[] = 'Devrait être à true, certaines informations propres à votre '
             . 'appareil pourront être affichées.';
      $adv[] = 'Pensez au fait que les informations EXIF peuvent être '
             . 'différentes suivant les modèles d\'appareil.<br />'
             . 'Si vous changez votre appareil ces champs pourraient en '
             . 'partie differents.';
      $adv[] = 'Beaucoup de photographes professionnels choissent false, '
             . 'ceci afin de protéger leur savoir-faire.' ;
      $adv[] = 'Ne confondez pas <strong>show</strong>_exif et '
             . '<strong>use</strong>_exif (consultez la pages de métadonnées '
             . 'sur notre wiki).';
      $cond = true;
      $confk = 'show_exif';
      break;

     Case 32 :
      $adv[] = 'Comment gérer les EXIF:';
      $adv[] = ' 1 - Copiez une image jpg (publique) dans ./tools/<br />'
             . ' 2 - Renommez celle-ci en sample.jpg.<br />'
             . ' 3 - Lancez ./tools/metadata.php<br />'
             . ' 4 - Analysez les résultats pour déterminer quels champs '
             . 'EXIF pourraient intéresser vos visiteurs.';
      $adv[] = 'Les débutants laisseront la valeur par défaut.';
      $adv[] = 'Les utilisateurs avancés penseront aux valeurs du tableau '
             . '$lang; voire même à l\'impact possible sur les templates.';
      $adv[] = 'Dans tous les cas, <strong>show</strong>_exif_fields et '
             . '<strong>use</strong>_exif_mapping seront '
             . 'totalement différents.';
      $cond = true;
      $confk = 'show_exif_fields';
      break;

     Case 33 :
      $adv[] = 'Valeur actuelle : ' . ( ( $conf['use_exif'] ) ? 'true':'false' )
             . '.';
      $adv[] = 'Les documentalistes et photographes professionnels choisiront '
             . 'cette valeur true, mais les débutants devraient laisser '
             . 'la valeur par défaut.';
      $adv[] = 'Faire attention aux champs mentionnés dans la synchronisation '
             . 'des métadonnées.<br />Les champs indiqués pourront être '
             . 'écrasés par des valeurs de champs EXIF quand bien même ces '
             . 'champs ne seraient pas vides.';
      $adv[] = 'Ne confondez pas <strong>show</strong>_exif et '
             . '<strong>use</strong>_exif (consultez la pages de métadonnées '
             . 'sur notre wiki).';
      $cond = true;
      $confk = 'use_exif';
      break;

     Case 34 :
      $adv[] = 'Comment gérer les EXIF:';
      $adv[] = ' 1 - Copiez une image jpg (publique) dans ./tools/<br />'
             . ' 2 - Renommez celle-ci en sample.jpg.<br />'
             . ' 3 - Lancez ./tools/metadata.php<br />'
             . ' 4 - Analysez les résultats pour déterminer quels champs '
             . 'EXIF pourraient intéresser vos visiteurs.';
      $adv[] = 'Les débutants laisseront la valeur par défaut.';
      $adv[] = 'Les utilisateurs avancés penseront aux valeurs du tableau '
             . '$lang; voire même à l\'impact possible sur les templates.';
      $adv[] = 'Les débutants laisseront $conf[\'use_exif\'] = false;';
      $adv[] = 'Les utilisateurs avancés feront très attention aux champs '
             . 'sélectionnés et modifiés par la synchronisation.';
      $adv[] = 'Faire attention aux champs mentionnés dans la synchronisation '
             . 'des métadonnées.<br />Ces champs pourront être '
             . 'écrasés par des valeurs de champs EXIF quand bien même ces '
             . 'champs ne seraient pas vides.';
      $adv[] = 'Dans tous les cas, <strong>show</strong>_exif_fields et '
             . '<strong>use</strong>_exif_mapping seront '
             . 'totalement différents.';
      $cond = true;
      $confk = 'use_exif_mapping';
      break;
  }
}

?>
