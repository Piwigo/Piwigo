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

$lang['Installation'] = 'Cài đặt';
$lang['Basic configuration'] = 'Cấu hình cơ bản';
$lang['Default gallery language'] = 'Ngôn ngữ hiển thị gallery mặc định';
$lang['Database configuration'] = 'Cấu hình cơ sở dữ liệu';
$lang['Admin configuration'] = 'Cấu hình Quản trị';
$lang['Start Install'] = 'Bắt đầu Cài đặt';
$lang['mail address must be like xxx@yyy.eee (example : jack@altern.org)'] = 'địa chỉ thư điện tử phải có dạng xxx@yyy.eee (ví dụ : jack@altern.org)';
$lang['Webmaster login'] = 'Đăng nhập của Webmaster';
$lang['It will be shown to the visitors. It is necessary for website administration'] = 'Khách thăm quan gallery sẽ thấy được thông tin của Webmaster. Điều này cần thiết cho Quản trị Webmaster';
$lang['Connection to server succeed, but it was impossible to connect to database'] = 'Kết nối thành công vào máy chủ, nhưng không thể kết nối vào cơ sở dữ liệu.';
$lang['Can\'t connect to server'] = 'Không thể kết nối vào máy chủ';
$lang['Copy the text in pink between hyphens and paste it into the file "local/config/database.inc.php"(Warning : database.inc.php must only contain what is in pink, no line return or space character)'] = 'Copy đoạn chữ màu hồng giữa các dấu gạch nối và dán nó vào file mysql.inc.php theo đường dẫn sau "include/mysql.inc.php"(Lưu ý: file mysql.inc.php chỉ được copy vào những chữ màu hồng, không được chèn thêm hàng hoặc ký tự đặc biệt )';

$lang['Host'] = 'Máy chủ MySQL';
$lang['localhost, sql.multimania.com, toto.freesurf.fr'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['User'] = 'Người dùng';
$lang['user login given by your host provider'] = 'thông tin đăng nhập của người dùng do nhà cung cấp máy chủ của của bạn đưa ra.';
$lang['Password'] = 'Mật khẩu';
$lang['user password given by your host provider'] = 'mật khẩu người dùng do nhà cung cấp máy chủ của bạn đưa ra';
$lang['Database name'] = 'Tên cơ sở dữ liệu';
$lang['also given by your host provider'] = 'cũng được cấp bởi nhà cung cấp máy chủ';
$lang['Database table prefix'] = 'Tiếp đầu ngữ của các bảng trong cơ sở dữ liệu';
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = 'tên các bảng trong cơ sở dữ liệu sẽ được thêm vào đầu bằng tiếp đầu ngữ (giúp bạn quản lý các bảng trong cơ sở dữ liệu được tốt hơn)';
$lang['enter a login for webmaster'] = 'nhập tên đăng nhập của Webmaster';
$lang['webmaster login can\'t contain characters \' or "'] = 'đăng nhập của Webmaster không thể chứa các ký tự hoặc';
$lang['please enter your password again'] = 'vui lòng nhập lại mật khẩu của bạn';
$lang['Webmaster password'] = 'Mật khẩu của Webmaster';
$lang['Keep it confidential, it enables you to access administration panel'] = 'Bảo quản kỹ thông tin này, nó cho phép bạn truy cập vào Bảng điều khiển của Quản trị.';
$lang['Password [confirm]'] = 'Mật khẩu [xác nhận]';
$lang['verification'] = 'xác nhận';
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = 'Cần trợ giúp ? Hãy gởi thắc mắc của bạn tại <a href="%s">diễn đàn của Piwigo</a>.';
$lang['Webmaster mail address'] = 'Địa chỉ thư điện tử của Webmaster';
$lang['Visitors will be able to contact site administrator with this mail'] = 'Khách tham quan có thể liên lạc với Quản trị thông qua địa chỉ thư điện tử này.';

$lang['PHP 5 is required'] = 'Phải có PHP 5 ';
$lang['It appears your webhost is currently running PHP %s.'] = 'Do máy chủ web của bạn đang chạy phiên bản PHP  %s.';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'Piwigo sẽ thử chuyển cấu hình của bạn sang PHP 5 bằng cách tạo ra hoặc biên tập một file .htaccess.';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = 'Chú ý rằng bạn có thể tự thay đổi cấu hình và khởi động lại Piwigo sau đó.';
$lang['Try to configure PHP 5'] = ' Thử cấu hình PHP 5';
$lang['Sorry!'] = 'Rất tiếc!';
$lang['Piwigo was not able to configure PHP 5.'] = 'Piwigo không thể cấu hình cho PHP 5.';
$lang["You may referer to your hosting provider's support and see how you could switch to PHP 5 by yourself."] = "Bạn nên yêu cầu hỗ trợ từ nhà cung cấp máy chủ và tìm hiểu xem nếu có thể tự chuyển được sang phiên bản PHP 5.";
$lang['Hope to see you back soon.'] = 'Hi vọng nhận được phản hồi của bạn sớm.';
?>