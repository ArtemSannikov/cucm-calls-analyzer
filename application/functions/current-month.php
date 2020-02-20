<?php

			//Первый день текущего месяца, время 00:00
			$first_day = date("Y-m-01")."T00:00";

			//Текущий день, время 00:00
			$current_day = date("Y-m-d")."T00:00";

			// Общая длительность телефонных звонков
			$query_duration_calls = mysql_query('SELECT SUM(`duration`) 
										  FROM '.DB_TABLE.' 
										  WHERE `dateTimeOrigination` >= UNIX_TIMESTAMP("'.$first_day.'") 
										  		AND `dateTimeDisconnect` <= UNIX_TIMESTAMP("'.$current_day.'")');

				$duration_calls = mysql_result($query_duration_calls, 0); //Выводим данные из базы в том формате, в котором они находятся в базе данных

				$h = floor($duration_calls / 3600);
				$m = floor(($duration_calls - $h * 3600) / 60);
				$s = floor($duration_calls - $h * 3600 - $m * 60);

			// Общее количество звонков
			$query_count_calls = mysql_query('SELECT COUNT(`id`) 
											  FROM '.DB_TABLE.' 
											  WHERE `dateTimeOrigination` >= UNIX_TIMESTAMP("'.$first_day.'") 
											  		AND `dateTimeDisconnect` <= UNIX_TIMESTAMP("'.$current_day.'")');
					
				$count_calls = mysql_result($query_count_calls, 0);

			// Состоявшиеся звонки (ненулевые звонки)
			$query_not_zero_calls = mysql_query('SELECT COUNT(`duration`) 
												 FROM '.DB_TABLE.' 
												 WHERE `duration` > 0 
												 	   AND `dateTimeOrigination` >= UNIX_TIMESTAMP("'.$first_day.'") 
												 	   AND `dateTimeDisconnect` <= UNIX_TIMESTAMP("'.$current_day.'")');
					
				$not_zero_calls = mysql_result($query_not_zero_calls, 0);

			// Несостоявшиеся звонки (нулевые звонки)
			$query_zero_calls = mysql_query('SELECT COUNT(`duration`) 
									 		 FROM '.DB_TABLE.' 
									 		 WHERE `duration` = 0 AND 
									 		 	   `dateTimeOrigination` >= UNIX_TIMESTAMP("'.$first_day.'") 
									 		 	   AND `dateTimeDisconnect` <= UNIX_TIMESTAMP("'.$current_day.'")');
					
				$zero_calls = mysql_result($query_zero_calls, 0);
				
				#Выводим результаты запроса
				echo '<h3>Статистика за текущий месяц</h3>
					  <hr/>
					  <p>Всего звонков: <strong>'.$count_calls.'</strong>, продолжительностью: <strong>'.$h.':'.$m.':'.$s.'</strong></p>
					  <p>Состоявшиеся звонки: <strong>'.$not_zero_calls.'</strong></p>
					  <p>Несостоявшиеся звонки: <strong>'.$zero_calls.'</strong></p>';

?>
