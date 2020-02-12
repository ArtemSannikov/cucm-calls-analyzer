CUCM calls analyzer
=====================

**CUCM** (Cisco Unified Communications Manager) - телефонная станция, которая является системой для обработки вывозов на базе программного обеспечения. CUCM работает с такими элементами сетей передачи голоса поверх протокола IP (VoIP) как: шлюзы, телефонные аппараты, мосты для конференцсвязи, голосовая почта, видеоконференцсвязью и многими другими.

**Важно**: система написана в процедурном стиле, в дальнейшем будет переведена на ООП

### Интерфейс

![Интерфейс](https://user-images.githubusercontent.com/31792522/74318714-900aff00-4d9f-11ea-8793-2f1f4dfa2ddd.jpg)

### Как запустить?

Чтобы запустить Web-морду, укажите следующие параметры в файле **_application/db.php_**:

```php
define('DB_USER', ''); // Пользователь БД
define('DB_PASSWORD', ''); // Пароль БД
define('DB_NAME', ''); // Имя БД
```

### Оптимизация базы данных

Чтобы ускорить выборку данных, прибегаем к созданию индексов для определённых полей в таблице.

* dateTimeOrigination (дата и время звонка, в формате EPOCH UTC)
* dateTimeDisconnect (время завершения сеанса связи, разговора)
* dateTimeConnect (время установления сеанса связи, разговора)

Выполняем SQL запросы для создания индексов

```sql
-- Создание INDEX
CREATE INDEX имя_индекса ON таблица(поле_для которого_нужно_создать_индекс);
-- Удаление INDEX
DROP INDEX имя_индекса ON таблица;
```

**Результат работы индексов (реальный пример)**

Для сравнения результата выполним простой SQL запрос к базе, до внедрения индексов и после.

```sql
SELECT `callingPartyNumber`,
	   `originalCalledPartyNumber`,
	   `finalCalledPartyNumber`,
	   `dateTimeOrigination`,
	   `dateTimeConnect`,
	   `dateTimeDisconnect`,
	   `duration` 
FROM `cdr` 
WHERE `dateTimeOrigination` >= 1577836800 AND `dateTimeDisconnect` <= 1580342400;
```

Выполняем SQL запрос (без индексов), время обработки: 24.8475 сек

![Без индексов](https://user-images.githubusercontent.com/31792522/74313064-c8591000-4d94-11ea-899e-96c722da15c1.jpg)

Выполняем ещё раз SQL запрос (после создания индексов), время обработки: 0.0024 сек

Кэш перед повторным запросом был очищен

![После индексов](https://user-images.githubusercontent.com/31792522/74313077-cd1dc400-4d94-11ea-9e74-dc0687fa0d02.jpg)


### Обновления

**Версия 0.1**

Раздел: Текущий месяц, Предыдущий месяц, Неактивные номера

Теперь преобразование даты из формата ГГГГ-ММ-ДД ЧЧ:ММ в UNIX (EPOCH) выполняет внутренняя функция SQL - UNIX_TIMESTAMP(), а не функция на строне PHP.