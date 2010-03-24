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
$lang['Basic configuration'] = 'Configurazione di base';
$lang['Default gallery language'] = 'Linguaggio di default della galleria';
$lang['Database configuration'] = 'Configurazione della base dati';
$lang['Admin configuration'] = 'Configurazione dell\'utente "Amministratore"';
$lang['Start Install'] = 'Inizia l\'installazione';
$lang['mail address must be like xxx@yyy.eee (example : jack@altern.org)'] = 'L\'indirizzo email deve essere del tipo xxx@yyy.eee (ad esempio: cippalippa@libero.rio)';

$lang['Webmaster login'] = 'Amministratore';
$lang['It will be shown to the visitors. It is necessary for website administration'] = 'verrà mostrato ai visitatori. È necessario per l\'amministrazione del sito';

$lang['Parameters are correct'] = 'I parametri sono corretti';
$lang['Connection to server succeed, but it was impossible to connect to database'] = 'Connessione al server riuscita. Non è stato però possibile connettersi alla base dati';
$lang['Can\'t connect to server'] = 'Non è stato possibile connettersi al server';

$lang['Host'] = 'MySQL host';
$lang['localhost, sql.multimania.com, toto.freesurf.fr'] = 'localhost, sql.multimania.com, pluto.libero.it';
$lang['User'] = 'Utente';
$lang['user login given by your host provider'] = 'nome utente di login alla base dati fornito dal tuo provider';
$lang['Password'] = 'Password';
$lang['user password given by your host provider'] = 'La password d\'accesso alla base dati fornita dal tuo provider';
$lang['Database name'] = 'Nome della base dati';
$lang['also given by your host provider'] = 'fornitovi dal provider';
$lang['Database table prefix'] = 'Prefisso delle tabelle della base dati';
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = 'Le tabelle della base dati lo avranno come prefisso (permette di gestire meglio le tabelle)';
$lang['enter a login for webmaster'] = 'Inserire un nome utente per il webmaster';
$lang['webmaster login can\'t contain characters \' or "'] = 'Il nome utente del webmaster non può contenere caratteri come \' o "';
$lang['please enter your password again'] = 'Reinserire la password';
$lang['Installation finished'] = 'Installazione completata';
$lang['Webmaster password'] = 'Password';
$lang['Keep it confidential, it enables you to access administration panel'] = 'da conservare con cura. Permette l\'accesso al pannello di amministrazione';
$lang['Password [confirm]'] = 'Password [confermare]';
$lang['verification'] = 'verifica';
$lang['Copy the text in pink between hyphens and paste it into the file "local/config/database.inc.php"(Warning : database.inc.php must only contain what is in pink, no line return or space character)'] = 'Copiate il testo in rosa trà i trattini e mettetelo nel file mysql.inc.php che si trova nella directory "include" alla base del vostro sito dove aveto installato Piwigo (il file mysql.inc.php non deve contenere altro che ciò che è in rosa tra i trattini, nessun ritorno a capo o spazio è autorizzato)';
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = 'Bisogno di un aiuto? Visitate il <a href="%s">forum di Piwigo</a>.';
$lang['install_end_message'] = 'La configurazione di Piwigo è conclusa. Procedete al prossimo step<br /><br />
* collegatevi alla pagina d\'accesso e usare come nome d\'utente e password quello del Webmaster<br />
* a questo punto sarete abilitati all\'accesso al pannello di amministrazione in cui troverete le istruzioni per l\'inserimento delle immagini nelle vostre directory';
$lang['Webmaster mail address'] = 'Indirizzo email del Amministratore';
$lang['Visitors will be able to contact site administrator with this mail'] = 'i visitatori potranno contattarvi utilizzando questo indirizzo email';

$lang['PHP 5 is required'] = 'È necessario PHP 5';
$lang['It appears your webhost is currently running PHP %s.'] = 'Sembrerebbe che la versione del vostro server è PHP %s.';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'Piwigo cerchrà di passare in PHP 5 creando o modificando il file .htaccess.';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = 'Notate che potete cambiare manualmente la configurazione e rilanciare Piwigo.';
$lang['Try to configure PHP 5'] = 'Provate a configuratre PHP 5';
$lang['Sorry!'] = 'Spiacente!';
$lang['Piwigo was not able to configure PHP 5.'] = 'Piwigo non a potuto configurare PHP 5.';
$lang["You may referer to your hosting provider's support and see how you could switch to PHP 5 by yourself."] = 'Dovete contattare il votro provider per chiedere come configurare PHP 5.';
$lang['Hope to see you back soon.'] = 'Sperando rivedervi prossimamente ...';

$lang['The next step of the installation is now possible'] = 'Il prossimo step d\'installazione è oramail possibile';
$lang['next step'] = 'step successivo';

?>