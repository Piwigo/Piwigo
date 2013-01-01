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



$lang['Installation'] = "התקנה";
$lang['Basic configuration'] = "הגדרות בסיסיות";
$lang['Default gallery language'] = "שפת גלריה ברירת מחדל";
$lang['Database configuration'] = "הגדרות בסיס נתונים";
$lang['Admin configuration'] = "הגדרות מנהל";
$lang['Start Install'] = "התחל התקנה";
$lang['mail address must be like xxx@yyy.eee (example : jack@altern.org)'] = "כתובת דואר אלקטרוני xxx@yyy.eee (example : jack@altern.com)";
$lang['Webmaster login'] = "התחברות מנהל האתר";
$lang['It will be shown to the visitors. It is necessary for website administration'] = "יוצג בפני המבקרים. נחוץ בשביל מנהל האתר";
$lang['Connection to server succeed, but it was impossible to connect to database'] = "התחבר לשרת בהצלחה, אך לא הצליח להתחבר לבסיס נתונים";
$lang['Can\'t connect to server'] = "לא מצליח להתחבר לשרת";
$lang['Host'] = "שרת מארח";
$lang['localhost, sql.multimania.com, toto.freesurf.fr'] = "localhost, sql.multimania.com, toto.freesurf.fr";
$lang['User'] = "משתמש";
$lang['user login given by your host provider'] = "המשתמש שניתן לך על ידי השרת המארח שלך";
$lang['Password'] = "סיסמה";
$lang['user password given by your host provider'] = "הסיסמה שניתנה לך על ידיד השרת המארח שלך";
$lang['Database name'] = "שם בסיס הנתונים";
$lang['also given by your host provider'] = "גם ניתן על ידי השרת המארח שלך";
$lang['Database table prefix'] = "קידומת טבלאות מסד הנתונים";
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = "שם טבלאות בסיס הנתונים יוצגו לאחר קידומת זאת (מאפשר לך לנהל את טבלאות בסיס הנתונים שלך טוב יותר)";
$lang['enter a login for webmaster'] = "הזן פרטי התחברות עבור מהל האתר";
$lang['webmaster login can\'t contain characters \' or "'] = "משתמש מנהל האתר לא יכול להחיל את התווים \' או מרחאות";
$lang['please enter your password again'] = "הכנס בבקשה את הסיסמה שוב";
$lang['Webmaster password'] = "ססמת מנהל האתר";
$lang['Keep it confidential, it enables you to access administration panel'] = "שמור זאת בסוד, זה מאפשר לך להיכנס לפאנל ניהול האתר";
$lang['Password [confirm]'] = "סיסמה [אושרה]";
$lang['verification'] = "אימות";
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = "צריך עזרה? היעזר ב <a href=\"%s\">הפורום של Piwigo</a>.";
$lang['Webmaster mail address'] = "דואר אלקטרוני של מנהל האתר";
$lang['Visitors will be able to contact site administrator with this mail'] = "אורחים יוכלו להשתמש בדואר האלקטרוני הזה כדי ליצור קשר עם מנהל האתר";
$lang['PHP 5 is required'] = "נדרש גירסת PHP 5";
$lang['It appears your webhost is currently running PHP %s.'] = "שרת המארח שלך רץ על גירסת PHP %s.";
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = "Piwigo תנסה להחליף את התצורה שלך ל PHP 5 על ידי יצירת או שינוי קובץ .htaccess";
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = "אתה יכול לשנות את התצורה שלך לבד ולהריץ את Piwigo לאחר מכן.";
$lang['Try to configure PHP 5'] = "נסה להגדיר PHP 5";
$lang['Sorry!'] = "סליחה!";
$lang['Piwigo was not able to configure PHP 5.'] = "Piwigo לא יכולה להגדיר את PHP 5.";
$lang['You may referer to your hosting provider\'s support and see how you could switch to PHP 5 by yourself.'] = "אתה צריך לפנות לתמיכת השרת המאחר שלך ולראות איך אתה יכול להחליף ל PHP 5 לבד.";
$lang['Hope to see you back soon.'] = "מקווה לראות אותך שוב בקרוב.";
$lang['Congratulations, Piwigo installation is completed'] = 'מזל טוב,התקנת Piwigo הושלמה';
$lang['An alternate solution is to copy the text in the box above and paste it into the file "local/config/database.inc.php" (Warning : database.inc.php must only contain what is in the textarea, no line return or space character)'] = 'הפתרון החלופי הוא להעתיק את הטקסט בתיבה מעל ולהדביק אותו בקובץ "local/config/database.inc.php" (אזהרה : database.inc.php חייב להחיל רק את התווים שבתיבת טקסט, לא לחזור על שורות או לעשות רווח)';
$lang['Creation of config file local/config/database.inc.php failed.'] = 'יצירת קובץ config local/config/database.inc.php ניכשל.';
$lang['Download the config file'] = 'הורד את קובץ config';
$lang['You can download the config file and upload it to local/config directory of your installation.'] = 'אתה יכול להוריד את קובץ config ולהעלות אותו אל local/config בתיקיית ההתקנה שלך.';
$lang['Just another Piwigo gallery'] = 'עוד גלריה מבית Piwigo';
$lang['Welcome to my photo gallery'] = 'ברוכים הבאים לגלרית התמונות שלי';
$lang['Don\'t hesitate to consult our forums for any help : %s'] = 'אל תהסס להתייעץ בפורומים שלנו בכל נושא: %s';
$lang['Welcome to your new installation of Piwigo!'] = 'ברוך הבא לגלרית piwigo החדשה שלך';
?>