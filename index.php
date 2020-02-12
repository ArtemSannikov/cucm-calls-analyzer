<?php
	ini_set('dispay_errors', 1);
	# Подключение к базе данных
	require_once('application/db.php');
	# Константы
	require_once('application/define.php');
	# Файл с функциями для обработки
	require_once('application/function.php');
?>

<!DOCTYPE html>
<html lang="ru-RU">
<head>
	<!-- Charset -->
	<meta charset="UTF-8">
	<!-- Title -->
	<title>CUCM calls analyzer</title>
	<!-- Adaptive -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Favicon -->
	<link rel="shortcut icon" href="favicon/favicon.png" type="image/png">
	<link rel="shortcut icon" href="favicon/favicon.ico" type="image/x-icon"/>
	<link rel="apple-touch-icon" href="favicon/favicon_apple_60.png" type="image/png">
	<link rel="apple-touch-icon" sizes="76x76" href="favicon/favicon_apple_76.png" type="image/png">
	<link rel="apple-touch-icon" sizes="120x120" href="favicon/favicon_apple_120.png" type="image/png">
	<link rel="apple-touch-icon" sizes="152x152" href="favicon/favicon_apple_152.png" type="image/png">
	<!-- Style of css -->
	<link rel="stylesheet" type="text/css" media="print" href="css/reset.css" />
	<link rel="stylesheet" type="text/css" href="css/print.css" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<!-- Library jQuery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>

<body>

<div id="wrapper">

	<div class="left">

		<form method="POST">

			<div class="row">
				<label class="bold">Номер телефона:</label>
				<input type="text" name="phone_in" value="" placeholder="Входящий" maxlength="12" style="margin: 0 0 15px 0;">
				<input type="text" name="phone_out" value="" placeholder="Исходящий" maxlength="12">
			</div>

			<div class="row">
				<label><input type="checkbox" name="show_zero_calls">&nbsp;Несостоявшиеся звонки</label>
			</div>

			<div class="row">
				<label for="begintime_field" class="bold">От:</label>
				<input type="datetime-local" name="begin_time" value="<?php begintime(); ?>" id="begintime_field">
			</div>

			<div class="row">
				<label for="endtime_field" class="bold">До:</label>
				<input type="datetime-local" name="end_time" value="<?php endtime(); ?>" id="endtime_field">
			</div>

			<div class="row">
				<input type="submit" value="В виде таблицы" name="as_a_table">
			</div>

			<div class="row">
				<input type="submit" value="В виде текста" name="as_a_text">
			</div>

			<div class="row">
				<input type="submit" value="Длительность разговоров" name="duration_of_calls" class="green_submit">
			</div>

			<hr>

			<div class="row">
				<input type="submit" value="Текущий месяц" name="current_month" class="gray_submit">
			</div>

			<div class="row">
				<input type="submit" value="Предыдущий месяц" name="previous_month" class="gray_submit">
			</div>

			<div class="row" style="margin: 0 0 0 0;">
				<input type="submit" value="Неактивные номера" name="inactive_numbers" class="gray_submit">
			</div>
			
		</form>

	</div>

	<div class="right">
		
		<?php get_data_calls(); ?>

	</div>

</div>

</body>

</html>