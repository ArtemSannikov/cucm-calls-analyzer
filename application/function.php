<?php

/**
 * Получаем номера телефонов и производим замену символов '?' и '*', на '%' и '_'
 * Создаём поиск по маске
**/

	function phone_mask() {

		// Получаем данные с полей 'in' и 'out'
		$phone_in = $_POST['phone_in'];
		$phone_out = $_POST['phone_out'];

		// Создаём массивы для последующей замены
		$mask_1 = array('*','?');
		$mask_2 = array('%','_');

		// Замена символов в поле $phone_in
		if(strpos($phone_in, '*') || strpos($phone_in, '?')){
			$mask_phone_in = str_replace($mask_1, $mask_2, $phone_in);
		}else{
			$mask_phone_in = $phone_in;
		}

		// Замена символов в поле $phone_out
		if(strpos($phone_out, '*') || strpos($phone_out, '?')){
			$mask_phone_out = str_replace($mask_1, $mask_2, $phone_out);
		}else{
			$mask_phone_out = $phone_out;
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

			echo $GLOBALS['begin_time'].'<br>'; // From date
			echo $GLOBALS['end_time'].'<br>'; // To date

		}
		# Статистика в виде текста (данные берутся с формы)
		elseif(isset($_POST['text'])){

		}

		# Длительность разговоров
		elseif(isset($_POST['duration_of_calls'])){

			echo '<h3>Длительность разговоров</h3>
			      <hr/>';

			/**
			 * Функция для преобразования времени из UNIX (EPOCH) в вид ЧЧ:ММ:СС
			**/

				function Convert_Epoch_To_Human($value_unix_date){
					$h = floor($value_unix_date / 3600);
					$m = floor(($value_unix_date - $h * 3600) / 60);
					$s = floor($value_unix_date - $h * 3600 - $m * 60);
					echo $h.':'.$m.':'.$s;
				}

			/**
			 * MySQL запросы
			**/

				# Количество входящих вызовов (*???????)
				$Count_Incoming_Call = mysql_query('SELECT SUM(`duration`) FROM `cdr` WHERE `callingPartyNumber` LIKE \'%_______\' AND `dateTimeOrigination` >=' .$GLOBALS['begin_time']. ' AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);
			
					$Result_Count_Incoming_Call = mysql_result($Count_Incoming_Call, 0);

					echo '<p><strong>Количество входящих</strong>: ';
						Convert_Epoch_To_Human($Result_Count_Incoming_Call);
					echo ' (ЧЧ:ММ:СС)</p>';

				# Количество исходящих город (*343??????? + ??????? + *80????????)

				$Count_Outgoing_Call_City_P1 = mysql_query('SELECT SUM(`duration`) FROM `cdr` WHERE `originalCalledPartyNumber` LIKE \'%343_______\' AND `dateTimeOrigination` >=' .$GLOBALS['begin_time']. ' AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);

						$Result_COCC_P1 = mysql_result($Count_Outgoing_Call_City_P1, 0);

				$Count_Outgoing_Call_City_P2 = mysql_query('SELECT SUM(`duration`) FROM `cdr` WHERE `originalCalledPartyNumber` LIKE \'_______\' AND `dateTimeOrigination` >=' .$GLOBALS['begin_time']. ' AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);
					
						$Result_COCC_P2 = mysql_result($Count_Outgoing_Call_City_P2, 0);

				$Count_Outgoing_Call_City_P3 = mysql_query('SELECT SUM(`duration`) FROM `cdr` WHERE `originalCalledPartyNumber` LIKE \'%80________\' AND `dateTimeOrigination` >=' .$GLOBALS['begin_time']. ' AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);
					
						$Result_COCC_P3 = mysql_result($Count_Outgoing_Call_City_P3, 0);

				$Result_Count_Outgoing_Call_City_P4 = $Result_COCC_P1 + $Result_COCC_P2 + $Result_COCC_P3;		

					echo '<p><strong>Количество исходящих город</strong>: ';
						Convert_Epoch_To_Human($Result_Count_Outgoing_Call_City_P4);
					echo ' (ЧЧ:ММ:СС)</p>';

				# Количество исходящих на сотовые телефоны (*9?????????)

				$Count_Outgoing_Call_Smartphone = mysql_query('SELECT SUM(`duration`) FROM `cdr` WHERE `originalCalledPartyNumber` LIKE \'%9_________\' AND `dateTimeOrigination` >=' .$GLOBALS['begin_time']. ' AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);

					$Result_COCS = mysql_result($Count_Outgoing_Call_Smartphone, 0);

					echo '<p><strong>Количество исходящих на сотовые телефоны</strong>: ';
						Convert_Epoch_To_Human($Result_COCS);
					echo ' (ЧЧ:ММ:СС)</p>';


				# Количество исходящих межгород (??????????? - *9????????? - *343??????? - *80????????)

				$Count_Outgoing_Call_Intercity_P1 = mysql_query('SELECT SUM(`duration`) FROM `cdr` WHERE `originalCalledPartyNumber` LIKE \'___________\' AND `dateTimeOrigination` >=' .$GLOBALS['begin_time']. ' AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);

					$Result_COCI_P1 = mysql_result($Count_Outgoing_Call_Intercity_P1, 0);

				$Result_COCI_P2 = $Result_COCI_P1 - $Result_COCS - $Result_COCC_P1 - $Result_COCC_P3;

				echo '<p><strong>Количество исходящих межгород</strong>: ';
					Convert_Epoch_To_Human($Result_COCI_P2);
				echo ' (ЧЧ:ММ:СС)</p>';

				# Количество конференций (b* + 00* + 87???)

				$Count_Conference_P1 = mysql_query('SELECT SUM(`duration`) FROM `cdr` WHERE `finalCalledPartyNumber` LIKE \'b%\' AND `dateTimeOrigination` >=' .$GLOBALS['begin_time']. ' AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);

					$Result_CC_P1 = mysql_result($Count_Conference_P1, 0);

				$Count_Conference_P2 = mysql_query('SELECT SUM(`duration`) FROM `cdr` WHERE `finalCalledPartyNumber` LIKE \'00%\' AND `dateTimeOrigination` >=' .$GLOBALS['begin_time']. ' AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);

					$Result_CC_P2 = mysql_result($Count_Conference_P2, 0);

				$Count_Conference_P3 = mysql_query('SELECT SUM(`duration`) FROM `cdr` WHERE `finalCalledPartyNumber` LIKE \'87___\' AND `dateTimeOrigination` >=' .$GLOBALS['begin_time']. ' AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);

					$Result_CC_P3 = mysql_result($Count_Conference_P3, 0);

				$Result_CC_P4 = $Result_CC_P1 + $Result_CC_P2 + $Result_CC_P3;

				echo '<p><strong>Количество конференций</strong>: ';
					Convert_Epoch_To_Human($Result_CC_P4);
				echo ' (ЧЧ:ММ:СС)</p>';

				# Количество внутренних звонков (????? + ????? + b* + 00*)

				$Count_Internal_Calls_P1 = mysql_query('SELECT SUM(`duration`) FROM `cdr` WHERE (`callingPartyNumber` LIKE \'_____\') AND (`finalCalledPartyNumber` LIKE \'_____\') AND `dateTimeOrigination` >=' .$GLOBALS['begin_time']. ' AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);

					$Result_CIC_P1 = mysql_result($Count_Internal_Calls_P1, 0);

				$Result_CIC_P2 = $Result_CIC_P1 + $Result_CC_P1 + $Result_CC_P2;

				echo '<p><strong>Количество внутренних звонков</strong>: ';
					Convert_Epoch_To_Human($Result_CIC_P2);
				echo ' (ЧЧ:ММ:СС)</p>';

				# Количество видеозвонков

				$Count_Video_Calls_P1 = mysql_query('SELECT SUM(`duration`) FROM `cdr` WHERE `destVideoCap_Codec` > 0 AND `dateTimeOrigination` >=' .$GLOBALS['begin_time']. ' AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);

					$Result_VC_P1 = mysql_result($Count_Video_Calls_P1, 0);

				echo '<p><strong>Количество видеозвонков</strong>: ';
					Convert_Epoch_To_Human($Result_VC_P1);
				echo ' (ЧЧ:ММ:СС)</p>';
		}

		# Статистика за текущий месяц
		elseif(isset($_POST['current_month'])){

			//Первый день текущего месяца, время 00:00 (преобразуем в UNIX формат)
			$get_first_day = date("Y-m-01")."T00:00";
				$prepare_first_day = new DateTime($get_first_day);
					$first_day = $prepare_first_day->getTimestamp();

			//Текущий день, время 00:00 (преобразуем в UNIX формат)
			$get_current_day = date("Y-m-d")."T00:00";
				$prepare_current_day = new DateTime($get_current_day);
					$current_day = $prepare_current_day->getTimestamp();

			// Общая длительность телефонных звонков
			$query_duration_calls = mysql_query('SELECT SUM(`duration`) 
										  FROM `cdr` 
										  WHERE `dateTimeOrigination` >=' . $first_day . ' AND `dateTimeDisconnect` <= ' . $current_day);

				$duration_calls = mysql_result($query_duration_calls, 0); //Выводим данные из базы в том формате, в котором они находятся в базе данных

				$h = floor($duration_calls / 3600);
				$m = floor(($duration_calls - $h * 3600) / 60);
				$s = floor($duration_calls - $h * 3600 - $m * 60);

			// Общее количество звонков
			$query_count_calls = mysql_query('SELECT COUNT(`id`) 
											  FROM `cdr` 
											  WHERE `dateTimeOrigination` >=' . $first_day . ' AND `dateTimeDisconnect` <= ' . $current_day);
					
				$count_calls = mysql_result($query_count_calls, 0);

			// Состоявшиеся звонки (ненулевые звонки)
			$query_not_zero_calls = mysql_query('SELECT COUNT(`duration`) 
												 FROM `cdr` 
												 WHERE `duration` > 0 AND `dateTimeOrigination` >=' . $first_day . ' AND `dateTimeDisconnect` <= ' . $current_day);
					
				$not_zero_calls = mysql_result($query_not_zero_calls, 0);

			// Несостоявшиеся звонки (нулевые звонки)
			$query_zero_calls = mysql_query('SELECT COUNT(`duration`) 
									  FROM `cdr` 
									  WHERE `duration` = 0 AND `dateTimeOrigination` >=' . $first_day . ' AND `dateTimeDisconnect` <= ' . $current_day);
					
				$zero_calls = mysql_result($query_zero_calls, 0);
				
				#Выводим результаты запроса
				echo '<h3>Статистика за текущий месяц</h3>
					  <hr/>
					  <p>Всего звонков: <strong>'.$count_calls.'</strong>, продолжительностью: <strong>'.$h.':'.$m.':'.$s.'</strong>.</p>
					  <p>Состоявшиеся звонки: <strong>'.$not_zero_calls.'</strong>.</p>
					  <p>Несостоявшиеся звонки: <strong>'.$zero_calls.'</strong>.</p>';
		}

		# Статистика за предыдущий месяц
		elseif(isset($_POST['previous_month'])){

			//Первый день прошлого месяца, время 00:00 (преобразуем в UNIX формат)
			$get_previous_month = date("Y-m-01", strtotime("-1 month"))."T00:00";
				$prepare_previous_month = new DateTime($get_previous_month);
					$previous_month = $prepare_previous_month->getTimestamp();

			//Первый день текущего месяца, время 00:00 (преобразуем в UNIX формат)
			$get_current_month = date("Y-m-01")."T00:00";
				$prepare_current_month = new DateTime($get_current_month);
					$current_month = $prepare_current_month->getTimestamp();

			// Общая длительность телефонных звонков
			$query_duration_calls = mysql_query('SELECT SUM(`duration`)
									FROM `cdr` 
									WHERE `dateTimeOrigination` >=' . $previous_month . ' AND `dateTimeDisconnect` <= ' . $current_month);

				$duration_calls = mysql_result($query_duration_calls, 0);

				$h = floor($duration_calls / 3600);
				$m = floor(($duration_calls - $h * 3600) / 60);
				$s = floor($duration_calls - $h * 3600 - $m * 60);

			// Общее количество звонков
			$query_count_calls = mysql_query('SELECT COUNT(`id`)
									FROM `cdr` 
									WHERE `dateTimeOrigination` >=' . $previous_month . ' AND `dateTimeDisconnect` <= ' . $current_month);
					
				$count_calls = mysql_result($query_count_calls, 0);

			 // Состоявшиеся звонки (ненулевые звонки)
			$query_not_zero_calls = mysql_query('SELECT COUNT(`duration`) 
										 FROM `cdr` 
										 WHERE `duration` > 0 AND `dateTimeOrigination` >=' . $previous_month . ' AND `dateTimeDisconnect` <= ' . $current_month);
					
				$not_zero_calls = mysql_result($query_not_zero_calls, 0);

			// Несостоявшиеся звонки (нулевые звонки)
			$query_zero_calls = mysql_query('SELECT COUNT(`duration`) 
									  FROM `cdr` 
									  WHERE `duration` = 0 AND `dateTimeOrigination` >=' . $previous_month . ' AND `dateTimeDisconnect` <= ' . $current_month);
					
				$zero_calls = mysql_result($query_zero_calls, 0);
				
				#Выводим результаты запроса
				echo '<h3>Статистика за предыдущий месяц</h3>
					  <hr/>
					  <p>Всего звонков: <strong>'.$count_calls.'</strong>, продолжительностью: <strong>'.$h.':'.$m.':'.$s.'</strong>.</p>
				      <p>Состоявшиеся звонки: <strong>'.$not_zero_calls.'</strong>.</p>
				      <p>Несостоявшиеся звонки: <strong>'.$zero_calls.'</strong>.</p>';
		}

		# Неактивные номера (номера телефонов, с которых не совершались звонки 90 дней)
		elseif(isset($_POST['inactive_numbers'])){

			// Получаем текущую дату ГГГГ-ММ-ДД, вычитаем из неё 90 дней
			$getDate = mysql_query('SELECT DATE_ADD(NOW(), INTERVAL -90 day)');
				$get_dateResult = mysql_result($getDate, 0);
			
			// Переводим полученную дату в UNIX (EPOCH) формат
			$prepare_unixDate = new DateTime($get_dateResult);
				$convert_unixDate = $prepare_unixDate->getTimestamp();

			// Выполняем запрос к базе данных

			/*$mysql_query = 
			"SELECT КТО_ЗВОНИЛ, MAX(ДАТА_ЗВОНКА)
			FROM НАЗВАНИЕ_ТАБЛИЦЫ 
			GROUP BY КТО_ЗВОНИЛ 
			HAVING MAX(ДАТА_ЗВОНКА)< DATEADD(day, -90, GETDATE())";*/

			$mysql_query = mysql_query("SELECT `id`, `origDeviceName`,`callingPartyNumber`, MAX(`dateTimeOrigination`) 
										FROM `cdr` 
										WHERE LENGTH(`callingPartyNumber`) = 5 
										GROUP BY `callingPartyNumber` 
										HAVING MAX(`dateTimeOrigination`) < ".$convert_unixDate." AND `origDeviceName` NOT LIKE 'CCX%' AND `origDeviceName` NOT LIKE '9%' AND `origDeviceName` NOT LIKE '8%'");

			echo '<h3>Неактивные номера</h3>
				  <p>Выводятся все номера, чья активность была более чем 90 дней назад.</p>
				  <hr/>';

			echo '<table width="100%">
					<tr>
						<td class="head-cell"><strong>ID rows</strong></td>
						<td class="head-cell"><strong>callingPartyNumber</strong></td>
						<td class="head-cell"><strong>origDeviceName</strong></td>
				 	</tr>';

			while($row = mysql_fetch_assoc($mysql_query)){
					echo '<tr>';

							echo '<td>'.$row['id'].'</td>';

							echo '<td>'.$row['callingPartyNumber'].'</td>';

							echo '<td>'.$row['origDeviceName'].'</td>';

					echo '</tr>';
			}

			echo '</table>';

		}

		# Если не нажата ни одна из кнопок в форме
		else{
			echo
				'<h3>CUCM calls analyzer</h3>
				 <p>Телефонная станция Cisco Unified Communications Manager является системой обработки вызовов на базе программного обеспечения.</p>
				 <hr/>
				 <p><strong>Важный момент</strong>:</p>
				 <p>Кнопки <span class="depends_blue">В виде таблицы</span> - <span class="depends_blue">В виде текста</span> - <span class="depends_blue">Длительность разговоров</span> зависят от данных, которые введены в форму</p>
				 <p>Кнопки <span class="depends_gray">Текущий месяц</span> - <span class="depends_gray">Предыдущий месяц</span> - <span class="depends_gray">Неактивные номера</span> не зависят от данных в форме</p>';
		}

	};

?>