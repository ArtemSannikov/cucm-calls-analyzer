<?php

/**
 * Подключение к базе данных
**/

	# Запуск сессии
	session_start();

	# Данные для подключения
	define('DB_SERVER', 'localhost'); // Сервер
	define('DB_USER', ''); // Пользователь БД
	define('DB_PASSWORD', ''); // Пароль БД
	define('DB_NAME', ''); // Имя БД

	# С какой таблицей будем работать
	define('CDR_TABLE', '');

	# Выполняем подключение
	$mysql_connection = mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD);

		# Выбираем базу для работы
		mysql_select_db(DB_NAME, $mysql_connection);

		# Устанавливаем кодировку для базы данных
		mysql_query('SET NAMES \'utf8\'');

	# Действие, если подключение не удалось
	if(!$mysql_connection){
		die('Ошибка соединения: ' . mysql_error());
	}

	# mysql_close($mysql_connection);
	
?>