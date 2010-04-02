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

$lang['Installation'] = '安装';
$lang['Basic configuration'] = '基本设置';
$lang['Default gallery language'] = '图库默认语言';
$lang['Database configuration'] = '数据库设置';
$lang['Admin configuration'] = '管理员帐户设置';
$lang['Start Install'] = '开始安装';
$lang['mail address must be like xxx@yyy.eee (example : jack@altern.org)'] = '邮箱地址格式 xxx@yyy.eee (例 : jack@altern.org)';

$lang['Webmaster login'] = '管理员';
$lang['It will be shown to the visitors. It is necessary for website administration'] = '所有用户都能看到此帐户。必须提供此帐户来管理网站';

$lang['Connection to server succeed, but it was impossible to connect to database'] = '服务器连接正常，但是无法连接到数据库';
$lang['Can\'t connect to server'] = '无法连接到服务器';

$lang['Host'] = 'MySQL主机地址';
$lang['localhost, sql.multimania.com, toto.freesurf.fr'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['User'] = '用户';
$lang['user login given by your host provider'] = '主机用户名';
$lang['Password'] = '密码';
$lang['user password given by your host provider'] = '主机用户密码';
$lang['Database name'] = '数据库名称';
$lang['also given by your host provider'] = '在主机设定的数据库名称';
$lang['Database table prefix'] = '表名称前缀';
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = '所有的表名称都加此前缀(有利于数据库管理)';
$lang['enter a login for webmaster'] = '请输入网管名';
$lang['webmaster login can\'t contain characters \' or "'] = '网管名不应包含字符 " 和 \'';
$lang['please enter your password again'] = '请再次输入密码';
$lang['Webmaster password'] = '密码';
$lang['Keep it confidential, it enables you to access administration panel'] = '请小心保管好此密码，它允许你操作管理板块。';
$lang['Password [confirm]'] = '密码 [ 确认 ]';
$lang['verification'] = '核实';
$lang['Copy the text in pink between hyphens and paste it into the file "local/config/database.inc.php"(Warning : database.inc.php must only contain what is in pink, no line return or space character)'] = '请拷贝短横线之间的粉红色文字并粘贴到位于Piwigo安装目录下的include文件夹里的mysql.inc.php文件中（每行不允许有空格或回车）';
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = '需要帮助？ 请到<a href="%s">Piwigo论坛</a>提出你的问题.';
$lang['Webmaster mail address'] = '管理员Email地址';
$lang['Visitors will be able to contact site administrator with this mail'] = '游客通过此Email跟你联系';

$lang['PHP 5 is required'] = '必须PHP 5版本';
$lang['It appears your webhost is currently running PHP %s.'] = '你主机PHP版本好像是PHP %s.';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'Piwigo试着创建或修改.htaccess文件来转换到PHP 5。';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = '注意；你也可以自己修改设置PHP然后重新启动Piwigo。';
$lang['Try to configure PHP 5'] = '试试配置PHP 5';
$lang['Sorry!'] = '对不起!';
$lang['Piwigo was not able to configure PHP 5.'] = 'Piwigo不能设置PHP 5.';
$lang["You may referer to your hosting provider's support and see how you could switch to PHP 5 by yourself."] = '你应该联系你的主机管理员并向其请教如何设置PHP 5.';
$lang['Hope to see you back soon.'] = '希望下次再见到你...';
?>
