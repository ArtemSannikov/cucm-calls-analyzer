<?php
	// Выполняем запрос к базе данных
	$mysql_query = mysql_query("SELECT `origDeviceName`,`callingPartyNumber`, MAX(`dateTimeOrigination`) 
								FROM ".DB_TABLE." 
								WHERE LENGTH(`callingPartyNumber`) = 5 
								GROUP BY `callingPartyNumber` 
								HAVING MAX(`dateTimeOrigination`) < UNIX_TIMESTAMP(DATE_ADD(NOW(), INTERVAL -90 day)) 
									   AND `origDeviceName` NOT LIKE 'CCX%' 
									   AND `origDeviceName` NOT LIKE '9%' 
									   AND `origDeviceName` NOT LIKE '8%'");

	echo '<h3>Неактивные номера</h3>
		  <p>Выводятся все номера, чья активность была более чем 90 дней назад.</p>
		  <hr/>';

	echo '<table width="100%">
			<tr>
				<td class="head-cell"><strong>#</strong></td>
				<td class="head-cell"><strong>callingPartyNumber</strong></td>
				<td class="head-cell"><strong>origDeviceName</strong></td>
			</tr>';

		while($row = mysql_fetch_assoc($mysql_query)){
			$count_device++;
			echo '<tr>';
				echo '<td>'.$count_device.'</td>';
				echo '<td>'.$row['callingPartyNumber'].'</td>';
				echo '<td>'.$row['origDeviceName'].'</td>';
			echo '</tr>';
		}

	echo '</table>';

?>