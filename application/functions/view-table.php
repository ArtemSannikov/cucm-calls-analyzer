<?php

# Поле 'Входящий' - заполнено, а 'Исходящий' - не заполнено
if(!empty($_POST['phone_in']) && empty($_POST['phone_out'])){

	//Проверяем, отмечен ли чекбокс для показа нулевых звонков
	if(isset($_POST['show_zero_calls'])){
		//Если чек бок отмечен
		$CountCalls = mysql_query('SELECT `callingPartyNumber`,
										  `originalCalledPartyNumber`,
										  `finalCalledPartyNumber`,
										  `dateTimeOrigination`,
										  `dateTimeConnect`,
										  `dateTimeDisconnect`,
										  `duration` 
								   FROM '.DB_TABLE.' 
								   WHERE `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
										 AND `dateTimeDisconnect` <= '.$GLOBALS['end_time'].' 
										 AND `callingPartyNumber` LIKE "'.$GLOBALS['phone_in'].'"');
	}else{
		//Если чекбокс не отмечен
		$CountCalls = mysql_query('SELECT `callingPartyNumber`,
										  `originalCalledPartyNumber`,
										  `finalCalledPartyNumber`,
										  `dateTimeOrigination`,
										  `dateTimeConnect`,
										  `dateTimeDisconnect`,
										  `duration` 
								   FROM '.DB_TABLE.'  
								   WHERE `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
								   		 AND `dateTimeDisconnect` <= '.$GLOBALS['end_time'].' 
								   		 AND `callingPartyNumber` LIKE "'.$GLOBALS['phone_in'].'" 
								   		 AND `duration` > 0');
	}

	//Производим подсчёт длительности всех разговоров
	$DurationCalls = mysql_query('SELECT SUM(`duration`) 
								  FROM '.DB_TABLE.'  
								  WHERE `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
								  		AND `dateTimeDisconnect` <= '.$GLOBALS['end_time'].' 
								  		AND `callingPartyNumber` LIKE "'.$GLOBALS['phone_in'].'"');

		$ResultDurationCalls = mysql_result($DurationCalls, 0); //Выводим данные из базы в том формате, в котором они находятся в базе данных

		$h = floor($ResultDurationCalls / 3600);
		$m = floor(($ResultDurationCalls - $h * 3600) / 60);
		$s = floor($ResultDurationCalls - $h * 3600 - $m * 60);	
				
	echo '<table width="100%">
			<tr>
				<td class="head-cell">#</td>
				<td class="head-cell">Calling party number</td>
				<td class="head-cell">Called party number (orig.)</td>
				<td class="head-cell">Called party number (final)</td>
				<td class="head-cell">Origination time</td>
				<td class="head-cell">Connect time</td>
				<td class="head-cell">Disconnect time</td>
				<td class="head-cell">Duration</td>
			</tr>
	';

	while($row = mysql_fetch_array($CountCalls)){

		//Сводим данные в таблицу

		$RowCountCalls++; //Считаем количество звонков

		echo '<tr>';

			echo '<td>'.$RowCountCalls.'</td>';
			echo '<td style="text-align: right;">'.$row['callingPartyNumber'].'</td>';
			echo '<td style="text-align: right;">'.$row['originalCalledPartyNumber'].'</td>';
			echo '<td style="text-align: right;">'.$row['finalCalledPartyNumber'].'</td>';

			//Преобразование из UNIX timestamp в PHP DateTime
			echo '<td>'.date("Y-m-d H:i:s", substr($row['dateTimeOrigination'], 0, 10)).'</td>';

			//Преобразование из UNIX timestamp в PHP DateTime
			echo '<td>';
				$compareDate = date("Y-m-d H:i:s", substr($row['dateTimeConnect'], 0, 10));
				if($compareDate == '1970-01-01 05:00:00'){
					echo "";
				}else{
						echo $compareDate;
				}
			echo '</td>';

			//Преобразование из UNIX timestamp в PHP DateTime
			echo '<td>'.date("Y-m-d H:i:s", substr($row['dateTimeDisconnect'], 0, 10)).'</td>';

			//Преобразование длительности разговора
			echo '<td>'.date("i:s", $row['duration']).'</td>';

		echo '</tr>';
	}

	//Общее количество времени, потраченного на разговоры
	echo '<tr>
			<td colspan="7" style="text-align: right;font-weight: bold;">Total duration</td>
			<td>'.$h.':'.$m.':'.$s.'</td>
		</tr>';

	echo '</table>';

}

# Поле 'Входящий' - не заполнено, а 'Исходящий' - заполнено
elseif(empty($_POST['phone_in']) && !empty($_POST['phone_out'])){

	//Проверяем, отмечен ли чекбокс для показа нулевых звонков
	if(isset($_POST['show_zero_calls'])){
		//Если чек бок отмечен
		$CountCalls = mysql_query('SELECT `callingPartyNumber`,
										 `originalCalledPartyNumber`,
										 `finalCalledPartyNumber`,
										 `dateTimeOrigination`,
										 `dateTimeConnect`,
										 `dateTimeDisconnect`,
										 `duration` 
								  FROM '.DB_TABLE.' 
								  WHERE `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
								  		AND `dateTimeDisconnect` <= '.$GLOBALS['end_time'].' 
								  		AND `finalCalledPartyNumber` LIKE "'.$GLOBALS['phone_out'].'"');
	}else{
		//Если чекбокс не отмечен
		$CountCalls = mysql_query('SELECT `callingPartyNumber`,
										  `originalCalledPartyNumber`,
										  `finalCalledPartyNumber`,
										  `dateTimeOrigination`,
										  `dateTimeConnect`,
										  `dateTimeDisconnect`,
										  `duration` 
								   FROM '.DB_TABLE.' 
								   WHERE `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
										 AND `dateTimeDisconnect` <= '.$GLOBALS['end_time'].' 
										 AND `finalCalledPartyNumber` LIKE "'.$GLOBALS['phone_out'].'" 
										 AND `duration` > 0');
	}

	//Производим подсчёт длительности всех разговоров
	$DurationCalls = mysql_query('SELECT SUM(`duration`) 
								  FROM '.DB_TABLE.' 
								  WHERE `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
								  		AND `dateTimeDisconnect` <= '.$GLOBALS['end_time'].' 
								  		AND `finalCalledPartyNumber` LIKE "'.$GLOBALS['phone_out'].'"');

		$ResultDurationCalls = mysql_result($DurationCalls, 0); //Выводим данные из базы в том формате, в котором они находятся в базе данных

		$h = floor($ResultDurationCalls / 3600);
		$m = floor(($ResultDurationCalls - $h * 3600) / 60);
		$s = floor($ResultDurationCalls - $h * 3600 - $m * 60);	
				
	echo '<table width="100%">
			<tr>
				<td class="head-cell">#</td>
				<td class="head-cell">Calling party number</td>
				<td class="head-cell">Called party number (orig.)</td>
				<td class="head-cell">Called party number (final)</td>
				<td class="head-cell">Origination time</td>
				<td class="head-cell">Connect time</td>
				<td class="head-cell">Disconnect time</td>
				<td class="head-cell">Duration</td>
			</tr>';

	while($row = mysql_fetch_array($CountCalls)){

		//Сводим данные в таблицу

		$RowCountCalls++; //Считаем количество звонков

		echo '<tr>';

			echo '<td>'.$RowCountCalls.'</td>';

			echo '<td style="text-align: right;">'.$row['callingPartyNumber'].'</td>';

			echo '<td style="text-align: right;">'.$row['originalCalledPartyNumber'].'</td>';

			echo '<td style="text-align: right;">'.$row['finalCalledPartyNumber'].'</td>';

			//Преобразование из UNIX timestamp в PHP DateTime
			echo '<td>'.date("Y-m-d H:i:s", substr($row['dateTimeOrigination'], 0, 10)).'</td>';

			//Преобразование из UNIX timestamp в PHP DateTime
			echo '<td>';
				$compareDate = date("Y-m-d H:i:s", substr($row['dateTimeConnect'], 0, 10));
				if($compareDate == '1970-01-01 05:00:00'){
					echo "";
				}else{
					echo $compareDate;
				}
			echo '</td>';

			//Преобразование из UNIX timestamp в PHP DateTime
			echo '<td>'.date("Y-m-d H:i:s", substr($row['dateTimeDisconnect'], 0, 10)).'</td>';

			//Преобразование длительности разговора
			echo '<td>'.date("i:s", $row['duration']).'</td>';

		echo '</tr>';
	}

	//Общее количество времени, потраченного на разговоры
	echo '<tr>
			<td colspan="7" style="text-align: right;font-weight: bold;">Total duration</td>
			<td>'.$h.':'.$m.':'.$s.'</td>
		</tr>';

	echo '</table>';
}

# Поля 'Входящий' и 'Исходящий'- заполнены
elseif(!empty($_POST['phone_in']) && !empty($_POST['phone_out'])){

	//Проверяем, отмечен ли чекбокс для показа нулевых звонков
	if(isset($_POST['show_zero_calls'])){
		//Если чек бок отмечен
		$CountCalls = mysql_query('SELECT `callingPartyNumber`,
										  `originalCalledPartyNumber`,
										  `finalCalledPartyNumber`,
										  `dateTimeOrigination`,
										  `dateTimeConnect`,
										  `dateTimeDisconnect`,
										  `duration` 
								   FROM '.DB_TABLE.' 
								   WHERE `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
									   	 AND `dateTimeDisconnect` <= '.$GLOBALS['end_time'].' 
									   	 AND `callingPartyNumber` LIKE "'.$GLOBALS['phone_in'].'" 
									   	 AND `finalCalledPartyNumber` LIKE "'.$GLOBALS['phone_out'].'"');
	}else{
		//Если чекбокс не отмечен
		$CountCalls = mysql_query('SELECT `callingPartyNumber`,
										  `originalCalledPartyNumber`,
										  `finalCalledPartyNumber`,
										  `dateTimeOrigination`,
										  `dateTimeConnect`,
										  `dateTimeDisconnect`,
										  `duration` 
								   FROM '.DB_TABLE.' 
								   WHERE `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
										 AND `dateTimeDisconnect` <= '.$GLOBALS['end_time'].' 
										 AND `callingPartyNumber` LIKE "'.$GLOBALS['phone_in'].'" 
										 AND `finalCalledPartyNumber` LIKE "'.$GLOBALS['phone_out'].'" 
										 AND `duration` > 0');
	}

	//Производим подсчёт длительности всех разговоров
	$DurationCalls = mysql_query('SELECT SUM(`duration`) 
								  FROM '.DB_TABLE.' 
								  WHERE `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
										AND `dateTimeDisconnect` <= '.$GLOBALS['end_time'].' 
										AND `callingPartyNumber` LIKE "'.$GLOBALS['phone_in'].'" 
										AND `finalCalledPartyNumber` LIKE "'.$GLOBALS['phone_out'].'"');

	$ResultDurationCalls = mysql_result($DurationCalls, 0); //Выводим данные из базы в том формате, в котором они находятся в базе данных

		$h = floor($ResultDurationCalls / 3600);
		$m = floor(($ResultDurationCalls - $h * 3600) / 60);
		$s = floor($ResultDurationCalls - $h * 3600 - $m * 60);	
				
	echo '<table width="100%">
			<tr>
				<td class="head-cell">#</td>
				<td class="head-cell">Calling party number</td>
				<td class="head-cell">Called party number (orig.)</td>
				<td class="head-cell">Called party number (final)</td>
				<td class="head-cell">Origination time</td>
				<td class="head-cell">Connect time</td>
				<td class="head-cell">Disconnect time</td>
				<td class="head-cell">Duration</td>
			</tr>';

	while($row = mysql_fetch_array($CountCalls)){

		//Сводим данные в таблицу

		$RowCountCalls++; //Считаем количество звонков

		echo '<tr>';

			echo '<td>'.$RowCountCalls.'</td>';

			echo '<td style="text-align: right;">'.$row['callingPartyNumber'].'</td>';

			echo '<td style="text-align: right;">'.$row['originalCalledPartyNumber'].'</td>';

			echo '<td style="text-align: right;">'.$row['finalCalledPartyNumber'].'</td>';

			//Преобразование из UNIX timestamp в PHP DateTime
			echo '<td>'.date("Y-m-d H:i:s", substr($row['dateTimeOrigination'], 0, 10)).'</td>';

			//Преобразование из UNIX timestamp в PHP DateTime
			echo '<td>';
				$compareDate = date("Y-m-d H:i:s", substr($row['dateTimeConnect'], 0, 10));
				if($compareDate == '1970-01-01 05:00:00'){
					echo "";
				}else{
					echo $compareDate;
				}
			echo '</td>';

			//Преобразование из UNIX timestamp в PHP DateTime
			echo '<td>'.date("Y-m-d H:i:s", substr($row['dateTimeDisconnect'], 0, 10)).'</td>';

			//Преобразование длительности разговора
			echo '<td>'.date("i:s", $row['duration']).'</td>';

		echo '</tr>';
	}

	//Общее количество времени, потраченного на разговоры
	echo '<tr>
			<td colspan="7" style="text-align: right;font-weight: bold;">Total duration</td>
			<td>'.$h.':'.$m.':'.$s.'</td>
		</tr>';

	echo '</table>';
}

# Поля 'Входящий' и 'Исходящий'- не заполнены
elseif(empty($_POST['phone_in']) && empty($_POST['phone_out'])){

	//Проверяем, отмечен ли чекбокс для показа нулевых звонков
	if(isset($_POST['show_zero_calls'])){
				//Если чек бок отмечен
		$CountCalls = mysql_query('SELECT `callingPartyNumber`,
										  `originalCalledPartyNumber`,
										  `finalCalledPartyNumber`,
										  `dateTimeOrigination`,
										  `dateTimeConnect`,
										  `dateTimeDisconnect`,
										  `duration` 
								   FROM '.DB_TABLE.' 
								   WHERE `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
										 AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);
	}else{
				//Если чекбокс не отмечен
		$CountCalls = mysql_query('SELECT `callingPartyNumber`,
										  `originalCalledPartyNumber`,
										  `finalCalledPartyNumber`,
										  `dateTimeOrigination`,
										  `dateTimeConnect`,
										  `dateTimeDisconnect`,
										  `duration` 
								   FROM '.DB_TABLE.' 
								   WHERE `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
								   		 AND `dateTimeDisconnect` <= '.$GLOBALS['end_time'].' 
								   		 AND `duration` > 0');
	}
			
	//Производим подсчёт длительности всех разговоров
	$DurationCalls = mysql_query('SELECT SUM(`duration`) 
								  FROM '.DB_TABLE.' 
								  WHERE `dateTimeOrigination` >= '.$GLOBALS['begin_time'].' 
								  		AND `dateTimeDisconnect` <= '.$GLOBALS['end_time']);

		$ResultDurationCalls = mysql_result($DurationCalls, 0); //Выводим данные из базы в том формате, в котором они находятся в базе данных

		$h = floor($ResultDurationCalls / 3600);
		$m = floor(($ResultDurationCalls - $h * 3600) / 60);
		$s = floor($ResultDurationCalls - $h * 3600 - $m * 60);	
				
	echo '<table width="100%">
			<tr>
				<td class="head-cell">#</td>
				<td class="head-cell">Calling party number</td>
				<td class="head-cell">Called party number (orig.)</td>
				<td class="head-cell">Called party number (final)</td>
				<td class="head-cell">Origination time</td>
				<td class="head-cell">Connect time</td>
				<td class="head-cell">Disconnect time</td>
				<td class="head-cell">Duration</td>
			</tr>';

	while($row = mysql_fetch_array($CountCalls)){

		//Сводим данные в таблицу

		$RowCountCalls++; //Считаем количество звонков

		echo '<tr>';

			echo '<td>'.$RowCountCalls.'</td>';

			echo '<td style="text-align: right;">'.$row['callingPartyNumber'].'</td>';

			echo '<td style="text-align: right;">'.$row['originalCalledPartyNumber'].'</td>';

			echo '<td style="text-align: right;">'.$row['finalCalledPartyNumber'].'</td>';

			//Преобразование из UNIX timestamp в PHP DateTime
			echo '<td>'.date("Y-m-d H:i:s", substr($row['dateTimeOrigination'], 0, 10)).'</td>';

			//Преобразование из UNIX timestamp в PHP DateTime
			echo '<td>';
				$compareDate = date("Y-m-d H:i:s", substr($row['dateTimeConnect'], 0, 10));
				if($compareDate == '1970-01-01 05:00:00'){
					echo "";
				}else{
					echo $compareDate;
				}
			echo '</td>';

			//Преобразование из UNIX timestamp в PHP DateTime
			echo '<td>'.date("Y-m-d H:i:s", substr($row['dateTimeDisconnect'], 0, 10)).'</td>';

			//Преобразование длительности разговора
			echo '<td>'.date("i:s", $row['duration']).'</td>';

		echo '</tr>';
	}

	//Общее количество времени, потраченного на разговоры
	echo '<tr>
			<td colspan="7" style="text-align: right;font-weight: bold;">Total duration</td>
			<td>'.$h.':'.$m.':'.$s.'</td>
		</tr>';

	echo '</table>';
}

?>