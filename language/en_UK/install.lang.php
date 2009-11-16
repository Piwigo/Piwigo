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

$lang['Installation'] = 'Installation';
$lang['Initial_config'] = 'Basic configuration';
$lang['Default_lang'] = 'Default gallery language';
$lang['step1_title'] = 'Database configuration';
$lang['step2_title'] = 'Admin configuration';
$lang['Start_Install'] = 'Start Install';
$lang['reg_err_mail_address'] = 'mail address must be like xxx@yyy.eee (example : jack@altern.org)';

$lang['install_webmaster'] = 'Webmaster login';
$lang['install_webmaster_info'] = 'It will be shown to the visitors. It is necessary for website administration';

$lang['step1_confirmation'] = 'Parameters are correct';
$lang['step1_err_db'] = 'Connection to server succeed, but it was impossible to connect to database';
$lang['step1_err_server'] = 'Can\'t connect to server';
$lang['step1_err_copy_2'] = 'The next step of the installation is now possible';
$lang['step1_err_copy_next'] = 'next step';
$lang['step1_err_copy'] = 'Copy the text in pink between hyphens and paste it into the file "include/config_database.inc.php"(Warning : config_database.inc.php must only contain what is in pink, no line return or space character)';

$lang['step1_host'] = 'MySQL host';
$lang['step1_host_info'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['step1_user'] = 'User';
$lang['step1_user_info'] = 'user login given by your host provider';
$lang['step1_pass'] = 'Password';
$lang['step1_pass_info'] = 'user password given by your host provider';
$lang['step1_database'] = 'Database name';
$lang['step1_database_info'] = 'also given by your host provider';
$lang['step1_prefix'] = 'Database table prefix';
$lang['step1_prefix_info'] = 'database tables names will be prefixed with it (enables you to manage better your tables)';
$lang['step2_err_login1'] = 'enter a login for webmaster';
$lang['step2_err_login3'] = 'webmaster login can\'t contain characters \' or "';
$lang['step2_err_pass'] = 'please enter your password again';
$lang['install_end_title'] = 'Installation finished';
$lang['step2_pwd'] = 'Webmaster password';
$lang['step2_pwd_info'] = 'Keep it confidential, it enables you to access administration panel';
$lang['step2_pwd_conf'] = 'Password [confirm]';
$lang['step2_pwd_conf_info'] = 'verification';
$lang['install_help'] = 'Need help ? Ask your question on <a href="%s">Piwigo message board</a>.';
$lang['install_end_message'] = 'The configuration of Piwigo is finished, here is the next step<br><br>
* go to the identification page and use the login/password given for webmaster<br>
* this login will enable you to access to the administration panel and to the instructions in order to place pictures in your directories';
$lang['conf_mail_webmaster'] = 'Webmaster mail address';
$lang['conf_mail_webmaster_info'] = 'Visitors will be able to contact site administrator with this mail';

$lang['PHP 5 is required'] = 'PHP 5 is required';
$lang['It appears your webhost is currently running PHP %s.'] = 'It appears your webhost is currently running PHP %s.';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = 'Note you can change your configuration by yourself and restart Piwigo after that.';
$lang['Try to configure PHP 5'] = 'Try to configure PHP 5';
$lang['Sorry!'] = 'Sorry!';
$lang['Piwigo was not able to configure PHP 5.'] = 'Piwigo was not able to configure PHP 5.';
$lang["You may referer to your hosting provider's support and see how you could switch to PHP 5 by yourself."] = "You may referer to your hosting provider's support and see how you could switch to PHP 5 by yourself.";
$lang['Hope to see you back soon.'] = 'Hope to see you back soon.';
?>