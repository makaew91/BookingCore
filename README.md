<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Запуск проекта (Docker + PostgreSQL)

### Требования
- Docker и Docker Compose v2

### Быстрый старт
1. Скопируйте окружение для Docker:
   ```bash
   cp .env.docker.example .env
   ```
2. Соберите и поднимите контейнеры:
   ```bash
   docker compose up -d --build
   ```
3. Примените миграции внутри контейнера:
   ```bash
   docker compose exec app php artisan migrate --force
   ```
4. Приложение доступно на `http://localhost:8000`.

Замечание: в `docker-compose.yml` настроена БД PostgreSQL (db), а `Dockerfile` собирает PHP 8.3 с расширением `pdo_pgsql`.

## Модуль бронирований охотничьих туров

### Миграции и модели
- `guides` (`App\Models\Guide`): `name`, `experience_years`, `is_active`
- `hunting_bookings` (`App\Models\HuntingBooking`): `tour_name`, `hunter_name`, `guide_id`, `date`, `participants_count`
- Ограничения: `unique (guide_id, date)` — один гид не может иметь два бронирования на одну дату

### Бизнес-правила
- У гида не должно быть другого бронирования на ту же дату
- `participants_count <= 10`

### API
- GET `/api/guides` — список активных гидов
  - Параметры: `min_experience` (необязательно) — фильтр по минимальному опыту
  - Ответ: массив ресурсів `GuideResource` (`id`, `name`, `experience_years`)

- POST `/api/bookings` — создание нового бронирования
  - Тело запроса: `tour_name`, `hunter_name`, `guide_id`, `date(YYYY-MM-DD)`, `participants_count`
  - Валидация: `StoreHuntingBookingRequest`
  - Ответы:
    - 201 — успешно создано (`HuntingBookingResource`)
    - 404 — гид не найден или не активен
    - 422 — ошибки валидации (в т.ч. занят гид на дату, лимит участников)

### Примеры запросов
Список гидов (минимум 3 года опыта):
```bash
curl "http://localhost:8000/api/guides?min_experience=3"
```

Создание бронирования:
```bash
curl -X POST http://localhost:8000/api/bookings \
  -H "Content-Type: application/json" \
  -d '{
    "tour_name": "Duck Hunt",
    "hunter_name": "John",
    "guide_id": 1,
    "date": "2025-10-20",
    "participants_count": 3
  }'
```

### Тесты
Запуск тестов в контейнере:
```bash
docker compose exec app php artisan test --colors=always
```

## Коротко об интеграции в ядро BookingCore
- Вынести модуль в отдельный пакет/модуль (например, `modules/Hunting`), зарегистрировать `ServiceProvider`
- Подключить роуты модуля в общий API-роутер BookingCore (единый префикс и middleware)
- Применить политики/права доступа при необходимости (например, только авторизованные охотники создают бронирования)
- Настроить миграции модуля через авто-загрузку в провайдере (publish/auto-discovery)
- Обсудить кросс-модульные зависимости (например, пользователи/платежи) и использовать события/слушателей для слабой связности
