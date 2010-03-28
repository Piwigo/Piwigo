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

$lang['Installation'] = 'Установка';
$lang['Basic configuration'] = 'Базовая настройка';
$lang['Default gallery language'] = 'Язык по умолчанию';
$lang['Database configuration'] = 'Настройка базы данных';
$lang['Admin configuration'] = 'Настройка администрирования';
$lang['Start Install'] = 'Начать установку';
$lang['mail address must be like xxx@yyy.eee (example : jack@altern.org)'] = 'адрес электронной почты должен быть похож на xxx@yyy.eee (например: jack@altern.org)';

$lang['Webmaster login'] = 'Логин вебмастера';
$lang['It will be shown to the visitors. It is necessary for website administration'] = 'Он будет показан посетителям. Необходим для администрирования сайта';

$lang['Parameters are correct'] = 'Все прошло успешно';
$lang['Connection to server succeed, but it was impossible to connect to database'] = 'Успешно соединились с сервером, но невозможно подключиться к базе данных';
$lang['Can\'t connect to server'] = 'Невозможно соединиться с сервером';
$lang['The next step of the installation is now possible'] = 'Можно переходить к следующему шагу';
$lang['next step'] = 'следующий шаг';
$lang['Copy the text in pink between hyphens and paste it into the file "local/config/database.inc.php"(Warning : database.inc.php must only contain what is in pink, no line return or space character)'] = 'Скопируйте текст с розового поля между дефисами и вставьте его в файл "include/mysql.inc.php"(Внимание: mysql.inc.php должен содержать только это, ни пустых строк, ни пробелов быть не должно)';

$lang['Host'] = 'Хост MySQL';
$lang['localhost, sql.multimania.com, toto.freesurf.fr'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['User'] = 'Пользователь';
$lang['user login given by your host provider'] = 'логин, который выдал провайдер';
$lang['Password'] = 'Пароль';
$lang['user password given by your host provider'] = 'пароль, который выдал провайдер';
$lang['Database name'] = 'Имя базы данных';
$lang['also given by your host provider'] = 'также выдается провайдером. Часто совпадает с логином';
$lang['Database table prefix'] = 'Префикс таблиц в базе данных';
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = 'названия таблиц в базе данных будут начинаться с этого (что упростит их обслуживание)';
$lang['enter a login for webmaster'] = 'нужно ввести логин для вебмастера';
$lang['webmaster login can\'t contain characters \' or "'] = 'логин вебмастера не должен содержать символы  \' или "';
$lang['please enter your password again'] = 'еще раз пароль';
$lang['Installation finished'] = 'Установка завершена';
$lang['Webmaster password'] = 'Пароль вебмастера';
$lang['Keep it confidential, it enables you to access administration panel'] = 'Не сообщайте его никому, он необходим для доступа к панели администрирования';
$lang['Password [confirm]'] = 'Повторите пароль';
$lang['verification'] = 'еще раз для исключения опечатки';
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = 'Нужна помощь? Задайте свои вопросы на <a href="%s">Форуме Piwigo</a>.';
$lang['install_end_message'] = 'Настройка Piwigo закончена, переходите к следующему шагу<br /><br />
* перейдите на страницу [ <a href="identification.php">идентификации</a> ] и введите логин и пароль вебмастера<br />
* это позволит получить доступ к панели администрирования и инструкциям по размещению фотографий в папках';
$lang['Webmaster mail address'] = 'Электронная почта вебмастера';
$lang['Visitors will be able to contact site administrator with this mail'] = 'Будет использоваться для контакта посетителей с администратором';
?>