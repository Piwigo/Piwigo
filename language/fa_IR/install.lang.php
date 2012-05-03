<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
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



$lang['Installation'] = "نصب";
$lang['Basic configuration'] = 'تنظیمات اساسی';
$lang['Default gallery language'] = 'زبان پیشفرض گالری';
$lang['Database configuration'] = 'تنظیمات پایگاه داده';
$lang['Admin configuration'] = 'تنظیمات مدیریت';
$lang['Start Install'] = 'آغاز نصب';
$lang['mail address must be like xxx@yyy.eee (example : jack@altern.org)'] = 'آدرس ایمیل باید چیزی شبیه به xxx@yyy.eee باشد. (مانند: jack@altern.org)';
$lang['Webmaster login'] = 'ورود مدیرکل';
$lang['It will be shown to the visitors. It is necessary for website administration'] = 'این برای بازدیدکنندگان نمایش داده می شود. برای مدیریت سایت ضروری است';
$lang['Connection to server succeed, but it was impossible to connect to database'] = 'ارتباط با سرور با موفقیت انجام شد، اما ارتباط با پایگاه داده با مشکل مواجه شد';
$lang['Can\'t connect to server'] = 'ارتباط با سرور ممکن نیست';
$lang['Host'] = 'میزبان (Host)';
$lang['localhost, sql.multimania.com, toto.freesurf.fr'] = 'localhost، sql.multimania.com، toto.freesurf.fr';
$lang['User'] = 'نام کاربری';
$lang['user login given by your host provider'] = 'نام کاربری خود را باید از هاست دریافت نمایید';
$lang['Password'] = "گذرواژه";
$lang['user password given by your host provider'] = 'گذرواژه را باید از هاست دریافت نمایید';
$lang['Database name'] = 'نام پایگاه داده';
$lang['also given by your host provider'] = 'نام پایگاه داده را باید از هاست دریافت نمایید';
$lang['Database table prefix'] = 'پیشوند جدول های پایگاه داده';
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = 'نام تمام جدول های پایگاه داده با این پیشوند آغاز خواهد شد (این گزینه برای مدیریت بهتر پایگاه داده مفید است)';
$lang['enter a login for webmaster'] = 'مشخصات ورود مدیرکل را وارد نمایید';
$lang['webmaster login can\'t contain characters \' or "'] = 'مشخصات ورود نباید دارای کاراکترهای \' و " باشند';
$lang['please enter your password again'] = 'خواهشمند است گذرواژه خود را دوباره وارد نمایید';
$lang['Webmaster password'] = 'گذرواژه ی مدیرکل';
$lang['Keep it confidential, it enables you to access administration panel'] = 'گذرواژه را محرمانه نگه دارید، آن دسترسی شما را به مدیریت فراهم می کند';
$lang['Password [confirm]'] = "تاييد گذرواژه";
$lang['verification'] = 'تأیید';
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = 'به کمک نیاز دارید ؟ پرسش خود را در <a href="%s">تالارهای پشتیبانی Piwigo</a> مطرح نمایید.';
$lang['Webmaster mail address'] = 'آدرس ایمیل مدیرکل';
$lang['Visitors will be able to contact site administrator with this mail'] = 'بازدیدکنندگان می توانند بوسیله ی این ایمیل با مدیرکل ارتباط برقرار کنند';
$lang['PHP 5 is required'] = 'نگارش پنجم PHP لازم است';
$lang['It appears your webhost is currently running PHP %s.'] = 'به نظر می رسد هاست شما دارای نگارش PHP %s باشد.';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'Piwigo می‌تواند تنظیمات PHP شما را با ایجاد یا ویرایش یک فایل .htaccess به PHP 5 تغییر دهد.';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = 'شما می توانید خودتان تنظیمات را تغییر دهید و Piwigo را دوباره راه اندازی کنید.';
$lang['Try to configure PHP 5'] = 'در حال تنظیم PHP 5';
$lang['Sorry!'] = "شرمنده!";
$lang['Piwigo was not able to configure PHP 5.'] = 'Piwigo قادر به پیکربندی PHP 5 نیست.';
$lang['You may referer to your hosting provider\'s support and see how you could switch to PHP 5 by yourself.'] = 'شما باید با سرویس دهنده ی هاست خود تماس گرفته و از آنها بپرسید که چگونه می توانید تنظیمات خود را به PHP 5 تغییر دهید.';
$lang['Hope to see you back soon.'] = 'به امید دیدار دوباره ی شما.';
$lang['Congratulations, Piwigo installation is completed'] = 'Piwigo با موفقیت نصب شد';
$lang['An alternate solution is to copy the text in the box above and paste it into the file "local/config/database.inc.php" (Warning : database.inc.php must only contain what is in the textarea, no line return or space character)'] = 'یک راه دیگر این است که شما متن بالا را کپی کنید و بدون اضافه یا کم کردن حتی یک حرف ،آن را در آدرس : "local/config/database.inc.php" قرار دهید';
$lang['Creation of config file local/config/database.inc.php failed.'] = 'ایجاد فایل local/config/database.inc.php با مشکل مواجه شد.';
$lang['Download the config file'] = 'دانلود فایل پیکربندی (Config)';
$lang['You can download the config file and upload it to local/config directory of your installation.'] = 'شما می توانید فایل پیکربندی را دانلود کنید و آن را در مسیر local/config در محلی که گالری را نصب کردید آپلود کنید.';
$lang['Don\'t hesitate to consult our forums for any help : %s'] = 'هر مشکلی که داشتید را در انجمن مطرح کنید: %s';
$lang['Just another Piwigo gallery'] = 'یک گالری دیگر با Piwigo';
$lang['Password ']['confirm'] = 'تأیید گذرواژه';
$lang['Welcome to my photo gallery'] = 'به گالری عکس من خوش آمدید';
$lang['Welcome to your new installation of Piwigo!'] = 'به گالری Piwigo ی خود خوش آمدید!';
?>