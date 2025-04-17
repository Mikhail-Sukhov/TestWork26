/**
 * Основной модуль инициализации при загрузке DOM
 * Использует jQuery.ready() для безопасного доступа к DOM элементам
 */
jQuery(document).ready(function($) {
    /**
     * @type {jQuery} $form - Форма поиска городов
     * Селектор формы с ID 'city-search-form'
     */
    const $form = $('#city-search-form');

    /**
     * @type {jQuery} $input - Поле ввода поиска
     * Селектор input с name="search" внутри формы
     */
    const $input = $form.find('input[name="search"]');

    /**
     * @type {jQuery} $tbody - Тело таблицы погоды
     * Селектор tbody внутри элемента с классом 'weather-table'
     */
    const $tbody = $('.weather-table tbody');

    // Обработка отправки формы
    $form.on('submit', function(e) {
        e.preventDefault(); // Предотвращение стандартного поведения формы
        performSearch(); // Вызов функции поиска
    });

    // Обработка ввода в поле поиска
    $input.on('input', function() {
        performSearch(); // Вызов функции поиска при каждом изменении ввода
    });

    /**
     * Выполнение AJAX-поиска
     * Использует локализованные параметры из PHP
     * @function performSearch
     * @description Отправляет AJAX-запрос на сервер для поиска городов
     * и обновляет таблицу результатов
     * @fires $.ajax
     * @see weatherTable.ajaxurl - URL для AJAX-запроса
     * @see weatherTable.nonce - Токен безопасности WordPress
     */
    function performSearch() {
        $.ajax({
            url: weatherTable.ajaxurl, // URL из локализованных данных PHP
            type: 'POST', // Метод запроса
            dataType: 'json', // Ожидаемый формат ответа
            data: {
                action: 'city_search', // Действие для обработки в WordPress
                nonce: weatherTable.nonce, // CSRF-токен
                search: $input.val() // Значение из поля ввода
            },
            beforeSend: function() {
                // Отображение состояния загрузки
                $tbody.html(`
                    <tr>
                        <td colspan="3">${weatherTable.loadingText}</td>
                    </tr>
                `);
            },
            success: function(response) {
                if (response.success) {
                    if (response.data.length > 0) {
                        // Генерация HTML-строк таблицы из данных ответа
                        const rows = response.data.map(item => `
                            <tr>
                                <td>${item.countries || '-'}</td>
                                <td>${item.title}</td>
                                <td>${item.temperature}</td>
                            </tr>
                        `).join('');
                        $tbody.html(rows);
                    } else {
                        // Отображение сообщения об отсутствии результатов
                        $tbody.html(`
                            <tr>
                                <td colspan="3">${weatherTable.noResultsText}</td>
                            </tr>
                        `);
                    }
                } else {
                    // Отображение общего сообщения об ошибке
                    $tbody.html(`
                        <tr>
                            <td colspan="3">${weatherTable.genericErrorText}</td>
                        </tr>
                    `);
                }
            },
            error: function() {
                // Обработка сетевых ошибок
                $tbody.html(`
                    <tr>
                        <td colspan="3">${weatherTable.genericErrorText}</td>
                    </tr>
                `);
            }
        });
    }
});