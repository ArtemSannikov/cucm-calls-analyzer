<?php
			//Первый день прошлого месяца, время 00:00 
			$previous_month = date("Y-m-01", strtotime("-1 month"))."T00:00";

			//Первый день текущего месяца, время 00:00
			$current_month = date("Y-m-01")."T00:00";

			// Общая длительность телефонных звонков
			$query_duration_calls = mysql_query('SELECT SUM(`duration`)
												 FROM '.DB_TABLE.' 
												 WHERE `dateTimeOrigination` >= UNIX_TIMESTAMP("'.$previous_month.'") 
														AND `dateTimeDisconnect` <= UNIX_TIMESTAMP("'.$current_month.'")');

				$duration_calls = mysql_result($query_duration_calls, 0);

				$h = floor($duration_calls / 3600);
				$m = floor(($duration_calls - $h * 3600) / 60);
				$s = floor($duration_calls - $h * 3600 - $m * 60);

			// Общее количество звонков
			$query_count_calls = mysql_query('SELECT COUNT(`id`)
											  FROM '.DB_TABLE.' 
											  WHERE `dateTimeOrigination` >= UNIX_TIMESTAMP("'.$previous_month.'") 
											  		AND `dateTimeDisconnect` <= UNIX_TIMESTAMP("'.$current_month.'")');
					
				$count_calls = mysql_result($query_count_calls, 0);

			 // Состоявшиеся звонки (ненулевые звонки)
			$query_not_zero_calls = mysql_query('SELECT COUNT(`duration`) 
												 FROM '.DB_TABLE.' 
												 WHERE `duration` > 0 
												 		AND `dateTimeOrigination` >= UNIX_TIMESTAMP("'.$previous_month.'") 
												 		AND `dateTimeDisconnect` <= UNIX_TIMESTAMP("'.$current_month.'")');
					
				$not_zero_calls = mysql_result($query_not_zero_calls, 0);

			// Несостоявшиеся звонки (нулевые звонки)
			$query_zero_calls = mysql_query('SELECT COUNT(`duration`) 
											 FROM '.DB_TABLE.' 
											 WHERE `duration` = 0 
											 	   AND `dateTimeOrigination` >= UNIX_TIMESTAMP("'.$previous_month.'") 
											 	   AND `dateTimeDisconnect` <= UNIX_TIMESTAMP("'.$current_month.'")');
					
				$zero_calls = mysql_result($query_zero_calls, 0);
				
				#Выводим результаты запроса
				echo '<h3>Статистика за предыдущий месяц</h3>
					  <hr/>
					  <p>Всего звонков: <strong>'.$count_calls.'</strong>, продолжительностью: <strong>'.$h.':'.$m.':'.$s.'</strong>.</p>
				      <p>Состоявшиеся звонки: <strong>'.$not_zero_calls.'</strong>.</p>
				      <p>Несостоявшиеся звонки: <strong>'.$zero_calls.'</strong>.</p>';
?>