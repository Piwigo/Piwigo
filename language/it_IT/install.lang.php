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

$lang['Installation'] = 'Installazione';
$lang['Initial_config'] = 'Configurazione di base';
$lang['Default_lang'] = 'Linguaggio di default della galleria';
$lang['step1_title'] = 'Configurazione della base dati';
$lang['step2_title'] = 'Configurazione del utente "Amministratore"';
$lang['Start_Install'] = 'Inizia l\'installazione';
$lang['reg_err_mail_address'] = 'L\'indirizzo email deve essere del tipo xxx@yyy.eee (ad esempio: cippalippa@libero.rio)';

$lang['install_webmaster'] = 'Amministratore';
$lang['install_webmaster_info'] = 'verrà mostrato ai visitatori. È necessario per l\'amministrazione del sito';

$lang['step1_confirmation'] = 'I parametri sono corretti';
$lang['step1_err_db'] = 'Connessione al server riuscita. Non è stato però possibile connettersi alla base dati';
$lang['step1_err_server'] = 'Non è stato possibile connettersi al server';

$lang['step1_host'] = 'MySQL host';
$lang['step1_host_info'] = 'localhost, sql.multimania.com, pluto.libero.it';
$lang['step1_user'] = 'Utente';
$lang['step1_user_info'] = 'nome utente di login alla base dati fornito dal tuo provider';
$lang['step1_pass'] = 'Password';
$lang['step1_pass_info'] = 'La password d\'accesso alla base dati fornita dal tuo provider';
$lang['step1_database'] = 'Nome della base dati';
$lang['step1_database_info'] = 'fornitovi dal provider';
$lang['step1_prefix'] = 'Prefisso delle tabelle della base dati';
$lang['step1_prefix_info'] = 'Le tabelle della base dati lo avranno come prefisso (permette di gestire meglio le tabelle)';
$lang['step2_err_login1'] = 'Inserire un nome utente per il webmaster';
$lang['step2_err_login3'] = 'Il nome utente del webmaster non può contenere caratteri come \' o "';
$lang['step2_err_pass'] = 'Reinserire la password';
$lang['install_end_title'] = 'Installazione completata';
$lang['step2_pwd'] = 'Password';
$lang['step2_pwd_info'] = 'da conservare con cura. Permette l\'accesso al pannello di amministrazione';
$lang['step2_pwd_conf'] = 'Password [confermare]';
$lang['step2_pwd_conf_info'] = 'verifica';
$lang['step1_err_copy'] = 'Copiate il testo in rosa trà i trattini e mettetelo nel file mysql.inc.php che si trova nella directory "include" alla base del vostro sito dove aveto installato Piwigo (il file mysql.inc.php non deve contenere altro che ciò che è in rosa tra i trattini, nessun ritorno a capo o spazio è autorizzato)';
$lang['install_help'] = 'Bisogno di un aiuto? Visitate il <a href="%s">forum di Piwigo</a>.';
$lang['install_end_message'] = 'La configurazione di Piwigo è conclusa. Procedete al prossimo step<br /><br />
* collegatevi alla pagina d\'accesso e usare come nome d\'utente e password quello del Webmaster<br />
* a questo punto sarete abilitati all\'accesso al pannello di amministrazione in cui troverete le istruzioni per l\'inserimento delle immagini nelle vostre directory';
$lang['conf_mail_webmaster'] = 'Indirizzo email del Amministratore';
$lang['conf_mail_webmaster_info'] = 'i visitatori potranno contattarvi utilizzando questo indirizzo email';
?>