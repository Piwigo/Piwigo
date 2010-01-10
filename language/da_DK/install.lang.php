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
$lang['Initial_config'] = 'Basic konfiguration';
$lang['Default_lang'] = 'Default galleri sprog';
$lang['step1_title'] = 'Database konfiguration';
$lang['step2_title'] = 'Admin konfiguration';
$lang['Start_Install'] = 'Start Installation';
$lang['reg_err_mail_address'] = 'mail addresse skal være som xxx@yyy.eee (example : jack@altern.org)';
$lang['install_webmaster'] = 'Webmaster login';
$lang['install_webmaster_info'] = 'Det vil blive vist til de besøgende. Det er nødvendigt for website administration';
$lang['step1_confirmation'] = 'Parametre er korrekt';
$lang['step1_err_db'] = 'Forbindelse til server oprettet, men det var ikke muligt at forbinde til databasen';
$lang['step1_err_server'] = 'Kan ikke forbinde til serveren';
$lang['step1_err_copy_2'] = 'Næste del af installationen er nu mulige';
$lang['step1_err_copy_next'] = 'næste del';
$lang['step1_err_copy'] = 'Kopier teksten i pink mellem bindestregerne og kopier det ind i filen "include/mysql.inc.php"(Advarsel : mysql.inc.php må kun indeholde det i pink, ingen linieskift eller mellemrum)';
$lang['step1_host'] = 'MySQL host';
$lang['step1_host_info'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['step1_user'] = 'Bruger';
$lang['step1_user_info'] = 'bruger login som du har fra din udbyder';
$lang['step1_pass'] = 'Password';
$lang['step1_pass_info'] = 'bruger password som du har fra din udbyder';
$lang['step1_database'] = 'Database navn';
$lang['step1_database_info'] = 'som du også har fået af din udbyder';
$lang['step1_prefix'] = 'Database tabel prefix';
$lang['step1_prefix_info'] = 'database tabel navne vil være forvalgt (giver dig bedre mulighed for at administrere tabellerne)';
$lang['step2_err_login1'] = 'indtast et login  webmaster';
$lang['step2_err_login3'] = 'webmaster login må ikke indeholde karakterernecan \' or "';
$lang['step2_err_pass'] = 'indtast dit password igen';
$lang['install_end_title'] = 'Installationen er færdig';
$lang['step2_pwd'] = 'Webmaster password';
$lang['step2_pwd_info'] = 'Hold det hemmeligt, det giver dig adgang til administrations panelet.';
$lang['step2_pwd_conf'] = 'Password [bekræft]';
$lang['step2_pwd_conf_info'] = 'bekræftelse';
$lang['install_help'] = 'Hjælp ? Stil dine spørgsmål på <a href="%s">Piwigo message board</a>.';
$lang['install_end_message'] = 'Konfiguration af Piwigo er færdig, her er næste skridt<br /><br />
* gå til identifikations siden og brug login/password som du har fået af webmaster<br />
* dette login vil give dig mulighed for at komme til administrationspanelet og til instruktionernerne om hvordan du placerer billeder i dine biblioteker';
$lang['conf_mail_webmaster'] = 'Webmaster mail addresse';
$lang['conf_mail_webmaster_info'] = 'Besøgende vil have mulighed for at kontakte administratoren med denne mail';

$lang['PHP 5 is required'] = 'PHP 5 er nødvendig';
$lang['It appears your webhost is currently running PHP %s.'] = 'Din webhost har PHP %s.';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'Piwigo vil forsøgte at skifte din konfiguration til PHP 5 ved at ændre / oprette .htaccess file.';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = 'Bemærk at du kan ændre konfigurationen selv ved at genstarte Piwigo.';
$lang['Try to configure PHP 5'] = 'Prøv at konfigurere PHP 5';
$lang['Sorry!'] = 'Hov!';
$lang['Piwigo was not able to configure PHP 5.'] = 'Piwigo var ikke istand til af konfigurere PHP 5.';
$lang["You may referer to your hosting provider's support and see how you could switch to PHP 5 by yourself."] = "Du henvises til din webhost for at se hvordan du selv kan skifte til PHP 5.";
$lang['Hope to see you back soon.'] = 'Håber snart at se dig igen.';
?>