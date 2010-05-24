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

$lang['Upgrade'] = 'Обновление';
$lang['Upgrade from version %s to %s'] = 'Обновление с версии %s до %s';
$lang['Statistics'] = 'Статистика';
$lang['total upgrade time'] = 'общее время обновления';
$lang['total SQL time'] = 'общее время SQL';
$lang['SQL queries'] = 'SQL запросы';
$lang['Upgrade informations'] = 'Информация об обновлениях';
$lang['Perform a maintenance check in [Administration>Specials>Maintenance] if you encounter any problem.'] = 'Выполните обслуживание [Администирирование> Инструменты> Обслуживание] если вы столкнулись с какими-либо проблемами.';
$lang['Only administrator can run upgrade: please sign in below.'] = 'Только администратор может запустить обновление: проверьте ниже.';
$lang['You do not have access rights to run upgrade'] = 'У вас нет прав на запуск обновлений';

// Upgrade informations from upgrade_1.3.1.php
$lang['All sub-categories of private categories become private'] = 'Все подкатегории приватных категорий будут приватными';
$lang['User permissions and group permissions have been erased'] = 'Разрешения пользователей и групп были стерты';
$lang['Only thumbnails prefix and webmaster mail address have been saved from previous configuration'] = 'Только префикс эскизов и адрес почты вебмастера были сохранены от предыдущей конфигурации';

$lang['This page proposes to upgrade your database corresponding to your old version of Piwigo to the current version. The upgrade assistant thinks you are currently running a <strong>release %s</strong> (or equivalent).'] = 'Эта страница предлагает модернизировать базу данных от Вашей старой версии Piwigo до новой версии.
Помощник обновления думает, что Вы используете в настоящее время <b> выпуск %s </b> (или эквивалентный).';
$lang['As a precaution, following plugins have been deactivated. You must check for plugins upgrade before reactiving them:'] = 'Желательно, чтобы следующие плагины были дезактивированы. Вы должны проверить обновления плагинов прежде, чем активировать их снова:';
$lang['In <i>%s</i>, before <b>?></b>, insert:'] ='В %s, до ?>, вставить:';
?>