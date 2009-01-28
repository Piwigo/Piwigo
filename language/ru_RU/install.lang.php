<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
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
$lang['Initial_config'] = 'Базовая настройка';
$lang['Default_lang'] = 'Язык по умолчанию';
$lang['step1_title'] = 'Настройка базы данных';
$lang['step2_title'] = 'Настройка администрирования';
$lang['Start_Install'] = 'Начать установку';
$lang['reg_err_mail_address'] = 'адрес электронной почты должен быть похож на xxx@yyy.eee (например: jack@altern.org)';

$lang['install_webmaster'] = 'Логин вебмастера';
$lang['install_webmaster_info'] = 'Он будет показан посетителям. Необходим для администрирования сайта';

$lang['step1_confirmation'] = 'Все прошло успешно';
$lang['step1_err_db'] = 'Успешно соединились с сервером, но невозможно подключиться к базе данных';
$lang['step1_err_server'] = 'Невозможно соединиться с сервером';
$lang['step1_err_copy_2'] = 'Можно переходить к следующему шагу';
$lang['step1_err_copy_next'] = 'следующий шаг';
$lang['step1_err_copy'] = 'Скопируйте текст с розового поля между дефисами и вставьте его в файл "include/mysql.inc.php"(Внимание: mysql.inc.php должен содержать только это, ни пустых строк, ни пробелов быть не должно)';

$lang['step1_host'] = 'Хост MySQL';
$lang['step1_host_info'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['step1_user'] = 'Пользователь';
$lang['step1_user_info'] = 'логин, который выдал провайдер';
$lang['step1_pass'] = 'Пароль';
$lang['step1_pass_info'] = 'пароль, который выдал провайдер';
$lang['step1_database'] = 'Имя базы данных';
$lang['step1_database_info'] = 'также выдается провайдером. Часто совпадает с логином';
$lang['step1_prefix'] = 'Префикс таблиц в базе данных';
$lang['step1_prefix_info'] = 'названия таблиц в базе данных будут начинаться с этого (что упростит их обслуживание)';
$lang['step2_err_login1'] = 'нужно ввести логин для вебмастера';
$lang['step2_err_login3'] = 'логин вебмастера не должен содержать символы  \' или "';
$lang['step2_err_pass'] = 'еще раз пароль';
$lang['install_end_title'] = 'Установка завершена';
$lang['step2_pwd'] = 'Пароль вебмастера';
$lang['step2_pwd_info'] = 'Не сообщайте его никому, он необходим для доступа к панели администрирования';
$lang['step2_pwd_conf'] = 'Повторите пароль';
$lang['step2_pwd_conf_info'] = 'еще раз для исключения опечатки';
$lang['install_help'] = 'Нужна помощь? Задайте свои вопросы на <a href="%s">Форуме Piwigo</a>.';
$lang['install_end_message'] = 'Настройка Piwigo закончена, переходите к следующему шагу<br /><br />
* перейдите на страницу [ <a href="identification.php">идентификации</a> ] и введите логин и пароль вебмастера<br />
* это позволит получить доступ к панели администрирования и инструкциям по размещению фотографий в папках';
$lang['conf_mail_webmaster'] = 'Электронная почта вебмастера';
$lang['conf_mail_webmaster_info'] = 'Будет использоваться для контакта посетителей с администратором';
?>