# Storefront Child Theme

Дочерняя тема для Storefront, расширяющая функционал WooCommerce-магазина возможностью отображения погоды в городах. Включает кастомные типы записей, AJAX-поиск и интеграцию с OpenWeatherMap API.

## Основные возможности
- **Кастомный тип записи "Cities"** с метаполями для координат
- **Таксономия "Countries"** для группировки городов
- **Виджет погоды** с выбором города из CPT
- **Страница с таблицей погоды** по городам с AJAX-поиском

## Требования
- Тема Storefront
- API-ключ [OpenWeatherMap](https://openweathermap.org/appid)

## Установка
1. Скопируйте папку `storefront-child` в `wp-content/themes/`
2. Активируйте дочернюю тему в админ панели WordPress
3. Вставьте API-ключ OpenWeatherMap в **Настройки → Общие → OpenWeatherMap API Key**

## Структура проекта

### Корневые файлы
- `style.css` - Основные стили дочерней темы
- `functions.php` - Подключение скриптов, стилей и модулей:
  - Регистрация текстового домена для перевода
  - Подключение родительских стилей
  - Загрузка функциональных модулей из `/inc`

### Папка `/inc`
#### `/admin`
- `weather-api-settings.php` - Настройки API погоды в админке
#### `/ajax`
- `weather-handlers.php` - Обработчики AJAX-запросов для поиска городов
#### `/helpers`
- `get-city-temperature.php` - Функция получения погоды с кэшированием
#### `/post-types`
- `cities.php` - Регистрация CPT "Cities" и таксономии "Countries"
- `meta-boxes-cities.php` - Метабокс для ввода координат
#### `/widget`
- `weather-cities.php` - Виджет отображения погоды для выбранного города
### Папка `/assets`
- `page-weather-table.js` - Логика AJAX-поиска на странице таблицы

### Шаблоны
- `page-weather-table.php` - Кастомный шаблон страницы с таблицей погоды:
  - Поддержка хуков `before_weather_table` и `after_weather_table`
  - Вывод данных через $wpdb с производительной выборкой

## Настройка
1. Создайте записи типа "Cities" с координатами
2. Назначьте страны через таксономию "Countries"
3. Добавьте виджет "Weather City Widget" в сайдбар
4. Создайте страницу с шаблоном "Weather Table" для вывода таблицы


## Хуки для разработчиков
- `before_weather_table` - Перед выводом таблицы
- `after_weather_table` - После вывода таблицы
