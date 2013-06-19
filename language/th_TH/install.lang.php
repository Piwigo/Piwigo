<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2013 Piwigo Team                  http://piwigo.org |
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
$lang['Default gallery language'] = "กำหนดภาษาพื้นฐานสำหรับแกลลอรี่";
$lang['Database configuration'] = "การตั้งค่าฐานข้อมูล";
$lang['Admin configuration'] = "การตั้งค่าผู้ดูแลระบบ";
$lang['Start Install'] = "เริ่มการติดตั้ง";
$lang['It will be shown to the visitors. It is necessary for website administration'] = "มันจะแสดงส่วนนี้ไปยังผู้เยี่ยมชม. ซึ่งมันจำเป็นสำหรับผู้ดูแลระบบของเว็บไซต์";
$lang['Connection to server succeed, but it was impossible to connect to database'] = "เชื่อมต่อเซิร์ฟเวอร์เรียบร้อยแล้ว, แต่ไม่สามารถเชื่อมต่อฐานข้อมูลได้";
$lang['Can\'t connect to server'] = "ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้";
$lang['Host'] = "เซิร์ฟเวอร์";
$lang['User'] = "ชื่อผู้ใช้งาน";
$lang['user login given by your host provider'] = "ชื่อผู้ใช้งานจะได้รับจากผู้ให้บริการเซิร์ฟเวอร์";
$lang['user password given by your host provider'] = "รหัสผ่านของผู้ใช้งานจะได้รับจากผู้ให้บริการเซิร์ฟเวอร์";
$lang['Database name'] = "ชื่อฐานข้อมูล";
$lang['also given by your host provider'] = "ซึ่งจะได้รับจากผู้ให้บริการเซิร์ฟเวอร์ของคุณ";
$lang['Database table prefix'] = "คำนำหน้าตารางฐานข้อมูล";
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = "ชื่อฐานข้อมูลจะถูกกำกับด้วยคำนำหน้าตารางฐานข้อมูล ที่คุณได้กำหนดไว้  (หากคุณกำหนดไว้ มันจะง่ายต่อการจัดการฐานข้อมูล)";
$lang['enter a login for webmaster'] = "กำหนดชื่อในการเข้าสู่ระบบสำหรับเว็บมาสเตอร์";
$lang['webmaster login can\'t contain characters \' or "'] = "ชื่อเข้าสู่ระบบจะต้องไม่ประกอบด้วยเครื่องหมาย ' หรือ \"";
$lang['please enter your password again'] = "กรุณากรอกรหัสผ่านอีกครั้ง";
$lang['Keep it confidential, it enables you to access administration panel'] = "โปรดเก็บไว้เป็นความลับ, เพราะมันสามารถใช้เพื่อเข้าถึงหน้าการจัดการระบบ Piwigo แกลลอรี่ ของคุณได้";
$lang['Password [confirm]'] = "รหัสผ่าน [ยืนยัน]";
$lang['verification'] = "การยืนยัน";
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = "ต้องการความช่วยเหลือ? โปรดถามคำถามไว้ได้ที่ <a href=\"%s\">บอร์ดข้อความของ Piwigo</a>.";
$lang['Visitors will be able to contact site administrator with this mail'] = "ผู้เยี่ยมชมจะสามารถที่จะใช้อีเมลนี้ เพื่อติดต่อกับผู้ดูแลระบบได้";
$lang['PHP 5 is required'] = "PHP 5 จำเป็นต้องมี";
$lang['It appears your webhost is currently running PHP %s.'] = "มันจะแสดงบนเวิร์ฟเวอร์ที่คุณใช้งานอยู่ ซึ่งคุณใช้งานอยู่บน PHP %s.";
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = "Piwigo อาจจะลองปรับการตั้งค่าของคุณไปยัง PHP 5 โดยการสร้างหรือแก้ไขปรับแต่งไฟล์ .htaccess.";
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = "จำไว้ว่า คุณสามารถเปลี่ยนแปลงการตั้งค่าในภายหลังได้ด้วยตนเอง.";
$lang['Try to configure PHP 5'] = "ลองตั้งค่า PHP 5 ใหม่";
$lang['Sorry!'] = "เสียใจ!";
$lang['Piwigo was not able to configure PHP 5.'] = "Piwigo ไม่สามารถตั้งค่า PHP 5. ได้";
$lang['You may referer to your hosting provider\'s support and see how you could switch to PHP 5 by yourself.'] = "คุณควรติดต่อไปยังผู้ให้บริการเซิร์ฟเวอร์ของคุณเพื่อสอบถามว่า เซิร์ฟเวอร์ดังกล่าวนี้สนับสนุนการทำงานของ PHP 5 หรือว่าคุณสามารถสลับไปใช้งาน PHP 5 ด้วยตนเอง ได้หรือไม่อย่างไร.";
$lang['Hope to see you back soon.'] = "หวังว่าจะได้เจอคุณเร็วๆ นี้.";
$lang['Congratulations, Piwigo installation is completed'] = 'ยินดีด้วย, การติดตั้ง Piwigo ได้ดำเนินการเสร็จเรียบร้อยแล้ว';
$lang['An alternate solution is to copy the text in the box above and paste it into the file "local/config/database.inc.php" (Warning : database.inc.php must only contain what is in the textarea, no line return or space character)'] = 'คัดลอกข้อความในกล่องข้อความด้านล่าง เพื่อนำไปวางในไฟล์ "local/config/database.inc.php" (ข้อควรระวัง : ไฟล์ database.inc.php จะต้องเป็นข้อความที่ได้คัดลอกจากภายในกล่องข้อความเท่านั้น, จะต้องไม่มีบรรทัดเกิน หรืออักขระว่าง)';
$lang['Creation of config file local/config/database.inc.php failed.'] = 'การสร้างไฟล์ตั้งค่า local/config/database.inc.php ล้มเหลว.';
$lang['Download the config file'] = 'ดาวน์โหลดไฟล์ตั้งค่า';
$lang['You can download the config file and upload it to local/config directory of your installation.'] = 'คุณสามารถดาวน์โหลดไฟล์ตั้งค่า และอัพไปไว้ยังไดเรกทอรี่ local/config ของ Piwigo.';
$lang['Just another Piwigo gallery'] = 'เพียงแค่ Piwigo แกลลอรี่ เว็บหนึ่ง';
$lang['Welcome to my photo gallery'] = 'ยินดีต้อนรับสู่แกลลอรี่รูปภาพของฉัน';
$lang['Don\'t hesitate to consult our forums for any help : %s'] = 'อย่าลังเลที่จะปรึกษาหรือโพสคำถามในฟอรั่มของเรา เพื่อขอความช่วยเหลือใดๆ :%s';
$lang['Welcome to your new installation of Piwigo!'] = 'ยินดีต้อนรับสู่การติดตั้งใหม่ของ Piwigo!';
$lang['localhost or other, supplied by your host provider'] = 'localhost หรืออื่น ๆ ที่จัดทำโดยผู้ให้บริการโฮสต์ของคุณ';
?>