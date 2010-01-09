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

$lang['Installation'] = 'Instalación';
$lang['Initial_config'] = 'Configuración de Base';
$lang['Default_lang'] = 'Lengua por defecto de la galería';
$lang['step1_title'] = 'Configuración de la Base de datos';
$lang['step2_title'] = 'Configuración de la cuenta Administrador';
$lang['Start_Install'] = 'Empezar la instalación';
$lang['reg_err_mail_address'] = 'La dirección mail debe ser la forma xxx@yyy.eee (ejemplo: jack@altern.org)';

$lang['install_webmaster'] = 'Administrador';
$lang['install_webmaster_info'] = 'Este identificado aparecerá en todos sus visitadores. Le sirve para administrar la sitio';

$lang['step1_confirmation'] = 'Los parámetros entrados son correctos';
$lang['step1_err_db'] = 'La conexión al camarero(servidor) es O.K., pero imposible conectarse a esta base de datos';
$lang['step1_err_server'] = 'Imposible conectarse al servidor';

$lang['step1_dbengine'] = 'Database type';
$lang['step1_dbengine_info'] = 'The type of database your piwigo data will be store in';
$lang['step1_host'] = 'Huésped';
$lang['step1_host_info'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['step1_user'] = 'Utilizador';
$lang['step1_user_info'] = 'Nombre de utilizador para su hébergeur';
$lang['step1_pass'] = 'Palabra de paso';
$lang['step1_pass_info'] = 'El abastecido por su hébergeur';
$lang['step1_database'] = 'Nombre de la base';
$lang['step1_database_info'] = 'El abastecido por su hébergeur';
$lang['step1_prefix'] = 'Prefijo nombres de mesa';
$lang['step1_prefix_info'] = 'El nombre de las mesas aparecerá con este prefijo (permite administrar mejor su base de datos)';
$lang['step2_err_login1'] = 'Por favor, recoja un pseudo para el webmaster';
$lang['step2_err_login3'] = 'El pseudo del webmaster no debe contener carácter " y \'';
$lang['step2_err_pass'] = 'Por favor, arregle su palabra de paso';
$lang['install_end_title'] = 'Instalación acabada';
$lang['step2_pwd'] = 'Palabra de paso';
$lang['step2_pwd_info'] = 'Debe quedar confidencial, permite acceder al tabla de administración.';
$lang['step2_pwd_conf'] = 'Palabra de paso [Confirmar]';
$lang['step2_pwd_conf_info'] = 'Comprobación';
$lang['step1_err_copy'] = 'Copie el texto en rosa entre las rayas y pegúelo en el fichero config_database.inc.php que se encuentra en el repertorio " include " a la base del lugar donde usted instaló a Piwigo (el fichero config_database.inc.php debe contener SÓLO lo que está en rosa entre las rayas, ninguna vuelta a la línea o espacio es autorizado)';
$lang['install_help'] = '¿ Necesidad de ayudante? Plantee su cuestión sobre él <a href="%s">foro de Piwigo</a>.';
$lang['install_end_message'] = 'La configuración de la aplicación correctamente se celebró, coloca en la etapa próxima<br><br>
* Vaya sobre la página de identificación y conéctese con pseudo dado para el webmaster<br>
* Éste le permite acceder a la parte administración y a las instrucciones para colocar las imágenes en los repertorios.';
$lang['conf_mail_webmaster'] = 'Dirige e-mail del Administrador';
$lang['conf_mail_webmaster_info'] = 'Los visitadores podrán ponerse en contacto con usted por este mail';

$lang['PHP 5 is required'] = 'PHP 5 es requerido';
$lang['It appears your webhost is currently running PHP %s.'] = 'Aparentemente, la versión PHP de su hébergeur es PHP %s.';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'Piwigo va a tratar de pasar en PHP 5 creando o modificando el fichero .htaccess.';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = 'Anote que usted mismo puede cambiar la configuración PHP y volver a lanzar a Piwigo después.';
$lang['Try to configure PHP 5'] = 'Tratar de configurar PHP 5';
$lang['Sorry!'] = 'Desolado!';
$lang['Piwigo was not able to configure PHP 5.'] = 'Piwigo no pudo configurar PHP 5.';
$lang["You may referer to your hosting provider's support and see how you could switch to PHP 5 by yourself."] = 'Usted debe ponerse en contacto con su hébergeur con el fin de saber cómo configurar PHP 5';
$lang['Hope to see you back soon.'] = 'Esperando verle de revisarle muy pronto...';

$lang['step1_err_copy_2'] = 'La etapa próxima de instalación es posible en lo sucesivo';
$lang['step1_err_copy_next'] = 'Etapa siguiente';


?>