<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
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
$lang['An_advice_about'] = 'Un nuevo consejo a propósito de';
$lang['contribute'] = 'Cómo contribuir'; 
$lang['navigation'] = 'Navegación';
$lang['Metadata'] = 'Méta-données';
$lang['current'] =  'Valor actual : %s.';
$lang['TN-height&width'] = 'Altura y anchura de miniatura debería ser plana.';
$lang['Adv_case'][0] = array( /* **contribute */
 'Si usted desea contribuir con su propia "astucia", gracias por publicarla',
 'Sobre los Foros de Piwigo (o por mensaje privado a uno de los desarrolladores),', 
 'Y seremos felices de añadirlo desde la publicación siguiente.', );
$lang['Adv_case'][1] = array( /* newcat_default_status */
 'Pruebe $conf[\'newcat_default_status\'] = \'private\';',
 'Usted tendrá más tiempo para describir y controlar su imágenes. ',
 'Del tiempo para decidirse entre un estatuto privado o público.',
 'Si usted escoge quedar privado, usted directamente pasa a la atribución de las autorizaciones. ',
 'Sus nuevas categorías serán preparadas más fácilmente.', );
$lang['Adv_case'][2] = array( /* slideshow_period */
 'Este plazo podría ser demasiado corto para las conexiones abajo gasto. ',
 'Piense en un valor superior como 4.', );
$lang['Adv_case'][3] = array( /* file_ext */
 'Jamás debería contener extensiones que podrían ser ejecutadas', 
 'sobre el servidor como *.php, *.PHP, *.asp, ...', );
$lang['Adv_case'][4] = array( /* show_iptc_mapping */
 'Cómo administrar el IPTC:',
 ' 1 - Copie una imagen jpg (pública) en ./tools/',
 ' 2 - Renombre éste sample.jpg.',
 ' 3 - Lance ./tools/metadata.php',
 ' 4 - Analice los resultados para determinar cuales campos ',
 'Los principiantes dejarán $conf[\'show_iptc\'] = false;',
 'Los usuarios avanzados pensarán en los valores del tablero $lang; Incluso al impacto posible sobre los templates.', );
$lang['Adv_case'][5] = array( /* top_number */
 'Este valor podría ser demasiado grande para conexiones baja velocidad.', 
 'Piense en un valor situado entre 25-50 con arreglo a lo talla de sus miniaturas.', );
$lang['Adv_case'][6] = array( /* top_number */
 '¿ Una única? Por lo menos para las imágenes aleatorias, piense alrededor de 5-10 según en tallas de sus miniaturas.', );
$lang['Adv_case'][7] = array( /* anti-flood_time */
 'Para un tratamiento fluido, su valor es sin duda demasiado grande. Un valor razonable sería 60 (valor por defecto).', );
$lang['Adv_case'][8] = array( /* calendar_datefield */
 'Los valores admitidos son ' . "'date_creation' ou 'date_available'" . ', muy diferente valor puede acabar en los resultados imprevisibles.', );
$lang['Adv_case'][9] = array( /* calendar_datefield */
 "' Date_creation ' ". ' no es informado. Ningún campo de los méta-datos (use_) actualiza la base.',
 'Seas acelera el uso de los méta-datos <strong>o</strong> cambie para $conf[\'calendar_datefield\'] = \'date_available\'',
 'Activate the usage of the méta-data simply by: ',
 '1 - $conf[\'use_iptc\'] = true; ou $conf[\'use_exif\'] = true; A la elección, los 2 son válidos.',
 '2 - Respectivamente a cada una hacer el modif:',
 '$conf[\'use_iptc_mapping\'] = array( ..., \'date_creation\' => \'2#055\', ...',
 'y/o:',
 '$conf[\'use_exif_mapping\'] = array(\'date_creation\' => \'DateTimeOriginal\', ...',
 '3 - Por fin una nueva mancha le está destinada: la sincronización de los méta-datos.', );
$lang['Adv_case'][10] = array( /* newcat_default_visible */
 'Es un error, un estatuto " private " es más simple, entonces escoja $conf[\'newcat_default_visible\'] = true;', );
$lang['Adv_case'][11] = array( /* level_separator */
 'Usted puede siempre probar otro separador como : $conf[\'level_separator\'] = \'+ \';', );
$lang['Adv_case'][12] = array( /* paginate_pages_around */
 'Los valores acostumbrados se sitúan entre 2 y 5. Para un sitio con una interfaz ligera, escogeramos : ',
 '$conf[\'paginate_pages_around\'] = 2;',
 'Con el fin de proponer más accesos directos, escogeramos: $conf[\'paginate_pages_around\'] = 7;', );
$lang['Adv_case'][13] = array( /* tn_width */
 'Debe ser un valor próximo de la anchura de sus miniaturas. Los valores acostumbrados se sitúan entre 96 y 150, como $conf[\'tn_width\'] = 128;', );
$lang['Adv_case'][14] = array( /* tn_height */
 'Debe ser un valor próximo de la altura de sus miniaturas. Los valores acostumbrados se sitúan entre 96 y 150, como $conf[\'tn_height\'] = 128;', );
$lang['Adv_case'][15] = array( /* tn_height */
 'Anchura y altura de miniatura deberían ser planas.',
 'Pruebe $conf[\'tn_height\'] = $conf[\'tn_width\'],',
 'o $conf[\'tn_width\'] = $conf[\'tn_height\'],', );
$lang['Adv_case'][16] = array( /* show_version */
 'Por razones de seguridad de su galería, prefiera $conf[\'show_version\'] = false;', );
$lang['Adv_case'][17] = array( /* show_thumbnail_caption */
 'Para una galería menos cargada, haga la prueba de $conf[\'show_thumbnail_caption\'] = false,', );
$lang['Adv_case'][18] = array( /* show_picture_name_on_title */
 'Para una galería menos cargada, haga la prueba de $conf[\'show_picture_name_on_title\'] = false,', );
$lang['Adv_case'][19] = array( /* tags_default_display_mode */
 'Por defecto a \'cloud\' (nube), más un "Tag" es utilizada más será escrito de allí grande.',
 'Usted puede cambiar la fijación de "tags", $conf[\'tags_default_display_mode\'] = \'letters\'', );
$lang['Adv_case'][20] = array( /* allow_random_representative */
 'Deje $conf[\'allow_random_representative\'] = true, ',
 'Pero estudie cómo usted podría evitarlo por razones de realización.' , );
$lang['Adv_case'][21] = array( /* prefix_thumbnail */
 'Atención, su $conf[\'prefix_thumbnail\']  no es estándar.',
 'No cambiar su prefijo excepto si sus miniaturas tienen un problema de fijación.',
 'Un sitio distante puede tener un prefijo diferente, le create_listing_file.php deberá ser modificado.',
 'Usted debería tener un mensaje de advertencia durante la sincronización en este caso.',
 'Trate de guardar el mismo prefijo de miniaturas para los sitios locales o distantes.',
 'Conserve este parámetro en vuestro  ./include/config_<strong>local.inc.php</strong>.',
 'Ver la página sobre la configuración en Wiki para más informaciones a propósito de ./include/config_<strong>local.inc.php</strong>.', );
$lang['Adv_case'][22] = array( /* users_page */
 'A menos que tener una conexión baja velocidad, usted ampliamente puede aumentar $conf[\'users_page\'] sobre todo si usted tiene más de 20 miembros.', );
$lang['Adv_case'][23] = array( /* mail_options */
 'Debería estar a false, solamente algún webmasters deberán indicar $conf[\'mail_options\'] = true;',
 'Un usuario avanzado de nuestro foro les habrá aconsejado en un solo caso de problema de e-mail.', );
$lang['Adv_case'][24] = array( /* check_upgrade_feed */
 'Debería estar a false, sólo los miembros del equipo Piwigo codifican $conf[\'check_upgrade_feed\'] = true; para sus pruebas.', );
$lang['Adv_case'][25] = array( /* rate_items */
 'Su $conf[\'rate_items\'] debería tener 4 o 5 elementos pero no menos.', );
$lang['Adv_case'][26] = array( /* rate_items */
 'Su $conf[\'rate_items\'] debería tener 4 o 5 elementos pero no más.',
 'Controle sus imágenes las mejor anotadas antes de retirar ciertos valores.',
 'Reducir los valores excesivos y modifique vuestro $conf[\'rate_items\'].', );
$lang['Adv_case'][27] = array( /* show_iptc */
 'Efectivamente puede a ser true, eventualmente escoja $conf[\'show_iptc\'] = false,',
 'Así como algunos fotógrafos profesionales escoja false aunque sus razones sean apenas profesionales.',
 'No confunda <strong>show</strong>_iptc y <strong>use</strong>_iptc (consulte las páginas con metadatos de nuestro wiki).', );
$lang['Adv_case'][28] = array( /* use_iptc */
 'Los documentalistas y los fotógrafos profesionales preferirán el valor true, pero los principiantes deberían dejar $conf[\'use_iptc\'] = false,',
 'Hacer atención en los campos mencionados en la sincronización del metadatos.',
 'Los campos indicados podrán ser aplastados por valores de campo IPTC aun cuando estos campos no estarían vacíos.',
 'No confunda <strong>show</strong>_iptc et <strong>use</strong>_iptc (consulte las páginas con metadatos de nuestro wiki).', );
$lang['Adv_case'][29] = array( /* use_iptc */
 'Cómo administrar IPTC:',
 '1 - Copie una imagen jpg (pública) en ./tools/',
 '2 - Renombre éste sample.jpg.',
 '3 - Lance ./tools/metadata.php',
 '4 - Analice los resultados para determinar cual campo IPTC podría completar su base de datos.',
 'Los principiantes dejarán $conf[\'use_iptc\'] = false,',
 'Los usuarios avanzados harán esfuerzos de documentación antes de trasladar sus imágenes.',
 'El campo IPTC debe ser descrito por $conf[\'use_iptc_mapping\']',
 'En todos los casos, <strong>show</strong>_iptc_mapping y <strong>use</strong>_iptc_mapping serán totalmente diferentes.', );
$lang['Adv_case'][30] = array( /* use_iptc_mapping */
 'Cómo administrar IPTC:',
 'Haga atención en los campos mencionados en la sincronización del metadatos.',
 'Los campos indicados podrán ser aplastados por valores de campo IPTC aun cuando estos campos no estarían vacíos.',
 'En todos los casos, <strong>show</strong>_iptc_mapping y <strong>use</strong>_iptc_mapping serán totalmente diferentes.', );
$lang['Adv_case'][31] = array( /* show_exif */
 'Debería estar a true, ciertas informaciones limpias de su aparato podrán ser fijadas.',
 'Piense en el hecho que las informaciones EXIF pueden ser diferentes según los modelos de aparato.',
 'Si usted cambia su aparato estos campos podrían en parte diferentes.',
 'Muchos fotógrafos profesionales escogen false, esto con el fin de proteger su destreza.' ,
 'No confunda <strong>show</strong>_exif y <strong>use</strong>_exif (Consulte las páginas con metadatos de nuestro wiki).', );
$lang['Adv_case'][32] = array( /* use_exif */
 'Los documentalistas y los fotógrafos profesionales preferirán el valor true, pero los principiantes dejarán el valor por defecto.',
 'Ocúpese de campos mencionados en la sincronización del metadatos.',
 'Los campos indicados podrán ser aplastados por valores de campos EXIF aun cuando estos campos no estarían vacíos.',
 'No confunda <strong>show</strong>_exif y <strong>use</strong>_exif (Consulte las páginas con metadatos de nuestro wiki).', );
$lang['Adv_case'][33] = array( /* **navigation */
 'Usted puede utilizar las flechas del teclado para navegar entre las imágenes.', );
$lang['Adv_case'][34] = array( /* compiled_template_cache_language */
 'Debería estar a true, la traducción será efectuada en el momento de la compilación.',
 'Si usted modifica los ficheros lenguas (traductores), usted debería contemplar el valor false.',
 'Situado en false, @translate templates serán tratados a cada utilización.' );
$lang['Adv_case'][35] = array( /* template_compile_check */
 'Por defecto en true, toda modificación de template es detectada y el template modificado es recompilado.',
 'Si usted no modifica más su templates durante varios días, ',
 'Usted debería contemplar el valor false.',
 'Escogiendo false, las modificaciones de template no son detectadas más, esto mejora tiempo de respuesta.',
 'En todos los casos Especiales > Mantenimiento > Purgar el templates compilado, es recomendado después de una modificación de este $conf.' );
?>