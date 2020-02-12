<?php

/**
 * Устанавливаем константы
**/

	# Идентификатор строки
	define("ID_ROWS", "id");

	# Номер вызывающего абонента
	define("CALLING_NUMBER", "callingPartyNumber");

	# Номер, на который был выполнен исходящий вызов, до переадресации вызова
	define("ORIG_CALLED_NUMBER", "originalCalledPartyNumber");
	
	# Номер вызываемого абонента
	define("FINAL_CALLED_NUMBER", "finalCalledPartyNumber");
	
	# Дата и время звонка, в формате EPOCH (UTC)
	define("CALL_BEGIN_TIME", "dateTimeOrigination");
	
	# Время установления сеанса связи, разговора
	define("CALL_CONNECT_TIME", "dateTimeConnect");
	
	# Время завершения сеанса связи, разговора
	define("CALL_END_TIME", "dateTimeDisconnect");
	
	# Общая длительность разговора
	define("CALL_DURATION", "duration");

	# Имя целевого устройства вызывающего абонента
	define("ORIG_DEVICE_NAME", "origDeviceName");

?>