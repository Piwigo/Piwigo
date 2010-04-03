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

$lang['Installation'] = 'Instalación';
$lang['Basic configuration'] = 'Configuración de Base';
$lang['Default gallery language'] = 'Idioma por defecto de la galería';
$lang['Database configuration'] = 'Configuración de la Base de datos';
$lang['Admin configuration'] = 'Configuración de la cuenta Administrador';
$lang['Start Install'] = 'Empezar la instalación';
$lang['mail address must be like xxx@yyy.eee (example : jack@altern.org)'] = 'La dirección mail debe ser de la forma xxx@yyy.eee (ejemplo: jack@altern.org)';

$lang['Webmaster login'] = 'Administrador';
$lang['It will be shown to the visitors. It is necessary for website administration'] = 'Este identificador aparecerá a todos sus visitantes. Le sirvira para administrar el sitio';

$lang['Connection to server succeed, but it was impossible to connect to database'] = 'La conexión al servidor es O.K., pero es imposible conectarse a esta base de datos';
$lang['Can\'t connect to server'] = 'Imposible conectarse al servidor';

$lang['Host'] = 'Huésped MySQL';
$lang['localhost, sql.multimania.com, toto.freesurf.fr'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['User'] = 'Usuario';
$lang['user login given by your host provider'] = 'Nombre de usuario para su alojador web';
$lang['Password'] = 'Palabra de paso';
$lang['user password given by your host provider'] = 'El proporcionado por su alojador web';
$lang['Database name'] = 'Nombre de la base';
$lang['also given by your host provider'] = 'El proporcionado por su alojador web';
$lang['Database table prefix'] = 'Prefijo nombres de mesa';
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = 'El nombre de las tablas aparecerá con este prefijo (permite administrar mejor su base de datos)';
$lang['enter a login for webmaster'] = 'Por favor, escriba un pseudo para el webmaster';
$lang['webmaster login can\'t contain characters \' or "'] = 'El pseudo del webmaster no debe contener carácter " y \'';
$lang['please enter your password again'] = 'Por favor, ponga su contraseña';
$lang['Webmaster password'] = 'Contraseña';
$lang['Keep it confidential, it enables you to access administration panel'] = 'Debe quedar confidencial, permite acceder al panel de administración.';
$lang['Password [confirm]'] = 'Contraseña [Confirmar]';
$lang['verification'] = 'Comprobación';
$lang['Copy the text in pink between hyphens and paste it into the file "local/config/database.inc.php"(Warning : database.inc.php must only contain what is in pink, no line return or space character)'] = 'Copie el texto en rosa entre los guillones y pegúelo en el fichero mysql.inc.php que se encuentra en el repertorio " include " a la base del lugar donde usted instaló  Piwigo (el fichero mysql.inc.php debe contener SÓLO lo que está en rosa entre las rayas, ninguna vuelta a la línea o espacio es autorizado)';
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = '¿ Necesidad de ayuda? Plantee su pregunta sobre él <a href="%s">foro de Piwigo</a>.';
$lang['Webmaster mail address'] = 'E-mail del Administrador';
$lang['Visitors will be able to contact site administrator with this mail'] = 'Los visitantes podrán ponerse en contacto con usted por este mail';

$lang['PHP 5 is required'] = 'PHP 5  requerido';
$lang['It appears your webhost is currently running PHP %s.'] = 'Aparentemente, la versión PHP de su alojador web es PHP %s.';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'Piwigo va a tratar de pasar en PHP 5 creando o modificando el fichero .htaccess.';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = 'Note que usted mismo puede cambiar la configuración PHP y volver a lanzar  Piwigo después.';
$lang['Try to configure PHP 5'] = 'Trate de configurar PHP 5';
$lang['Sorry!'] = 'Lo siento!';
$lang['Piwigo was not able to configure PHP 5.'] = 'Piwigo no pudo configurar PHP 5.';
$lang["You may referer to your hosting provider's support and see how you could switch to PHP 5 by yourself."] = 'Usted debe ponerse en contacto con su alojador web con el fin de saber cómo configurar PHP 5';
$lang['Hope to see you back soon.'] = 'Esperando verle  muy pronto...';


$lang['Database type'] = 'Tipo de base de datos';
$lang['The type of database your piwigo data will be store in'] = 'La base de datos en la cual será almacenado su dato Piwigo';
$lang['Congratulations, Piwigo installation is completed'] = 'Félicitation, Piwigo está completamente instalado';

$lang['An alternate solution is to copy the text in the box above and paste it into the file "local/config/database.inc.php" (Warning : database.inc.php must only contain what is in the textarea, no line return or space character)'] = 'Una solución alternativa es copiar el texto en la zona más abajo and de pegarlo en el fichero  "local/config/database.inc.php" (Atención: database.inc.php debe contener sólo lo que se encuentra en la zona, no de regreso a la línea, ningún espacio';
$lang['Creation of config file local/config/database.inc.php failed.'] = 'La creación del fichero de configuración local/config/database.inc.php fue suspendido.';
$lang['Download the config file'] = 'Descargar el fichero de configuración';
$lang['You can download the config file and upload it to local/config directory of your installation.'] = 'Usted puede descargar el fichero de configuración y carga en el repertorio local/config de su instalación Piwigo.';

?>