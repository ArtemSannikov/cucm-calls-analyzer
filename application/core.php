<?php

/**
 * Обработка номеров в полях: 'Входящий' и 'Исходящий'
 * Функции производят замену символов '*', ',' на '%', '_'
**/

	# Поле 'Входящий'
	 function phone_in() {

		// Получаем данные с поля
		$post_phone_in = $_POST['phone_in'];

		// Создаём массив для последующей замены символов в строке
		$symbol_array = array(
			'*' => '%',
			'?' => '_'
		);

		// Создаём глобальную переменную
		global $phone_in;

		// Замена символов в поле $phone_in
		if(!empty($post_phone_in)){

			// Выводим номер, который ввёл пользователь
			// с символами '?' и '*' в поле 'Входящий'
			echo $post_phone_in;

			// Производим замену символов,
			// а затем вызываем переменную через массив $GLOBALS['phone_in'] для дальнейшего SQL запроса
			$phone_in = strtr($post_phone_in, $symbol_array);// strtr(строка_поиска, массив_для_замены_символов)
		}

	 }

	 # Поле 'Исходящий'
	 function phone_out() {

		$post_phone_out = $_POST['phone_out'];

		$symbol_array = array(
			'*' => '%',
			'?' => '_'
		);

		global $phone_out;

		if(!empty($post_phone_out)){

			echo $post_phone_out;

			$phone_out = strtr($post_phone_out, $symbol_array);
		}

	 }

/**
 * Работа с датой, поля 'From' и 'To'
**/

	# Поле 'From', текущая дата
	function begintime(){

		// Получаем дату с поля 'From'
		$post_begin_time = $_POST['begin_time'];

		// Если пользователь не установил нужную дату, то по умолчанию выставляем текущую
		if(!$post_begin_time){
			echo date("Y-m-d")."T00:00";
		}else{
			echo $post_begin_time;
		}

		// Выполняем преобразование полученной даты в UNIX формат (база содержит даты только в UNIX формате)
		// Это нужно для дальнейшего запроса SQL
		$prepare_begin_time = new DateTime($post_begin_time);
			global $begin_time; // Определяем глобальную переменную
				$begin_time = $prepare_begin_time->getTimestamp();

	}

	# Поле 'To', завтрашняя дата
	function endtime(){

		$post_end_time = $_POST['end_time'];

		if(!$post_end_time){
			echo date("Y-m-d", strtotime("+1 day"))."T00:00";
		}else{
			echo $post_end_time;
		}

		$prepare_end_time = new DateTime($post_end_time);
			global $end_time;
				$end_time = $prepare_end_time->getTimestamp();

	}

/**
 * Обрабатываем форму со страницы '../index.php', а затем выполняем запрос
**/

	function get_data_calls() {

		# Статистика в виде таблицы (данные берутся с формы)
		if(isset($_POST['as_a_table'])){
			require_once('functions/view-table.php');
		}
		# Статистика в виде текста (данные берутся с формы)
		elseif(isset($_POST['as_a_text'])){
			require_once('functions/view-text.php');
		}

		# Длительность разговоров
		elseif(isset($_POST['duration_of_calls'])){
			require_once('functions/duration-calls.php');
		}

		# Статистика за текущий месяц
		elseif(isset($_POST['current_month'])){
			require_once('functions/current-month.php');
		}

		# Статистика за предыдущий месяц
		elseif(isset($_POST['previous_month'])){
			require_once('functions/previous-month.php');
		}

		# Неактивные номера (номера телефонов, с которых не совершались звонки 90 дней)
		elseif(isset($_POST['inactive_numbers'])){
			require_once('functions/inactive-numbers.php');
		}

		# Если не нажата ни одна из кнопок в форме
		else{
			echo
				'<h3>CUCM calls analyzer</h3>
				 <p>Телефонная станция Cisco Unified Communications Manager является системой обработки вызовов на базе программного обеспечения.</p>
				 <hr/>
				 <p><strong>Важный момент</strong>:</p>
				 <p>Кнопки <span class="depends_blue">В виде таблицы</span> - <span class="depends_blue">В виде текста</span> зависят от всех данных, которые введены в форму</p>
				 <p>Кнопка <span class="depends_green">Длительность разговоров</span> зависит от введённой даты в поле "От" и "До"</p>
				 <p>Кнопки <span class="depends_gray">Текущий месяц</span> - <span class="depends_gray">Предыдущий месяц</span> - <span class="depends_gray">Неактивные номера</span> не зависят от данных в форме</p>';
		}

	};

?>