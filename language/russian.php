<?php
/***************************************************************************
 *                                russian.php                              *
 *                            -------------------                          *
 *   application   : PhpWebGallery 1.3 <http://phpwebgallery.net>          *
 *                                                                         *
 ***************************************************************************/

/***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/

	$lang['only_members'] = "Только зарегистрированные пользователи могут обратиться к этой странице";
	$lang['invalid_pwd'] = "Недопустимый пароль!";
	$lang['access_forbiden'] = "Вы не уполномочены обратиться к этой странице";
	$lang['submit'] = "Submit";
	$lang['login'] = "вход в систему";
	$lang['password'] = "пароль";
	$lang['new'] = "новый";
	$lang['delete'] = "удалить";
	$lang['category'] = "категория";
	$lang['thumbnail'] = "эскиз";
	$lang['date'] = "дата";

	$lang['diapo_default_page_title'] = "Категория не выделена";
	$lang['thumbnails'] = "Эскизы";
	$lang['categories'] = "Категории";
	$lang['hint_category'] = "просмотреть изображения из корня этой категории";
	$lang['total_images'] = "общее количество";
	$lang['title_menu'] = "Меню";
	$lang['change_login'] = "сменить имя входа в систему";
	$lang['login'] = "вход в систему";
	$lang['hint_login'] = "идентификация допускает настройку вида сайта";
	$lang['logout'] = "выход из системы";
	$lang['customize'] = "настроить";
	$lang['hint_customize'] = "настройка вида галереи";
	$lang['hint_search'] = "поиск";
	$lang['search'] = "поиск";
	$lang['favorite_cat'] = "избранное";
	$lang['favorite_cat_hint'] = "отобразить избранное";
	$lang['about'] = "о движке";
	$lang['hint_about'] = "подробная информация относительно PhpWebGallery...";
	$lang['admin'] = "admin";
	$lang['hint_admin'] = "доступно только для администраторов";
	$lang['no_category'] = "Категория не выбрана.<br />Пожалуйста выберите ее в меню.";
	$lang['page_number'] = "номер страницы";
	$lang['previous_page'] = "Предыдущее";
	$lang['next_page'] = "Следующее";
	$lang['nb_image_category'] = "количество изображений в этой категории";

	$lang['recent_image'] = "Выложено не более";
	$lang['days'] = "дней назад";
	$lang['send_mail'] = "Комментарии шлите на эл. почту  ";
	$lang['title_send_mail'] = "Комментарий относительно фотогалереи на вашем сайте";
	$lang['sub-cat'] = "подкатегории";
	$lang['images_available'] = "изображений в этой категории";
	$lang['total'] = "изображений";
	$lang['upload_picture'] = "Загрузить изображение";

	$lang['registration_date'] = "зарегистрировано";
	$lang['creation_date'] = "создано";
	$lang['comment'] = "комментарий";
	$lang['author'] = "автор";
	$lang['size'] = "размер";
	$lang['filesize'] = "размер файла";
	$lang['file'] = "файл";
	$lang['generation_time'] = "Страница создана";
	$lang['favorites'] = "Избранное";
	$lang['search_result'] = "Результаты поиска";

	// about page
	$lang['about_page_title'] = "Про PhpWebGallery";
	$lang['about_title'] = "О движке галереи...";
	$lang['about_message'] = "<div style=\"text-align:center;font-weigh:bold;\">Информация о PhpWebGallery</div>
<ul>
<li>Это разработка вебсайта <a href=\"".$conf['site_url']."\" style=\"text-decoration:underline\">PhpWebGallery</a> version ".$conf['version'].". PhpWebGallery является сетевым приложением предоставляющим Вам возможность легко создать сетевую галерею изображений.</li>
<li>Технически, PhpWebGallery полностью разработан на базе PHP (elePHPant) с использоанием MySQL сервера баз данных (the SQuirreL).</li>
<li>Если Вы имеете какие-то предложения или комментарии, пожалуйста посетите <a href=\"".$conf['site_url']."\" style=\"text-decoration:underline\">PhpWebGallery</a> официальный сайт, и его специализированный <a href=\"".$conf['forum_url']."\" style=\"text-decoration:underline\">форум</a>.</li>

</ul>";
	$lang['about_return'] = "назад";

	// identification page
	$lang['ident_page_title'] = "Идентификация";
	$lang['ident_title'] = "Идентификация";

	$lang['ident_register'] = "Регистрация";
	$lang['ident_forgotten_password'] = "Забыли ваш пароль?";
	$lang['ident_guest_visit'] = "Зайдите на галерею как посетитель";

	// page personnalisation
	$lang['customize_page_title'] = "Настройка";
	$lang['customize_title'] = "Настройка";
	$lang['customize_nb_image_per_row'] = "Количество изображений в строке";
	$lang['customize_nb_row_per_page'] = " Количество строк на странице";

	$lang['customize_language'] = "Язык";

	$lang['maxwidth'] = "Максимальная ширина изображений";
	$lang['maxheight'] = "Максимальная высота изображений";
	$lang['err_maxwidth'] = "максимальной шириной должно быть число, превосходящее 50";
	$lang['err_maxheight'] = " максимальной высотой должно быть число, превосходящее 50";

	// photo page
	$lang['previous_image'] = "Предыдущее";
	$lang['next_image'] = "Следующее";
	$lang['back'] = "Нажмите на изображение, чтобы возвратиться к странице эскизов";
	$lang['info_image_title'] = "Информация изображения";
	$lang['link_info_image'] = "Изменить информацию";
	$lang['true_size'] = "Реальный размер";
	$lang['comments_title'] = "Комментарии от пользователей сайта";
	$lang['comments_del'] = "удалить этот комментарий";
	$lang['comments_add'] = "Добавить комментарий";
	$lang['month'][1] = "Январь";
	$lang['month'][2] = "Февраль";
	$lang['month'][3] = "Март";
	$lang['month'][4] = "Апрель";
	$lang['month'][5] = "Май";
	$lang['month'][6] = "Июнь";
	$lang['month'][7] = "Июль";
	$lang['month'][8] = "Август";
	$lang['month'][9] = "Сентябрь";
	$lang['month'][10] = "Октябрь";
	$lang['month'][11] = "Ноябрь";
	$lang['month'][12] = "Декабрь";
	$lang['day'][0] = "Воскресенье";
	$lang['day'][1] = "Понедельник";
	$lang['day'][2] = "Вторник";
	$lang['day'][3] = "Среда";
	$lang['day'][4] = "Четверг";
	$lang['day'][5] = "Пятница";
	$lang['day'][6] = "Суббота";

	$lang['add_favorites_alt'] = "Добавить к избранному";
	$lang['add_favorites_hint'] = "Добавить это изображение к вашему избранному";
	$lang['del_favorites_alt'] = "Удалить из избранного";
	$lang['del_favorites_hint'] = "Удалить это изображение из избранного";
	
	// page register
	$lang['register_page_title'] = "Регистрация";
	$lang['register_title'] = " Регистрация";
	$lang['reg_err_login1'] = "Пожалуйста, введите имя входа в систему";
	$lang['reg_err_login2'] = "имя входа в систему не должно закончиться пробелом";
	$lang['reg_err_login3'] = "имя входа в систему не должно начаться с пробела";
	$lang['reg_err_login4'] = "имя входа в систему не должно содержать символы \" и '";
	$lang['reg_err_login5'] = "это имя входа в систему уже используется";
	$lang['reg_err_pass'] = "пожалуйста введите ваш пароль еще раз";
	$lang['reg_confirm'] = "еще раз";
	$lang['reg_mail_address'] = "адрес электронной почты";
	$lang['reg_err_mail_address'] = " адрес электронной почты должен быть похож на xxx@yyy.eee (пример: jack@altern.org)";
	
	// page search
	$lang['search_title'] = "Поиск";
	$lang['invalid_search'] = "поиск ведется по 3 символам и больше";
	$lang['search_field_search'] = "Поиск";
	$lang['search_return_main_page'] = "Возвратиться к странице эскизов";

	// page upload
	$lang['upload_forbidden'] = "Вы не можете загрузить изображения в эту категории";
	$lang['upload_file_exists'] = "Имя изображения, уже используется";
	$lang['upload_filenotfound'] = "Вы должны выбрать формат файла для изображения";
	$lang['upload_cannot_upload'] = "не может передать изображение на сервер";
	$lang['upload_title'] = "Изображение загружено";
	$lang['upload_advise'] = "Выберите изображение, чтобы разместить в категорию : ";
	$lang['upload_advise_thumbnail'] = "Дополнительно рекомендуется: выберите эскиз, чтобы связать его с ";
	$lang['upload_advise_filesize'] = "размер файла изображения не должен превышать : ";
	$lang['upload_advise_width'] = "ширина изображения не должна превышать : ";
	$lang['upload_advise_height'] = "высота изображения не должна превышать : ";
	$lang['upload_advise_filetype'] = "изображение должно быть форматов jpg, gif или png";
	$lang['upload_err_username'] = "необходимо задать имя пользователя ";
	$lang['upload_username'] = "Имя пользователя";
	$lang['upload_successful'] = "Изображение передано успешно. Администратор проверит правильность этого как можно скорее";

// new or modified in release 1.3
$lang['charset'] = 'windows-1251';
$lang['no'] = 'нет';
$lang['yes'] = 'да';
$lang['guest'] = 'гость';
$lang['mail_address'] = 'адрес электронной почты';
$lang['public'] = 'общее';
$lang['private'] = 'частное';
$lang['add'] = 'создать';
$lang['dissociate'] = 'отделить';
$lang['mandatory'] = 'обязательный';
$lang['err_date'] = 'неправильная дата';
$lang['picture'] = 'изображение';
$lang['IP'] = 'IP';
$lang['close'] = 'закрыть';
$lang['open'] = 'открыть';
$lang['keywords'] = 'ключевое слово';
$lang['errors_title'] = 'Ошибка';
$lang['infos_title'] = 'Информация';
$lang['default'] = 'по умолчанию';
$lang['comments'] = 'комментарии';
$lang['category_representative'] = 'представить';
$lang['stats'] = 'статистика';
$lang['most_visited_cat_hint'] = 'показать последние посещенные изображения';
$lang['most_visited_cat'] = 'последних посещений';
$lang['best_rated_cat_hint'] = 'показать изображения с лучшим рейтингом';
$lang['best_rated_cat'] = 'лучший рейтинг';
$lang['recent_cat_hint'] = 'показать наиболее посещаемые изображения';
$lang['recent_cat'] = 'наиболее посещаемые';
$lang['recent_cat_title'] = 'Последние изображения';
$lang['visited'] = 'посещений';
$lang['times'] = 'время';
$lang['customize_theme'] = 'Тема интерфейса';
  $lang['conf_default_theme_info'] = 'тема оформления по умолчанию';

$lang['customize_expand'] = 'Развернуть все категории';
$lang['customize_show_nb_comments'] = 'Показывать количество комментариев';
$lang['customize_short_period'] = 'Короткий период';
$lang['customize_long_period'] = 'Длительный период';
$lang['customize_template'] = 'тема оформления';
$lang['err_periods'] = 'период должен быть целым числом';
$lang['err_periods_2'] = 'период должен быть больше 0. Длительный период должен быть больше короткого периода.';
$lang['create_cookie'] = 'создать cookie';
$lang['customize_day'] = 'день';
$lang['customize_week'] = 'неделя';
$lang['customize_month'] = 'месяц';
$lang['customize_year'] = 'год';
$lang['slideshow'] = 'слайдшоу';
$lang['period_seconds'] = '(задержка)';
$lang['slideshow_stop'] = 'остановить слайдшоу';
$lang['comment_added'] = 'Ваш комментарий был зарегистрирован';
$lang['comment_to_validate'] = 'Администратор должен авторизовать ваши комментарии прежде чем они станут видны.';
$lang['comment_anti-flood'] = 'Анти-флудинговая система: пожалуйста подождите перед вводом следующего комментария';
$lang['comment_user_exists'] = 'Это ИМЯ уже используется другим пользователем';
$lang['invalid_search'] = 'Искомые слова должны содержать более 3-х символови не содержать знаков пунктуации';
$lang['search_mode_or'] = 'одно из слов';
$lang['search_mode_and'] = 'все слова';
$lang['search_comments'] = 'separate different words with spaces';
$lang['upload_name'] = 'Имя изображения';
$lang['upload_author'] = 'Автор';
$lang['upload_creation_date'] = 'Дата создания (DD/MM/YYYY)';
$lang['upload_comment'] = 'Комментарий';
$lang['mail_hello'] = 'Привет,';
$lang['mail_new_upload_subject'] = 'Новые изображения на сайте';
$lang['mail_new_upload_content'] = 'A new picture has been uploaded on the gallery. It is waiting for your validation. Let\'s meet in the administration panel to authorize or refuse this picture.';
$lang['mail_new_comment_subject'] = 'Новые комментарии на сайте';
$lang['mail_new_comment_content'] = 'Зарегистрированы новые комментарии в галерее. If you chose to validate each comment, you first have to validate this comment in the administration panel to make it visible in the gallery.'."\n\n".'Вы можете посмотреть новые комментарии в панели администратора';
$lang['connected_user'] = 'Подключен пользователь -';
$lang['title_comments'] = 'Пользовательские комментарии';
$lang['stats_last_days'] = 'последних дней';
$lang['hint_comments'] = 'Посмотреть последние комментарии пользователей';
$lang['menu_login'] = 'идентификация';
//-------------------------------------------------------------- administration
if ( $isadmin )
{
		$lang['title_add'] = "Добавить/Изменить имя пользователя";
		$lang['title_liste_users'] = "Список пользователей";
		$lang['title_history'] = "История";
		$lang['title_update'] = "Обновить базу данных";
		$lang['title_configuration'] = "Параметры PhpWebGallery";
		$lang['title_instructions'] = "Инструкции";
		$lang['title_categories'] = "Управление категориями";
		$lang['title_edit_cat'] = "Редактирование категории";
		$lang['title_info_images'] = "Изменить информацию изображения категории";
		$lang['title_thumbnails'] = "Создать эскизы";
		$lang['title_thumbnails_2'] = "для";
		$lang['title_default'] = "Администрирование PhpWebGallery";

		$lang['menu_title'] = "Администрирование";
		$lang['menu_config'] = "Параметры";
		$lang['menu_users'] = "Пользователи";
		$lang['menu_add_user'] = "добавить";
		$lang['menu_list_user'] = "список";
		$lang['menu_categories'] = "Категории";
		$lang['menu_update'] = "Обновление базы данных";
		$lang['menu_thumbnails'] = "Эскизы";
		$lang['menu_history'] = "История";
		$lang['menu_instructions'] = "Инструкции";
		$lang['menu_back'] = "Назад на фотоальбом";
		$lang['title_waiting'] = "Изображения, ожидающие проверки";
		$lang['menu_waiting'] = "Ожидание";

		$lang['default_message'] = "Панель администратора PhpWebGallery";

		// page de configuration  
		$lang['conf_err_prefixe'] = "префикс эскиза не должен содержать символов с диакритическими знаками";
		$lang['conf_err_mail'] = "адрес электронной почты некорректен. Должен быть похож на name@server.com";

		$lang['conf_err_sid_size'] = "размер идентификатора сеанса должен быть целочисленным значением между 4 и 50";
		$lang['conf_err_sid_time'] = "время сеанса должно быть целочисленным значением между 5 и 60";
		$lang['conf_err_max_user_listbox'] = "максимальное количество пользователей должно быть целочисленным значением между 0 и 255";
		$lang['conf_confirmation'] = "Информация зарегистрирована в базе данных";

		$lang['conf_general_title'] = "Основная конфигурация";
		$lang['conf_general_webmaster'] = "Логин вебмастера";
		$lang['conf_general_webmaster_info'] = "это будет показано посетителям. Необходимо для администрирования вебсайта";
		$lang['conf_general_mail'] = "Адрес почты вебмастера";
		$lang['conf_general_mail_info'] = "посетители могут контактировать посредством этой электронной почты";
		$lang['conf_general_prefix'] = "Префикс имен файлов эскизов";
		$lang['conf_general_prefix_info'] = "эскизы используют этот префикс (не изменяйте если не уверенны).";

		$lang['conf_general_access'] = "Тип доступа";
		$lang['conf_general_access_1'] = "свободно";
		$lang['conf_general_access_2'] = "ограничено";
		$lang['conf_general_access_info'] = "- свободно: любой может вести сайт, любой посетитель может создать учетную запись, чтобы настроить вид вебсайта<br />- ограничено: web-мастер создает учетные записи. Только зарегистрированные пользователи могут вести сайт";
		$lang['conf_general_max_user_listbox'] = "Максимальный размер окна списка пользователей";
		$lang['conf_general_max_user_listbox_info'] = "- это - максимальное число пользователей, которых PhpWebGallery отображают в окне списка вместо простого текстового поля на идентифицирующей странице<br />- введите число между 0 и 255, 0 используется, если  Вы хотите отображать только окно списка";

		$lang['conf_comments'] = "Комментарии пользователей";
		$lang['conf_comments_title'] = "Конфигурация ".$lang['conf_comments'];
		$lang['conf_comments_show_comments'] = $lang['conf_comments'];
		$lang['conf_comments_show_comments_info'] = "отображать комментарии пользователей под каждым изображением ?";
		$lang['conf_comments_comments_number'] = "Количество комментариев на странице";
		$lang['conf_comments_comments_number_info'] = "количество комментариев, отображаемых на каждой странице. Это число неограниченно для изображения. Введите число между 5 и 50.";
		$lang['conf_err_comment_number'] = "Количество комментариев на странице должно быть между 5 и 50.";

		$lang['conf_remote_site_delete_info'] = "Удаление отдаленного сервера удалит все изображение и категории, относящиеся к этому серверу.";
		$lang['conf_upload_title'] = "Параметры пользовательской загрузки файлов";
		$lang['conf_upload_available'] = "Авторизовать для загрузки изображений";
		$lang['conf_upload_available_info'] = "авторизация загрузки изображений пользователями в категории вебсайта (не на отдаленном сервере).";
		$lang['conf_upload_maxfilesize'] = "Максимальный размер файла";
		$lang['conf_upload_maxfilesize_info'] = "максимальный размер файла для передаваемых изображений. Должно быть числом от 10 до 1000 Kb.";
		$lang['conf_err_upload_maxfilesize'] = "Максимальный размер файла для передаваемых изображений должен быть числом от 10 до 1000 Kb.";


		$lang['conf_upload_maxwidth'] = "Максимальная ширина";
		$lang['conf_upload_maxwidth_info'] = "максимальная ширина, разрешенная для переданных изображений. Должна быть числом, превосходящим 10 пикселей";
		$lang['conf_err_upload_maxwidth'] = "Максимальная ширина, разрешенная для переданных изображений, должна быть числом, превосходящим 10 пикселей ";
		$lang['conf_upload_maxheight'] = "Максимальная высота";
		$lang['conf_upload_maxheight_info'] = "максимальная высота, разрешенная для переданных изображений. Должна быть числом, превосходящим 10 пикселей";
		$lang['conf_err_upload_maxwidth'] = "Максимальная высота, разрешенная для переданных изображений, должна быть числом, превосходящим 10 пикселей ";
		$lang['conf_upload_maxwidth_thumbnail'] = "Максимальная ширина эскизов";
		$lang['conf_upload_maxwidth_thumbnail_info'] = "максимальная ширина, разрешенная для переданных эскизов. Должна быть числом, превосходящим 10 пикселей";
		$lang['conf_err_upload_maxwidth_thumbnail'] = "Максимальная ширина, разрешенная для переданных эскизов, должна быть числом, превосходящим 10 пикселей";
		$lang['conf_upload_maxheight_thumbnail'] = "Максимальная высота эскизов";
		$lang['conf_upload_maxheight_thumbnail_info'] = "максимальная высота, разрешенная для переданных эскизов. Должна быть числом, превосходящим 10 пикселей ";
		$lang['conf_err_upload_maxheight_thumbnail'] = "Максимальная высота, разрешенная для переданных эскизов, должна быть числом, превосходящим 10 пикселей";
 
		$lang['conf_default_title'] = "Заданные по умолчанию свойства дисплея для незарегистрированных посетителей и новых учетных записей";
		$lang['conf_default_language_info'] = "заданный по умолчанию язык";

		$lang['conf_session_title'] = "Сессионные настройки";
		$lang['conf_session_size'] = "Размер идентификатора";
		$lang['conf_session_size_info'] = "- чем длиннее ваш идентификатор, тем более безопасен ваш сайт - <br /> - ввести число между 4 и 50";
		$lang['conf_session_time'] = "Время жизни сессии";
		$lang['conf_session_time_info'] = "- чем короче время жизни сессии, тем более безопасный ваш сайт - <br /> ввести число между 5 и 60, в минутах";
		$lang['conf_session_key'] = "Ключевое слово";

	        $lang['conf_session_key_info'] = "- ключевое слово сеанса улучшает кодирование идентификатора сеанса - <br /> ввести любое предложение до 255 символов";
		$lang['conf_session_delete'] = "Удалять устаревшие сеансы";
		$lang['conf_session_delete_info'] = "рекомендуется освобождать таблицу базы данных сеанса, потому что устаревшие сеансы остаются в базе данных (это безопасно для защиты)";

		$lang['user_err_modify'] = "Этот пользователь не может быть изменен или удален";
		$lang['user_err_unknown'] = "Этот пользователь не существует в базе данных";

		$lang['adduser_info_message'] = "Информация зарегистрирована в базе данных для пользователя ";
		$lang['adduser_info_password_updated'] = "(пароль модифицирован)";
		$lang['adduser_info_back'] = "назад к списку пользователей";
		$lang['adduser_fill_form'] = "Пожалуйста заполните следующую форму";
		$lang['adduser_unmodify'] = "неизменяемо";
		$lang['adduser_status'] = "статус";
		$lang['adduser_status_admin'] = "администратор";
		$lang['adduser_status_member'] = "участник";
		$lang['adduser_status_guest'] = "гость";

		$lang['permuser_info_message'] = "Не разрешено";
		$lang['permuser_title'] = "Разрешения для пользователя.";
		$lang['permuser_warning'] = "Внимание: \"<span style=\"font-weight:bold;\">недоступно</span>\" к корню категории препятствуют обращению к целой категории";
		$lang['permuser_authorized'] = "доступно";
		$lang['permuser_forbidden'] = "недоступно";
		$lang['permuser_parent_forbidden'] = "родительская категория запрещена";

		$lang['listuser_confirm'] = "Вы действительно хотите удалить этого пользователя";
		$lang['listuser_info_deletion'] = "удалено из базы данных";
		$lang['listuser_user_group'] = "Группа пользователей - ";
		$lang['listuser_modify'] = "изменить";
		$lang['listuser_modify_hint'] = "изменить информацию ";
		$lang['listuser_permission'] = "разрешения";
		$lang['listuser_permission_hint'] = "изменить разрешения";
		$lang['listuser_delete'] = "удалить";
		$lang['listuser_delete_hint'] = "удалить пользователя";
		$lang['listuser_button_all'] = "все";
		$lang['listuser_button_invert'] = "обратить";
		$lang['listuser_button_create_address'] = "создать адрес электронной почты";
 
		$lang['cat_invisible'] = "невидимо";
		$lang['cat_edit'] = "Редактировать";
		$lang['cat_up'] = "Вверх";
		$lang['cat_down'] = "Вниз";
		$lang['cat_image_info'] = "Информация об изображении";
		$lang['cat_total'] = "общее количество";

		$lang['editcat_confirm'] = "Информация зарегистрирована в базе данных";
		$lang['editcat_back'] = "категории";
		$lang['editcat_title1'] = "Параметры для";
		$lang['editcat_name'] = "Имя";
		$lang['editcat_comment'] = "Коментарий";

  $lang['editcat_status'] = 'Статус';

		$lang['infoimage_general'] = "Общие параметры для категории";
		$lang['infoimage_useforall'] = "использовать для всех изображений?";
		$lang['infoimage_creation_date'] = "дата создания";
		$lang['infoimage_detailed'] = "Параметры для каждого изображения";
		$lang['infoimage_title'] = "заголовок";
		$lang['infoimage_comment'] = "коментарий";

 		$lang['update_missing_tn'] = "эскиз отсутствует для";
		$lang['update_disappeared_tn'] = "эскиз исчез";
		$lang['update_disappeared'] = "не существует";
		$lang['update_part_deletion'] = "Удаление изображений, которые не имеют никакого эскиза или которыене существуют";
		$lang['update_deletion_conclusion'] = "изображений, удалено из базы данных";
		$lang['update_part_research'] = "Поиск новых изображений в каталогах";
		$lang['update_research_added'] = "добавить";
		$lang['update_research_tn_ext'] = "эскиз в";
		$lang['update_research_conclusion'] = "изображений, добавлено в базу данных";
		$lang['update_default_title'] = "Выберите опции";
		$lang['update_only_cat'] = "модифицировать категории (без изображений)";
		$lang['update_all'] = "модифицировать все";

  $lang['tn_width'] = 'ширина';
  $lang['tn_height'] = 'высота';

		$lang['tn_no_support'] = "Изображения недоступны или не поддерживаются";
		$lang['tn_format'] = "для формата файла";
		$lang['tn_thisformat'] = "для этого формата файла";
		$lang['tn_err_width'] = "шириной должно быть число, превосходящее";
		$lang['tn_err_height'] = "высотой должно быть число, превосходящее";
		$lang['tn_results_title'] = "Результаты миниатюризации";
		$lang['tn_picture'] = "изображение";
		$lang['tn_results_gen_time'] = "создано";

		$lang['tn_stats'] = "Основной статус";
		$lang['tn_stats_nb'] = "число миниатюризированных изображений";
		$lang['tn_stats_total'] = "общее время";
		$lang['tn_stats_max'] = "максимальное время";
		$lang['tn_stats_min'] = "минимальное время";
		$lang['tn_stats_mean'] = "среднее время";

		$lang['tn_err'] = "Вы сделали ошибки";
		$lang['tn_params_title'] = "Параметры миниатюризации";
		$lang['tn_params_GD'] = "версия GD-библиотеки";
		$lang['tn_params_GD_info'] = "- GD - библиотека управления изображения для PHP <br /> - выбрать версию, установленную на вашем сервере. Если Вы выбираете неправильно, Вы будете видеть только сообщения об ошибках. Возвратитесь и выберите другую версию. Если никакие версии не работают, это означает, что ваш сервер не поддерживает GD.";
		$lang['tn_params_width_info'] = "максимальная ширина, которую эскизы могут занять";
		$lang['tn_params_height_info'] = "максимальная высота, которую эскизы могут занять";
		$lang['tn_params_create'] = "создать";
		$lang['tn_params_create_info'] = "Не пробуйте миниатюризировать слишком много изображений за один раз. <br /> Действительно, миниатюризация использует много ресурсов центрального процессора. Если Вы устанавливали PhpWebGallery на бесплатном провайдере, слишком высокая загрузка центрального процессора может привести к удалению вашего вебсайта.";
		$lang['tn_params_format'] = "формат файла";
		$lang['tn_params_format_info'] = "только формат jpeg поддерживается для создания эскиза";
		$lang['tn_alone_title'] = "изображения без эскизов (только jpeg и png)";
		$lang['tn_dirs_title'] = "Список каталогов";
		$lang['tn_dirs_alone'] = "изображения без эскизов";

		$lang['help_images_title'] = "Добавить изображения";
		$lang['help_images_intro'] = "Как размещать изображения в ваши категории:";
		$lang['help_images'][0] = "в каталоге \"galleries\", создайте каталоги, которые будут представлять ваши категории.";
		$lang['help_images'][1] = "в каждом каталоге, Вы можете создать так много подкаталогов, сколько Вы пожелаете.";
		$lang['help_images'][2] = "вы можете создать так много категорий и рубрик для каждой категории, сколько Вы пожелаете.";
		$lang['help_images'][3] = "файлы изображений должны иметь формат jpeg (расширение jpg или JPG), формат gif (расширение gif или GIF) или формат png (расширение png или PNG).";
		$lang['help_images'][4] = "постарайтесь не использовать пробелы \" \" или дефис \"-\" в файлах изображений. Я советую Вам использовать символ подчеркивания \"_\", который обрабатывается PhpWebGallery и обеспечит нормальные результаты.";

		$lang['help_thumbnails_title'] = "Эскизы";
		$lang['help_thumbnails'][0] = "в каждом каталоге, содержащем изображение, отображаемые на вашем сайте, есть подкаталог, названный \"thumbnail\". Если такого нет, создайте его, чтобы разместить там ваши эскизы.";
		$lang['help_thumbnails'][1] = "эскизы могут не иметь то же самое расширение, как их связанное изображение (например изображение с .jpg расширением может иметь эскиз с .GIF расширением).";
		$lang['help_thumbnails'][2] = "эскиз, связанный с изображением должен быть предустановлен с префиксом, заданным на странице конфигурации (image.jpg-> TN-image.GIF например).";
		$lang['help_thumbnails'][3] = "я советую Вам использовать модуль для Windows, загружаемый с сайта PhpWebGallery для управления и создания эскизов.";
		$lang['help_thumbnails'][4] = "вы можете использовать страницу создания эскиза, интегрированную в PhpWebGallery, но я не советую Вам это делать, потому что качество эскиза может быть невысоким, плюс это предполагает высокую загрузку центрального процессора, что может стать проблемой, если Вы используете бесплатный хостинг.";
		$lang['help_thumbnails'][5] = "если Вы хотите создавать эскизы на хостинг-провайдере, Вы должны задать chmod 775 на каталог \"galleries\" и все его подкаталоги.";

		$lang['help_database_title'] = "Обновление базы данных";
		$lang['help_database'][0] = "после того, как файлы изображений и эскизы, правильно помещены в каталоги, нажмите на \"обновление базы данных\" в меню панели администрирования.";

		$lang['help_infos_title'] = "Разная информация";
		$lang['help_infos'][0] = "Как только Вы создали вашу галерею, войдите в список пользователей и измените разрешения для пользователя \"посетитель\". Все новые зарегистрированные пользователи будут иметь по умолчанию те же самые разрешения как и  пользователь \"посетитель\".";
		$lang['help_infos'][1] = "Если у Вас есть вопросы, не смущайтесь смотреть на форуме и задавать там вопросы. Форум (доска объявлений) доступен на сайте PhpWebGallery.";

		$lang['help_remote_title'] = "Удаленный сайт";
		$lang['help_remote'][0] = "PhpWebGallery предлагает возможность использовать несколько серверов, чтобы сохранить изображения, которые составят вашу галерею. Это может быть полезно, если ваша галерея установлена на сервере с ограниченным пространством и если у Вас большое количество изображений, которые должны быть показаны. Пожалуйста, следуйте этой процедуре: ";
		$lang['help_remote'][1] = "1. отредактируйте файл \"create_listing_file.php\" (Вы найдете его в каталоге \"admin\"), измените строку \"$prefixe_thumbnail = \"TN-\";\" если префикс для ваших эскизов - не \" TN-\".";
		$lang['help_remote'][2] = "2. разместите измененный файл \"create_listing_file.php\" на вашем отдаленном вебсайте, в корневом каталоге ваших каталогов image (в каталоге \"galleries\" этого вебсайта) посредством ftp.";
		$lang['help_remote'][3] = "3. запустите сценарий, используя url http://domaineDistant/repGalerie/create_listing_file.php, который создаст файл listing.";
		$lang['help_remote'][4] = "4. верните файл listing.xml с вашего отдаленного вебсайта, и положите его в каталог \"admin\" текущего вебсайта.";
		$lang['help_remote'][5] = "5. пожалуйста, запустите обновление данных изображений с помощью интерфейса администрирования с использованием файла listing.xml, после чего удалите его из каталога \"admin\".";
		$lang['help_remote'][6] = "Вы можете модифицировать содержание отдаленного вебсайта, повторяя описанную манипуляцию. Вы можете также уничтожить отдаленный вебсайт, выбирая опцию в разделе конфигурации панели администрирования. ".

		$lang['help_upload_title'] = "Добавленние изображений пользователями.";
		$lang['help_upload'][0] = "PhpWebGallery предоставляет возможность передачи изображений для пользователей. Чтобы сделать это:";
		$lang['help_upload'][1] = "1. разрешите опцию в разделе конфигурации панели администрирования.";
		$lang['help_upload'][2] = "2. установите права на запись в каталогах изображений.";

		$lang['install_message'] = "Сообщение";
		
		$lang['step1_confirmation'] = "Параметры правильны";
		$lang['step1_err_db'] = "Подключение к серверу успешно, но было невозможно подключиться к базе данных";
		$lang['step1_err_server'] = "Невозможно подключиться к серверу";
		$lang['step1_err_copy'] = "Скопируйте текст между дефисами и вставьте его в файл \"include/mysql.inc.php\"(Внимание : mysql.inc.php должен содержать только то, что находится в синем)";
		$lang['step1_err_copy_2'] = "Следующий шаг инсталляции теперь возможен";
		$lang['step1_err_copy_next'] = "следующий шаг";
		$lang['step1_title'] = "Шаг 1/2";
		$lang['step1_host'] = "MySQL host";

		$lang['step1_user'] = "Пользователь";
		$lang['step1_user_info'] = "пользовательский вход в систему, данный вашим хост-провайдером";
		$lang['step1_pass'] = "Пароль";
		$lang['step1_pass_info'] = "пользовательский пароль, данный вашим хост-провайдером";
		$lang['step1_database'] = " Имя базы данных";
		$lang['step1_database_info'] = "также данный вашим хост-провайдером";
		$lang['step1_prefix'] = "Префикс таблиц базы данных";
		$lang['step1_prefix_info'] = "имена таблиц базы данных будут предустановленны с этим префиксом (что даст возможность Вам лучше управлять вашими таблицами)";
		
		$lang['step2_err_login1'] = "введите имя входа в систему для web-мастера";
		$lang['step2_err_login3'] = "имя входа в систему web-мастера не может содержать символы ' или \"";
		$lang['step2_err_pass'] = "пожалуйста введите ваш пароль снова";
		$lang['step2_err_mail'] = $lang['conf_err_mail'];

		$lang['install_end_title'] = "Инсталляция закончилась";
		$lang['install_end_message'] = "Конфигурация PhpWebGallery закончена, вот - следующий шаг<br /><br />
									Для соображения безопасности, пожалуйста удалите файл \"install.php\" из каталога \"admin\"<br />
									После того как этот файл будет удален, следуйте за следующим инструкциям:
									<ul>
										<li>идите на страницу идентификации: [ <a href='../identification.php'>сюда</a> ] и воспользуйтесь входом в систему web-мастера</li>
										<li>этот вход в систему даст возможность Вам обратиться к [ <a href='admin.php'>панели администрирования</a> ] и к командам, чтобы размещать изображения в ваши каталоги</li>
									</ul>";
		$lang['step2_title'] = "Шаг 2/2";
		$lang['step2_pwd'] = "пароль вебмастера";
		$lang['step2_pwd_info'] = "Сохраните это (конфиденциально), это даст возможность Вам обратиться к панели администрирования";
		$lang['step2_pwd_conf'] = "подтверждение пароля";
		$lang['step2_pwd_conf_info'] = "верификация";
  // new or modified in release 1.3
  $lang['remote_site'] = 'Remote site';
  $lang['title_add'] = 'Создать пользователя';
  $lang['title_modify'] = 'Изменить пользователя';
  $lang['title_groups'] = 'Менеджер групп';
  $lang['title_user_perm'] = 'Изменить настройки пользователя';
  $lang['title_cat_perm'] = 'Изменить настройки категорий';
  $lang['title_group_perm'] = 'Изменить настройки групп';
  $lang['title_picmod'] = 'Изменить информацию по изображению';
  $lang['menu_groups'] = 'Группы';
  $lang['menu_comments'] = 'Комментарии';
  $lang['conf_general_log'] = 'История';
  $lang['conf_general_log_info'] = 'хранить историю посещений сайта? История посещений будет доступна в панели администратора';
  $lang['conf_general_mail_notification'] = 'Оповещение по почте';
  $lang['conf_general_mail_notification_info'] = 'автоматическое оповещение по почте администратора (и только его) когда пользователь создает комментарий или добавляет изображение.';
  $lang['conf_comments_validation'] = 'Проверка';
  $lang['conf_comments_validation_info'] = 'администратор проверяет комментарии пользователей прежде чем они станут доступны на сайте';
  $lang['conf_comments_forall'] = 'Для всех?';
  $lang['conf_comments_forall_info'] = 'все посетители могут давать комментарии';
  $lang['conf_default_nb_image_per_row_info'] = 'заданное по умолчанию количество изображений в строке';
  $lang['conf_default_nb_row_per_page_info'] = 'количество строк на странице';
  $lang['conf_default_short_period_info'] = 'по дням. Период, в течении которого  изображения показываются с красной меткой. Короткий период должен превосходить 1 день.';
  $lang['conf_default_long_period_info'] = 'по дням. Период, в течении которого  изображения показываются с зеленой меткой. Длительный период должен превосходить короткий период.';

  $lang['conf_default_expand_info'] = 'раскрыть все категории в меню по умолчанию?';
  $lang['conf_default_show_nb_comments_info'] = 'показывать количество комментариев для каждого изображения на странице эскизов';
  $lang['conf_default_maxwidth_info'] = 'Максимальная ширина для показа изображений: изображения будут иметь данную ширину только при показе, файл изображения не изменяется. Оставьте пустым, чтобы не менять исходные размеры.';
  $lang['conf_default_maxheight_info'] = 'То же, что и Максимальная ширина, только для высоты';
  $lang['conf_session_cookie'] = 'Авторизация cookies';
  $lang['conf_session_cookie_info'] = 'пользователям не нужно регистрироваться при каждом посещении. Менее безопасный.';
  $lang['adduser_associate'] = 'Ассоциировать группу';
  $lang['group_add'] = 'Создать группу';
  $lang['group_add_error1'] = 'Имя группы не должно содержать " или \'';
  $lang['group_add_error2'] = 'Это имя уже используется другой группой';
  $lang['group_confirm'] = 'Вы действительно хотите удалить эту группу?';
  $lang['group_list_title'] = 'Список существующих групп';
  $lang['group_err_unknown'] = 'Этой группы нет в базе данных';
  $lang['cat_permission'] = 'свойства';
  $lang['cat_update'] = 'обновить';
  $lang['cat_add'] = 'Создать виртуальную категорию';
  $lang['cat_parent'] = 'родительская категория';
  $lang['cat_error_name'] = 'Имя категории не должно быть пустым';
  $lang['cat_virtual'] = 'виртуальный';
  $lang['cat_first'] = 'Двигать вверх';
  $lang['cat_last'] = 'Двигать вниз';
  $lang['editcat_visible_info'] = '(невидима, кроме администратора)';
  $lang['editcat_visible'] = 'Видимых';
  $lang['editcat_uploadable'] = 'Обновить доступные';
  $lang['infoimage_keyword_separation'] = '(separate with coma ",")';
  $lang['infoimage_addtoall'] = 'создать все';
  $lang['infoimage_removefromall'] = 'удалить из всех';
  $lang['infoimage_associate'] = 'Ассоциировать с категорией';
  $lang['update_wrong_dirname'] = 'Имена директориев и файлов должны состоять из букв и символов "-", "_" или "."';
  $lang['stats_pages_seen'] = 'страниц просмотрено';
  $lang['stats_visitors'] = 'гость';
  $lang['stats_empty'] = 'очистить историю';
  $lang['stats_pages_seen_graph_title'] = 'Количество просмотренных страниц в день';
  $lang['stats_visitors_graph_title'] = 'Количество посетителей в день';
  $lang['comments_last_title'] = 'Последние комментарии';
  $lang['comments_non_validated_title'] = 'Комментарии ожидающие проверки';
  $lang['help_database'][1] = 'чтобы избежать модификации слишком многих изображений при одиночном обновлении, рекомендуется проводить обновление только нужной категории используя ссылку "обновить" в разделе "Категории".';
  $lang['help_upload'][3] = "Категории должны быть доступны для обновления.";
  $lang['help_upload'][4] = "Переданные изображения пользователями не видимы сразу на вебсайте, они должны быть утверждены администратором. Для этого администратор должен зайти на страницу \"ожидание\" панели администрирования, проверить правильность или отказаться от предложенных изображений, после чего запустить обновление данных изображений.";

  $lang['help_virtual_title'] = 'Связь между изображениями и категориями с виртуальными категориями';
  $lang['help_virtual'][0] = 'PhpWebGallery разделяет категории, где изображения сохранены и категории, где изображения будут показаны..';
  $lang['help_virtual'][1] = 'По умолчанию, изображения показываются только в их реальных категориях: т.е. в соответствии с каталогами на сервере.';
  $lang['help_virtual'][2] = 'Чтобы связать изображение с категорией, Вы должны ассоциациировать её на странице информации об изображении или на информации о всех изображениях категории.';
  $lang['help_virtual'][3] = 'Используя этот принцип, можно создавать виртуальные категории в PhpWebGallery без реальных каталогов соответствующих этой категории. Вы только должны создать эту категорию в разделе "Категории" панели администратора.';

  $lang['help_groups_title'] = 'Группы пользователей';
  $lang['help_groups'][0] = 'PhpWebGallery может управлять группами пользователей. Может быть очень полезно иметь общие разрешения для частных категорий.';
  $lang['help_groups'][1] = '1. Создайте группу "Семья" в меню "Группы" панели администратора.';
  $lang['help_groups'][2] = '2. В меню "Пользователи", отредактируйте и затем ассоциируйте их с группой "Семья".';
  $lang['help_groups'][3] = '3. Изменяя разрешения для категории или для группы, Вы будете видеть, что все категории, доступные для группы, доступны и для ее членов.';
  $lang['help_groups'][4] = 'Пользователи могут входить в несколько групп. Разрешения приорететнее запрещений: если пользователь "ГПС" входит в группу "коллеги" и "друзья", и только группа "коллеги" могут видеть категорию "Рождество 2003", пользователь "ГПС" будет видеть "Рождество 2003".';
  $lang['help_access_title'] = 'Авторизация доступа';
  $lang['help_access'][0] = 'PhpWebGallery может запретить доступ к категориям. Категории могут быть "общими" или "частными". Чтобы запретить доступ к категории: ';
  $lang['help_access'][1] = '1. Изменить информацию о категории (в разделе "категории" панели администратора), и сделать её "частной".';
  $lang['help_access'][2] = '2. На странице разрешений (для группы или пользователя) можно разрешить доступ или нет к частным категориям.';
  $lang['permuser_only_private'] = 'Будут показаны только личные категории';
  $lang['waiting_update'] = 'Validated pictures will be displayed only once pictures database updated';
  $lang['conf_upload_available_info'] = 'авторизация загрузки изображений пользователями в категории вебсайта (не на удаленном сервере). Этот параметр необходим, чтобы разрешить обновление категорий (по умолчанию все категории "необновляемые").';
  $lang['install_help'] = 'Нужна помощь ? Задайте Ваш вопрос на <a href="http://forum.phpwebgallery.net">PhpWebGallery форуме</a>.';
  $lang['install_warning'] = 'Файл "admin/install.php" еще существует. Пожалуйста удалите его с сервера, в интересах безопасности.';
  // new or modified in release 1.3.1
  $lang['cat_unknown_id'] = 'Этой категории нет в базе данных';
}
?>