Полный контекст ihona.tj
О проекте
ihona.tj — портал недвижимости Таджикистана, построенный на базе Liberu Real Estate (модульная Laravel-платформа).

Цель
Создать экосистему для:

Локального рынка — аренда/продажа жилья в Таджикистане

Иностранных туристов — гестхаусы, хостелы, горные туры

Инвесторов — аналитика, оценка, прогнозы

Бизнес-модель
Сегмент	Кто использует
Аренда	Местные жители, экспаты
Продажа	Покупатели, инвесторы
Туризм	Гестхаусы, хостелы, гиды
Аналитика	Агентства, банки, страховщики
Уникальные особенности для Таджикистана
Поле	Зачем
has_generator	Электричество в горных районах
mountain_view	Вид на Памир, Фанские горы
altitude	Высота над уровнем моря
water_source	Скважина, родник, привозная
max_guests	Для гестхаусов
Типы недвижимости
apartment — Квартира

house — Дом

guesthouse — Гестхаус

hostel — Хостел

land — Земельный участок

commercial — Коммерческая

cottage — Дача

Территории (города/регионы)
Название	Код
Душанбе	dushanbe
Худжанд	khujand
Бохтар	bokhtar
Куляб	kulyab
Памир (ГБАО)	pamir
Турсунзаде	tursunzade
Вахдат	vahdat
Языки
Код	Язык	Приоритет
ru	Русский	Основной
tg	Тоҷикӣ	Локальный
uz	O'zbek	Узбекская диаспора
en	English	Туристы
Валюта
TJS (Сомони) — основная валюта.

Платежи и финансы
Ипотека — через банки-партнёры

Страхование — Такаффул, London-Dushanbe

Оценка — AI + рыночные данные

Технический стек
Слой	Технология
Backend	Laravel 13, PHP 8.5
Admin	Filament 5
Frontend	Nuxt 3 (Vue 3)
БД	MySQL 8
Кэш	Redis (DB 1)
Очереди	Redis + Horizon
Веб-сервер	Nginx + PHP-FPM
Карты	Yandex Maps (основные), 2GIS (детализация)
AI	LLM для прогнозов цен
Роли пользователей
Роль	Доступ
super_admin	Полный доступ
host	Хозяин гестхауса
tourist	Турист
sales_agent	Менеджер
guide	Гид
Ключевые модули Liberu (включены)
real-estate-core — ядро

real-estate-properties — объекты

real-estate-parties — стороны

real-estate-listings — объявления

real-estate-valuations — оценка

real-estate-viewings — просмотры

real-estate-matching — подбор

localization-core — мультиязычность

analytics-google — Google Analytics

analytics-meta — Meta Analytics

Ключевые модули (отключены)
real-estate-rightmove — британский портал

real-estate-zoopla — британский портал

real-estate-onthemarket — британский портал

SEO-стратегия
Элемент	Подход
SSR	Nuxt 3 серверный рендеринг
Мультиязычность	prefix_except_default (ru как основной)
Карты	Yandex Maps для РФ/СНГ
Схемы	JSON-LD для недвижимости
Текущее состояние (август 2026)
✅ Backend работает (PHP-FPM + Nginx)

✅ Redis кэш (DB 1)

✅ Filament админка

✅ Google OAuth

✅ Миграции и сидеры

✅ Русские переводы (частично)

⏳ Frontend (Nuxt 3)

⏳ Yandex Maps интеграция

⏳ Роли для host/tourist/guide
