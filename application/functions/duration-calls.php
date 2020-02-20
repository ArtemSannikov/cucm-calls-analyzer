<?php
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
?>