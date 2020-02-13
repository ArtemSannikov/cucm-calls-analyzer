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

define('CDR_TABLE', ''); // Таблица для работы
```

### Алгоритм работы

**Кнопки, которые зависят от всех введённых данных в форму**

Текст

**Кнопка, которая зависит от введённой даты в поле "От" и "До"**

_Длительность разговоров_ - выводится статистика в виде текста, которая содержит информацию по общей длительности разговоров (поиск выполняется по маскам).

``Количество входящих: (*???????)``

``Количество исходящих город: (*343??????? + ??????? + *80????????)``

``Количество исходящих на сотовые телефоны: (*9?????????)``

``Количество исходящих межгород: (??????????? - *9????????? - *343??????? - *80????????)``

``Количество конференций: (b* + 00* + 87???)``

``Количество внутренних звонков: (????? + ????? + b* + 00*)``

``Количество видеозвонков: SQL запрос, destVideoCap_Codec > 0``

Информация по маскам:

* ????? - внутренние пятизнаки
* b* и 00*- конференц-звонки при спонтанном создании
* 87??? - комнаты конференций (планируемые конференции)
* ???????????/*??????? - все звонки наружу
* *9????????? - все звонки на сотики
* *343???????/??????? - все звонки по Екатеринбургу
* *80???????? - звонки на бесплатные 8-800

**Кнопки, которые не зависят от данных в форме**

_Неактивные номера_ - от текущей даты отсчитывается 90 дней, и просматривается активность за этот промежуток. Если активность устройства была нулевая, выводится имя устройства и его номер.

_Предыдущий месяц_, _Текущий месяц_ - выводится статистика в виде текста со следующей информацией: состоявшиеся и несостоявшиеся звонки, а так же подсчитывается их количество и общая длительность).

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

**Версия 0.1, Дата: 2020-02-12**

Раздел: Текущий месяц, Предыдущий месяц, Неактивные номера

Теперь преобразование даты из формата ГГГГ-ММ-ДД ЧЧ:ММ в UNIX (EPOCH) выполняет внутренняя функция SQL - UNIX_TIMESTAMP(), а не функция на строне PHP.