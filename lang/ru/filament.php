<?php

return [
    'nav_groups' => [
        'sales_lettings' => 'Продажи и аренда',
        'people_relationships' => 'Контакты и отношения',
        'property_management' => 'Управление недвижимостью',
        'marketing_portals' => 'Маркетинг и порталы',
        'insights_tools' => 'Аналитика и инструменты',
        'instructions_media' => 'Инструкции и медиа',
        'organisation' => 'Организация',
        'property_configuration' => 'Настройка объектов',
        'platform_settings' => 'Настройки платформы',
        'integrations_api' => 'Интеграции и API',
        'operations_diagnostics' => 'Эксплуатация и диагностика',
        'account_support' => 'Аккаунт и поддержка',
        'browse_discover' => 'Поиск и просмотр',
        'my_activity' => 'Моя активность',
    ],

    'resources' => [
        'user' => ['singular' => 'Пользователь', 'plural' => 'Пользователи'],
        'team' => ['singular' => 'Команда', 'plural' => 'Команды'],
        'status_definition' => ['singular' => 'Статус', 'plural' => 'Статусы'],
        'branch' => ['singular' => 'Филиал', 'plural' => 'Филиалы'],
        'territory' => ['singular' => 'Территория', 'plural' => 'Территории'],
        'agency' => ['singular' => 'Агентство', 'plural' => 'Агентства'],
        'instruction' => ['singular' => 'Инструкция', 'plural' => 'Инструкции'],
        'letting' => ['singular' => 'Аренда', 'plural' => 'Аренды'],
        'listing' => ['singular' => 'Объявление', 'plural' => 'Объявления'],
        'marketing_campaign' => ['singular' => 'Маркетинговая кампания', 'plural' => 'Маркетинговые кампании'],
        'media_document' => ['singular' => 'Медиафайл', 'plural' => 'Медиафайлы'],
        'offer' => ['singular' => 'Предложение', 'plural' => 'Предложения'],
        'onthemarket_sync' => ['singular' => 'Синхронизация OnTheMarket', 'plural' => 'Синхронизации OnTheMarket'],
        'portal_report' => ['singular' => 'Отчёт портала', 'plural' => 'Отчёты порталов'],
        'party' => ['singular' => 'Контрагент', 'plural' => 'Контрагенты'],
        'property_category' => ['singular' => 'Категория объекта', 'plural' => 'Категории объектов'],
        'property_template' => ['singular' => 'Шаблон объекта', 'plural' => 'Шаблоны объектов'],
        'property' => ['singular' => 'Объект недвижимости', 'plural' => 'Объекты недвижимости'],
        'management_record' => ['singular' => 'Запись управления', 'plural' => 'Записи управления'],
        'rightmove_sync' => ['singular' => 'Синхронизация Rightmove', 'plural' => 'Синхронизации Rightmove'],
        'sales_progression' => ['singular' => 'Сопровождение сделки', 'plural' => 'Сопровождения сделок'],
        'viewing' => ['singular' => 'Показ', 'plural' => 'Показы'],
        'zoopla_sync' => ['singular' => 'Синхронизация Zoopla', 'plural' => 'Синхронизации Zoopla'],
        'rental_application' => ['singular' => 'Заявка на аренду', 'plural' => 'Заявки на аренду'],
        'news_article' => ['singular' => 'Новость', 'plural' => 'Новости'],
        'inspection' => ['singular' => 'Инспекция', 'plural' => 'Инспекции'],
        'maintenance_request' => ['singular' => 'Заявка на обслуживание', 'plural' => 'Заявки на обслуживание'],
        'vendor_quote' => ['singular' => 'Расценка подрядчика', 'plural' => 'Расценки подрядчиков'],
        'work_order' => ['singular' => 'Наряд на работу', 'plural' => 'Наряды на работу'],
        'match_profile' => ['singular' => 'Профиль подбора', 'plural' => 'Профили подбора'],
        'valuation' => ['singular' => 'Оценка', 'plural' => 'Оценки'],
        'property_saved_search' => ['singular' => 'Сохранённый поиск', 'plural' => 'Сохранённые поиски'],
    ],

    'app_nav' => [
        'browse_properties' => 'Смотреть объекты',
        'search_properties' => 'Поиск объектов',
        'news_updates' => 'Новости',
        'calculators' => 'Калькуляторы',
        'saved_properties' => 'Избранное',
        'contact_support' => 'Связаться с поддержкой',
        'profile' => 'Профиль',
    ],

    'stub_page_note' => 'Filament-адаптер установлен. Бизнес-логика по-прежнему находится в соответствующем core-модуле.',

    'property' => [
        'fields' => [
            'title' => 'Заголовок',
            'status' => 'Статус',
            'address' => 'Адрес',
            'branch_id' => 'Филиал',
            'description' => 'Описание',
            'internal_notes' => 'Внутренние заметки',
            'price' => 'Цена',
            'currency' => 'Валюта',
            'bedrooms' => 'Спальни',
            'bathrooms' => 'Санузлы',
            'reception_rooms' => 'Гостиные',
            'area_sqft' => 'Площадь, м²',
            'year_built' => 'Год постройки',
            'property_category_id' => 'Категория',
            'property_template_id' => 'Шаблон объявления',
            'postal_code' => 'Почтовый индекс',
            'country' => 'Страна',
            'tenure' => 'Форма владения',
            'council_tax_band' => 'Налоговая категория',
            'energy_rating' => 'Класс энергоэффективности',
            'energy_score' => 'Оценка энергоэффективности',
            'walkability_score' => 'Индекс пешей доступности',
            'transit_score' => 'Индекс транспортной доступности',
            'bike_score' => 'Индекс велодоступности',
            'virtual_tour_url' => 'Ссылка на виртуальный тур',
            'virtual_tour_provider' => 'Провайдер виртуального тура',
            'live_tour_available' => 'Доступен живой показ',
            'model_3d_url' => 'Ссылка на 3D-модель',
            'floor_plan_image' => 'Планировка (изображение)',
            'is_featured' => 'Рекомендуемый',
            'holographic_enabled' => 'Голографический показ включён',
            'holographic_tour_url' => 'Ссылка на голографический тур',
            'holographic_provider' => 'Провайдер голографии',
            'features' => 'Особенности',
            'insurance_policy_id' => 'Полис страхования (ID)',
            'insurance_coverage_amount' => 'Страховая сумма',
            'insurance_premium' => 'Страховая премия',
            'insurance_expiry_date' => 'Дата окончания страховки',
        ],
        'statuses' => [
            'draft' => 'Черновик',
            'available' => 'Доступен',
            'under_offer' => 'Предложение принято',
            'sold' => 'Продан',
            'let' => 'Сдан в аренду',
            'withdrawn' => 'Снят',
            'For Sale' => 'На продажу',
            'For Rent' => 'В аренду',
            'to_let' => 'Сдаётся',
            'let_agreed' => 'Аренда согласована',
            'sold_stc' => 'Продан (сделка ожидает завершения)',
            'sstc' => 'Продан (сделка ожидает завершения)',
            'exchanged' => 'Договор подписан',
            'archived' => 'В архиве',
            'coming_soon' => 'Скоро в продаже',
            'Rented' => 'Сдан в аренду',
        ],
    ],

    'listing' => [
        'fields' => [
            'title' => 'Заголовок',
            'status' => 'Статус',
            'price' => 'Цена',
            'created_at' => 'Создано',
            'available_from' => 'Доступно с',
        ],
        'statuses' => [
            'draft' => 'Черновик',
            'ready' => 'Готово',
            'published' => 'Опубликовано',
            'suspended' => 'Приостановлено',
            'withdrawn' => 'Снято',
        ],
    ],

    'valuation' => [
        'fields' => [
            'subject' => 'Тема',
            'status' => 'Статус',
            'valued_amount' => 'Оценённая сумма',
            'fee_amount' => 'Сумма комиссии',
            'currency' => 'Валюта',
            'comparable_data' => 'Сравнительные данные',
            'recommendation' => 'Рекомендация',
            'scheduled_at' => 'Запланировано на',
            'follow_up_at' => 'Дата повторного контакта',
        ],
        'statuses' => [
            'draft' => 'Черновик',
            'scheduled' => 'Запланирована',
            'completed' => 'Завершена',
            'converted' => 'Конвертирована',
            'cancelled' => 'Отменена',
        ],
    ],

    'match_profile' => [
        'fields' => [
            'subject' => 'Тема',
            'score' => 'Оценка соответствия',
            'party_id' => 'Контрагент (ID)',
            'requirements' => 'Требования',
            'affordability' => 'Доступность',
            'preferences' => 'Предпочтения',
            'alerts' => 'Оповещения',
            'feedback' => 'Отзыв',
            'exclusions' => 'Исключения',
        ],
    ],

    'offer' => [
        'fields' => [
            'subject' => 'Тема',
            'amount' => 'Сумма',
            'currency' => 'Валюта',
            'terms' => 'Условия',
            'qualification' => 'Квалификация покупателя',
            'negotiation' => 'Переговоры',
            'proof' => 'Подтверждение платёжеспособности',
            'conditions' => 'Условия сделки',
        ],
        'statuses' => [
            'draft' => 'Черновик',
            'submitted' => 'Подано',
            'countered' => 'Встречное предложение',
            'accepted' => 'Принято',
            'rejected' => 'Отклонено',
            'withdrawn' => 'Отозвано',
        ],
    ],

    'media_document' => [
        'fields' => [
            'kind' => 'Тип',
            'path' => 'Путь к файлу',
            'title' => 'Заголовок',
            'sort_order' => 'Порядок сортировки',
        ],
        'kinds' => [
            'photo' => 'Фото',
            'floorplan' => 'План этажа',
            'video' => 'Видео',
            'certificate' => 'Сертификат',
            'brochure' => 'Брошюра',
            'document' => 'Документ',
        ],
    ],

    'viewing' => [
        'fields' => [
            'subject' => 'Тема',
            'status' => 'Статус',
            'starts_at' => 'Начало',
            'ends_at' => 'Окончание',
            'access' => 'Доступ',
            'accompaniment' => 'Сопровождение',
            'reminders' => 'Напоминания',
            'feedback' => 'Отзыв',
        ],
        'statuses' => [
            'requested' => 'Запрошен',
            'confirmed' => 'Подтверждён',
            'completed' => 'Завершён',
            'cancelled' => 'Отменён',
            'no_show' => 'Неявка',
        ],
    ],

    'portal_report' => [
        'fields' => [
            'portal' => 'Портал',
            'report_type' => 'Тип отчёта',
            'property_id' => 'Объект (ID)',
            'listing_id' => 'Объявление (ID)',
            'status' => 'Статус',
            'error' => 'Ошибка',
        ],
    ],

    'marketing_campaign' => [
        'fields' => [
            'name' => 'Название',
            'channel' => 'Канал',
            'property_id' => 'Объект (ID)',
            'listing_id' => 'Объявление (ID)',
            'status' => 'Статус',
            'notes' => 'Заметки',
        ],
    ],

    'sales_progression' => [
        'fields' => [
            'subject' => 'Тема',
            'property_id' => 'Объект (ID)',
            'offer_id' => 'Предложение (ID)',
            'status' => 'Статус',
            'notes' => 'Заметки',
        ],
    ],

    'vendor_quote' => [
        'fields' => [
            'vendor_id' => 'Подрядчик (ID)',
            'property_id' => 'Объект (ID)',
            'work_description' => 'Описание работ',
            'quote_amount' => 'Сумма расценки',
            'quote_date' => 'Дата расценки',
            'valid_until' => 'Действительно до',
            'status' => 'Статус',
        ],
        'statuses' => [
            'pending' => 'Ожидание',
            'accepted' => 'Принято',
            'rejected' => 'Отклонено',
            'expired' => 'Истекло',
            'withdrawn' => 'Отозвано',
        ],
    ],

    'work_order' => [
        'fields' => [
            'property_id' => 'Объект (ID)',
            'vendor_id' => 'Подрядчик (ID)',
            'title' => 'Заголовок',
            'description' => 'Описание',
            'work_type' => 'Вид работ',
            'status' => 'Статус',
        ],
        'statuses' => [
            'pending' => 'Ожидание',
            'approved' => 'Одобрено',
            'scheduled' => 'Запланировано',
            'in_progress' => 'В работе',
            'completed' => 'Завершено',
            'cancelled' => 'Отменено',
        ],
    ],

    'party' => [
        'fields' => [
            'type' => 'Тип',
            'name' => 'Имя',
            'email' => 'Email',
            'phone' => 'Телефон',
        ],
        'types' => [
            'applicant' => 'Заявитель',
            'buyer' => 'Покупатель',
            'vendor' => 'Продавец',
            'landlord' => 'Арендодатель',
            'tenant' => 'Арендатор',
            'solicitor' => 'Юрист',
            'contractor' => 'Подрядчик',
            'tourist' => 'Турист',
            'guide' => 'Гид',
        ],
    ],

    'rental_application' => [
        'fields' => [
            'property_id' => 'Объект (ID)',
            'party_id' => 'Контрагент (ID)',
            'status' => 'Статус',
            'employment_status' => 'Статус занятости',
            'annual_income' => 'Годовой доход',
            'desired_move_in_date' => 'Желаемая дата заезда',
        ],
        'statuses' => [
            'draft' => 'Черновик',
            'submitted' => 'Подано',
            'under_review' => 'На рассмотрении',
            'approved' => 'Одобрено',
            'rejected' => 'Отклонено',
        ],
    ],

    'maintenance_request' => [
        'fields' => [
            'property_id' => 'Объект (ID)',
            'title' => 'Заголовок',
            'description' => 'Описание',
            'priority' => 'Приоритет',
            'status' => 'Статус',
        ],
        'priorities' => [
            'low' => 'Низкий',
            'normal' => 'Обычный',
            'high' => 'Высокий',
            'urgent' => 'Срочный',
        ],
        'statuses' => [
            'pending' => 'Ожидание',
            'in_progress' => 'В работе',
            'completed' => 'Завершено',
            'cancelled' => 'Отменено',
        ],
    ],

    'inspection' => [
        'fields' => [
            'property_id' => 'Объект (ID)',
            'type' => 'Тип',
            'status' => 'Статус',
            'scheduled_at' => 'Запланировано на',
            'notes' => 'Заметки',
        ],
        'types' => [
            'routine' => 'Плановая',
            'check_in' => 'При заезде',
            'check_out' => 'При выезде',
            'mid_tenancy' => 'В середине срока аренды',
        ],
        'statuses' => [
            'scheduled' => 'Запланирована',
            'in_progress' => 'В процессе',
            'completed' => 'Завершена',
            'cancelled' => 'Отменена',
        ],
    ],

    'user_form' => [
        'fields' => [
            'name' => 'Имя',
            'email' => 'Email',
            'password' => 'Пароль',
            'email_verified_at' => 'Email подтверждён',
            'roles' => 'Роли',
        ],
    ],

    'zoopla_sync' => [
        'fields' => [
            'listing_id' => 'Объявление (ID)',
            'property_id' => 'Объект (ID)',
            'external_id' => 'Внешний ID',
            'status' => 'Статус',
        ],
    ],

    'rightmove_sync' => [
        'fields' => [
            'listing_id' => 'Объявление (ID)',
            'property_id' => 'Объект (ID)',
            'external_id' => 'Внешний ID',
            'status' => 'Статус',
        ],
    ],

    'onthemarket_sync' => [
        'fields' => [
            'listing_id' => 'Объявление (ID)',
            'property_id' => 'Объект (ID)',
            'external_id' => 'Внешний ID',
            'status' => 'Статус',
        ],
    ],

    'management_record' => [
        'fields' => [
            'subject' => 'Тема',
            'capability' => 'Функция',
            'status' => 'Статус',
            'failure_reason' => 'Причина сбоя',
        ],
        'capabilities' => [
            'rent_schedule' => 'График арендных платежей',
            'statements' => 'Отчётность',
            'inspections' => 'Инспекции',
            'compliance' => 'Соответствие требованиям',
            'maintenance' => 'Обслуживание',
            'contractors' => 'Подрядчики',
            'owner_approvals' => 'Согласования собственника',
        ],
        'statuses' => [
            'draft' => 'Черновик',
            'in_progress' => 'В работе',
            'completed' => 'Завершено',
            'cancelled' => 'Отменено',
        ],
    ],

    'news_article' => [
        'fields' => [
            'title' => 'Заголовок',
            'slug' => 'Слаг (URL)',
            'content' => 'Содержание',
            'published_at' => 'Дата публикации',
        ],
    ],

    'letting' => [
        'fields' => [
            'subject' => 'Тема',
            'capability' => 'Функция',
            'status' => 'Статус',
            'failure_reason' => 'Причина сбоя',
        ],
        'capabilities' => [
            'applications' => 'Заявки',
            'referencing' => 'Проверка арендатора',
            'deposits' => 'Депозиты',
            'agreements' => 'Договоры',
            'move_in_out' => 'Заезд/выезд',
            'renewals' => 'Продления',
            'rent_changes' => 'Изменение арендной платы',
            'notices' => 'Уведомления',
        ],
        'statuses' => [
            'draft' => 'Черновик',
            'in_progress' => 'В работе',
            'completed' => 'Завершено',
            'cancelled' => 'Отменено',
        ],
    ],

    'instruction' => [
        'fields' => [
            'subject' => 'Тема',
            'status' => 'Статус',
            'approved_at' => 'Одобрено',
            'withdrawn_at' => 'Отозвано',
        ],
        'statuses' => [
            'draft' => 'Черновик',
            'pending_approval' => 'На согласовании',
            'approved' => 'Одобрено',
            'withdrawn' => 'Отозвано',
            'rejected' => 'Отклонено',
        ],
    ],

    'status_definition_form' => [
        'fields' => [
            'entity' => 'Сущность',
            'key' => 'Ключ',
            'label' => 'Отображаемое название',
            'active' => 'Активен',
        ],
    ],

    'branch_form' => [
        'fields' => [
            'name' => 'Название',
            'code' => 'Код',
            'email' => 'Email',
            'phone' => 'Телефон',
        ],
    ],

    'territory' => [
        'fields' => [
            'name' => 'Название',
            'code' => 'Код',
            'boundary' => 'Граница (JSON)',
        ],
    ],

    'team_form' => [
        'fields' => [
            'name' => 'Название',
            'user_id' => 'Владелец',
            'personal_team' => 'Личная команда',
        ],
    ],

    'property_template' => [
        'fields' => [
            'name' => 'Название',
            'content' => 'Содержимое шаблона',
        ],
    ],

    'property_saved_search' => [
        'fields' => [
            'name' => 'Название',
            'criteria' => 'Критерии поиска',
        ],
    ],

    'property_category' => [
        'fields' => [
            'name' => 'Название',
            'slug' => 'Слаг (URL)',
        ],
    ],

    'agency_form' => [
        'fields' => [
            'name' => 'Название',
            'code' => 'Код',
            'active' => 'Активно',
        ],
    ],

    'pages' => [
        'activity_comments' => 'Активность и комментарии',
        'analytics_core' => 'Аналитика (ядро)',
        'analytics_google' => 'Аналитика Google',
        'analytics_meta' => 'Аналитика Meta',
        'api_access' => 'Доступ к API',
        'application_core' => 'Ядро приложения',
        'audit' => 'Аудит',
        'currency_context' => 'Валюты',
        'developer_experience' => 'Инструменты разработчика',
        'feature_flags' => 'Флаги функций',
        'files_media' => 'Файлы и медиа',
        'import_export' => 'Импорт и экспорт',
        'integrations' => 'Интеграции',
        'jetstream_bridge' => 'Мост Jetstream',
        'localization' => 'Локализация',
        'notifications' => 'Уведомления',
        'observability' => 'Наблюдаемость',
        'profiles' => 'Профили',
        'scheduler_queues' => 'Планировщик и очереди',
        'search' => 'Поиск',
        'two_factor_authentication' => 'Двухфакторная аутентификация',
        'webhooks' => 'Вебхуки',
        'foundation_operations' => 'Операции платформы',
        'manage_site_settings' => 'Настройки сайта',
    ],
];
