<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
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



$lang['Installation'] = 'Yükleme';
$lang['Basic configuration'] = 'Temel yapılandırma';
$lang['Default gallery language'] = 'Varsayılan galeri dili';
$lang['Database configuration'] = 'Veritabanı yapılandırması';
$lang['Admin configuration'] = 'Admin yapılandırması';
$lang['Start Install'] = 'Yüklemeye Başla';
$lang['mail address must be like xxx@yyy.eee (example : jack@altern.org)'] = 'E-Posta adresiniz böyle olmalı: xxx@yyy.eee (örnek : erkan@test.org)';
$lang['Webmaster login'] = 'Site yöneticisi giriş';
$lang['It will be shown to the visitors. It is necessary for website administration'] = 'Bu ziyaretçilere gösterilecek. Bu web sitesi yönetimi için gereklidir';
$lang['Connection to server succeed, but it was impossible to connect to database'] = 'Sunucuya bağlantı başarılı, ama veritabanına bağlantı kurulamadı';
$lang['Can\'t connect to server'] = 'Sunucuya bağlanamadı';
$lang['Host'] = 'Host';
$lang['localhost, sql.multimania.com, toto.freesurf.fr'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['User'] = 'Kullanıcı';
$lang['user login given by your host provider'] = 'host sağlağıcı tarafından size verilen kullanıcı girişi';
$lang['Password'] = 'Şifre';
$lang['user password given by your host provider'] = 'host sağlağıcı tarafından size verilen şifre';
$lang['Database name'] = 'Veritabanı adı';
$lang['also given by your host provider'] = 'host sağlağıcı tarafından verildi';
$lang['Database table prefix'] = 'Veritabanı tabloları öneki';
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = 'veritabanı tablolarını isimlerini onunla öneki eklenecektir (daha iyi tablolar yönetmenize olanak sağlar)';
$lang['enter a login for webmaster'] = 'Site Yöneticisi için bir giriş';
$lang['webmaster login can\'t contain characters \' or "'] = 'Site yöneticisi  \' veya " karakterlerini içeremez';
$lang['please enter your password again'] = 'şifrenizi tekrar giriniz';
$lang['Webmaster password'] = 'Site yöneticisi şifresi';
$lang['Keep it confidential, it enables you to access administration panel'] = 'gizli tutun, yönetim paneline ulaşmanızı sağlar';
$lang['Password [confirm]'] = 'Şifre [kabul]';
$lang['verification'] = 'doğrulama';
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = 'Yardım ihtiyacınız var? Sorularınızı <a href="%s">Piwigo forumda sorabilirsiniz</a>.';
$lang['Webmaster mail address'] = 'Site yöneticisi e-posta adresi';
$lang['Visitors will be able to contact site administrator with this mail'] = 'Ziyaretçilerin site yöneticisi ile bağlantıya geçmesi için bu postayı kullanmaları mümkün olacak';
$lang['PHP 5 is required'] = 'PHP 5 gerekli';
$lang['It appears your webhost is currently running PHP %s.'] = 'Web hostunuz şu anda PHP %s çalışıyor görünüyor.';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'Piwigo .htaccess dosyası yaparak veya değiştirerek ayarlarınızı PHP 5 için düzenlemeyi deneyebilir.';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = 'Eğer kendi başınıza yapılandırmasını değiştirirseniz bundan sonra Piwigo yeniden başlatınız.';
$lang['Try to configure PHP 5'] = 'PHP 5 yapılandırmak için çalışın';
$lang['Sorry!'] = 'Üzgünüm!';
$lang['Piwigo was not able to configure PHP 5.'] = 'Piwigo PHP 5 yapılandırması mümkün değildi.';
$lang['You may referer to your hosting provider\'s support and see how you could switch to PHP 5 by yourself.'] = 'Host sağlayıcınız ile PHP 5\'e geçebilmek için konuşun.';
$lang['Hope to see you back soon.'] = 'Sizi tekrar görmeyi umut ederiz.';
$lang['Congratulations, Piwigo installation is completed'] = 'Tebrikler, Piwigo kurulum tamamlandı.';
$lang['An alternate solution is to copy the text in the box above and paste it into the file "local/config/database.inc.php" (Warning : database.inc.php must only contain what is in the textarea, no line return or space character)'] = 'Diğer çözüm aşağıdaki kutudaki yazıyı kopyalayın ve "local/config/database.inc.php" dosyası içine pasteleyin.(Uyarı : database.inc.php sadece yazı içermeli hiçbir ifade veya boşluk karakteri içermemelidir)';
$lang['Creation of config file local/config/database.inc.php failed.'] = 'Yapılandırmada dosyası local/config/database.inc.php oluşturma başarısız oldu.';
$lang['Download the config file'] = 'Yapılandırma dosyasını indir';
$lang['You can download the config file and upload it to local/config directory of your installation.'] = 'Yapılandırma dosyasını indirip düzenleyebilir ve kurulum dizininde local/config bölümüne yükleyebilirsiniz.';
$lang['Just another Piwigo gallery'] = 'Bir başka Piwigo galerisi';
$lang['Welcome to my photo gallery'] = 'Resim galerime hoş geldiniz';
?>