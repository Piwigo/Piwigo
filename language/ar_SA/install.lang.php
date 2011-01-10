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

$lang['Installation'] = 'التثبيت ';
$lang['Basic configuration'] = 'التكوينات الأساسية';
$lang['Default gallery language'] = 'اللغة الافتراضية للمعرض';
$lang['Database configuration'] = 'تكوين قاعدة البيانات';
$lang['Admin configuration'] = 'اعدادات المدير';
$lang['Start Install'] = 'ابدأ التثبيت';
$lang['mail address must be like xxx@yyy.eee (example : jack@altern.org)'] = 'البريد يجب ان يكون على هذه الهيئة xxx@yyy.ee ( مثلاً : Jack@altern.org)';
$lang['Webmaster login'] = 'دخول المدير';
$lang['It will be shown to the visitors. It is necessary for website administration'] = 'سوف تظهر للزوار ، ضرورية للادارة و لوحة التحكم';
$lang['Connection to server succeed, but it was impossible to connect to database'] = 'تم الاتصال بالخادم الموقع ، لكن لم يتم يتم الاتصال بقاعدة البيانات';
$lang['Can\'t connect to server'] = 'لا يمكن الاتصال بالخادم';
$lang['Database type'] = 'نوع قاعدة البيانات';
$lang['The type of database your piwigo data will be store in'] = 'قاعدة بيانات المعرض Piwigo سوف تخزن ';
$lang['Host'] = 'المضيف';
$lang['localhost, sql.multimania.com, toto.freesurf.fr'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['User'] = 'المستخدم';
$lang['user login given by your host provider'] = 'اصرح للمستخدم  الدخول بواسطة المضيف الخاص بك';
$lang['Password'] = 'كلمة المرور';
$lang['user password given by your host provider'] = 'اعطي  للمستخدم كلمة المرور  بواسطة المضيف الخاص بك';
$lang['Database name'] = 'أسم قاعدة البيانات';
$lang['also given by your host provider'] = 'ايضا بواسط المضيف الخاص بك ';
$lang['Database table prefix'] = 'بداية جداول قاعدة البيانات';
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = 'الاسماءالمبتدأ في جداول قاعدة البيانات ( تمكنك من أدارة قاعدة البيانات بشكل أفضل)ـ';
$lang['enter a login for webmaster'] = 'دخول مدير الموقع';
$lang['webmaster login can\'t contain characters \' or "'] = 'دخول المدر لا \ يمكن تتضمن الأحرف  \' أو "';
$lang['please enter your password again'] = 'فضلا ً أعد كتابة كلمة المرور مرةأخرى';
$lang['Webmaster password'] = 'كلمة مرور مدير الموقع';
$lang['Keep it confidential, it enables you to access administration panel'] = 'ابقائه سريا، فإنه يتيح لك الوصول إلى لوحة الإدارة';
$lang['Password [confirm]'] = 'كلمة المرور [confirm]';
$lang['verification'] = 'التحقق';
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = 'هل تحتاج لمساعده؟ يمكنك السؤال  <a href="%s">Piwigo لجنة الأسئلة في </a>.';
$lang['Webmaster mail address'] = 'بريد مدير الموقع';
$lang['Visitors will be able to contact site administrator with this mail'] = ' سيتمكن الزوار لاستخدام هذا البريد إلى الاتصال بالمسؤول عن الموقع';
$lang['PHP 5 is required'] = 'مطلوب  PHP 5 ';
$lang['It appears your webhost is currently running PHP %s.'] = 'PHP %s يبدوا أن المضيف يستخدم حاليا ';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'قد يحاول تبديل التكوين الخاص بك إلىPHP 5 عن طريق إنشاء أو تعديل ملف htaccess . Piwigo ';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = 'ملاحظة يمكنك تغيير التكوين الخاص بك من نفسك وإعادة Piwigo بعد ذلك.';
$lang['Try to configure PHP 5'] = 'PHP 5 محاولة تكوين ';
$lang['Sorry!'] = 'مـعـذرة !1';
$lang['Piwigo was not able to configure PHP 5.'] = ' Piwigo غير قادر على تكوين PHP 5';
$lang['You may referer to your hosting provider\'s support and see how you could switch to PHP 5 by yourself.'] = 'تحقق من مزود الاستضافة حول دعمه لـ PHP 5';
$lang['Hope to see you back soon.'] = 'نأمل أن نرى عودتك إلى هنا قريبا';
$lang['Congratulations, Piwigo installation is completed'] = 'تم بحمد الله تثبيت معرضPiwigo  بنجاح  ، مبروك ';
$lang['An alternate solution is to copy the text in the box above and paste it into the file "local/config/database.inc.php" (Warning : database.inc.php must only contain what is in the textarea, no line return or space character)'] = 'حل آخر هو نسخ النص في المربع أعلاه ولصقه في ملف"local/config/database.inc.php" (تحذير : database.inc.php يجب أن تحتوي فقط ما هو موجود في   النص،  الخط أو حرف مسافة)';
$lang['Creation of config file local/config/database.inc.php failed.'] = 'فشل في إنشاء ملف التكوين local/config/database.inc.php ';
$lang['Download the config file'] = 'تحميل ملف التكوين';
$lang['You can download the config file and upload it to local/config directory of your installation.'] = 'يمكنك تحميل ملف التكوين وتحميله إلى الدليل المحلي';
$lang['SQLite and PostgreSQL are currently in experimental state.'] = 'تحت التجربية حاليا  SQLite و PostgreSQL ';
$lang['Learn more'] = 'تعلم أكثر';

?>