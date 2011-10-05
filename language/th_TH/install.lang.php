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

$lang['Installation'] = "การติดตั้ง";
$lang['Basic configuration'] = "การตั้งค่าพื้นฐาน";
$lang['Default gallery language'] = "การตั้งค่าภาษา";
$lang['Database configuration'] = "การตั้งค่าฐานข้อมูล";
$lang['Admin configuration'] = "การตั้งค่าผู้ดูแลระบบ";
$lang['Start Install'] = "เริ่มการติดตั้ง";
$lang['mail address must be like xxx@yyy.eee (example : jack@altern.org)'] = "อีเมล์จะต้อมมีลักษณะ xxx@yyy.eee (ตัวอย่าง: jack@altern.org)";
$lang['Webmaster login'] = "เข้าสู่ระบบผู้ดูแลเว็บ";
$lang['It will be shown to the visitors. It is necessary for website administration'] = "หน้าเว็บนี้มันจะแสดงให้ผู้เข้าชมเว็บไซต์เห็น ดังนั้นคุณจึงจำเป็นต้องตั้งค่าปรับแต่งเว็บไซต์";
$lang['Connection to server succeed, but it was impossible to connect to database'] = "เชื่อมต่อกับเซิร์ฟเวอร์ประสบความสำเร็จ, แต่ยังมีปัญหาในการเชื่อมต่อกับฐานข้อมูล";
$lang['Can\'t connect to server'] = "ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้";
$lang['Host'] = "โฮส";
$lang['localhost, sql.multimania.com, toto.freesurf.fr'] = "ตัวอย่าง : localhost, sql.multimania.com, toto.freesurf.fr";
$lang['User'] = "ชื่อผู้ใช้งาน";
$lang['user login given by your host provider'] = "ชื่อผู้ใช้งานนี้ืที่กำหนดโดยผู้ให้บริการโฮสของคุณ";
$lang['Password'] = "รหัสผ่าน";
$lang['user password given by your host provider'] = "รหัสผ่านผู้ใช้ที่กำหนดโดยผู้ให้บริการโฮสต์ของคุณ";
$lang['Database name'] = "ชื่อฐานข้อมูล";
$lang['also given by your host provider'] = "ซึ่งได้รับโดยผู้ให้บริการโฮสต์ของคุณ";
$lang['Database table prefix'] = "คำนำหน้าตารางฐานข้อมูล";
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = "กำหนดคำนำหน้่าตารางของฐานข้อมูล (ช่วยให้คุณสามารถจัดการตารางของฐานข้อมูลคุณง่ายขึ้น)";
$lang['enter a login for webmaster'] = "ป้อนข้อมูลเข้าสู่ระบบสำหรับเว็บมาสเตอร์";
$lang['webmaster login can\'t contain characters \' or "'] = "ชื่อในการเข้าสู่ระบบไม่สามารถมีอักขระ ' หรือ \"";
$lang['please enter your password again'] = "กรุณาใส่รหัสผ่านของคุณอีกครั้ง";
$lang['Webmaster password'] = "รหัสผ่านเว็บมาสเตอร์";
$lang['Keep it confidential, it enables you to access administration panel'] = "รหัสเก็บไว้เป็นความลับ มันจะช่วยให้คุณสามารถเข้าถึงหน้าจัดการเว็บไซต์";
$lang['Password [confirm]'] = "รหัสผ่าน [ยืนยันอีกครั้ง]";
$lang['verification'] = "การตรวจสอบ";
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = "ต้องการความช่วยเหลือ? ถามคำถามของคุณได้ที่ <a href=\"%s\">เว็บบอร์ด Piwigo</a>.";
$lang['Webmaster mail address'] = "ที่อยู่อีเมลของเว็บมาสเตอร์";
$lang['Visitors will be able to contact site administrator with this mail'] = "ผู้เข้าชมจะสามารถใช้อีเมลนี้ในการติดต่อกับผู้ดูแลเว็บไซต์";
$lang['PHP 5 is required'] = "PHP 5 (จำเป็นต้องมี)";
$lang['It appears your webhost is currently running PHP %s.'] = "มันจะปรากฏขึ้นบนโฮสต์ของคุณที่กำลังทำงานอยู่ PHP %s.";
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = "Piwigo อาจพยายามที่จะเปลี่ยนการกำหนดค่าของคุณเพื่อให้ PHP 5 โดยการสร้างหรือการปรับเปลี่ยนไฟล์ .htaccess";
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = "หมายเหตุ คุณสามารถเปลี่ยนการตั้งค่าด้วยตัวคุณเองและเริ่มต้น Piwigo หลังจากที่.";
$lang['Try to configure PHP 5'] = "พยายามที่จะกำหนดค่า PHP 5";
$lang['Sorry!'] = "เสียใจ!";
$lang['Piwigo was not able to configure PHP 5.'] = "Piwigo ไม่สามารถที่จะกำหนดค่า PHP 5.";
$lang['You may referer to your hosting provider\'s support and see how you could switch to PHP 5 by yourself.'] = "คุณควรติดต่อผู้ให้บริการโฮสของคุณว่า โฮสดังกล่าวสนับสนุนการใช้งานบน PHP 5 หรือไม่";
$lang['Hope to see you back soon.'] = "หวังที่จะเห็นคุณกลับมาเร็ว ๆ นี้.";
$lang['Congratulations, Piwigo installation is completed'] = 'ขอแสดงความยินดี, การติดตั้ง Piwigo ได้เสร็จสมบูรณ์แล้ว.';
$lang['An alternate solution is to copy the text in the box above and paste it into the file "local/config/database.inc.php" (Warning : database.inc.php must only contain what is in the textarea, no line return or space character)'] = 'วิธีอื่นคือการคัดลอกข้อความในกล่องดังกล่าวข้างต้นและวางลงในแฟ้มที่ "โฟลเดอร์ Piwigoของคุณ/config/database.inc.php" (คำเตือน ไฟล์: database.inc.php จะต้องมีเฉพาะข้อความที่ให้กอปปี้มาวางเท่านั้น, และจะต้องไม่มีอักขระช่องว่างหรือบรรทัดว่าง)';
$lang['Creation of config file local/config/database.inc.php failed.'] = 'การสร้างไฟล์การตั้งค่า ที่ โฟลเดอร์ Piwigoของคุณ/config/database.inc.php ล้มเหลว.';
$lang['Download the config file'] = 'ดาวน์โหลดไฟล์การตั้งค่า';
$lang['You can download the config file and upload it to local/config directory of your installation.'] = 'คุณสามารถดาวน์โหลดไฟล์การตั้งค่าและอัปโหลดไปไว้ในไดเรกทอรี โฟลเดอร์ Piwigoของคุณ/config .';
$lang['Just another Piwigo gallery'] = 'ก็แค่เว็บแกลเลอรี่ Piwigo เว็บหนึ่ง';
$lang['Welcome to my photo gallery'] = 'ยินดีต้อนรับสู่แกลลอรี่ของฉัน';

?>