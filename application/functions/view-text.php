<?php

# Поле 'Входящий' - заполнено, а 'Исходящий' - не заполнено
if(!empty($_POST['phone_in']) && empty($_POST['phone_out'])){

	// Длительность звонков
	$DurationCalls = mysql_query('SELECT SUM(`duration`) 
								  FROM '.DB_TABLE.' 
								  WHERE `callingPartyNumber`LIKE "'.$GLOBALS['phone_in'].'" 
								  		AND `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
								  		AND `dateTimeDisconnect` <= ' . $GLOBALS['end_time']);

		$ResultDurationCalls = mysql_result($DurationCalls, 0); //Выводим данные из базы в том формате, в котором они находятся в базе данных

		$h = floor($ResultDurationCalls / 3600);
		$m = floor(($ResultDurationCalls - $h * 3600) / 60);
		$s = floor($ResultDurationCalls - $h * 3600 - $m * 60);

	// Ненулевые звонки
	$NotZeroCalls = mysql_query('SELECT COUNT(`duration`) 
								 FROM '.DB_TABLE.' 
								 WHERE `callingPartyNumber`LIKE "'.$GLOBALS['phone_in'].'" 
								 	   AND `duration` > 0 
								 	   AND `dateTimeOrigination` >= '. $GLOBALS['begin_time'].' 
								 	   AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);
					
		$ResultNotZeroCalls = mysql_result($NotZeroCalls, 0);


	// Подсчёт количества звонков
	$CountCalls = mysql_query('SELECT COUNT(`id`) 
							   FROM '.DB_TABLE.' 
							   WHERE `callingPartyNumber`LIKE "'.$GLOBALS['phone_in'].'" 
							   		 AND `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
							   		 AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);
					
		$ResultCountCalls = mysql_result($CountCalls, 0);
				
	// Выводим результаты запроса
	echo '<p><strong>#Статистика по заданному фильтру</strong></p>';
	echo '<p>Всего звонков: <strong>'.$ResultCountCalls.'</strong>, продолжительностью: <strong>'.$h.':'.$m.':'.$s.'</strong></p>';
	echo '<p>Состоявшиеся звонки: <strong>'.$ResultNotZeroCalls.'</strong></p>';

	// Нулевые звонки (выводятся в том случае, если отмечен чекбокс 'Несостоявшиеся звонки')
	if(isset($_POST['show_zero_calls'])){

		$ZeroCalls = mysql_query('SELECT COUNT(`duration`) 
								  FROM '.DB_TABLE.' 
								  WHERE `callingPartyNumber`LIKE "'.$GLOBALS['phone_in'].'" 
								  		AND `duration` = 0 
								  		AND `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
								  		AND `dateTimeDisconnect` <= ' . $GLOBALS['end_time']);
						
			$ResultZeroCalls = mysql_result($ZeroCalls, 0);

			echo '<p>Несостоявшиеся звонки: <strong>'.$ResultZeroCalls.'</strong></p>';

	}


}

# Поле 'Входящий' - не заполнено, а 'Исходящий' - заполнено
elseif(empty($_POST['phone_in']) && !empty($_POST['phone_out'])){

	// Длительность звонков
	$DurationCalls = mysql_query('SELECT SUM(`duration`) 
								  FROM '.DB_TABLE.' 
								  WHERE `finalCalledPartyNumber` LIKE "'.$GLOBALS['phone_out'].'" 
								  		AND `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
								  		AND `dateTimeDisconnect` <= ' . $GLOBALS['end_time']);

		$ResultDurationCalls = mysql_result($DurationCalls, 0); //Выводим данные из базы в том формате, в котором они находятся в базе данных

		$h = floor($ResultDurationCalls / 3600);
		$m = floor(($ResultDurationCalls - $h * 3600) / 60);
		$s = floor($ResultDurationCalls - $h * 3600 - $m * 60);

	// Ненулевые звонки
	$NotZeroCalls = mysql_query('SELECT COUNT(`duration`) 
								 FROM '.DB_TABLE.' 
								 WHERE `finalCalledPartyNumber` LIKE "'.$GLOBALS['phone_out'].'" 
								 	   AND `duration` > 0 
								 	   AND `dateTimeOrigination` >= '. $GLOBALS['begin_time'].' 
								 	   AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);
					
		$ResultNotZeroCalls = mysql_result($NotZeroCalls, 0);

	// Подсчёт количества звонков
	$CountCalls = mysql_query('SELECT COUNT(`id`) 
							   FROM '.DB_TABLE.' 
							   WHERE `finalCalledPartyNumber` LIKE "'.$GLOBALS['phone_out'].'" 
							   		 AND `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
							   		 AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);
					
		$ResultCountCalls = mysql_result($CountCalls, 0);
				
	// Выводим результаты запроса
	echo '<p><strong>#Статистика по заданному фильтру</strong></p>';
	echo '<p>Всего звонков: <strong>'.$ResultCountCalls.'</strong>, продолжительностью: <strong>'.$h.':'.$m.':'.$s.'</strong></p>';
	echo '<p>Состоявшиеся звонки: <strong>'.$ResultNotZeroCalls.'</strong></p>';

	// Нулевые звонки (выводятся в том случае, если отмечен чекбокс 'Несостоявшиеся звонки')
	if(isset($_POST['show_zero_calls'])){

		$ZeroCalls = mysql_query('SELECT COUNT(`duration`) 
								  FROM '.DB_TABLE.' 
								  WHERE `finalCalledPartyNumber` LIKE "'.$GLOBALS['phone_out'].'" 
								  		AND `duration` = 0 
								  		AND `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
								  		AND `dateTimeDisconnect` <= ' . $GLOBALS['end_time']);
						
			$ResultZeroCalls = mysql_result($ZeroCalls, 0);

		echo '<p>Несостоявшиеся звонки: <strong>'.$ResultZeroCalls.'</strong></p>';

	}

}

# Поля 'Входящий' и 'Исходящий'- заполнены
elseif(!empty($_POST['phone_in']) && !empty($_POST['phone_out'])){

	// Длительность звонков
	$DurationCalls = mysql_query('SELECT SUM(`duration`) 
								  FROM '.DB_TABLE.' 
								  WHERE `callingPartyNumber` LIKE "'.$GLOBALS['phone_in'].'" 
								  		AND `finalCalledPartyNumber` LIKE "'.$GLOBALS['phone_out'].'" 
								  		AND `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
								  		AND `dateTimeDisconnect` <= ' . $GLOBALS['end_time']);

		$ResultDurationCalls = mysql_result($DurationCalls, 0); //Выводим данные из базы в том формате, в котором они находятся в базе данных

		$h = floor($ResultDurationCalls / 3600);
		$m = floor(($ResultDurationCalls - $h * 3600) / 60);
		$s = floor($ResultDurationCalls - $h * 3600 - $m * 60);

	// Ненулевые звонки
	$NotZeroCalls = mysql_query('SELECT COUNT(`duration`) 
								 FROM '.DB_TABLE.' 
								 WHERE `callingPartyNumber` LIKE "'.$GLOBALS['phone_in'].'" 
								 		AND `finalCalledPartyNumber` LIKE "'.$GLOBALS['phone_out'].'" 
								 	    AND `duration` > 0 
								 	    AND `dateTimeOrigination` >= '. $GLOBALS['begin_time'].' 
								 	    AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);
					
		$ResultNotZeroCalls = mysql_result($NotZeroCalls, 0);

	// Подсчёт количества звонков
	$CountCalls = mysql_query('SELECT COUNT(`id`) 
							   FROM '.DB_TABLE.' 
							   WHERE `callingPartyNumber` LIKE "'.$GLOBALS['phone_in'].'" 
							   		AND `finalCalledPartyNumber` LIKE "'.$GLOBALS['phone_out'].'" 
							   		 AND `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
							   		 AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);
					
		$ResultCountCalls = mysql_result($CountCalls, 0);
				
	// Выводим результаты запроса
	echo '<p><strong>#Статистика по заданному фильтру</strong></p>';
	echo '<p>Всего звонков: <strong>'.$ResultCountCalls.'</strong>, продолжительностью: <strong>'.$h.':'.$m.':'.$s.'</strong></p>';
	echo '<p>Состоявшиеся звонки: <strong>'.$ResultNotZeroCalls.'</strong></p>';

	// Нулевые звонки (выводятся в том случае, если отмечен чекбокс 'Несостоявшиеся звонки')
	if(isset($_POST['show_zero_calls'])){

		$ZeroCalls = mysql_query('SELECT COUNT(`duration`) 
								  FROM '.DB_TABLE.' 
								  WHERE `callingPartyNumber` LIKE "'.$GLOBALS['phone_in'].'" 
								  		AND `finalCalledPartyNumber` LIKE "'.$GLOBALS['phone_out'].'" 
								  		AND `duration` = 0 
								  		AND `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
								  		AND `dateTimeDisconnect` <= ' . $GLOBALS['end_time']);
						
			$ResultZeroCalls = mysql_result($ZeroCalls, 0);

		echo '<p>Несостоявшиеся звонки: <strong>'.$ResultZeroCalls.'</strong></p>';

	}

}

# Поля 'Входящий' и 'Исходящий'- не заполнены
elseif(empty($_POST['phone_in']) && empty($_POST['phone_out'])){

	// Длительность звонков
	$DurationCalls = mysql_query('SELECT SUM(`duration`) 
								  FROM '.DB_TABLE.' 
								  WHERE `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
								  		AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);

		$ResultDurationCalls = mysql_result($DurationCalls, 0); //Выводим данные из базы в том формате, в котором они находятся в базе данных

		$h = floor($ResultDurationCalls / 3600);
		$m = floor(($ResultDurationCalls - $h * 3600) / 60);
		$s = floor($ResultDurationCalls - $h * 3600 - $m * 60);

	// Ненулевые звонки
	$NotZeroCalls = mysql_query('SELECT COUNT(`duration`) 
								 FROM '.DB_TABLE.' 
								 WHERE `duration` > 0 
									   AND `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
									   AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);
					
		$ResultNotZeroCalls = mysql_result($NotZeroCalls, 0);

	// Подсчёт количества звонков
	$CountCalls = mysql_query('SELECT COUNT(`id`)
							   FROM '.DB_TABLE.' 
							   WHERE `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
							   		 AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);
					
		$ResultCountCalls = mysql_result($CountCalls, 0);
				
	// Выводим результаты запроса
	echo '<p><strong>#Статистика по заданному фильтру</strong></p>';
	echo '<p>Всего звонков: <strong>'.$ResultCountCalls.'</strong>, продолжительностью: <strong>'.$h.':'.$m.':'.$s.'</strong></p>';
	echo '<p>Состоявшиеся звонки: <strong>'.$ResultNotZeroCalls.'</strong></p>';

	// Нулевые звонки (выводятся в том случае, если отмечен чекбокс 'Несостоявшиеся звонки')
	if(isset($_POST['show_zero_calls'])){

		$ZeroCalls = mysql_query('SELECT COUNT(`duration`) 
								  FROM '.DB_TABLE.' 
								  WHERE `duration` = 0 
								  		AND `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
								  		AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);
						
			$ResultZeroCalls = mysql_result($ZeroCalls, 0);

		echo '<p>Несостоявшиеся звонки: <strong>'.$ResultZeroCalls.'</strong></p>';

	}

}

?>